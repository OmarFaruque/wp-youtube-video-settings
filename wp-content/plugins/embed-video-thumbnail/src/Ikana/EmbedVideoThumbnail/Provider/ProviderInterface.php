<?php

namespace Ikana\EmbedVideoThumbnail\Provider;

/**
 * Interface ProviderInterface
 * @package Ikana\EmbedVideoThumbnail\Provider
 */
interface ProviderInterface
{
    /**
     * ProviderInterface constructor.
     * @param array $options
     */
    public function __construct(array $options = array());

    /**
     * @return mixed
     */
    public function getName();

    /**
     * @param $content
     * @return array
     */
    public function getContentData($content);

    /**
     * @param $id
     * @return mixed
     */
    public function apiCall($id);
}
