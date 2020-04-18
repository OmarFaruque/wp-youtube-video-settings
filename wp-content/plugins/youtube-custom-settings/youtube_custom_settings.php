<?php
/**
 * Plugin Name: Youtube Custom Settings
 * Plugin URI: http://larasoftbd.net/
 * Description: Youtube Custom Settings. 
 * Version: 1.0.0
 * Author: larasoftbd
 * Author URI: https://larasoftbd.net
 * Text Domain: youtube_custom_settings
 * Domain Path: /languages
 * Requires at least: 4.0
 * Tested up to: 4.8
 *
 * @package     youtube_custom_settings
 * @category 	Core
 * @author 		LaraSoft
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
define('youtube_custom_settingsDIR', plugin_dir_path( __FILE__ ));
define('youtube_custom_settingsURL', plugin_dir_url( __FILE__ ));

require_once(youtube_custom_settingsDIR . 'inc/class.php');

new youtube_custom_settingsClass;
