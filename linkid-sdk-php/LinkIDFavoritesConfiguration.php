<?php

/**
 * Created by PhpStorm.
 * User: wvdhaute
 * Date: 03/12/15
 * Time: 11:15
 */
class LinkIDFavoritesConfiguration
{

    public $title;
    public $info;
    public $logoEncoded;
    public $backgroundColor;
    public $textColor;

    /**
     * LinkIDFavoritesConfiguration constructor.
     * @param $title
     * @param $info
     * @param $logoEncoded
     * @param $backgroundColor
     * @param $textColor
     */
    public function __construct($title, $info, $logoEncoded, $backgroundColor, $textColor)
    {
        $this->title = $title;
        $this->info = $info;
        $this->logoEncoded = $logoEncoded;
        $this->backgroundColor = $backgroundColor;
        $this->textColor = $textColor;
    }


}

function parseLinkIDFavoritesConfiguration($xmlFavoritesConfiguration)
{
    return new LinkIDFavoritesConfiguration(
        isset($xmlFavoritesConfiguration->title) ? $xmlFavoritesConfiguration->title : null,
        isset($xmlFavoritesConfiguration->info) ? $xmlFavoritesConfiguration->info : null,
        isset($xmlFavoritesConfiguration->logoEncoded) ? $xmlFavoritesConfiguration->logoEncoded : null,
        isset($xmlFavoritesConfiguration->backgroundColor) ? $xmlFavoritesConfiguration->backgroundColor : null,
        isset($xmlFavoritesConfiguration->textColor) ? $xmlFavoritesConfiguration->textColor : null
    );
}