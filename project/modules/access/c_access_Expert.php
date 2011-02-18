<?php


class c_access_Expert extends c_class {

    public  $user;
	
    function __construct ($caller) {
        $this->caller = $caller;
        $this->db = $caller->myDB;
        $this->user = $caller->user;
    }
	
    public function getContent($user=null) {
    	$this->user = $user;
    	return $this->execute();
    }

    public function execute() {
        if (!is_array($this->user) || ($this->user['type']!= Setup::$USER_TYPE_EXPERT)) {
            header('location:/');
        }
        $parts = explode('/',URL::$SUBSPACE);
        if ($parts[0] != $this->caller->myRootClassAlias) {
            $content = 'not found';
        } else {

            $selected = isset(URL::$GET['tab']) ? URL::$GET['tab'] : 'selected';

            switch ($parts[1]) {
                case 'review':
                    require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/posts/c_posts_API.php";
                    $content = $this->render($this->getTpl('review', 'posts', true),
                               array('list' => c_posts_API::getLeadsForReview($this->db,$selected,$this->user['userid']),
                                   'selected' => $selected)
                               );
                    $aciontitle = '<div class="bigtitle">Кандидаты на публикацию</div>';
                break;

                default:
                    $content = 'not found';
                    $aciontitle = '?';
                break;
            }
        }

        $index = $this->caller->tplroot.'/generic/default.tpl.php';
        return $this->render($index,array(
                'reportlink'    => '',
                'menu'          => $aciontitle,
                'search'        => '',
                'content'       => $content,
                'bottom'        => $this->getBottom() // called from parent (no changes to bottom, it's pretty static)
        ));
    }
	
}
