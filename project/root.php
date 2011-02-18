<?php

require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/access/c_access_API.php";

// basic authentication and class dispatching 

class Root extends Core {
    public $rootClass;
    public $user;
    public $timzone;

    function __construct($core) {

        date_default_timezone_set(Setup::$DEFAULT_TIMEZONE_STR);

        $keys = array_keys(CMap::$MAP);
        if (!in_array($core->myRootClassAlias,$keys)) {
                $core->myRootClassAlias = '404';
        }
        $this->user = c_access_API::getUser($core->myDB);

        $this->utilizeUserRole();
        
        $core->user = $this->user;
        $this->rootClass = $this->addClass($core->myRootClassAlias,$core,$this->user);
    }
    
    function getContent() {
        return $this->rootClass->getContent($this->user);
    }

    function utilizeUserRole() {

        // any rules applicable to different roles

        // 1) experts & admins are able to see additional section
        //    where they can discuss privately all candidates for "kick-backs"
        //
        if (isset($this->user['type']) && (
            ($this->user['type'] == Setup::$USER_TYPE_ADMIN) ||
            ($this->user['type'] == Setup::$USER_TYPE_EXPERT))
                ) {
            Setup::$MENU['expertise'] = 'Экспертиза';
        }

        if (count(Settings::$OPEN_INLY_FOR)>0 && isset($this->user['type'])) {

        }

    }

}

