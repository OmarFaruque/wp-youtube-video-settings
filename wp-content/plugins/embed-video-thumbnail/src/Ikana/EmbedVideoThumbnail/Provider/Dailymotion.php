<?php

namespace Ikana\EmbedVideoThumbnail\Provider;

/**
 * Class Dailymotion
 * @package Ikana\EmbedVideoThumbnail\Provider
 */
class Dailymotion extends AbstractProvider implements ProviderInterface
{
    /**
     * @var string
     */
    protected $regex = '(?:(?:https:|http:)?(?:\/\/)?(?:www\.)?)(?:dailymotion.com|dai.ly)(?:\/embed)?(?:\/video|hub)?\/([a-z0-9]+)[^\#<>]*(?:\#video=([a-z0-9]+))?';

    /**
     * @var string
     */
    private $api = 'https://api.dailymotion.com/video/%ID%?fields=title,description,thumbnail_url';

    /**
     * @return string
     */
    public function getName()
    {
        return 'dailymotion';
    }

    /**
     * @param $id
     * @return array|mixed|string
     */
    public function apiCall($id)
    {
        $url = str_replace('%ID%', $id, $this->api);
        $data = file_get_contents($url);
        if ($data !== false) {
            $data = json_decode($data, true);
        }
        return $data;
    }

    /**
     * @param $id
     * @return string
     */
    protected function getEmbedURL($id)
    {
        $url = '//www.dailymotion.com/embed/video/'.$id.'?';
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
            $thumb =  $data['thumbnail_url'];
            $title = $data['title'];
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

    /**
     * @return string
     */
    public function getRegex()
    {
        return '#(?:<iframe.*src=\"|[^"\[]|\[embed\])?'.$this->regex.'(?:\".*><\/iframe>|[^"\[<>]|\[\/embed\])?([^<>]?)#i';
    }
}
