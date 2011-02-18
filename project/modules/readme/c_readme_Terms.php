<?php

class c_readme_Terms extends c_class {

	function __construct($caller) {
		$this->context = $caller;
		$this->userId = $caller->userId;
		$this->userName = $caller->userName;
		$this->userRating = $caller->userRating;
		$this->tplroot = $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_TPL_PATH;
	}
    
    public function getContent($userId) {
    	$this->userId = $userId;
    	return $this->execute();
    }

    public function execute() {
    	$this->tab = '';
        $index = $this->tplroot.'/readme/'.'terms.tpl.php';
        $result = $this->render($index, array());
		return $result;
    }
}