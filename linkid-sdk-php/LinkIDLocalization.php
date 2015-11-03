<?php

require_once('LinkIDLocalizationKeyType.php');

/**
 * Created by PhpStorm.
 * User: wvdhaute
 * Date: 03/11/15
 * Time: 08:41
 */
class LinkIDLocalization
{


    /**
     * @var string key of the localization
     */
    public $key;
    /**
     * @var LinkIDLocalizationKeyType key type
     */
    public $keyType;
    /**
     * @var array key is language code, value is localization
     */
    public $values;

    /**
     * LinkIDLocalization constructor.
     * @param string $key
     * @param LinkIDLocalizationKeyType $keyType
     * @param array $values
     */
    public function __construct($key, $keyType, array $values)
    {
        $this->key = $key;
        $this->keyType = $keyType;
        $this->values = $values;
    }


}