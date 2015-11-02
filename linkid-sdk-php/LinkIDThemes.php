<?php

require_once('LinkIDTheme.php');

/**
 * Created by PhpStorm.
 * User: wvdhaute
 * Date: 02/11/15
 * Time: 15:55
 */
class LinkIDThemes
{

    /**
     * @var array the list of themes allowed to be used by the application
     */
    public $themes;

    /**
     * LinkIDThemes constructor.
     * @param array $themes
     */
    public function __construct(array $themes)
    {
        $this->themes = $themes;
    }


}