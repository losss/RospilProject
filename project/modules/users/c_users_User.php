<?php

require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/users/c_users_API.php";

class c_users_User {
	
    public $userid;
    public $name;
    public $email;
    public $usercode;
    public $cookie;
    public $cookie_ts;
    public $password;
    public $type;
    public $status;
    public $registered_ts;
    public $last_ip_address;

    function __construct($caller,$email,$password,$name) {
    	$this->caller = $caller;
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
        $this->API = new c_users_API($caller);
        $this->tplroot = $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_TPL_PATH;
    }
    
}
?>
