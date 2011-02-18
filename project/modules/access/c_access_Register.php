<?php

require_once $_SERVER['DOCUMENT_ROOT'].Settings::$CORE_LIB_PATH."/recaptchalib.php";

class c_access_Register extends c_class {

    private $caller;

    function __construct($caller) {
        $this->caller = $caller;
        $this->user = $caller->user;
    }

    public function getContent() {
        return $this->execute();
    }

    public function execute() {

        $index = $this->caller->tplroot.'/generic/default.tpl.php';
        return $this->render($index,array(
                'reportlink'    => '',
                'menu'          => $this->getTitle(strtolower($this->caller->myRootClassAlias)),
                'search'        => '',
                'content'       => $this->getCenter(strtolower($this->caller->myRootClassAlias)),
                'bottom'        => $this->getBottom() // called from parent (no changes to bottom, it's pretty static)
        ));

    }

    public function getTitle($regtype) {

        switch ($regtype) {
            case 'regexpert':
                return '<div class="bigtitle">Регистрируемся как Эксперт</div>';
                break;
            case 'register':
                return '<div class="bigtitle">Регистрируемся</div>';
                break;
            default:
                header('location:/404');
                exit(0);
                break;
        }
    }

    public function getCenter($regtype) {

        switch ($regtype) {
            case 'regexpert':
                $tpl = 'register_expert';
                break;
            case 'register':
                $tpl = 'register';
                break;
            default:
                header('location:/404');
                exit(0);
                break;
        }

        return $this->render($this->getTpl($tpl, 'access', true),
               array('recaptcha' => recaptcha_get_html(Setup::$RECAPTCHA_PUBLIC))
               );
    }
}