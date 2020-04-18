<?php

namespace Ikana\EmbedVideoThumbnail\Provider;

/**
 * Class AbstractProvider
 * @package Ikana\EmbedVideoThumbnail\Provider
 */
abstract class AbstractProvider
{
    /**
     * @var string
     */
    protected $regex;

    /**
     * @var array
     */
    protected $options = array();

    /**
     * AbstractProvider constructor.
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->options = $options;
    }

    /**
     * @return bool
     */
    public function isTitleEnabled()
    {
        return !empty($this->options[$this->getName()]['api']['display_title']);
    }

    /**
     * @return bool
     */
    public function isThumbEnabled()
    {
        return !empty($this->options[$this->getName()]['api']['display_thumb']);
    }

    /**
     * @return bool
     */
    public function isAutoplayEnabled()
    {
        return !empty($this->options[$this->getName()]['embed']['autoplay']);
    }

    /**
     * @return bool
     */
    public function isLoopEnabled()
    {
        return !empty($this->options[$this->getName()]['embed']['loop']);
    }

    /**
     * @return bool
     */
    public function isRelEnabled()
    {
        return !empty($this->options[$this->getName()]['embed']['rel']);
    }

    /**
     * @return bool
     */
    public function isModestEnabled()
    {
        return !empty($this->options[$this->getName()]['embed']['modestbranding']);
    }

    /**
     * @return bool
     */
    public function isThumbCopyEnabled()
    {
        return !empty($this->options[$this->getName()]['api']['thumb_copy']);
    }

    /**
     * @return bool
     */
    public function areControlsDisabled()
    {
        return empty($this->options[$this->getName()]['embed']['controls']);
    }

    /**
     * @return bool
     */
    public function hasNoCookie()
    {
        return !empty($this->options[$this->getName()]['embed']['no_cookie']);
    }

    /**
     * @param $id
     * @param $thumb
     * @return mixed
     */
    public function copyThumb($id, $thumb)
    {
        $provider_dir = IKANAWEB_EVT_IMAGE_PATH.DIRECTORY_SEPARATOR . $this->getName();
        if (!is_dir($provider_dir)) {
            wp_mkdir_p($provider_dir);
        }
        $fileName = basename($thumb);
        $destinationFile = $provider_dir . '/' . $id . '-' . $fileName;
        $copied = true;
        if (!is_file($destinationFile)) {
            $copied = copy($thumb, $destinationFile);
        }
        if (!$copied) {
            return $thumb;
        }
        $uploadUrlPath = get_option('upload_url_path');
        if (!empty($uploadUrlPath)) {
            $destination = $uploadUrlPath;
        } else {
            $destination = '/wp-content/uploads';
        }
        return str_replace(dirname(IKANAWEB_EVT_IMAGE_PATH), $destination, $destinationFile);
    }

    /**
     * @return string
     */
    public function getRegex()
    {
        return '#(?:<iframe[^>]*src=\"|[^"\[]|\[embed\])?' . $this->regex . '(?:\".*><\/iframe>|[^"\[]|\[\/embed\])?#i';
    }

    /**
     * @param string $content
     * @return array
     */
    public function getContentData($content)
    {
        $data = [];

        preg_match_all(
            $this->getRegex(),
            $content,
            $matches,
            PREG_SET_ORDER
        );

        if (!empty($matches[0][1])) {
            foreach ($matches as $m) {
                $queryString = !empty($m[2]) ? trim($m[2]) : '';
                $data[trim($m[0])] = $this->buildData($m[1], $queryString);
            }
        }

        $keys = array_map('strlen', array_keys($data));
        array_multisort($keys, SORT_DESC, $data);

        return $data;
    }

    /**
     * @param int $id
     * @return array
     */
    abstract protected function buildData($id);
}
