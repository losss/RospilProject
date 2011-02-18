<?php

require_once $_SERVER['DOCUMENT_ROOT'].Settings::$CORE_LIB_PATH."/recaptchalib.php";

class c_posts_PostLead extends c_class {

    function __construct ($caller) {
	$this->tplroot = $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_TPL_PATH;
        $this->caller = $caller;
    }
	
    public function getContent() {
        return $this->execute();
    }
		
    public function execute() {

        $index = $this->caller->tplroot.'/generic/default.tpl.php';
        return $this->render($index,array(
                'reportlink'    => '',
                'menu'          => '<div class="bigtitle">Сообщаем о махинациях</div>',
                'search'        => '',
                'content'       => $this->getCenter(),
                'bottom'        => $this->getBottom() // called from parent (no changes to bottom, it's pretty static)
        ));

    }

    public function getCenter() {
        return $this->render($this->getTpl('report_corruption', 'posts', true),
               array('recaptcha' => recaptcha_get_html(Setup::$RECAPTCHA_PUBLIC))
               );
    }
    
} 