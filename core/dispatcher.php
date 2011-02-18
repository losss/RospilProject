<?php

class Dispatcher {
	
	// static dispatcher of core classes which are not getting loaded by default
	
    private static $instance = null;

    //
    // instances of optional classes to load
    //
    
    private function __construct() { }

    function __destruct() {}

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Dispatcher();
        }
        return self::$instance;
    }
 }