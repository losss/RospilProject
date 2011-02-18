<?php


class c_access_Login extends c_class {

    private $caller;

    function __construct($caller) {
        $this->caller = $caller;
    }

    public function getContent() {
        return $this->execute();
    }

    public function execute() {

        $index = $this->caller->tplroot.'/generic/default.tpl.php';
        return $this->render($index,array(
                'reportlink'    => '',
                'menu'          => '<div class="bigtitle">Авторизация</div>',
                'search'        => '',
                'content'       => $this->getCenter(),
                'bottom'        => $this->getBottom() // called from parent (no changes to bottom, it's pretty static)
        ));

    }

    public function getCenter() {
        return $this->render($this->getTpl('login', 'access', true));
    }
}