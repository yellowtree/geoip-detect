<?php

namespace YellowTree\GeoipDetect\CheckCompatibility;

class Maxmind {
    public $files = null;
    public $filesByOthers = null;
    public $checksumResult = null;

    protected $adminNotices = [];

    /**
     * Get a unique Id for the situation
     */
    function getId() {
        $this->filesChecksums();

        $encoded = md5(serialize($this->checksumResult));
        return $encoded;
    }

    function getFiles() {
        if (is_array($this->filesByOthers)) return;

        // Load files from autocomposer
        try {
            new \GeoIp2\Database\Reader('');
        } catch(\Throwable $e) { }
        try {
            new \MaxMind\Db\Reader('');
        } catch(\Throwable $e) { }
    
    
        $loaded = get_included_files();
        $this->files = array_filter($loaded, function($value) {
            return str_ends_with($value, 'Reader.php');
        });
        $this->filesByOthers = array_filter($this->files, function($value) {
            return !str_contains($value, '/plugins/geoip-detect/');
        });

        return $this->filesByOthers;
    }

    function makePathRelative($absolutePath) {
            // Simplistic: no realpath used
            if (str_starts_with($absolutePath, ABSPATH)) {
                return mb_substr($absolutePath, mb_strlen(ABSPATH) - 1);
            };
            return $absolutePath;
        }

    function filesChecksums() {
        if ($this->checksumResult) {
            return;
        }
        $this->getFiles();

        if (!$this->filesByOthers) {
            $this->checksumResult = [];
            return false;
        }

        $localFiles = [
            '/vendor/maxmind-db/reader/src/MaxMind/Db/Reader.php',
            '/vendor/geoip2/geoip2/src/Database/Reader.php',
        ];

        $md5_whitelist = [];
        foreach($localFiles as $file) {
            if (!is_file(GEOIP_PLUGIN_DIR . $file) && GEOIP_DETECT_DEBUG) {
                \trigger_error('Weird. The file ' . $file . ' missing.');
                continue;
            }
            $md5_whitelist[] = md5_file(GEOIP_PLUGIN_DIR . $file);
        }

        foreach($this->filesByOthers as $file) {
            $checksum = md5_file($file);
            $this->checksumResult[$file] = in_array($checksum, $md5_whitelist);
        }
    }

    function checkCompatible() {
        // Only show on plugin pages
        if (empty($_GET['page']) || $_GET['page'] !== 'geoip-detect/geoip-detect.php') {
            return;
        }

        $readerClassInfo = new \ReflectionClass('\MaxMind\Db\Reader');
        if (!$readerClassInfo->hasMethod('getWithPrefixLen')) {
            $this->getFiles();
            $data = implode(' , ', array_map([$this, 'makePathRelative'], $this->filesByOthers));
            $line1 = __('Appearently, there is another plugin installed that also uses the Maxmind libraries, but their version of these libraries is outdated.', 'geoip-detect');
            $line2 = __('These incompatible files have been found to be loaded from another plugin: ', 'geoip-detect') . $data;
            $line3 = __('Please test if looking up an IP adress works without an PHP Error. If it works, you can dismiss this notice. It will appear again when their libraries are changed.', 'geoip-detect');

            $body = <<<BODY
<p><i>$line1</i></p>
<p>$line2</p>
<p>$line3</p>
BODY;
            $this->adminNotices[] = [
                'id' => 'maxmind_vendor_old_' . md5($data),
                'title' => __('Geolocation IP Detection: Warning: Old Maxmind Libraries detected.', 'geoip-detect'),
                'body' => $body,
            ];
            add_action( 'all_admin_notices', [$this, 'admin_notice'] );
            return false;
        }
        return true;
    }

    function admin_notice() {
        foreach ($this->adminNotices as $notice) {
            geoip_detect_admin_notice_template($notice['id'], $notice['title'], $notice['body'], true);
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