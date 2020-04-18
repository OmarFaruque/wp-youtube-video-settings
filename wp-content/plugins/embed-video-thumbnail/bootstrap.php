<?php

defined('ABSPATH') or die();

$upload_dir = wp_upload_dir();
define('IKANAWEB_EVT_NAME', 'Embed Video Thumbnail');
define('IKANAWEB_EVT_SLUG', 'ikanaweb_evt');
define('IKANAWEB_EVT_BASENAME', plugin_basename(__DIR__ . '/embed-video-thumbnail.php'));
define('IKANAWEB_EVT_URL', plugins_url('', IKANAWEB_EVT_BASENAME));
define('IKANAWEB_EVT_TEXT_DOMAIN', 'embed-video-thumbnail');
define('IKANAWEB_EVT_VERSION', '1.3.1');
define('IKANAWEB_EVT_IMAGE_PATH', $upload_dir['basedir'].DIRECTORY_SEPARATOR.'ikana-embed-video-thumbnail');
define('IKANAWEB_EVT_REVIEW_URL', 'https://wordpress.org/support/plugin/embed-video-thumbnail/reviews/#new-post');
define('IKANAWEB_EVT_SUPPORT_URL', "https://wordpress.org/support/plugin/embed-video-thumbnail");

function ikanaweb_evt_autoload($className)
{
    $rootDir = __DIR__ .DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR;
    $className = ltrim($className, '\\');
    $fileName  = '';
    if ($lastNsPos = strrpos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
    $fileName = $rootDir.$fileName;
    if (file_exists($fileName)) {
        require $fileName;
    }
}

spl_autoload_register('ikanaweb_evt_autoload');

$locale = get_locale();
$locale = apply_filters('plugin_locale', $locale, IKANAWEB_EVT_TEXT_DOMAIN);
load_plugin_textdomain(IKANAWEB_EVT_TEXT_DOMAIN, false, dirname(IKANAWEB_EVT_BASENAME).'/lang');

require_once(__DIR__ . '/admin/admin-init.php');
