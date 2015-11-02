<?php

require_once('LinkIDLocalizedImages.php');

/**
 * Created by PhpStorm.
 * User: wvdhaute
 * Date: 02/11/15
 * Time: 15:51
 */
class LinkIDTheme
{

    /**
     * @var string the theme's name
     */
    public $name;
    /**
     * @var bool is it the application's default theme?
     */
    public $defaultTheme;
    /**
     * @var LinkIDLocalizedImages logo that is shown in the manage linkID section
     */
    public $logo;
    /**
     * @var LinkIDLocalizedImages logo that is shown during the authentication process
     */
    public $authLogo;
    /**
     * @var LinkIDLocalizedImages background to be shown during the authentication process on phones
     */
    public $background;
    /**
     * @var LinkIDLocalizedImages background to be shown during the authentication process on tablets
     */
    public $tabletBackground;
    /**
     * @var LinkIDLocalizedImages background to be shown during the authentication process on other devices
     */
    public $alternativeBackground;
    /**
     * @var string background color to be shown during the authentication process (hex)
     */
    public $backgroundColor;
    /**
     * @var string text color to be shown during the authentication process (hex)
     */
    public $textColor;

    /**
     * LinkIDTheme constructor.
     * @param string $name
     * @param bool $defaultTheme
     * @param LinkIDLocalizedImages $logo
     * @param LinkIDLocalizedImages $authLogo
     * @param LinkIDLocalizedImages $background
     * @param LinkIDLocalizedImages $tabletBackground
     * @param LinkIDLocalizedImages $alternativeBackground
     * @param string $backgroundColor
     * @param string $textColor
     */
    public function __construct($name, $defaultTheme, LinkIDLocalizedImages $logo = null, LinkIDLocalizedImages $authLogo = null,
                                LinkIDLocalizedImages $background = null, LinkIDLocalizedImages $tabletBackground = null,
                                LinkIDLocalizedImages $alternativeBackground = null, $backgroundColor = null, $textColor = null)
    {
        $this->name = $name;
        $this->defaultTheme = $defaultTheme;
        $this->logo = $logo;
        $this->authLogo = $authLogo;
        $this->background = $background;
        $this->tabletBackground = $tabletBackground;
        $this->alternativeBackground = $alternativeBackground;
        $this->backgroundColor = $backgroundColor;
        $this->textColor = $textColor;
    }


}