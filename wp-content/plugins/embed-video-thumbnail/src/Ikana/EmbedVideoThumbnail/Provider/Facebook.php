<?php

namespace Ikana\EmbedVideoThumbnail\Provider;

/**
 * Class Facebook
 * @package Ikana\EmbedVideoThumbnail\Provider
 */
class Facebook extends AbstractProvider implements ProviderInterface
{
    /**
     * @var string
     */
    protected $regex = '(?:(?:https:|http:)?(?:\/\/)?(?:www\.)?)facebook\.com\/facebook\/videos\/(?:.*\/)?([0-9]+)\/[?]?.*';

    /**
     * @var string
     */
    private $api = 'https://graph.facebook.com/%s/picture?redirect=false';

    /**
     * @return string
     */
    public function getName()
    {
        return 'facebook';
    }

    /**
     * @param $id
     * @return array|mixed|string
     */
    public function apiCall($id)
    {
        return json_decode(file_get_contents(sprintf($this->api, $id)));
    }

    /**
     * @param $id
     * @return string
     */
    protected function getEmbedURL($id)
    {
        $url = '//www.facebook.com/video/embed?video_id='.$id.'&';
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
            $thumb = $data->data->url;

            $highResolutionThumb = str_replace(array('_t', '_n'), '_b', $thumb);

            if (file_get_contents($highResolutionThumb) !== false) {
                $thumb = $highResolutionThumb;
            }
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
