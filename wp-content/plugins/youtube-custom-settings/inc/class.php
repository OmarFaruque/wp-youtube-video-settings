<?php
/*
* youtube_custom_settings Class 
*/

if (!class_exists('youtube_custom_settingsClass')) {
    class youtube_custom_settingsClass{
        public $plugin_url;
        public $plugin_dir;
        public $wpdb;
        public $option_tbl; 
        
        /**Plugin init action**/ 
        public function __construct() {
            global $wpdb;
            $this->plugin_url 				= youtube_custom_settingsURL;
            $this->plugin_dir 				= youtube_custom_settingsDIR;
            $this->wpdb 					= $wpdb;	
            $this->option_tbl               = $this->wpdb->prefix . 'options';
         
            $this->init();
        }

        private function init(){

            //Backend Script
            add_action( 'admin_enqueue_scripts', array($this, 'youtube_custom_settings_backend_script') );
            //Frontend Script
            add_action( 'wp_enqueue_scripts', array($this, 'youtube_custom_settings_frontend_script') );
            //Add Menu Options
            add_action('admin_menu', array($this, 'youtube_custom_settings_admin_menu_function'));

            //Customize oEmbed markup
            add_filter('embed_oembed_html', array($this, 'shapeSpace_oembed_html'), 99, 4);

            //custom button function function 
            add_action('wp_ajax_nopriv_custom_buttonfunction', array($this, 'custom_buttonfunction'));
            add_action( 'wp_ajax_custom_buttonfunction', array($this, 'custom_buttonfunction') );

            //custom button function function 
            add_action('wp_ajax_nopriv_enable_full_screenfunction', array($this, 'enable_full_screenfunction'));
            add_action( 'wp_ajax_enable_full_screenfunction', array($this, 'enable_full_screenfunction') );

            /*Settins hook to wp admin via js */
            add_action( 'wp_head', array($this, 'youtubeJStoFrontHead') );
            
            //add filter
            //add_filter( 'the_content', array($this, 'filter_the_content_in_the_main_loop') );

            // Shortcode for frontend use
            add_shortcode( 'youtube', array($this, 'youtube_custom_shortcode') );

            // Weather shortcode 
            add_shortcode( 'shortcode-weather-atlas', array($this, 'function_shortcode_weather_atlas_widget') );
        }


        public function function_shortcode_weather_atlas_widget( $attributes )
        {
            return $this->weather_atlas_widget( $attributes );
        }

        private function weather_atlas_widget( $attributes )
        {
            $get_locale_root_array = explode( "_", get_locale() );
            $get_locale_root       = $get_locale_root_array[ 0 ];
            if ( $get_locale_root == 'de' )
            {
                $language_root_wp = 'de';
            }
            elseif ( $get_locale_root == 'en' )
            {
                $language_root_wp = 'en';
            }
            elseif ( $get_locale_root == 'es' )
            {
                $language_root_wp = 'es';
            }
            elseif ( $get_locale_root == 'ru' )
            {
                $language_root_wp = 'ru';
            }
            elseif ( $get_locale_root == 'zh' )
            {
                $language_root_wp = 'zh';
            }
            else
            {
                $language_root_wp = 'en';
            }
    
            
            $ip = '';
    
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];        
            }
            $country = file_get_contents('http://ip-api.com/json/'.$ip);
            $country = json_decode($country, true);
            $city = $country['city'];    
            
            $city_selector    = isset( $attributes[ 'city_selector' ] ) ? $attributes[ 'city_selector' ] : $city;
            $country_selector = isset( $attributes[ 'country_selector' ] ) ? $attributes[ 'country_selector' ] : 250;
            $http_root        = isset( $attributes[ 'http_root' ] ) ? $attributes[ 'http_root' ] : "https://www.weather-us.com";
            $unit_c_f         = ! empty( $attributes[ 'unit_c_f' ] ) ? $attributes[ 'unit_c_f' ] : 'f';
            if ( $unit_c_f == 'c' )
            {
                $def_units_temperature   = '°C';
                $unit_kph_mph            = 'kph';
                $def_units_windspeed     = 'km/h';
                $unit_mm_in              = 'mm';
                $def_units_precipitation = 'mm';
                $unit_mb_in              = 'mb';
                $def_units_pressure      = 'mbar';
                $unit_km_mi              = 'km';
                $def_units_distance      = 'km';
            }
            else
            {
                $def_units_temperature   = '°F';
                $unit_kph_mph            = 'mph';
                $def_units_windspeed     = 'mph';
                $unit_mm_in              = 'in';
                $def_units_precipitation = '"';
                $unit_mb_in              = 'in';
                $def_units_pressure      = '"Hg';
                $unit_km_mi              = 'mi';
                $def_units_distance      = 'mi';
            }
            $def_units_degree  = '°';
            $def_units_percent = '%';
            $layout            = ! empty( $attributes[ 'layout' ] ) ? $attributes[ 'layout' ] : 'vertical';
            $header            = ! empty( $attributes[ 'header' ] ) ? $attributes[ 'header' ] : FALSE;
            $sunrise_sunset    = isset( $attributes[ 'sunrise_sunset' ] ) ? $attributes[ 'sunrise_sunset' ] : 1;
            $current           = isset( $attributes[ 'current' ] ) ? $attributes[ 'current' ] : 1;
            $hourly            = isset( $attributes[ 'hourly' ] ) ? $attributes[ 'hourly' ] : 0;
            $daily             = isset( $attributes[ 'daily' ] ) ? $attributes[ 'daily' ] : 3;
            // $detailed_forecast = isset( $attributes[ 'detailed_forecast' ] ) ? $attributes[ 'detailed_forecast' ] : 1;
            $is_shortcode = isset( $attributes[ 'is_shortcode' ] ) ? $attributes[ 'is_shortcode' ] : 1;
            
            $weather_atlas_data = $this->weather_atlas_data( $city_selector );
            
            $return = '';
    
            
            // json2array
            $weather_atlas_data_array = json_decode( $weather_atlas_data, TRUE );
            if ( ( ! empty ( $weather_atlas_data ) ) AND ( is_array( $weather_atlas_data_array ) ) )
            {
                if ( array_key_exists( "city", $weather_atlas_data_array ) )
                {
                    $city_selector                         = array_key_exists( 'city_selector', $weather_atlas_data_array[ 'city' ] ) ? $weather_atlas_data_array[ 'city' ][ 'city_selector' ] : FALSE;
                    $country_selector                      = array_key_exists( 'country_selector', $weather_atlas_data_array[ 'city' ] ) ? $weather_atlas_data_array[ 'city' ][ 'country_selector' ] : FALSE;
                    $http_root                             = array_key_exists( 'http_root', $weather_atlas_data_array[ 'city' ] ) ? $weather_atlas_data_array[ 'city' ][ 'http_root' ] : FALSE;
                    ${'country_name_' . $language_root_wp} = array_key_exists( 'country_name_' . $language_root_wp, $weather_atlas_data_array[ 'city' ] ) ? $weather_atlas_data_array[ 'city' ][ 'country_name_' . $language_root_wp ] : FALSE;
                    if ( $country_selector == 250 )
                    {
                        ${'country_name_' . $language_root_wp} = strtok( ${'country_name_' . $language_root_wp}, "," );
                    }
                    ${'country_name_rewrite_' . $language_root_wp} = array_key_exists( 'country_name_rewrite_' . $language_root_wp, $weather_atlas_data_array[ 'city' ] ) ? $weather_atlas_data_array[ 'city' ][ 'country_name_rewrite_' . $language_root_wp ] : FALSE;
                    ${'city_name_' . $language_root_wp}            = array_key_exists( 'city_name_' . $language_root_wp, $weather_atlas_data_array[ 'city' ] ) ? $weather_atlas_data_array[ 'city' ][ 'city_name_' . $language_root_wp ] : FALSE;
                    ${'city_name_rewrite_' . $language_root_wp}    = array_key_exists( 'city_name_rewrite_' . $language_root_wp, $weather_atlas_data_array[ 'city' ] ) ? $weather_atlas_data_array[ 'city' ][ 'city_name_rewrite_' . $language_root_wp ] : FALSE;
                    $time_of_sunrise                               = array_key_exists( 'time_of_sunrise', $weather_atlas_data_array[ 'city' ] ) ? $weather_atlas_data_array[ 'city' ][ 'time_of_sunrise' ] : FALSE;
                    $time_of_sunset                                = array_key_exists( 'time_of_sunset', $weather_atlas_data_array[ 'city' ] ) ? $weather_atlas_data_array[ 'city' ][ 'time_of_sunset' ] : FALSE;
                    $timezone_abbr                                 = array_key_exists( 'timezone_abbr', $weather_atlas_data_array[ 'city' ] ) ? $weather_atlas_data_array[ 'city' ][ 'timezone_abbr' ] : FALSE;
                }
                
                if ( array_key_exists( "current", $weather_atlas_data_array ) )
                {
                    ${'current_temp_' . $unit_c_f}           = array_key_exists( 'current_temp_' . $unit_c_f, $weather_atlas_data_array[ 'current' ] ) ? $weather_atlas_data_array[ 'current' ][ 'current_temp_' . $unit_c_f ] : FALSE;
                    ${'current_temp_feelslike_' . $unit_c_f} = array_key_exists( 'current_temp_feelslike_' . $unit_c_f, $weather_atlas_data_array[ 'current' ] ) ? $weather_atlas_data_array[ 'current' ][ 'current_temp_feelslike_' . $unit_c_f ] : FALSE;
                    $current_icon                            = array_key_exists( 'current_icon', $weather_atlas_data_array[ 'current' ] ) ? $weather_atlas_data_array[ 'current' ][ 'current_icon' ] : FALSE;
                    $current_text_en                         = array_key_exists( 'current_text_en', $weather_atlas_data_array[ 'current' ] ) ? $weather_atlas_data_array[ 'current' ][ 'current_text_en' ] : FALSE;
                    ${'current_wind_' . $unit_kph_mph}       = array_key_exists( 'current_wind_' . $unit_kph_mph, $weather_atlas_data_array[ 'current' ] ) ? $weather_atlas_data_array[ 'current' ][ 'current_wind_' . $unit_kph_mph ] : FALSE;
                    $current_wind_dir                        = array_key_exists( 'current_wind_dir', $weather_atlas_data_array[ 'current' ] ) ? $weather_atlas_data_array[ 'current' ][ 'current_wind_dir' ] : FALSE;
                    $current_wind_deg                        = array_key_exists( 'current_wind_deg', $weather_atlas_data_array[ 'current' ] ) ? $weather_atlas_data_array[ 'current' ][ 'current_wind_deg' ] : FALSE;
                    $current_humidity_relative               = array_key_exists( 'current_humidity_relative', $weather_atlas_data_array[ 'current' ] ) ? $weather_atlas_data_array[ 'current' ][ 'current_humidity_relative' ] : FALSE;
                    // ${'current_dew_point_' . $unit_c_f}         = array_key_exists( 'current_dew_point_' . $unit_c_f, $weather_atlas_data_array[ 'current' ] ) ? $weather_atlas_data_array[ 'current' ][ 'current_dew_point_' . $unit_c_f ] : FALSE;
                    ${'current_pressure_' . $unit_mb_in} = array_key_exists( 'current_pressure_' . $unit_mb_in, $weather_atlas_data_array[ 'current' ] ) ? $weather_atlas_data_array[ 'current' ][ 'current_pressure_' . $unit_mb_in ] : FALSE;
                    // ${'current_precip_' . $unit_mm_in}         = array_key_exists( 'current_precip_' . $unit_mm_in, $weather_atlas_data_array[ 'current' ] ) ? $weather_atlas_data_array[ 'current' ][ 'current_precip_' . $unit_mm_in ] : FALSE;
                    // ${'current_visibility_' . $unit_km_mi}         = array_key_exists( 'current_visibility_' . $unit_km_mi, $weather_atlas_data_array[ 'current' ] ) ? $weather_atlas_data_array[ 'current' ][ 'current_visibility_' . $unit_km_mi ] : FALSE;
                    $current_uv_index = array_key_exists( 'current_uv_index', $weather_atlas_data_array[ 'current' ] ) ? $weather_atlas_data_array[ 'current' ][ 'current_uv_index' ] : FALSE;
                }
                
                $font_size = ! empty( $attributes[ 'font_size' ] ) ? $attributes[ 'font_size' ] : FALSE;
                
                if ( ( array_key_exists( "current", $weather_atlas_data_array ) ) AND ( array_key_exists( "current_temp_c", $weather_atlas_data_array[ 'current' ] ) ) )
                {
                    list( $background_color, $text_color ) = $this->weather_atlas_temperature_color( $weather_atlas_data_array[ 'current' ][ 'current_temp_c' ] );
                    
                    $background_color = ! empty( $attributes[ 'background_color' ] ) ? $attributes[ 'background_color' ] : $background_color;
                    $text_color       = ! empty( $attributes[ 'text_color' ] ) ? $attributes[ 'text_color' ] : $text_color;
                }
                else
                {
                    $background_color = '#fafafa';
                    $text_color       = '#333';
                }
                $border_color = $this->weather_atlas_adjust_brightness( $background_color, - 17 );
                $style        = ! empty( $attributes[ 'style' ] ) ? $attributes[ 'style' ] : FALSE;
                
                $return .= "<div class='weather-atlas-wrapper' style='";
                if ( ! empty ( $font_size ) )
                {
                    $return .= "font-size:$font_size;";
                }
                $return .= "background:$background_color;border:$border_color;color:$text_color;";
                if ( ! empty ( $style ) )
                {
                    $return .= "$style";
                }
                $return .= "'>";
                
                $return .= "<div class='weather-atlas-header' style='border-bottom:$border_color'>";
                
                if ( empty ( $is_shortcode ) )
                {
                    $return .= "<div class='weather-atlas-header-title-wrapper'>";
                    $return .= "<div class='weather-atlas-header-title'>";
                }
                
                if ( ( ! empty( $header ) ) AND ( empty ( $_COOKIE[ 'city_selector' ] ) ) )
                {
                    $header_title = $header;
                }
                else
                {
                    $header_title = ${'city_name_' . $language_root_wp};
                }
                /*
                    $header_title = apply_filters( 'filter_bad_words', $header_title );
                */
                $return .= $header_title;
                
                if ( empty ( $is_shortcode ) )
                {
                    $return .= "</div>";
                    
                    $return .= "<div class='city_selector_toggle_div autocomplete' style='display:none;'>";
                    
                    $return .= "<script>/*<![CDATA[*/var weather_atlas_language = '$language_root_wp';/*]]>*/</script>";
                    $return .= "<input class='city_name' id='city_name' name='city_name' type='text' value='' placeholder='";
                    $return .= __( 'type and select location from drop-down', 'weather-atlas' );
                    $return .= "'>";
                    $return .= "</div>";
                    
                    $return .= "</div>";
                    
                    $return .= "<div class='city_selector_toggle'>";
                    $return .= "<a href='#' class='city_selector_toggle_link' style='color:$text_color' title='";
                    $return .= __( 'Location', 'weather-atlas' );
                    $return .= "'>";
                    $return .= "&#9673;";
                    $return .= "</a>";
                    $return .= "</div>";
                }
                
                $return .= "</div>";
                
                $return .= "<div class='weather-atlas-body'>";
                
                if ( empty( $current ) )
                {
                    $layout = 'vertical';
                }
                if ( $layout == 'horizontal' )
                {
                    $return .= "<div class='current_horizontal'>";
                }
                
                $return .= "<div class='current_temp'>";
                if ( ! empty( $current_icon ) )
                {
                    $return .= "<i class='wi wi-fw wi-weather-$current_icon'></i>";
                }
                if ( ( isset( ${'current_temp_' . $unit_c_f} ) ) AND ( is_numeric( ${'current_temp_' . $unit_c_f} ) ) )
                {
                    $return .= "<span class='temp'>" . ${'current_temp_' . $unit_c_f} . "$def_units_degree</span>";
                }
                if ( ! empty( $current_text_en ) )
                {
                    $return .= "<div class='current_text'>";
                    $return .= __( $current_text_en, 'weather-atlas' );
                    $return .= "</div>";
                }
                if ( ( ! empty ( $sunrise_sunset ) ) AND ( ! empty ( $time_of_sunrise ) ) AND ( ! empty ( $time_of_sunset ) ) AND ( ! empty ( $timezone_abbr ) ) )
                {
                    $return .= "<div class='sunrise_sunset'>" . $time_of_sunrise . "<i class='wi wi-fw wi-weather-32'></i>" . $time_of_sunset . " " . $timezone_abbr . "</div>";
                }
                $return .= "</div>";
                
                if ( ( ! empty ( $current ) ) AND ( array_key_exists( "current", $weather_atlas_data_array ) ) )
                {
                    $return .= "<span class='current_text_2'>";
                    if ( is_numeric( ${'current_temp_feelslike_' . $unit_c_f} ) )
                    {
                        $return .= __( 'Feels like', 'weather-atlas' ) . ": ";
                        $return .= ${'current_temp_feelslike_' . $unit_c_f} . "<small>" . $def_units_temperature . "</small><br />";
                    }
                    if ( is_numeric( ${'current_wind_' . $unit_kph_mph} ) )
                    {
                        $return .= __( 'Wind', 'weather-atlas' ) . ": ";
                        $return .= ${'current_wind_' . $unit_kph_mph} . "<small>" . $def_units_windspeed . "</small>";
                        
                        if ( $language_root_wp == 'en' )
                        {
                            $return .= " " . $current_wind_dir;
                        }
                        else
                        {
                            $return .= " " . $current_wind_deg . "<small>" . $def_units_degree . "</small>";
                        }
                        $return .= "<br />";
                    }
                    if ( is_numeric( $current_humidity_relative ) )
                    {
                        $return .= __( 'Humidity', 'weather-atlas' ) . ": ";
                        $return .= $current_humidity_relative . "<small>" . $def_units_percent . "</small><br />";
                    }
                    if ( is_numeric( ${'current_pressure_' . $unit_mb_in} ) )
                    {
                        $return .= __( 'Pressure', 'weather-atlas' ) . ": ";
                        $return .= ${'current_pressure_' . $unit_mb_in} . "<small>" . $def_units_pressure . "</small><br />";
                    }
                    if ( is_numeric( $current_uv_index ) )
                    {
                        $return .= __( 'UV index', 'weather-atlas' ) . ": ";
                        $return .= $current_uv_index;
                    }
                    $return .= "</span>";
                }
                
                if ( $layout == 'horizontal' )
                {
                    $return .= "</div>";
                }
                
                if ( ( ! empty ( $hourly ) ) AND ( array_key_exists( "hourly", $weather_atlas_data_array ) ) )
                {
                    $return .= "<div class='hourly hours' style='border-bottom:$border_color'>";
                    
                    for ( $ii = 1; $ii <= $hourly; $ii ++ )
                    {
                        if ( array_key_exists( $ii, $weather_atlas_data_array[ 'hourly' ] ) )
                        {
                            $return .= "<span class='extended_hour extended_hour_$ii'>";
                            
                            $hour = array_key_exists( 'hour', $weather_atlas_data_array[ 'hourly' ][ $ii ] ) ? $weather_atlas_data_array[ 'hourly' ][ $ii ][ 'hour' ] : FALSE;
                            
                            if ( is_numeric( ${'hour'} ) )
                            {
                                $return .= $hour;
                                $return .= "<small>";
                                $return .= __( 'h', 'weather-atlas' );
                                $return .= "</small>";
                            }
                            
                            $return .= "</span>";
                        }
                    }
                    $return .= "</div>";
                    
                    $return .= "<div class='hourly'>";
                    for ( $ii = 1; $ii <= $hourly; $ii ++ )
                    {
                        if ( array_key_exists( $ii, $weather_atlas_data_array[ 'hourly' ] ) )
                        {
                            ${'hour_temp_' . $unit_c_f} = array_key_exists( 'hour_temp_' . $unit_c_f, $weather_atlas_data_array[ 'hourly' ][ $ii ] ) ? $weather_atlas_data_array[ 'hourly' ][ $ii ][ 'hour_temp_' . $unit_c_f ] : FALSE;
                            // ${'hour_temp_feelslike_' . $unit_c_f} = array_key_exists( 'hour_temp_feelslike_' . $unit_c_f, $weather_atlas_data_array[ 'hourly' ][ $ii ] ) ? $weather_atlas_data_array[ 'hourly' ][ $ii ][ 'hour_temp_feelslike_' . $unit_c_f ] : FALSE;
                            $hour_icon    = array_key_exists( 'hour_icon', $weather_atlas_data_array[ 'hourly' ][ $ii ] ) ? $weather_atlas_data_array[ 'hourly' ][ $ii ][ 'hour_icon' ] : FALSE;
                            $hour_text_en = array_key_exists( 'hour_text_en', $weather_atlas_data_array[ 'hourly' ][ $ii ] ) ? $weather_atlas_data_array[ 'hourly' ][ $ii ][ 'hour_text_en' ] : FALSE;
                            // ${'hour_wind_' . $unit_kph_mph}     = array_key_exists( 'hour_wind_' . $unit_kph_mph, $weather_atlas_data_array[ 'hourly' ][ $ii ] ) ? $weather_atlas_data_array[ 'hourly' ][ $ii ][ 'hour_wind_' . $unit_kph_mph ] : FALSE;
                            // $hour_wind_dir                      = array_key_exists( 'hour_wind_dir', $weather_atlas_data_array[ 'hourly' ][ $ii ] ) ? $weather_atlas_data_array[ 'hourly' ][ $ii ][ 'hour_wind_dir' ] : FALSE;
                            // $hour_wind_deg                      = array_key_exists( 'hour_wind_deg', $weather_atlas_data_array[ 'hourly' ][ $ii ] ) ? $weather_atlas_data_array[ 'hourly' ][ $ii ][ 'hour_wind_deg' ] : FALSE;
                            // $hour_humidity_relative             = array_key_exists( 'hour_humidity_relative', $weather_atlas_data_array[ 'hourly' ][ $ii ] ) ? $weather_atlas_data_array[ 'hourly' ][ $ii ][ 'hour_humidity_relative' ] : FALSE;
                            // ${'hour_dew_point_' . $unit_c_f}    = array_key_exists( 'hour_dew_point_' . $unit_c_f, $weather_atlas_data_array[ 'hourly' ][ $ii ] ) ? $weather_atlas_data_array[ 'hourly' ][ $ii ][ 'hour_dew_point_' . $unit_c_f ] : FALSE;
                            // ${'hour_pressure_' . $unit_mb_in}   = array_key_exists( 'hour_pressure_' . $unit_mb_in, $weather_atlas_data_array[ 'hourly' ][ $ii ] ) ? $weather_atlas_data_array[ 'hourly' ][ $ii ][ 'hour_pressure_' . $unit_mb_in ] : FALSE;
                            // ${'hour_precip_' . $unit_mm_in}     = array_key_exists( 'hour_precip_' . $unit_mm_in, $weather_atlas_data_array[ 'hourly' ][ $ii ] ) ? $weather_atlas_data_array[ 'hourly' ][ $ii ][ 'hour_precip_' . $unit_mm_in ] : FALSE;
                            // $hour_precip_probability            = array_key_exists( 'hour_precip_probability', $weather_atlas_data_array[ 'hourly' ][ $ii ] ) ? $weather_atlas_data_array[ 'hourly' ][ $ii ][ 'hour_precip_probability' ] : FALSE;
                            // ${'hour_visibility_' . $unit_km_mi} = array_key_exists( 'hour_visibility_' . $unit_km_mi, $weather_atlas_data_array[ 'hourly' ][ $ii ] ) ? $weather_atlas_data_array[ 'hourly' ][ $ii ][ 'hour_visibility_' . $unit_km_mi ] : FALSE;
                            // $hour_uv_index                      = array_key_exists( 'hour_uv_index', $weather_atlas_data_array[ 'hourly' ][ $ii ] ) ? $weather_atlas_data_array[ 'hourly' ][ $ii ][ 'hour_uv_index' ] : FALSE;
                            
                            $return .= "<span class='extended_hour extended_hour_$ii'";
                            if ( ! empty( $hour_text_en ) )
                            {
                                $return .= " title='";
                                $return .= __( $hour_text_en, 'weather-atlas' );
                                $return .= "'";
                            }
                            $return .= ">";
                            if ( is_numeric( ${'hour_temp_' . $unit_c_f} ) )
                            {
                                $return .= ${'hour_temp_' . $unit_c_f} . "<small>" . $def_units_temperature . "</small>";
                            }
                            if ( ! empty( $hour_icon ) )
                            {
                                $return .= "<br /><i class='wi wi-fw wi-weather-$hour_icon'></i>";
                            }
                            $return .= "</span>";
                        }
                    }
                    
                    $return .= "</div>";
                }
                
                if ( ( ! empty ( $daily ) ) AND ( array_key_exists( "daily", $weather_atlas_data_array ) ) )
                {
                    $return .= "<div class='daily days' style='border-bottom:$border_color'>";
                    for ( $ii = 1; $ii <= $daily; $ii ++ )
                    {
                        if ( array_key_exists( $ii, $weather_atlas_data_array[ 'daily' ] ) )
                        {
                            $return .= "<span class='extended_day extended_day_$ii'>";
                            
                            $day_name_en_short = array_key_exists( 'day_name_en_short', $weather_atlas_data_array[ 'daily' ][ $ii ] ) ? $weather_atlas_data_array[ 'daily' ][ $ii ][ 'day_name_en_short' ] : FALSE;
                            if ( ! empty( $day_name_en_short ) )
                            {
                                $return .= __( $day_name_en_short, 'weather-atlas' );
                            }
                            
                            $return .= "</span>";
                        }
                    }
                    $return .= "</div>";
                    
                    $return .= "<div class='daily'>";
                    for ( $ii = 1; $ii <= $daily; $ii ++ )
                    {
                        if ( array_key_exists( $ii, $weather_atlas_data_array[ 'daily' ] ) )
                        {
                            ${'day_temp_high_' . $unit_c_f} = array_key_exists( 'day_temp_high_' . $unit_c_f, $weather_atlas_data_array[ 'daily' ][ $ii ] ) ? $weather_atlas_data_array[ 'daily' ][ $ii ][ 'day_temp_high_' . $unit_c_f ] : FALSE;
                            ${'day_temp_low_' . $unit_c_f}  = array_key_exists( 'day_temp_low_' . $unit_c_f, $weather_atlas_data_array[ 'daily' ][ $ii ] ) ? $weather_atlas_data_array[ 'daily' ][ $ii ][ 'day_temp_low_' . $unit_c_f ] : FALSE;
                            $day_icon                       = array_key_exists( 'day_icon', $weather_atlas_data_array[ 'daily' ][ $ii ] ) ? $weather_atlas_data_array[ 'daily' ][ $ii ][ 'day_icon' ] : FALSE;
                            $day_text_en                    = array_key_exists( 'day_text_en', $weather_atlas_data_array[ 'daily' ][ $ii ] ) ? $weather_atlas_data_array[ 'daily' ][ $ii ][ 'day_text_en' ] : FALSE;
                            
                            // ${'day_wind_' . $unit_kph_mph} = array_key_exists( 'day_wind_' . $unit_kph_mph, $weather_atlas_data_array[ 'daily' ][ $ii ] ) ? $weather_atlas_data_array[ 'daily' ][ $ii ][ 'day_wind_' . $unit_kph_mph ] : FALSE;
                            // $day_wind_dir                  = array_key_exists( 'day_wind_dir', $weather_atlas_data_array[ 'daily' ][ $ii ] ) ? $weather_atlas_data_array[ 'daily' ][ $ii ][ 'day_wind_dir' ] : FALSE;
                            // $day_wind_deg                  = array_key_exists( 'day_wind_deg', $weather_atlas_data_array[ 'daily' ][ $ii ] ) ? $weather_atlas_data_array[ 'daily' ][ $ii ][ 'day_wind_deg' ] : FALSE;
                            // $day_humidity_relative         = array_key_exists( 'day_humidity_relative', $weather_atlas_data_array[ 'daily' ][ $ii ] ) ? $weather_atlas_data_array[ 'daily' ][ $ii ][ 'day_humidity_relative' ] : FALSE;
                            // ${'day_precip_' . $unit_mm_in} = array_key_exists( 'day_precip_' . $unit_mm_in, $weather_atlas_data_array[ 'daily' ][ $ii ] ) ? $weather_atlas_data_array[ 'daily' ][ $ii ][ 'day_precip_' . $unit_mm_in ] : FALSE;
                            // $day_precip_probability        = array_key_exists( 'day_precip_probability', $weather_atlas_data_array[ 'daily' ][ $ii ] ) ? $weather_atlas_data_array[ 'daily' ][ $ii ][ 'day_precip_probability' ] : FALSE;
                            // $day_uv_index                  = array_key_exists( 'day_uv_index', $weather_atlas_data_array[ 'daily' ][ $ii ] ) ? $weather_atlas_data_array[ 'daily' ][ $ii ][ 'day_uv_index' ] : FALSE;
                            
                            $return .= "<span class='extended_day extended_day_$ii'";
                            if ( ! empty( $day_text_en ) )
                            {
                                $return .= " title='";
                                $return .= __( $day_text_en, 'weather-atlas' );
                                $return .= "'";
                            }
                            $return .= ">";
                            
                            if ( is_numeric( ${'day_temp_high_' . $unit_c_f} ) )
                            {
                                if ( ${'day_temp_high_' . $unit_c_f} != '-99' )
                                {
                                    $return .= ${'day_temp_high_' . $unit_c_f} . "/";
                                }
                                else
                                {
                                    $return .= "min ";
                                }
                            }
                            if ( is_numeric( ${'day_temp_low_' . $unit_c_f} ) )
                            {
                                $return .= ${'day_temp_low_' . $unit_c_f} . "<small>" . $def_units_temperature . "</small>";
                            }
                            if ( ! empty( $day_icon ) )
                            {
                                $return .= "<br /><i class='wi wi-fw wi-weather-$day_icon'></i>";
                            }
                            
                            $return .= "</span>";
                        }
                    }
                    $return .= "</div>";
                }
                
                $return .= "</div>";
                
                $return .= "<div class='weather-atlas-footer' style='border-top:$border_color'>";
                
                $return .= "<a href='$http_root/$language_root_wp";
                if ( ( ! empty ( ${'country_name_rewrite_' . $language_root_wp} ) AND ( ${'city_name_rewrite_' . $language_root_wp} ) ) )
                {
                    $return .= "/" . ${'country_name_rewrite_' . $language_root_wp} . "/" . ${'city_name_rewrite_' . $language_root_wp};
                }
                if ( ( $country_selector == 250 ) AND ( $unit_c_f == 'c' ) )
                {
                    $return .= "?units=c,mm,mb,km";
                }
                elseif ( ( $country_selector != 250 ) AND ( $unit_c_f == 'f' ) )
                {
                    $return .= "?units=f,in,in,mi";
                }
                $return .= "' title='Weather Atlas - ";
                $return .= __( 'Weather forecast', 'weather-atlas' );
                $return .= " " . ${'city_name_' . $language_root_wp} . ", " . ${'country_name_' . $language_root_wp};
                $return .= "' style='color:$text_color;' target='_blank'>";
                
                /*
                if ( ( empty ( $detailed_forecast ) ) AND ( empty ( $_COOKIE[ 'city_selector' ] ) ) )
                {
                    $return .= "Weather from Weather Atlas";
                }
                else
                {
                    $return .= __( 'Detailed forecast', 'weather-atlas' ) . " &#9656;";
                }
                */
                $return .= __( 'Weather forecast', 'weather-atlas' );
                $return .= " <span class='weather-atlas-footer-block'>";
                $return .= ${'city_name_' . $language_root_wp} . ", " . ${'country_name_' . $language_root_wp};
                
                $return .= " &#9656;";
                $return .= "</span>";
                
                $return .= "</a>";
                
                $return .= "</div>";
                
                $return .= "</div>";
            }
            
            return $return;
        }


        private function weather_atlas_adjust_brightness( $hex, $steps )
        {
            // if rgb(a)
            if ( preg_match( '/(?<=\()(.+)(?=\))/is', $hex, $within_brackets ) )
            {
                $hex_array = explode( ",", $within_brackets[ 0 ] );
                // convert to hex
                // $hex       = sprintf( "#%02x%02x%02x", $hex_array[ 0 ], $hex_array[ 1 ], $hex_array[ 2 ] );
                $return = "1px solid rgba(" . $hex_array[ 0 ] . ", " . $hex_array[ 1 ] . ", " . $hex_array[ 2 ] . ", 0.1)";
            }
            else
            {
                $steps = max( - 255, min( 255, $steps ) );
                $hex   = str_replace( '#', '', $hex );
                if ( strlen( $hex ) == 3 )
                {
                    $hex = str_repeat( substr( $hex, 0, 1 ), 2 ) . str_repeat( substr( $hex, 1, 1 ), 2 ) . str_repeat( substr( $hex, 2, 1 ), 2 );
                }
                
                $color_parts = str_split( $hex, 2 );
                $return      = '1px solid #';
                
                foreach ( $color_parts as $color )
                {
                    $color  = hexdec( $color );
                    $color  = max( 0, min( 255, $color + $steps ) );
                    $return .= str_pad( dechex( $color ), 2, '0', STR_PAD_LEFT );
                }
            }
            
            return $return;
        }

        private function weather_atlas_hex( $value )
        {
            return sprintf( "%02X", $value );
        }
        private function weather_atlas_temperature_color( $celsius )
        {
            $background = "";
            $color      = "";
            
            if ( $celsius < 10 )
            {
                $celsius    = $celsius - 3;
                $background = $this->weather_atlas_hex( $this->weather_atlas_range_pos( $celsius, 0, 10 ) * 255 );
            }
            else
            {
                $celsius    = $celsius + 4;
                $background = $this->weather_atlas_hex( $this->weather_atlas_range_pos( $celsius, 70, 35 ) * 255 );
            }
            if ( $celsius <= 10 )
            {
                $background = $background . $this->weather_atlas_hex( $this->weather_atlas_range_pos( $celsius, - 35, 10 ) * 255 );
            }
            else
            {
                $background = $background . $this->weather_atlas_hex( $this->weather_atlas_range_pos( $celsius, 45, 10 ) * 255 );
            }
            if ( $celsius < - 35 )
            {
                $background = $background . $this->weather_atlas_hex( $this->weather_atlas_range_pos( $celsius, - 90, - 35 ) * 255 );
            }
            else
            {
                $background = $background . $this->weather_atlas_hex( $this->weather_atlas_range_pos( $celsius, 18, 10 ) * 255 );
            }
            if ( ( $celsius < 10 ) OR ( $celsius > 10 ) )
            {
                $color = "fff";
            }
            else
            {
                $color = "000";
            }
            
            return array (
                "#" . $background,
                "#" . $color
            );
        }


        private function weather_atlas_range_pos( $value, $start, $stop )
        {
            if ( $start < $stop )
            {
                if ( $value < $start )
                {
                    return 0;
                }
                elseif ( $value > $stop )
                {
                    return 1;
                }
                else
                {
                    return ( $value - $start ) / ( $stop - $start );
                }
            }
            else
            {
                if ( $value < $stop )
                {
                    return 1;
                }
                elseif ( $value > $start )
                {
                    return 0;
                }
                else
                {
                    return ( $start - $value ) / ( $start - $stop );
                }
            }
        }

        private function weather_atlas_data( $city_selector )
        {
            $weather_transient_name = 'weather_atlas_transient_' . $city_selector;
            $return                 = '';
            
            if ( FALSE === ( $value = get_transient( $weather_transient_name ) ) )
            {
                // json data
                $wp_remote_get_url      = 'https://www.weather-atlas.com/weather/api.php';
                $wp_remote_get_url      .= '?city_selector=' . $city_selector;
                $wp_remote_get_url      .= '&key=' . md5( get_site_url() );
                $wp_remote_get_url      .= '&format=json';
                $wp_remote_get_response = wp_remote_get( esc_url_raw( $wp_remote_get_url ) );
                $weather_transient_data = wp_remote_retrieve_body( $wp_remote_get_response );
                
                if ( ! empty ( $weather_transient_data ) )
                {
                    set_transient( $weather_transient_name, $weather_transient_data, 900 );
                    
                    $return = $weather_transient_data;
                }
            }
            else
            {
                $return = get_transient( $weather_transient_name );
            }
            
            return $return;
        }

        function weather_atlas()
        {
            require_once $this->plugin_dir . 'inc/weather-atlas/weather-atlas.php';
        }


        /*
        * Appointment backend Script
        */
        function youtube_custom_settings_backend_script(){
            
            wp_enqueue_style( 'byoutube_custom_settingsCSS', $this->plugin_url . 'asset/css/youtube_custom_settings_backend.css', array(), true, 'all' );
            wp_enqueue_script( 'byoutube_custom_settingsJS', $this->plugin_url . 'asset/js/youtube_custom_settings_backend.js', array(), true );
            wp_localize_script( 'byoutube_custom_settingsJS', 'youtubeAjax', admin_url( 'admin-ajax.php' ));

            //Core media script
            wp_enqueue_media();

            // Your custom js file
            wp_register_script( 'media-lib-uploader-js', plugins_url( 'media-lib-uploader.js' , __FILE__ ), array('jquery') );
            //wp_enqueue_script( 'media-lib-uploader-js' );

        }

        /*
        * Appointment frontend Script
        */
        function youtube_custom_settings_frontend_script(){

            $all_url = json_decode(get_option('add_all_url'), true);

            // css
            wp_enqueue_style( 'fyoutube_custom_settingsCSS', $this->plugin_url . 'asset/css/youtube_custom_settings_frontend.css', array(), true, 'all' );
            wp_enqueue_style( 'weatheratlascss', $this->plugin_url . 'asset/css/weather-atlas-public.min.css', array(), true, 'all' );
            wp_enqueue_style( 'weatheratlasfotncss', $this->plugin_url . 'asset/font/weather-icons/weather-icons.min.css', array(), true, 'all' );

            // js
            wp_enqueue_script('fyoutube_custom_settingsJS', $this->plugin_url . 'asset/js/youtube_custom_settings_frontend.js', array('jquery'), time(), true);
            
            wp_localize_script( 'fyoutube_custom_settingsJS', 'youtubeAjax', 
                array(
                    'ajax' => admin_url( 'admin-ajax.php' ),
                    'all_url' => $all_url
                )
            );

        }
          

        /*
        * Admin Menu
        */
        function youtube_custom_settings_admin_menu_function(){
            add_menu_page( 'Youtube Custom Settings', 'Youtube Custom Settings', 'manage_options', 'youtube_custom_settings-menu', array($this, 'submenufunction'), 'dashicons-list-view', 50 );
        }

        // function filter_the_content_in_the_main_loop( $content ) {
        //     $DOM = new DOMDocument();
        //     $DOM->loadHTML($content);
        //     $list = $DOM->getElementsByTagName('iframe');
        //     $i = 0;

        //     foreach($list as $p){
        //         $p->setAttribute('class', 'iframe'.$i++);
        //     }
        //     $DOM=$DOM->saveHTML(); 
        //     $content = $DOM;

        //     return $content;
        // }


        //get client ip
        protected function get_ip(){

            $ip = '';

            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];        
            }

            return $ip;
        }

        // Shortcode for breaking-news
        function youtube_custom_shortcode($atts){

            if($atts['menu'] == 'localnews'){

                $ip = $this->get_ip();
                $country = file_get_contents('http://ip-api.com/json/'.$ip);
                $country = json_decode($country, true);
                $google_news_api = get_option( 'google_news_api' );
                $countryCode = $country['countryCode'];
                // $countryCode = 'US';

                $url = file_get_contents('http://newsapi.org/v2/top-headlines?country='.$countryCode.'&apiKey='. $google_news_api);
                $url = json_decode($url, true);
                $url = $url['articles'];
                ?>
                <div class="google-breaking-news">
                <?php
                    for ($i = 0; $i < count($url); $i++) {
                    ?>
                        
                        <article class="single-news-post">
                            <a target="_blank" href="<?php echo $url[$i]['url']; ?>">
                                <h2><?php echo $url[$i]['title']; ?></h2>
                            </a>
                            <a href="<?php echo $url[$i]['url']; ?>" class="post-img"><img src="<?php echo $url[$i]['urlToImage']; ?>" alt=""></a>
                            <div class="post-info">
                                <div class="author-info">
                                    <h3><?php echo $url[$i]['author']; ?></h3>
                                </div>
                                <div class="post-date text-right">
                                    <a target="_blank" href="<?php echo $url[$i]['url']; ?>"><?php echo $url[$i]['publishedAt']; ?></a>
                                </div>
                            </div>
                            <div class="readmore">
                                <p><?php echo $url[$i]['description']; ?><a href="<?php echo $url[$i]['url']; ?>">[Read more]</a></p>
                            </div>    
                        </article>

                    <?php
                    }
                ?>
                </div>
                <?php                   
            }elseif($atts['menu'] == 'weather'){

                // header('Content-Type: application/json; charset=utf-8');
                $ip = $this->get_ip();
                $country = file_get_contents('http://ip-api.com/json/'.$ip);
                $country = json_decode($country, true);
                $city = $country['city'];
                

                $city_code = file_get_contents("https://www.weather-atlas.com/weather/includes/autocomplete_city.php?limit=15&language=en&term=".$city);
            //    $city_code =  preg_replace('/^.*(\(.*\)).*$/', '$1', $city_code);
                // $city_code = trim($city_code, '[]');
                // $city_code = utf8_encode($city_code);
                $city_code = explode(',', $city_code);
                $city_code = ltrim(str_replace('}', '', $city_code[1]));
                $city_code = explode(':', $city_code);
                $city_code = str_replace('"', '', $city_code[1]);
                // $city_code = json_decode($city_code[1], true);
                // echo 'city code: ' . $city_code. '<br/>';
                ?>
                    <!-- <a class="weatherwidget-io" href="https://forecast7.com/en/22d8589d54/khulna/" data-label_1="KHULNA" data-label_2="WEATHER" data-font="Times New Roman" data-theme="weather_one" >KHULNA WEATHER</a> -->
                    <div id="weather">
                        <?php echo do_shortcode( '[shortcode-weather-atlas city_selector='.$city_code.']' ); ?>
                    </div>
                    <?php
            }elseif($atts['menu'] == 'events'){
                ?>
                <!-- <script>

                    var unirest = require("unirest");

                    var req = unirest("GET", "https://jgentes-crime-data-v1.p.rapidapi.com/crime");

                    req.query({
                        "startdate": "9%2F19%2F2015",
                        "enddate": "9%2F25%2F2015",
                        "lat": "37.757815",
                        "long": "-122.5076392"
                    });

                    req.headers({
                        "x-rapidapi-host": "jgentes-Crime-Data-v1.p.rapidapi.com",
                        "x-rapidapi-key": "055398b754mshd4bb292b8e54f03p12ef29jsnf6bebd89fa75"
                    });


                    req.end(function (res) {
                        if (res.error) throw new Error(res.error);

                        console.log(res.body);
                    });

                </script> -->
                <?php

                    // $curl = curl_init();

                    // curl_setopt_array($curl, array(
                    //     CURLOPT_URL => "https://jgentes-crime-data-v1.p.rapidapi.com/crime?startdate=9%252F19%252F2015&enddate=9%252F25%252F2015&lat=37.757815&long=-122.5076392",
                    //     CURLOPT_RETURNTRANSFER => true,
                    //     CURLOPT_FOLLOWLOCATION => true,
                    //     CURLOPT_ENCODING => "",
                    //     CURLOPT_MAXREDIRS => 10,
                    //     CURLOPT_TIMEOUT => 30,
                    //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    //     CURLOPT_CUSTOMREQUEST => "GET",
                    //     CURLOPT_HTTPHEADER => array(
                    //         "x-rapidapi-host: jgentes-Crime-Data-v1.p.rapidapi.com",
                    //         "x-rapidapi-key: 055398b754mshd4bb292b8e54f03p12ef29jsnf6bebd89fa75"
                    //     ),
                    // ));

                    // $response = curl_exec($curl);
                    // $err = curl_error($curl);

                    // curl_close($curl);

                    // if ($err) {
                    //     echo "cURL Error #:" . $err;
                    // } else {
                    //     echo $response;
                    // }

            }




            
        }


        // Customize oEmbed markup
        function shapeSpace_oembed_html($html, $url, $attr, $post_id) {

            $enable_full_screen = (get_option( 'enable_full_screen' ) == 1) ? 'allowfullscreen' : '';

            $id = explode("/", $url);
            $videos_all_url = $id[2];
            
            if($videos_all_url == 'youtu.be'){

            $end_id = end($id);
            
            $html = '<iframe src="//www.youtube.com/embed/'.$end_id.'?enablejsapi=1&amp;rel=0&amp;showinfo=0&amp;" frameborder="0" '.$enable_full_screen.'></iframe><div class="start-video"></div>';
            
            return '<div class="oembed">        
                        <div class="video_overly_ch1 video_play_op"></div>
                        <div class="video_overly_ch2 video_play_op"></div>
                        <div class="video_overly_ch3 video_play_op"></div>
                        <div class="video_overly_ch4 video_play_op"></div>
                        <div class="video_overly_ch_m1 video_play_op"></div>
                        <div class="video_overly_ch5 video_play_op"></div>
                        <div class="video_overly_ch6 video_play_op"></div>
                        <div class="video_overly_ch7 video_play_op"></div>
                        <div class="video_overly_ch_m2"></div>
                        <div class="video_overly_sch1"></div>
                        
                        '. $html .'
                    </div>';

            }elseif($videos_all_url == 'www.dailymotion.com'){

                $end_id = end($id);

                $html = '<iframe frameborder="0"
                src="//www.dailymotion.com/embed/video/'.$end_id.'?mute=0&info=0&logo=0&social=0&queue-enable=false" allow="autoplay" '.$enable_full_screen.'></iframe>';

                return '<div class="oembed">        
                            <div class="dailymotion_video_s"></div>
                            '. $html .'
                        </div>';

                //return ' <iframe frameborder="0" allowfullscreen="true" width="640" height="360" src="http://www.dailymotion.com/embed/video/x3om8ig?mute=0&info=0&logo=0&related=0&social=0&highlight=FFCC33"></iframe>';

            }elseif($videos_all_url == 'www.facebook.com'){

                $id = array_values(array_filter($id));

                $end_id = end($id);

                $html = '<iframe src="http://www.facebook.com/video/embed?video_id='.$end_id.'" frameborder="0" '.$enable_full_screen.'></iframe>';

                return $html;
            }else{
                return $html;
            }
                
        }


        /*
        * Admin Menu Function
        */
        function submenufunction(){
            if (isset($_POST['button_image'])){
                $icon = $_POST['button_image'];
                update_option( 'button_image', $icon);
            }

            if (isset($_POST['add_url'])){

                if(get_option('add_all_url') == 'null')
                {
                    $all_url = array();
                }else{
                    $all_url = json_decode(get_option('add_all_url'), true);
                }
                
                $single_url = $_POST['add_url'];

                array_push($all_url, $single_url);
                
                $all_url_f = array_values($all_url);
                $add_all_url = json_encode($all_url_f);

                update_option( 'add_all_url', $add_all_url);
            }

            if (isset($_POST['remove_url'])){

                
                $all_url = json_decode(get_option('add_all_url'), true);
                $remove_url = $_POST['remove_url'];

                $all_url_r = array_diff($all_url, array($remove_url));
                $all_url_r_f = array_values($all_url_r);

                $add_all_url = json_encode($all_url_r_f);

                update_option( 'add_all_url', $add_all_url);
            }

            if (isset($_POST['google_news_api'])){
                
                $google_news_api = $_POST['google_news_api'];
                update_option( 'google_news_api', $google_news_api);
            }

            ob_start();
            ?>
                <div class="youtube_custom_settings-submenu">
                    <div class="youtube_custom_settings-submenu-title">
                        <h1><?php _e('Youtube Custom Settings', 'youtube_custom_settings'); ?></h1>
                    </div>

                    <div class="tab">
                        <button class="tablinks active" onclick="openTab(event, 'videos_setting')"><?php _e('Videos setting', 'youtube_custom_settings'); ?></button>
                        <button class="tablinks" onclick="openTab(event, 'block_url')"><?php _e('Block URL', 'youtube_custom_settings') ?></button>
                        <button class="tablinks" onclick="openTab(event, 'settingsTab')"><?php _e('Api Settings', 'youtube_custom_settings') ?></button>
                    </div>

                    <div id="videos_setting" style="display:block;" class="tabcontent youtube_custom_settings-submenu-body">
                        <table class="deleteWrapTable">
                            <tbody>
                                <tr> 
                                    <th><?php _e('Enable video full screen', 'youtube_custom_settings'); ?></th>
                                    <td><div class='checkbox' id='hideSearch'>
                                            <label class='checkbox__container'>
                                            <input class='checkbox__toggle' type='checkbox' value="1" name='enable_full_screen' <?php echo $checked = (get_option( 'enable_full_screen' ) == 1) ? 'checked' : '' ; ?>/>
                                            <span class='checkbox__checker'></span>
                                            <span class='checkbox__txt-left'>On</span>
                                            <span class='checkbox__txt-right'>Off</span>
                                            <svg class='checkbox__bg' space='preserve' style='enable-background:new 0 0 110 43.76;' version='1.1' viewbox='0 0 110 43.76'>
                                                <path class='shape' d='M88.256,43.76c12.188,0,21.88-9.796,21.88-21.88S100.247,0,88.256,0c-15.745,0-20.67,12.281-33.257,12.281,S38.16,0,21.731,0C9.622,0-0.149,9.796-0.149,21.88s9.672,21.88,21.88,21.88c17.519,0,20.67-13.384,33.263-13.384,S72.784,43.76,88.256,43.76z'></path>
                                            </svg>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                                <tr> 
                                    <th><?php _e('Show your custom button', 'youtube_custom_settings'); ?></th>
                                    <td><div class='checkbox' id='hideSearch'>
                                            <label class='checkbox__container'>
                                            <input class='checkbox__toggle' type='checkbox' value="1" name='custom_button' <?php echo $checked = (get_option( 'custom_button' ) == 1) ? 'checked' : '' ; ?>/>
                                            <span class='checkbox__checker'></span>
                                            <span class='checkbox__txt-left'>On</span>
                                            <span class='checkbox__txt-right'>Off</span>
                                            <svg class='checkbox__bg' space='preserve' style='enable-background:new 0 0 110 43.76;' version='1.1' viewbox='0 0 110 43.76'>
                                                <path class='shape' d='M88.256,43.76c12.188,0,21.88-9.796,21.88-21.88S100.247,0,88.256,0c-15.745,0-20.67,12.281-33.257,12.281,S38.16,0,21.731,0C9.622,0-0.149,9.796-0.149,21.88s9.672,21.88,21.88,21.88c17.519,0,20.67-13.384,33.263-13.384,S72.784,43.76,88.256,43.76z'></path>
                                            </svg>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                                <tr> 
                                    <th><?php _e('Upload your button icon', 'youtube_custom_settings'); ?></th>
                                    <td>
                                        <form method="post">
                                            <?php if(get_option('button_image') == '')
                                            {
                                                echo '<input id="image-url" type="text" name="button_image" />';
                                            }else{
                                                echo '<img src="'.get_option('button_image').'" height="42" width="42">';
                                                echo '<input type="hidden" id="image-url" type="text" name="button_image" />';
                                            }?>

                                            <input id="upload-button" type="button" class="button" value="Upload Image" />
                                            <input type="submit" class="image_up_b" value="Submit" />
                                        </form>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div id="block_url" class="tabcontent youtube_custom_settings-submenu-body">
                        <div class="delete_denger">
                            <strong><?php _e('Note: ', 'youtube_custom_settings'); ?></strong><span><?php _e('Danger zone.', 'youtube_custom_settings'); ?></span>
                        </div>
                        <table class="deleteWrapTable">
                            <tbody>
                                <tr class="block_url_tr"> 
                                    <th><?php _e('Enter the block URL', 'youtube_custom_settings'); ?></th>
                                    <td>
                                        <form method="post">
                                            <div class="block_url">
                                                <input type="text" required="required" id="add_url" name="add_url" class="add_url_class">
                                                <button type="submit" name="add" id="add" class="button btn btn-success">+</button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                                <tr> 
                                    <th><?php _e('Remove block list URL', 'youtube_custom_settings'); ?></th>
                                    <td>
                                        <form method="post">
                                            <div class="block_url">
                                                <input type="text" required="required" id="add_url" name="remove_url" class="add_url_class">
                                                <button type="submit" name="add" id="add" class="button btn btn-success">x</button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                                <tr class="block_url_tr"> 
                                    <th><?php
                                    $all_url = json_decode(get_option('add_all_url'), true);
                                    if (!empty($all_url)){
                                        _e('Your block URL list', 'youtube_custom_settings'); 
                                    }
                                        ?></th>
                                    <td>
                                        <?php
                                        
                                        $all_url = json_decode(get_option('add_all_url'), true);
                                        $all_url = array_values($all_url);

                                        if (isset($all_url)){
                                            echo '<ul class="block_list">';
                                            for( $i = 0; $i < count($all_url); $i++ )
                                            {
                                                echo '<li>' .$all_url[$i]. '</li>';
                                            }
                                            echo '</ul>';
                                        }

                                        ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div id="settingsTab" class="tabcontent youtube_custom_settings-submenu-body">
                        <table class="deleteWrapTable">
                            <tbody>
                                <tr class="block_url_tr"> 
                                    <th><?php _e('Enter your google news api', 'youtube_custom_settings'); ?></th>
                                    <td>
                                        <form method="post">
                                            <div class="google_news_api">
                                                <input type="text" required="required" value="<?php echo $google_news_api = (get_option( 'google_news_api' ) == '') ? '9a47449ffc2b4fcc8f1877ecfa13908c': get_option( 'google_news_api' ); ?>" id="google_news_api" name="google_news_api" class="add_url_class">
                                                <button type="submit" name="add" id="add" class="button btn btn-success">+</button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                                <tr class="block_url_tr"> 
                                    <th><?php _e('Local News Shortcode', 'youtube_custom_settings'); ?></th>
                                    <td class="shortcode"><?php echo '[youtube menu=localnews]'; ?></td>
                                </tr>
                                <tr class="block_url_tr"> 
                                    <th><?php _e('Weather Shortcode', 'youtube_custom_settings'); ?></th>
                                    <td class="shortcode"><?php echo '[youtube menu=weather]'; ?></td>
                                </tr>
                                <tr class="block_url_tr"> 
                                    <th><?php _e('Event shortcode', 'youtube_custom_settings'); ?></th>
                                    <td class="shortcode"><?php echo sprintf('Click <a href="%s">here</a> for event shortcode & <a target="_blank" href="%s">M.E Calender</a> are required for event.', admin_url( 'edit.php?post_type=mec_calendars' ), 'https://wordpress.org/plugins/modern-events-calendar-lite/') ?></td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                    
                </div>
            <?php
            $output = ob_get_clean();
            echo $output;
        }

        /*
        * Hide top bar function 
        * ajax call from voting-back.js
        */
        function custom_buttonfunction(){

        $value = $_POST['value'];

        update_option( 'custom_button', $value);

        echo json_encode(
            array(
                'message' => 'success',
                'value' => $value
            )
        );
        die();
        }
        function enable_full_screenfunction(){

            $value = $_POST['value'];
    
            update_option( 'enable_full_screen', $value);
    
            echo json_encode(
                array(
                    'message' => 'success',
                    'value' => $value
                )
            );
            die();
        }

        /*
        * youtubeJStoFrontHead JS to Admin head
        */
        function youtubeJStoFrontHead(){

            $button_image = get_option( 'button_image' );

            if(get_option( 'custom_button' ) == 1){
                echo '<style>
                .oembed .start-video {
                    background: url('.$button_image.') no-repeat;
                    height: 24%;
                    width: 14%;
                    left: 43%;
                    top: 38%;                
                    position: absolute;
                    cursor: pointer;
                    z-index: 9;
                    background-size: contain !important;
                }
                </style>';
            }else{
                echo '<style>
                .oembed .start-video { 
                    display: none;
                }
                </style>';
            }

        }


    } // End Class
} // End Class check if exist / not