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

            /*Settins hook to wp admin via js */
            add_action( 'wp_head', array($this, 'youtubeJStoFrontHead') );
            
            //add filter
            //add_filter( 'the_content', array($this, 'filter_the_content_in_the_main_loop') );
            
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

        function filter_the_content_in_the_main_loop( $content ) {
            $DOM = new DOMDocument();
            $DOM->loadHTML($content);
            $list = $DOM->getElementsByTagName('iframe');
            $i = 0;

            foreach($list as $p){
                $p->setAttribute('class', 'iframe'.$i++);
            }
            $DOM=$DOM->saveHTML(); 
            $content = $DOM;

            return $content;
        }

        // Customize oEmbed markup
        function shapeSpace_oembed_html($html, $url, $attr, $post_id) {

            $id = explode("/", $url);
            $end_id = end($id);

            $width = $attr['width'];
            $height = $attr['height'];

            
            $html = '<iframe src="//www.youtube.com/embed/'.$end_id.'?enablejsapi=1&amp;rel=0&amp;showinfo=0&amp;" frameborder="0" ></iframe><div class="start-video"></div>';
            
            return '<div class="oembed">        
                        <div class="video_overly_ch1"></div>
                        <div class="video_overly_ch2"></div>
                        <div class="video_overly_ch3"></div>
                        <div class="video_overly_ch4"></div>
                        <div class="video_overly_ch_m1"></div>
                        <div class="video_overly_ch5"></div>
                        <div class="video_overly_ch6"></div>
                        <div class="video_overly_ch7"></div>
                        <div class="video_overly_ch_m2"></div>
                        <div class="video_overly_sch1"></div>
                        
                        '. $html .'
                    </div>';
                
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

            
                
            ob_start();
            ?>
                <div class="youtube_custom_settings-submenu">
                    <div class="youtube_custom_settings-submenu-title">
                        <h1><?php _e('Youtube Custom Settings', 'youtube_custom_settings'); ?></h1>
                    </div>
                    <div class="youtube_custom_settings-submenu-body">
                        
                            <table class="deleteWrapTable">
                                <tbody>
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
                                                <?php if(get_option('button_image') == 'null')
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