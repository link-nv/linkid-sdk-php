<?php

/**
 * Created by PhpStorm.
 * User: wvdhaute
 * Date: 02/11/15
 * Time: 15:45
 */
class LinkIDLocalizedImage
{

    /**
     * @var string url
     */
    public $url;
    /**
     * @var string language
     */
    public $language;

    /**
     * LinkIDLocalizedImage constructor.
     * @param $url
     * @param $language
     */
    public function __construct($url, $language)
    {
        $this->url = $url;
        $this->language = $language;
    }


}