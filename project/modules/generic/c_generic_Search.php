<?php

require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/posts/c_posts_API.php";

class c_generic_Search extends c_class {

    private $db;
    private $q; 

    // caller is Root class
    function __construct ($caller) {
        parent::__construct();
        $this->caller = $caller;
        $this->db = $caller->myDB;
        if (isset($caller->user)) $this->user = $caller->user;
        if (isset(URL::$GET['q'])) $this->q = $this->cleanUp(URL::$GET['q']);
        else $this->q = '';
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
        
        $s = c_posts_API::search($this->db, $this->q);

        return $this->render($this->getTpl('search_results', 'generic', true),
                array(
                    'found_orgs'    => count($s['orgs']),
                    'found_leads'   => count($s['leads']),
                    'orgs'          => $s['orgs'],
                    'leads'         => $s['leads'],
                    'q'             => $this->q
                    )
                );
    }

    public function cleanUp($q) {
        return preg_replace('/["\'\[\]\n\t\r\.\,\|\(\)@#$%<>?:;\/=]/','',$q);
    }
	
}
