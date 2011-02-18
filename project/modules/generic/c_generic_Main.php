<?php

require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/posts/c_posts_API.php";
require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/generic/c_generic_Pagination.php";

class c_generic_Main extends c_class {

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

        switch (strtolower($this->caller->myRootClassAlias)) {
            case '_____':
                $content = $this->getCommonContent();
                break;
            case 'expertise':
                if (isset($this->user['type']) && (
                        ($this->user['type'] == Setup::$USER_TYPE_ADMIN) ||
                        ($this->user['type'] == Setup::$USER_TYPE_EXPERT))
                ) {
                    $content = $this->getCommonContent(true); // experts
                } else {
                    header("location:/404");
                }
                break;
            default:
                header("location:/404");
                break;
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

    public function getCommonContent($experts=false) {

        $currentPage = Pagination::getCurrentPage();
        $totalPages = c_posts_API::getTotalLeadsPages($this->db,$experts,0);

        $qa = ($experts?
                $this->render($this->getTpl('experts_right_column', 'generic', true)):
                $this->render($this->getTpl('quick_about', 'generic', true))
                );

        return $this->render($this->getTpl('content', 'generic', true),
                array(
                    'counter'=>$this->render($this->getTpl('counter', 'generic', true),
                            array('sum' => c_posts_API::getMainCounter($this->db,0,$experts),
                                  'experts' => $experts)),
                    'stream'=>$this->render($this->getTpl(($experts?'experts_':'').'stream', 'generic', true),
                            array(
                                'list' => c_posts_API::getLeadsList($this->db,$experts,0,$currentPage),
                                'context' => 'main',
                                'pagination' => Pagination::get($this->caller,$totalPages)
                                )
                            ), 
                    'quick_about' => $qa,
                    )
                );
    }
	
}
