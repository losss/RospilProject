<?php

require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/access/c_access_API.php";

class c_access_Logout extends c_class {

    private $caller;

    function __construct($caller) {
        $this->caller = $caller;
    }

    public function getContent() {
        return $this->execute();
    }

    public function execute() {
        c_access_API::logout();
    }

}