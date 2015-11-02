<?php

require_once('LinkIDLocalizedImage.php');

/**
 * Created by PhpStorm.
 * User: wvdhaute
 * Date: 02/11/15
 * Time: 15:45
 */
class LinkIDLocalizedImages
{

    /**
     * @var array image map
     */
    public $imageMap;

    /**
     * LinkIDLocalizedImages constructor.
     * @param $imageMap
     */
    public function __construct($imageMap)
    {
        $this->imageMap = $imageMap;
    }


}