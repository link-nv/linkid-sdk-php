<?php

/*
 * LinkID Attribute
 *
 * @author Wim Vandenhaute
 */

class LinkIDAttribute {

    public $id;
    public $name;
    public $value;

    /**
     * Constructor
     */
    public function __construct($id, $name, $value) {

        $this->id = $id;
        $this->name = $name;
        $this->value = $value;
    }

}

?>