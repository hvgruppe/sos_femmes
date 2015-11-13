<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

include_once('tbs_class.php'); // Load the TinyButStrong template engine
include_once('tbs_plugin_opentbs.php');


class Tbswrapper {

    /**
     * TinyButStrong instance
     *
     * @var object
     */
    public static $TBS = null;

    /**
     * default constructor
     *
     */
    public function __construct() {

        if (self::$TBS == null) {
            $this->TBS = new clsTinyButStrong();
          
        }
    }

}

?>