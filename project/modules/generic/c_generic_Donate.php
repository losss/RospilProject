<?php

require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/posts/c_posts_API.php";

class c_generic_Donate extends c_class {

    private $db;

    // caller is Root class
    function __construct ($caller) {
        parent::__construct();
        $this->caller = $caller;
        $this->db = $caller->myDB;
        if (isset($caller->user)) $this->user = $caller->user;
    }
	
    public function getContent($user=null) {
    	$this->user = $user; 
    	return $this->execute();
    }

    public function execute() {
        $index = $this->caller->tplroot.'/generic/default.tpl.php';
        return $this->render($index,array(
                'reportlink'    => $this->getReportLink(),
                'menu'          => $this->getMenu(),
                'search'        => $this->getSearch(),
                'content'       => $this->getCenter(),
                'bottom'        => $this->getBottom() // called from parent (no changes to bottom, it's pretty static)
        ));
    }

    public function getCenter() {
        return $this->render($this->getTpl('donate', 'generic', true));
    }
	
}
