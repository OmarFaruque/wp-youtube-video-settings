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
            add_shortcode( 'newsfeed', array($this, 'youtube_custom_shortcode') );

            add_action( 'init', array($this, 'weather_atlas') );
            
        }

        function weather_atlas()
        {
            require_once $this->plugin_url . 'inc/weather-atlas/weather-atlas.php';
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

            wp_enqueue_style( 'fyoutube_custom_settingsCSS', $this->plugin_url . 'asset/css/youtube_custom_settings_frontend.css', array(), true, 'all' );
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

            echo '<pre>';
            print_r($atts);
            echo '</pre>';

            $ip = $this->get_ip();
            $country = file_get_contents('http://ip-api.com/json/'.$ip);
            $country = json_decode($country, true);
            echo 'return country: <br/><pre>';
            print_r($country);
            echo '</pre>';

            if($atts['menu'] == 'local-news'){

                $google_news_api = get_option( 'google_news_api' );
                //$countryCode = $country['countryCode'];
                $countryCode = 'US';

                $url = file_get_contents('http://newsapi.org/v2/top-headlines?country='.$countryCode.'&apiKey='. $google_news_api);
                $url = json_decode($url, true);
                $url = $url['articles'];
                ?>
                <div class="google-breaking-news">
                <?php
                    for ($i = 0; $i < count($url); $i++) {
                    ?>
                        
                        <article class="single-news-post">
                            <a href="<?php echo $url[$i]['url']; ?>">
                                <h2><?php echo $url[$i]['title']; ?></h2>
                            </a>
                            <a href="<?php echo $url[$i]['url']; ?>" class="post-img"><img src="<?php echo $url[$i]['urlToImage']; ?>" alt=""></a>
                            <div class="post-info">
                                <div class="author-info">
                                    <h3><?php echo $url[$i]['author']; ?></h3>
                                </div>
                                <div class="post-date text-right">
                                    <a href="<?php echo $url[$i]['url']; ?>"><?php echo $url[$i]['publishedAt']; ?></a>
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
                ?>
                    <a class="weatherwidget-io" href="https://forecast7.com/en/22d8589d54/khulna/" data-label_1="KHULNA" data-label_2="WEATHER" data-font="Times New Roman" data-theme="weather_one" >KHULNA WEATHER</a>

                    
                    <?php
            }elseif($atts['menu'] == 'local-events'){

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

            }elseif($atts['menu'] == 'crime-alert'){
                

            }




            
        }


        // Customize oEmbed markup
        function shapeSpace_oembed_html($html, $url, $attr, $post_id) {

            $enable_full_screen = (get_option( 'enable_full_screen' ) == 1) ? 'allowfullscreen' : '';

            $id = explode("/", $url);
            $videos_all_url = $id[2];

            // echo '<pre>';
            // print_r($id);
            // echo '</pre>';
            
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
                // echo '<pre>';
                // print_r($id);
                // echo '</pre>';

                $end_id = end($id);

                $html = '<iframe src="http://www.facebook.com/video/embed?video_id='.$end_id.'" frameborder="0" '.$enable_full_screen.'></iframe>';

                    //$html = '<iframe frameborder="0" allowtransparency="true" allowfullscreen="false" scrolling="no" allow="encrypted-media"
                     //src="http://www.facebook.com/video/embed?video_id='.$end_id.'" style="border: none; visibility: visible;" ></iframe>';
                     
                return $html;
            }else{
                return $html;
            }
                
        }


        /*
        * Admin Menu Function
        */
        function submenufunction(){
            // echo 'jony'. get_option( 'custom_button' ) . '</br>';
            // echo 'image :' . get_option( 'button_image' ) . '</br>';
            // echo 'all url :' . get_option( 'add_all_url' ) . '</br>';

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
                                    <th><?php _e('Google breaking news shortcode', 'youtube_custom_settings'); ?></th>
                                    <td class="shortcode"><?php echo '[breaking-news]'; ?></td>
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