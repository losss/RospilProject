<?php

require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/posts/c_posts_API.php";
require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/generic/c_generic_Pagination.php";

class c_generic_Orgs extends c_class {

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

        $parts = explode('/',URL::$SUBSPACE);
        if (isset($parts[1]) && (intval($parts[1]) > 0) ) {
            $content = $this->getOrgPage($parts[1]);
        } else {
            $content = $this->getCenter();
        }

        $index = $this->caller->tplroot.'/generic/default.tpl.php';
        return $this->render($index,array(
                'reportlink'    => $this->getReportLink(),
                'menu'          => $this->getMenu(),
                'search'        => $this->getSearch(),
                'content'       => $content,
                'bottom'        => $this->getBottom() // called from parent (no changes to bottom, it's pretty static)
        ));
    }

    public function getCenter() {
        return $this->render(
                $this->getTpl('orgs', 'generic', true),
                array('list'=>  c_posts_API::getOrgs($this->db,'total_cases DESC',' WHERE total_cases > 0 '))
                );
    }
    public function getOrgPage($orgid) {

        $currentPage = Pagination::getCurrentPage();
        $totalPages = c_posts_API::getTotalLeadsPages($this->db,false,$orgid);

        return $this->render($this->getTpl('orgpage', 'generic', true),
                array(
                    'orginfo'=>$this->render($this->getTpl('orginfo', 'generic', true),
                            array('orginfo' => c_posts_API::getOrgById($this->db,$orgid))),
                    'stream'=>$this->render($this->getTpl('stream', 'generic', true),
                            array(
                                'list' => c_posts_API::getLeadsList($this->db,false,$orgid,$currentPage),
                                'context' => 'org',
                                'pagination' => Pagination::get($this->caller,$totalPages)
                                )
                            ),
                    'orgname' => c_posts_API::getOrgNameById($this->db,$orgid)
                    )
                );

    }
	
}
