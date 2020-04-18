<?php

namespace Ikana\EmbedVideoThumbnail\Provider;

/**
 * Class Twitch
 * @package Ikana\EmbedVideoThumbnail\Provider
 */
class Twitch extends AbstractProvider implements ProviderInterface
{

    /**
     * @var string
     */
    protected $regex = '(?:(?:https:|http:)?(?:\/\/)?(?:www\.)?)twitch\.tv/videos/([0-9]+)[?]?.*';
    /**
     * @var string
     */
    private $api = 'https://api.twitch.tv/kraken/videos/c';

    /**
     * @return string
     */
    public function getName()
    {
        return 'twitch';
    }

    /**
     * @param $id
     * @return array|mixed|string
     */
    public function apiCall($id)
    {
        return json_decode(file_get_contents($this->api.$id));
    }

    /**
     * @param $id
     * @return string
     */
    protected function getEmbedURL($id)
    {
        $url = '//player.vimeo.com/video/'.$id.'?';
        if ($this->isAutoplayEnabled()) {
            $url.='autoplay=1&';
        }
        return $url;
    }

    /**
     * @param $id
     * @return array
     */
    protected function buildData($id)
    {
        $data = $this->apiCall($id);

        if (!empty($data)) {
            $thumb = $data[0]['thumbnail_large'];
            $title = $data[0]['title'];
        }

        if (isset($thumb)) {
            if ($this->isThumbCopyEnabled()) {
                $thumb = $this->copyThumb($id, $thumb);
            }
            $thumb = str_replace(['http:', 'https:'], '', $thumb);
        }

        return array(
            'id' => $id,
            'embed' => $this->getEmbedURL($id),
            'thumb' => $this->isThumbEnabled() && isset($thumb) ? $thumb : '',
            'title' => $this->isTitleEnabled() && isset($title) ? $title : '',
            'source' => $this->getName()
        );
    }
}
