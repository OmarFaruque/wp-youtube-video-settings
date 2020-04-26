<?php

	// If this file is called directly, abort.
	if ( ! defined( 'WPINC' ) )
	{
		die;
	}
	
	/**
	 * The code that runs during plugin activation.
	 */
	function activate_weather_atlas()
	{
		echo 'jony_path :' . plugin_dir_path( __FILE__ );
		require_once plugin_dir_path( __FILE__ ) . 'inc/weather-atlas/includes/class-weather-atlas-activator.php';
		Weather_Atlas_Activator::activate();
	}
	
	/**
	 * The code that runs during plugin deactivation.
	 */
	function deactivate_weather_atlas()
	{
		require_once plugin_dir_path( __FILE__ ) . 'inc/weather-atlas/includes/class-weather-atlas-deactivator.php';
		Weather_Atlas_Deactivator::deactivate();
	}
	
	register_activation_hook( __FILE__, 'activate_weather_atlas' );
	register_deactivation_hook( __FILE__, 'deactivate_weather_atlas' );
	
	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require plugin_dir_path( __FILE__ ) . 'inc/weather-atlas/includes/class-weather-atlas.php';
	
	/**
	 * Begins execution of the plugin.
	 */
	function run_weather_atlas()
	{
		$plugin = new Weather_Atlas();
		$plugin->run();
	}
	
	run_weather_atlas();
