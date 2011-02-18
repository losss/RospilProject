<?php


class c_access_Admin extends c_class {

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
        if (!is_array($this->user) || ($this->user['type']!= Setup::$USER_TYPE_ADMIN)) {
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
                               array('list' => c_posts_API::getLeadsForReview($this->db,$selected),'selected'=>$selected)
                               );
                    $aciontitle = '<div class="bigtitle">Кандидаты на публикацию</div>';
                break;

                case 'experts':
                    require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/access/c_access_API.php";
                    $content = $this->render($this->getTpl('experts', 'users', true),
                               array(
                                   'list' => c_access_API::getExperts($this->db),
                                   'candidates' => c_access_API::getExperts($this->db,false)
                                   )
                               );
                    $aciontitle = '<div class="bigtitle">Управление экспертами</div>';
                break;

                case 'publish':
                    require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/posts/c_posts_API.php";
                    if (!isset ($parts[2])) {
                        $content = 'not found';
                        $aciontitle = '<div class="bigtitle">?</div>';
                    } else {
                        $content = $this->render($this->getTpl('publish', 'posts', true),
                                   array('lead' => c_posts_API::getLead($this->db,$parts[2]),
                                         'orgs' => c_posts_API::getOrgs($this->db))
                                   );
                        $aciontitle = '<div class="bigtitle">Публикуем</div>';
                    }
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
