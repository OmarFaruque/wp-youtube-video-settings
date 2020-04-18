<?php

namespace Ikana\EmbedVideoThumbnail;

use Ikana\EmbedVideoThumbnail\Provider\Dailymotion;
use Ikana\EmbedVideoThumbnail\Provider\Facebook;
use Ikana\EmbedVideoThumbnail\Provider\ProviderInterface;
use Ikana\EmbedVideoThumbnail\Provider\Twitch;
use Ikana\EmbedVideoThumbnail\Provider\Vimeo;
use Ikana\EmbedVideoThumbnail\Provider\Youtube;

/**
 * Class EmbedVideoThumbnail
 * @package Ikana\EmbedVideoThumbnail
 */
class EmbedVideoThumbnail
{

    /**
     * @var  string
     */
    protected $pluginBasename;
    /**
     * @var string
     */
    protected $pluginURL;

    /**
     * @var object
     */
    protected $wpdb;

    /**
     * @var ProviderInterface[]
     */
    protected $providers;

    /**
     * @var MobileDetect
     */
    protected $mobileDetect;

    /**
     * @var array
     */
    public $options;

    /**
     * EmbedVideoThumbnail constructor.
     * @param $wpdb
     */
    public function __construct($wpdb)
    {
        $this->pluginBasename = IKANAWEB_EVT_BASENAME;
        $this->pluginURL = IKANAWEB_EVT_URL;
        $this->wpdb = $wpdb;
        $this->options = $this->getOptions();
        $this->mobileDetect = new MobileDetect();
    }

    /**
     *
     */
    public function boot()
    {
        add_filter("plugin_action_links_$this->pluginBasename", [$this, 'addSettingsLink']);
        if (!$this->isBootable()) {
            return;
        }
        $this->registerProviders();
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        add_filter('the_content', [$this, 'replaceContent'], 12);
        add_filter('ikevt_video_to_thumbnail', [$this, 'replaceContent'], 12);
        add_shortcode('ikevt_video_to_thumbnail', [$this, 'triggerShortCode']);
    }

    /**
     * @return bool
     */
    public function isBootable()
    {
        global $post;

        return
            $this->isEnabled()
            && $this->isDeviceEnabled()
            && $this->isPostEnabled($post)
        ;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return !empty($this->options['global']['enable']);
    }

    /**
     * @return bool
     */
    public function isDeviceEnabled()
    {
        return (
            (
                !empty($this->options['device']['desktop']['enable'])
                && !$this->mobileDetect->isMobile()
                && !$this->mobileDetect->isTablet()
            ) ||
            (
                !empty($this->options['device']['tablet']['enable'])
                && $this->mobileDetect->isMobile()
                && $this->mobileDetect->isTablet()
            ) ||
            (
                !empty($this->options['device']['mobile']['enable'])
                && $this->mobileDetect->isMobile()
                && !$this->mobileDetect->isTablet()
            ) ||
            (
                !empty($this->options['device']['amp']['enable'])
                && function_exists('is_amp')
                && is_amp()
            )
        );
    }

    /**
     * @param array $links
     * @return array
     */
    public function addSettingsLink($links)
    {
        $links[] = '<a href="tools.php?page=ikanaweb_evt_options">' . __('Settings') . '</a>';
        return $links;
    }

    /**
     *
     */
    public function enqueueScripts()
    {
        wp_register_script('ikn-evt-js-main', $this->pluginURL . '/assets/js/main.js', ['jquery']);
        wp_enqueue_script('ikn-evt-js-main');
        wp_register_style('ikn-evt-css-main', $this->pluginURL . '/assets/css/main.css', __DIR__);
        wp_enqueue_style('ikn-evt-css-main');
        $script='';
        foreach ($this->getProviders() as $provider) {
            $buttonImage = $this->options[$provider->getName()]['embed']['playbutton']['url'];
            if (empty($buttonImage)) {
                $buttonImage = IKANAWEB_EVT_URL . '/assets/images/play-default.png';
            }
            $script.= '.' . $this->options['template']['container_class'] . '[data-source="' .$provider->getName()."\"] 
            .ikn-evt-play-button {background:url('" . $buttonImage . "') no-repeat;}";
        }
        wp_add_inline_style('ikn-evt-css-main', $script);
    }


    /**
     * @return $this
     */
    public function registerProviders()
    {
        $providers = $this->getDefaultProviders();
        $extensions = apply_filters('ikevt_extension_providers', []);

        $providers = array_merge($providers, $extensions);

        foreach ($providers as $provider) {
            if (!isset($this->options[$provider->getName()])
                || !empty($this->options[$provider->getName()]['enable'])) {
                $this->addProvider($provider);
            }
        }
        return $this;
    }


    /**
     * @param string $content
     * @return mixed|string
     */
    public function replaceContent($content)
    {
        $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');

        foreach ($this->getProviders() as $provider) {
            $data = $provider->getContentData($content);
            foreach ($data as $toReplace => $info) {
                $fakeHrefReplacer = md5($toReplace);
                $content = str_replace(
                    [
                        'href="' . $toReplace,
                        $toReplace,
                        'href="' . $fakeHrefReplacer
                    ],
                    [
                        'href="' . $fakeHrefReplacer,
                        $this->buildPreviewContainer($info),
                        'href="' . $toReplace
                    ],
                    $content
                );
            }
        }
        return $content;
    }

    /**
     *
     */
    public function uninstall()
    {
        if (is_dir(IKANAWEB_EVT_IMAGE_PATH)) {
            Utils::recursiveRemoveDirectory(IKANAWEB_EVT_IMAGE_PATH);
        }
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        global $ikanaweb_evt;

        $ikanaweb_evt['template']['container_class'] = 'ikn-evt-frame';

        $data = [];

        foreach ($ikanaweb_evt as $key => $value) {
            $path = explode('--', $key);

            $arr = [];
            $tmp = &$arr;
            foreach ($path as $segment) {
                $tmp[$segment] = [];
                $tmp = &$tmp[$segment];
            }
            $tmp = $value;
            $data = array_merge_recursive($data, $arr);
        }
        return $data;
    }

    /**
     * @param array $data
     * @return string
     */
    public function buildPreviewContainer($data)
    {
        $dataAttributes = [
            'id' => $data['id'],
            'source' => $data['source'],
            'embed-url' => $data['embed']
        ];
        $container = '<div class="'.$this->options['template']['container_class'].'"';
        foreach ($dataAttributes as $name => $value) {
            $container.=' data-'.$name.'="'.$value.'"';
        }
        $container.='>';
        $container.='<div class="ikn-evt-container">';
        $container.='<div class="ikn-evt-play-button"></div>';
        if (!empty($data['thumb'])) {
            $container.= '<img class="ikn-evt-thumbnail" src="'.$data['thumb'].'"';
            if (!empty($data['title'])) {
                $container.=' alt="'.htmlspecialchars($data['title'], ENT_QUOTES).'"';
            }
            $container.='/>';
        }
        if (!empty($data['title'])) {
            $container.= '<div class="ikn-evt-heading-container">';
            $container.= '<p class="ikn-evt-heading-title">'.$data['title'].'</p>';
            $container.= '</div>';
        }
        $container.='</div>';
        $container.='</div>';
        return $container;
    }

    /**
     * @return ProviderInterface[]
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /**
     * @param ProviderInterface $provider
     */
    public function addProvider(ProviderInterface $provider)
    {
        $this->providers[] = $provider;
    }

    /**
     * @param array $providers
     * @return $this
     */
    public function setProviders(array $providers)
    {
        $this->providers = [];
        foreach ($providers as $provider) {
            if ($provider instanceof ProviderInterface) {
                $this->addProvider($provider);
            }
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getDefaultProviders()
    {
        return [
            new Youtube($this->getOptions()),
            new Vimeo($this->getOptions()),
            new Dailymotion($this->getOptions()),
            new Facebook($this->getOptions()),
//            new Twitch($this->getOptions()),
        ];
    }


    /**
     * @param $post
     * @return bool
     */
    public function isPostEnabled($post)
    {
        if (empty($post)) {
            return true;
        }

        $postTypeEnabled = empty($this->options['global']['post_type'])
            || !empty($this->options['global']['post_type'][$post->post_type])
        ;
        $disabledPosts = [];
        if (!empty($this->options['global']['exclude_posts'])) {
            $disabledPosts = explode("\n", $this->options['global']['exclude_posts']);
        }

        $postIDEnabled = !in_array($post->ID, $disabledPosts, true);

        return $postTypeEnabled && $postIDEnabled;
    }

    /**
     * @param array $attrs
     * @param null $content
     *
     * @return mixed|string
     */
    public function triggerShortCode($attrs, $content = null)
    {
        $a = shortcode_atts( [
            'url' => null
        ], $attrs );

        if (null !== $a['url']) {
            return $this->replaceContent($a['url']);
        }

        return $this->replaceContent(do_shortcode($content));
    }
}
