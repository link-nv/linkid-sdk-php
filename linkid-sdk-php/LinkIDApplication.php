<?php

class LinkIDApplication
{
    public $name;
    public $friendlyName;

    /**
     * LinkIDApplication constructor.
     * @param $name
     * @param $friendlyName
     */
    public function __construct($name, $friendlyName)
    {
        $this->name = $name;
        $this->friendlyName = $friendlyName;
    }


}