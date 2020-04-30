<?php
/*
* event_integration Class 
*/

if (!class_exists('event_integrationClass')) {
    class event_integrationClass{
        public $plugin_url;
        public $plugin_dir;
        public $wpdb;
        public $option_tbl; 
        
        /**Plugin init action**/ 
        public function __construct() {
            global $wpdb;
            $this->plugin_url 				= event_integrationURL;
            $this->plugin_dir 				= event_integrationDIR;
            $this->wpdb 					= $wpdb;	
            $this->option_tbl               = $this->wpdb->prefix . 'options';
         
            $this->init();
        }

        private function init(){

            //Backend Script
            add_action( 'admin_enqueue_scripts', array($this, 'event_integration_backend_script') );
            //Frontend Script
            add_action( 'wp_enqueue_scripts', array($this, 'event_integration_frontend_script') );

            //Add Menu Options
            add_action('admin_menu', array($this, 'event_integration_admin_menu_function'));

            add_filter('peepso_navigation_profile', array($this, 'filter_peepso_navigation_profile'), -1);
            
        }


        /*
        * Appointment backend Script
        */
        function event_integration_backend_script(){
            
            wp_enqueue_style( 'b_event_integrationCSS', $this->plugin_url . 'asset/css/event_integration_backend.css', array(), true, 'all' );
            wp_enqueue_script( 'b_event_integrationJS', $this->plugin_url . 'asset/js/event_integration_backend.js', array(), true );

        }

        /*
        * Appointment frontend Script
        */
        function event_integration_frontend_script(){

            wp_enqueue_style( 'f_event_integrationCSS', $this->plugin_url . 'asset/css/event_integration_frontend.css', array(), true, 'all' );

            wp_enqueue_script('f_event_integrationJS', $this->plugin_url . 'asset/js/event_integration_frontend.js', array('jquery'), time(), true);
           
        }

        /*
        * Admin Menu
        */
        function event_integration_admin_menu_function(){
            add_menu_page( 'Event Integration', 'Event Integration', 'manage_options', 'event-integration-menu', array($this, 'submenufunction'), 'dashicons-calendar-alt', 50 );
        }

        // update Settings
        public function updateSettings($data){
            update_option( 'alleventpage', $data );
        }

        //submenu function
        function submenufunction(){
            if(isset($_POST['alleventpage'])) $this->updateSettings($_POST['alleventpage']);
        ?>
            <div class="event-integration-submenu">
                <div class="event-integration-submenu-title">
                    <h1><?php _e('Event Integration Settings', 'event_integration'); ?></h1>
                </div>
                <!-- Settings -->
                <div class="event-integration">
                    <div id="settings" class="tabcontent">
                        <div class="settingsInner">
                            <form id="settingsForm" method="post" action="">
                                <table class="event-integration-data-table">
                                    <tbody>
                                        <tr>
                                            <th class="text-left"><?php _e('User Event Page', 'event_integration' ); ?></th>
                                            <td class="text-left">
                                                <?php $allpages = get_all_page_ids(); ?>
                                                <select name="alleventpage" class="form-control" id="alleventpage">
                                                    <?php   foreach( $allpages as $sp):
                                                        $selected = (get_option( 'alleventpage') == $sp ) ? 'selected' : '';
                                                        ?>
                                                        <option <?php echo $selected; ?> value="<?php echo $sp; ?>"><?php echo get_the_title($sp); ?></option>
                                                    <?php endforeach; ?>

                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-left"><?php _e('User Event Shortcode', 'event_integration'); ?></th>
                                            <td class="text-left"><?php echo '[MEC_fes_form]'; ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }

        
        /*
        * Add links to the profile segment submenu
        */
        public function filter_peepso_navigation_profile($links)
        {
            $alleventpage = get_the_permalink( get_option( 'alleventpage', 1 ) );

            // echo 'jony_array : </br><pre>';
            // print_r($links);
            // echo '</pre>';

            $links['event'] = array(
                'label'=> __('Event', 'peepso-core'),
                'href' => $alleventpage,
                'icon' => 'ps-icon-calendar'
            );

            return $links;
        }


    } // End Class
} // End Class check if exist / not

