<?php

namespace YellowTree\GeoipDetect\CheckCompatibility;

class Maxmind {
    public $files = null;
    public $filesByOthers = null;
    protected $adminNotices = [];

    function getFiles() {
        // Load files from autocomposer
        try {
            new \GeoIp2\Database\Reader('');
        } catch(\Throwable $e) { }
        try {
            new \MaxMind\Db\Reader('');
        } catch(\Throwable $e) { }
    
    
        $loaded = get_included_files();
        $loaded = array_map(function($absolutePath) {
            // Simplistic: no realpath used
            if (str_starts_with($absolutePath, ABSPATH)) {
                return mb_substr($absolutePath, mb_strlen(ABSPATH) - 1);
            };
            return $absolutePath;
        }, $loaded);
        $this->files = array_filter($loaded, function($value) {
            return str_ends_with($value, 'Reader.php');
        });
        $this->filesByOthers = array_filter($this->files, function($value) {
            return !str_contains($value, '/plugins/geoip-detect/');
        });
        return $this->filesByOthers;
    }

    function checkCompatible() {
        $readerClassInfo = new \ReflectionClass('\MaxMind\Db\Reader');
        if (!$readerClassInfo->hasMethod('getWithPrefixLen2')) {
            // This would raise an Fatal error during lookup.
            define('GEOIP_DETECT_LOOKUP_DISABLED', true);

            $this->getFiles();
            $data = implode(' , ', $this->filesByOthers);
            $line1 = __('Appearently, there is another plugin installed that also uses the Maxmind libraries, but their version of these libraries is outdated.', 'geoip-detect');
            $line2 = __('These files have been found to be loaded from another plugin: ', 'geoip-detect') . $data;
            $line3 = __('Please disable that plugin, update that plugin or use a different data source in Geolocation IP Detection. Until then, the lookup for Maxmind sources is disabled.', 'geoip-detect');

            $body = <<<BODY
<p><i>$line1</i></p>
<p>$line2</p>
<p>$line3</p>
BODY;
            $this->adminNotices[] = [
                'id' => 'maxmind_vendor_old_' . md5($data),
                'title' => __('Geolocation IP Detection: Error: Old Maxmind Libraries detected.', 'geoip-detect'),
                'body' => $body,
            ];
            add_action( 'all_admin_notices', [$this, 'admin_notice'] );
            return false;
        }
        return true;
    }

    function admin_notice() {
        foreach ($this->adminNotices as $notice) {
            geoip_detect_admin_notice_template($notice['id'], $notice['title'], $notice['body']);
        }
    }
}


function geoip_detect_check_incompabilities() {
    if (get_option('geoip-detect-source') === 'auto' || get_option('geoip-detect-source') === 'manual') {
        $info = new Maxmind;
        $info->checkCompatible();
    }
}
add_action('plugins_loaded', __NAMESPACE__ . '\geoip_detect_check_incompabilities', 100);