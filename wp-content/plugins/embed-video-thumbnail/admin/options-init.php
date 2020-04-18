<?php

/**
 * For full documentation, please visit: http://docs.reduxframework.com/
 * For a more extensive sample-config file, you may look at:
 * https://github.com/reduxframework/redux-framework/blob/master/sample/sample-config.php
 */

if (! class_exists('Redux')) {
    return;
}

// This is your option name where all the Redux data is stored.
$opt_name = IKANAWEB_EVT_SLUG;
$translationDomain = IKANAWEB_EVT_TEXT_DOMAIN;

/**
 * get plugin data
 */
/**
 * ---> SET ARGUMENTS
 * All the possible arguments for Redux.
 * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
 * */

$theme = wp_get_theme(); // For use with some settings. Not necessary.

$args = array(
    'opt_name' => $opt_name,
    'use_cdn' => true,
    'display_name' => IKANAWEB_EVT_NAME,
    'display_version' => IKANAWEB_EVT_VERSION,
    'page_title' => IKANAWEB_EVT_NAME,
    'update_notice' => true,
    'admin_bar' => false,
    'menu_icon' => 'dashicons-format-video',
    'menu_type' => 'submenu',
    'menu_title' => IKANAWEB_EVT_NAME,
    'page_priority' => null,
    'page_parent' => 'tools.php',
    'page_parent_post_type' => 'your_post_type',
    'default_show' => true,
    'default_mark' => '',
    'class' => $opt_name,
    'dev_mode' => false,
    'hints' => array(
        'icon' => 'el el-info-sign',
        'icon_position' => 'right',
        'icon_color' => 'lightgray',
        'icon_size' => 'normal',
        'tip_style' => array(
            'color' => 'light',
            'shadow' => '1',
            'rounded' => '1',
            'style' => 'bootstrap',
        ),
        'tip_position' => array(
            'my' => 'top left',
            'at' => 'bottom right',
        ),
        'tip_effect' => array(
            'show' => array(
                'effect' => 'slide',
                'duration' => '500',
                'event' => 'mouseover',
            ),
            'hide' => array(
                'effect' => 'slide',
                'duration' => '500',
                'event' => 'mouseleave unfocus',
            ),
        ),
    ),
    'header_right' =>
        '
        <p>
        '.sprintf(
        __("We hope you like our plugin<br/>Feel free to leave a review <a href=\"%s\" target=\"blank\">here</a>", $translationDomain),
                IKANAWEB_EVT_REVIEW_URL
        ).'
        </p>
        <p>
        '.sprintf(
            __('If you encounter an issue with our plugin, you can open a ticket <a href="%s" target="blank">on the support forum</a>', $translationDomain),
            IKANAWEB_EVT_SUPPORT_URL
        ).'
        </p>
        ',
    'logo_src' => IKANAWEB_EVT_URL.'/admin/image/icon-128x128.png',
    'output' => true,
    'output_tag' => true,
    'settings_api' => true,
    'cdn_check_time' => '1440',
    'compiler' => true,
    'page_permissions' => 'manage_options',
    'save_defaults' => true,
    'show_import_export' => true,
    'database' => 'options',
    'transient_time' => '3600',
    'templates_path' => dirname(__FILE__).'/templates',
    'network_sites' => true,
);

// SOCIAL ICONS -> Setup custom links in the footer for quick links in your panel footer icons.
$args['share_icons'][] = array(
    'url'   => 'https://www.ikanaweb.fr',
    'title' => 'Visit our website',
    'icon'  => 'el el-website'
);
$args['share_icons'][] = array(
    'url'   => 'https://www.linkedin.com/in/fr%C3%A9d%C3%A9ric-guichard-3b6835111',
    'title' => 'Find us on Linkedin',
    'icon'  => 'el el-linkedin'
);
Redux::setArgs($opt_name, $args);

/*
 * ---> END ARGUMENTS
 */

/*
 * ---> START HELP TABS
 */
/*
$tabs = array(
    array(
        'id'      => 'redux-help-tab-1',
        'title'   => __( 'Theme Information 1', 'admin_folder' ),
        'content' => __( '<p>This is the tab content, HTML is allowed.</p>', 'admin_folder' )
    ),
    array(
        'id'      => 'redux-help-tab-2',
        'title'   => __( 'Theme Information 2', 'admin_folder' ),
        'content' => __( '<p>This is the tab content, HTML is allowed.</p>', 'admin_folder' )
    )
);
Redux::setHelpTab( $opt_name, $tabs );

 //Set the help sidebar
$content = __( '<p>This is the sidebar content, HTML is allowed.</p>', $translationDomain );
Redux::setHelpSidebar( $opt_name, $content );
*/

/*
 * <--- END HELP TABS
 */


/*
 *
 * ---> START SECTIONS
 *
 */

Redux::setSection($opt_name, array(
    'title'  => __('General settings', $translationDomain),
    'id'     => 'section-general',
    'icon'   => 'el el-home',
    'fields' => array(
        array(
            'id'       => 'global--enable',
            'type'     => 'switch',
            'title'    => __('Enable plugin', $translationDomain),
            'default' => true,
        ),
        array(
            'id'       => 'global--post_type',
            'type'     => 'checkbox',
            'title'    => __('Enable by post type', $translationDomain),
            'subtitle'    => __('Select pages types where plugin will be enabled', $translationDomain),
            'data' => 'post_type'
        ),
        array(
            'id'       => 'global--exclude_posts',
            'type'     => 'textarea',
            'title'    => __('Disable by post id', $translationDomain),
            'subtitle'    => __('Write one post id per line.', $translationDomain)
        ),
    )
));

Redux::setSection($opt_name, array(
    'title'  => __('Provider', $translationDomain),
    'id'     => 'section-display',
    'icon'   => 'el el-video'
));

Redux::setSection($opt_name, array(
    'title'  => __('Youtube', $translationDomain),
    'id'     => 'section-display-youtube',
    'subsection' => true,
    'fields' => array(
        array(
            'id'       => 'section-display-youtube-start-1',
            'indent' => true,
            'type'     => 'section',
            'title'    => __("Display settings", $translationDomain),
        ),
        array(
            'id'       => 'youtube--enable',
            'type'     => 'switch',
            'title'    => __('Enable', $translationDomain),
            'default' => true,
        ),
        array(
            'id'       => 'section-display-youtube-end-1',
            'indent' => false,
            'type'     => 'section',
        ),
        array(
            'id'       => 'section-display-youtube-start-2',
            'indent' => true,
            'type'     => 'section',
            'required' => array('youtube--enable','equals',true),
            'title'    => __("API", $translationDomain),
        ),
        array(
            'id'       => 'youtube--api--key',
            'type'     => 'text',
            'title'    => __('API key', $translationDomain),
            'subtitle'    => __('Mandatory parameter to fetch video titles', $translationDomain),
            'desc' => __('Checkout <a href="https://developers.google.com/youtube/v3/getting-started" target="_blank">https://developers.google.com/youtube/v3/getting-started</a> for more information', $translationDomain),
            'default' => ""
        ),
        array(
            'id'       => 'youtube--api--display_title',
            'required' => array('youtube--api--key','!=',''),
            'type'     => 'switch',
            'title'    => __('Display video title', $translationDomain),
            'default' => true,
        ),
        array(
            'id'       => 'youtube--api--display_thumb',
            'type'     => 'switch',
            'title'    => __('Display video thumbnail', $translationDomain),
            'default' => true,
        ),
        array(
            'id'       => 'youtube--api--thumb_copy',
            'type'     => 'switch',
            'required' => array('youtube--api--display_thumb','equals',true),
            'title'    => __('Copy thumbnail locally', $translationDomain),
            'subtitle'    => __("By default video thumbnails are fetched from a remote server. Copying thumbnails locally improve performance", $translationDomain),
            'default' => false,
        ),
        array(
            'id'       => 'section-display-youtube-end-2',
            'indent' => false,
            'type'     => 'section',
        ),
        array(
            'id'       => 'section-display-youtube-start-3',
            'indent' => true,
            'required' => array('youtube--enable','equals',true),
            'type'     => 'section',
            'title'    => __("Video player settings", $translationDomain),
        ),
        array(
            'id'       => 'youtube--embed--playbutton',
            'type'     => 'media',
            'title'    => __( 'Play button image', $translationDomain ),
            'desc'     => __( '72 x 72 px', $translationDomain ),
            'default' => array('url' => IKANAWEB_EVT_URL . '/assets/images/youtube-72-72.png'),
        ),
        array(
            'id'       => 'youtube--embed--autoplay',
            'type'     => 'switch',
            'title'    => __('Auto play', $translationDomain),
            'default' => true,
        ),
        array(
            'id'       => 'youtube--embed--rel',
            'type'     => 'switch',
            'title'    => __('Display related content at the end', $translationDomain),
            'default' => true,
        ),
        array(
            'id'       => 'youtube--embed--modestbranding',
            'type'     => 'switch',
            'title'    => __('Remove youtube logo', $translationDomain),
            'default' => false,
        ),
        array(
            'id'       => 'youtube--embed--controls',
            'type'     => 'switch',
            'title'    => __('Enable controls', $translationDomain),
            'default' => true,
        ),
        array(
            'id'       => 'youtube--embed--no_cookie',
            'type'     => 'switch',
            'title'    => __('Disable cookies', $translationDomain),
            'default' => true,
        ),
        array(
            'id'       => 'section-display-youtube-end-3',
            'indent' => false,
            'type'     => 'section',
        ),
    )
));

Redux::setSection($opt_name, array(
    'title'  => __('Vimeo', $translationDomain),
    'id'     => 'section-display-vimeo',
    'subsection' => true,
    'fields' => array(
        array(
            'id'       => 'section-display-vimeo-start-1',
            'indent' => true,
            'type'     => 'section',
            'title'    => __("Display settings", $translationDomain),
        ),
        array(
            'id'       => 'vimeo--enable',
            'type'     => 'switch',
            'title'    => __('Enable', $translationDomain),
            'default' => true,
        ),
        array(
            'id'       => 'section-display-vimeo-end-1',
            'indent' => false,
            'type'     => 'section',
        ),
        array(
            'id'       => 'section-display-vimeo-start-2',
            'required' => array('vimeo--enable','equals',true),
            'indent' => true,
            'type'     => 'section',
            'title'    => __("API", $translationDomain),
        ),
        array(
            'id'       => 'vimeo--api--display_title',
            'type'     => 'switch',
            'title'    => __('Display video title', $translationDomain),
            'default' => true,
        ),
        array(
            'id'       => 'vimeo--api--display_thumb',
            'type'     => 'switch',
            'title'    => __('Display video thumbnail', $translationDomain),
            'default' => true,
        ),
        array(
            'id'       => 'vimeo--api--thumb_copy',
            'type'     => 'switch',
            'required' => array('vimeo--api--display_thumb','equals', true),
            'title'    => __('Copy thumbnail locally', $translationDomain),
            'subtitle'    => __("By default video thumbnails are fetched from a remote server. Copying thumbnails locally improve performance", $translationDomain),
            'default' => false,
        ),
        array(
            'id'       => 'section-display-vimeo-end-2',
            'indent' => false,
            'type'     => 'section',
        ),
        array(
            'id'       => 'section-display-vimeo-start-3',
            'indent' => true,
            'required' => array('vimeo--enable','equals',true),
            'type'     => 'section',
            'title'    => __("Video player settings", $translationDomain),
        ),
        array(
            'id'       => 'vimeo--embed--playbutton',
            'type'     => 'media',
            'title'    => __( 'Play button image', $translationDomain ),
            'desc'     => __( '72 x 72 px', $translationDomain ),
            'default' => array('url' => IKANAWEB_EVT_URL . '/assets/images/play-default.png'),
        ),
        array(
            'id'       => 'vimeo--embed--autoplay',
            'type'     => 'switch',
            'title'    => __('Auto play', $translationDomain),
            'default' => true,
        ),
        array(
            'id'       => 'vimeo--embed--loop',
            'type'     => 'switch',
            'title'    => __('Video loop', $translationDomain),
            'default' => true,
        ),
        array(
            'id'       => 'section-display-vimeo-end-3',
            'indent' => false,
            'type'     => 'section',
        ),
    )
));

Redux::setSection($opt_name, array(
    'title'  => __('Dailymotion', $translationDomain),
    'id'     => 'section-display-dailymotion',
    'subsection' => true,
    'fields' => array(
        array(
            'id'       => 'section-display-dailymotion-start-1',
            'indent' => true,
            'type'     => 'section',
            'title'    => __("Display settings", $translationDomain),
        ),
        array(
            'id'       => 'dailymotion--enable',
            'type'     => 'switch',
            'title'    => __('Enable', $translationDomain),
            'default' => true,
        ),
        array(
            'id'       => 'section-display-dailymotion-end-1',
            'indent' => false,
            'type'     => 'section',
        ),
        array(
            'id'       => 'section-display-dailymotion-start-2',
            'required' => array('dailymotion--enable','equals',true),
            'indent' => true,
            'type'     => 'section',
            'title'    => __("API", $translationDomain),
        ),
        array(
            'id'       => 'dailymotion--api--display_title',
            'required' => array('dailymotion.api.key','not',""),
            'type'     => 'switch',
            'title'    => __('Display video title', $translationDomain),
            'default' => false,
        ),
        array(
            'id'       => 'dailymotion--api--display_thumb',
            'type'     => 'switch',
            'title'    => __('Display video thumbnail', $translationDomain),
            'default' => true,
        ),
        array(
            'id'       => 'dailymotion--api--thumb_copy',
            'type'     => 'switch',
            'required' => array('dailymotion--api--display_thumb','equals', true),
            'title'    => __('Copy thumbnail locally', $translationDomain),
            'subtitle'    => __("By default video thumbnails are fetched from a remote server. Copying thumbnails locally improve performance", $translationDomain),
            'default' => false,
        ),
        array(
            'id'       => 'section-display-dailymotion-end-2',
            'indent' => false,
            'type'     => 'section',
        ),
        array(
            'id'       => 'section-display-dailymotion-start-3',
            'indent' => true,
            'required' => array('dailymotion--enable','equals',true),
            'type'     => 'section',
            'title'    => __("Video player settings", $translationDomain),
        ),
        array(
            'id'       => 'dailymotion--embed--playbutton',
            'type'     => 'media',
            'title'    => __( 'Play button image', $translationDomain ),
            'desc'     => __( '72 x 72 px', $translationDomain ),
            'default' => array('url' => IKANAWEB_EVT_URL . '/assets/images/play-default.png'),
        ),
        array(
            'id'       => 'dailymotion--embed--autoplay',
            'type'     => 'switch',
            'title'    => __('Auto play', $translationDomain),
            'default' => true,
        ),
        array(
            'id'       => 'section-display-dailymotion-end-3',
            'indent' => false,
            'type'     => 'section',
        ),
    )
));

Redux::setSection($opt_name, array(
    'title'  => __('Facebook', $translationDomain),
    'id'     => 'section-display-facebook',
    'subsection' => true,
    'fields' => array(
        array(
            'id'       => 'section-display-facebook-start-1',
            'indent' => true,
            'type'     => 'section',
            'title'    => __("Display settings", $translationDomain),
        ),
        array(
            'id'       => 'facebook--enable',
            'type'     => 'switch',
            'title'    => __('Enable', $translationDomain),
            'default' => true,
        ),
        array(
            'id'       => 'section-display-facebook-end-1',
            'indent' => false,
            'type'     => 'section',
        ),
        array(
            'id'       => 'section-display-facebook-start-2',
            'indent' => true,
            'type'     => 'section',
            'required' => array('facebook--enable','equals',true),
            'title'    => __("API", $translationDomain),
        ),
        array(
            'id'       => 'facebook--api--display_title',
            'required' => array('facebook--api--key','!=',''),
            'type'     => 'switch',
            'title'    => __('Display video title', $translationDomain),
            'default' => true,
        ),
        array(
            'id'       => 'facebook--api--display_thumb',
            'type'     => 'switch',
            'title'    => __('Display video thumbnail', $translationDomain),
            'default' => true,
        ),
        array(
            'id'       => 'section-display-facebook-end-2',
            'indent' => false,
            'type'     => 'section',
        ),
        array(
            'id'       => 'section-display-facebook-start-3',
            'indent' => true,
            'required' => array('facebook--enable','equals',true),
            'type'     => 'section',
            'title'    => __("Video player settings", $translationDomain),
        ),
        array(
            'id'       => 'facebook--embed--playbutton',
            'type'     => 'media',
            'title'    => __( 'Play button image', $translationDomain ),
            'desc'     => __( '72 x 72 px', $translationDomain ),
            'default' => array('url' => IKANAWEB_EVT_URL . '/assets/images/play-default.png'),
        ),
        array(
            'id'       => 'section-display-facebook-end-3',
            'indent' => false,
            'type'     => 'section',
        ),
    )
));

Redux::setSection($opt_name, array(
    'title'  => __('Device', $translationDomain),
    'id'     => 'section-devices',
    'desc'   => __("Toggle plugin activation by device", $translationDomain),
    'icon'   => 'el el-screen',
    'fields' => array(
        array(
            'id'       => 'device--desktop--enable',
            'type'     => 'switch',
            'title'    => __('Enable on desktop', $translationDomain),
            'default' => true,
        ),
        array(
            'id'       => 'device--tablet--enable',
            'type'     => 'switch',
            'title'    => __('Enable on tablet', $translationDomain),
            'default' => true,
        ),
        array(
            'id'       => 'device--mobile--enable',
            'type'     => 'switch',
            'title'    => __('Enable on smartphone', $translationDomain),
            'default' => false,
        ),
        array(
            'id'       => 'device--amp--enable',
            'type'     => 'switch',
            'required' => array('device--mobile--enable','equals', true),
            'title'    => __('Enable on AMP pages', $translationDomain),
            'default' => false,
        ),
    )
));
/*
 * <--- END SECTIONS
 */
