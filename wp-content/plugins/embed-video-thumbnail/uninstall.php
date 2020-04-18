<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'bootstrap.php';

global $wpdb;

$yil = new Ikana\EmbedVideoThumbnail\EmbedVideoThumbnail(plugin_basename(__FILE__), $wpdb);
$yil->uninstall();
