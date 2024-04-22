export const options = window.geoip_detect?.options || {
    ajaxurl: "/wp-admin/admin-ajax.php",
    default_locales: ['en'],
    cookie_duration_in_days: 7,
    cookie_name: 'geoip-detect-result',
    do_body_classes: false
};
