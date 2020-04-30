<?php
/**
 * Plugin Name: Event Integration
 * Plugin URI: http://larasoftbd.net/
 * Description: Event Integration. 
 * Version: 1.0.0
 * Author: larasoft
 * Author URI: https://larasoftbd.net
 * Text Domain: event_integration
 * Domain Path: /languages
 * Requires at least: 4.0
 * Tested up to: 4.8
 *
 * @package     event_integration
 * @category 	Core
 * @author 		LaraSoft
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
define('event_integrationDIR', plugin_dir_path( __FILE__ ));
define('event_integrationURL', plugin_dir_url( __FILE__ ));

require_once(event_integrationDIR . 'inc/class.php');

new event_integrationClass;
