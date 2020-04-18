<?php
/*

Plugin Name: Embed Video Thumbnail
Plugin URI: https://www.ikanaweb.fr
Description: Customize and automatically replace embed videos with their thumbnail
Version: 1.3.1
Author: ikanaweb
Author URI: https://www.ikanaweb.fr
Text Domain: embed-video-thumbnail
Domain Path: lang
License: GPL2

*/

defined('ABSPATH') or die();

require_once __DIR__.DIRECTORY_SEPARATOR . 'bootstrap.php';

if (version_compare(PHP_VERSION, '5.3.0') < 0) {
    add_action('admin_notices', 'ikevt_php_version_error');
    function ikevt_php_version_error() {
        $errorMessage = sprintf(
            '<strong>%s</strong> requires PHP 5.3 or higher. You’re still on %s.',
            IKANAWEB_EVT_NAME,
            PHP_VERSION
        );
        echo '
            <div class="notice notice-warning">
                <p> ' . $errorMessage . ' </p>
            </div>
        ';
    }
   return;
}

global $wpdb;

Redux::init(IKANAWEB_EVT_SLUG);

$yil = new Ikana\EmbedVideoThumbnail\EmbedVideoThumbnail($wpdb);
$yil->boot();

new \Ikana\EmbedVideoThumbnail\PluginReview(
    IKANAWEB_EVT_TEXT_DOMAIN,
    IKANAWEB_EVT_NAME,
    IKANAWEB_EVT_REVIEW_URL
);