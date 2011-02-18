<?

require_once $_SERVER['DOCUMENT_ROOT'].Settings::$CORE_LIB_PATH."/recaptchalib.php";
require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/posts/c_posts_API.php";

class c_posts_Post extends c_class {

    function __construct ($caller) {
	$this->tplroot = $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_TPL_PATH;
        $this->caller = $caller;
        $this->user = $caller->user;
        $this->db = $caller->myDB;
    }

    public function getContent() {
        return $this->execute();
    }

    public function execute() {

        $content = '';
        $parts = explode('/',URL::$SUBSPACE);
        if (!isset($parts[1]) || !is_numeric($parts[1]) || !$parts[1] ) {
            $content = 'not found';
        }

        $index = $this->caller->tplroot.'/generic/default.tpl.php';
        return $this->render($index,array(
                'reportlink'    => $this->getReportLink(),
                'menu'          => $this->getMenu(),
                'search'        => $this->getSearch(),
                'content'       => ($content?$content:$this->getCenter($parts[1])),
                'bottom'        => $this->getBottom() // called from parent (no changes to bottom, it's pretty static)
        ));

    }

    public function getCenter($id) {
        $lead = c_posts_API::getLead($this->db, $id);

        if ($lead['published_ts']) {
            $tpl = 'case';
        } else {
            $tpl = 'case_experts';
        }

        // unpublished leads can be seen only by admins and experts
        //
        if ((!$lead['published_ts']) &&
            (
                (!isset($this->user['type'])) ||
                ($this->user['type'] != Setup::$USER_TYPE_ADMIN) &&
                ($this->user['type'] != Setup::$USER_TYPE_EXPERT )
            )
        ) {
            header("location:/404");
            exit(0);
        }

        /*
         * (admin=true OR expert=true) => show 
         * everybody else => show only if published
         *
         */



        $adds = c_posts_API::getAdditions($this->db, $id);
        return $this->render($this->getTpl($tpl, 'posts', true),
               array('recaptcha' => recaptcha_get_html(Setup::$RECAPTCHA_PUBLIC),
                     'p' => $lead,
                     'orginfo' => c_posts_API::getOrgById($this->db,$lead['orgid']),
                     'adds' => $adds
                   )
               );
    }

}