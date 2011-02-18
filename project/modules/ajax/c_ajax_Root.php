<?php

// m=posts&f=votesolution&id=

class c_ajax_Root extends API {

    private $function;
    private $response;

    function __construct($caller,$user=null) {

        $this->caller = $caller;
        $this->user = $user;
        $this->db = $caller->myDB;
        $this->function = (isset(URL::$POST['f'])?URL::$POST['f']:URL::$GET['f']);

        if (!isset($this->function) || !$this->function) {
            Logger::log("[AJAX] f is not set ".print_r(URL::$POST));
            exit(0);
        }

        $this->processAuthIndependent();

        if (!isset($this->response)) {
            $openFunctions = array('report','register','login');
            
            if (!is_array($user) || in_array($this->function, $openFunctions)) {
                $this->processUnAuth();
            } else {
                $this->processAuth();
            }
        }
        
    }

    function processAuthIndependent() {
        switch ($this->function) {
            case 'regexpert':
                require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/access/c_access_API.php";
                if (isset($this->user['userid']) && ($this->user['userid'] > 0)) {
                    // update user with expert info
                    $out = c_access_API::expertApplication($this->db,$this->user['userid'],URL::$POST);
                } else {
                    $valid = $this->checkRecaptcha();
                    if ($valid) {
                        // register new user with expert info
                        $out = c_access_API::registerUser($this->db,URL::$POST);
                    } else {
                        $out = array('status' => 'error','message'=>'Символы с картинки введены неправильно. Попробуйте еще раз.');
                    }
                }
                if (!isset($out)) {
                    $out = array('status' => 'error','message'=>'Наш сервер скрывается от администраторов, наверное чувствует вину. Мы его ищем.');
                }
                $this->response = @json_encode($out);
            break;
            case 'getcomments':
                require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/posts/c_posts_API.php";
                $out = c_posts_API::getComments($this->db,URL::$POST['leadid'],false,URL::$POST['page'],URL::$POST['perpage']);
                if (!isset($out)) {
                    $out = array('status' => 'error','message'=>'Наш сервер скрывается от администраторов, наверное чувствует вину. Мы его ищем.');
                }
                $this->response = @json_encode($out);
                break;
            case 'getleads':
                require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/posts/c_posts_API.php";
                $out = c_posts_API::getMainPage($this->db,false,URL::$POST['orgid'],URL::$POST['page'],URL::$POST['ids']);
                $currentPage = Pagination::getCurrentPage();
                $totalPages = c_posts_API::getTotalLeadsPages($this->db,false,URL::$POST['orgid']);
                $pagination = Pagination::get($this->caller,$totalPages);
                $out['pagination'] = $pagination;
                if (!isset($out)) {
                    $out = array('status' => 'error','message'=>'Наш сервер скрывается от администраторов, наверное чувствует вину. Мы его ищем.');
                }
                $this->response = @json_encode($out);
            break;
        }
    }

    function processAuth() {
        switch ($this->function) {
            case 'isent':
                if (!$this->user['userid']) {
                    $out = array('status' => 'error','message'=>'Вам нельзя так делать.');
                } else {
                    require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/posts/c_posts_API.php";
                    $out = c_posts_API::sentPetition($this->db,URL::$POST['leadid'],$this->user['userid']);
                    if (!isset($out)) {
                        $out = array('status' => 'error','message'=>'Наш сервер скрывается от администраторов, наверное чувствует вину. Мы его ищем.');
                    }
                }
                $this->response = @json_encode($out);
                break;
            case 'deletescreen':
                if ($this->user['type'] != Setup::$USER_TYPE_ADMIN) {
                    $out = array('status' => 'error','message'=>'Вам нельзя так делать.');
                } else {
                    require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/posts/c_posts_API.php";
                    $out = c_posts_API::deleteScreen($this->db,URL::$POST['leadid']);
                    if (!isset($out)) {
                        $out = array('status' => 'error','message'=>'Наш сервер скрывается от администраторов, наверное чувствует вину. Мы его ищем.');
                    }
                }
                $this->response = @json_encode($out);
                break;
            case 'deleteadd':
                if ($this->user['type'] != Setup::$USER_TYPE_ADMIN) {
                    $out = array('status' => 'error','message'=>'Вам нельзя так делать.');
                } else {
                    require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/posts/c_posts_API.php";
                    $out = c_posts_API::deleteAdd($this->db,URL::$POST['addid'],$this->user['userid']);
                    if (!isset($out)) {
                        $out = array('status' => 'error','message'=>'Наш сервер скрывается от администраторов, наверное чувствует вину. Мы его ищем.');
                    }
                }
                $this->response = @json_encode($out);
                break;
            case 'addtolead':
                if ($this->user['type'] != Setup::$USER_TYPE_ADMIN) {
                    $out = array('status' => 'error','message'=>'Вам нельзя так делать.');
                } else {
                    require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/posts/c_posts_API.php";
                    $out = c_posts_API::addToLead($this->db,URL::$POST);
                    if (!isset($out)) {
                        $out = array('status' => 'error','message'=>'Наш сервер скрывается от администраторов, наверное чувствует вину. Мы его ищем.');
                    }
                }
                $this->response = @json_encode($out);
                break;
            case 'preselect':
                if ($this->user['type'] != Setup::$USER_TYPE_ADMIN) {
                    $out = array('status' => 'error','message'=>'Вам нельзя так делать.');
                } else {
                    require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/posts/c_posts_API.php";
                    $out = c_posts_API::markAsPreselected($this->db,URL::$POST['leadid']);
                    if (!isset($out)) {
                        $out = array('status' => 'error','message'=>'Наш сервер скрывается от администраторов, наверное чувствует вину. Мы его ищем.');
                    }
                }
                $this->response = @json_encode($out);
                break;
            case 'cancel':
                if ($this->user['type'] != Setup::$USER_TYPE_ADMIN) {
                    $out = array('status' => 'error','message'=>'Вам нельзя так делать.');
                } else {
                    require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/posts/c_posts_API.php";
                    $out = c_posts_API::markAsCancelled($this->db,URL::$POST['leadid']);
                    if (!isset($out)) {
                        $out = array('status' => 'error','message'=>'Наш сервер скрывается от администраторов, наверное чувствует вину. Мы его ищем.');
                    }
                }
                $this->response = @json_encode($out);
                break;
            case 'resetcancel':
                if ($this->user['type'] != Setup::$USER_TYPE_ADMIN) {
                    $out = array('status' => 'error','message'=>'Вам нельзя так делать.');
                } else {
                    require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/posts/c_posts_API.php";
                    $out = c_posts_API::markAsCancelled($this->db,URL::$POST['leadid'],true);
                    if (!isset($out)) {
                        $out = array('status' => 'error','message'=>'Наш сервер скрывается от администраторов, наверное чувствует вину. Мы его ищем.');
                    }
                }
                $this->response = @json_encode($out);
                break;
            case 'deletedoc':
                if (($this->user['type'] != Setup::$USER_TYPE_EXPERT) && ($this->user['type'] != Setup::$USER_TYPE_ADMIN)) {
                    $out = array('status' => 'error','message'=>'Вам нельзя так делать.');
                } else {
                    require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/posts/c_posts_API.php";
                    $out = c_posts_API::deleteExpertDoc($this->db,URL::$POST['leadid']);
                    if (!isset($out)) {
                        $out = array('status' => 'error','message'=>'Наш сервер скрывается от администраторов, наверное чувствует вину. Мы его ищем.');
                    }
                }
                $this->response = @json_encode($out);
                break;
            case 'attachfile':
                if (($this->user['type'] != Setup::$USER_TYPE_EXPERT) && ($this->user['type'] != Setup::$USER_TYPE_ADMIN)) {
                    $out = array('status' => 'error','message'=>'Вам нельзя так делать.');
                } else {
                    require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/posts/c_posts_API.php";
                    $out = c_posts_API::attachFile($this->db,URL::$POST['leadid'],URL::$POST['file'],$this->user['name'],$this->user['userid']);
                    if (!isset($out)) {
                        $out = array('status' => 'error','message'=>'Наш сервер скрывается от администраторов, наверное чувствует вину. Мы его ищем.');
                    }
                }
                $this->response = @json_encode($out);
                break;
            case 'attachpic':
                if (($this->user['type'] != Setup::$USER_TYPE_EXPERT) && ($this->user['type'] != Setup::$USER_TYPE_ADMIN)) {
                    $out = array('status' => 'error','message'=>'Вам нельзя так делать.');
                } else {
                    require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/posts/c_posts_API.php";
                    $out = c_posts_API::attachPic($this->db,URL::$POST['leadid'],URL::$POST['file'],$this->user['name'],$this->user['userid']);
                    if (!isset($out)) {
                        $out = array('status' => 'error','message'=>'Наш сервер скрывается от администраторов, наверное чувствует вину. Мы его ищем.');
                    }
                }
                $this->response = @json_encode($out);
                break;
            case 'assign':
                if (($this->user['type'] != Setup::$USER_TYPE_EXPERT) && ($this->user['type'] != Setup::$USER_TYPE_ADMIN)) {
                    $out = array('status' => 'error','message'=>'Вам нельзя так делать.');
                } else {
                    require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/posts/c_posts_API.php";
                    $out = c_posts_API::assignToExpert($this->db,URL::$POST['leadid'],$this->user['userid'],$this->user['name']);
                    if (!isset($out)) {
                        $out = array('status' => 'error','message'=>'Наш сервер скрывается от администраторов, наверное чувствует вину. Мы его ищем.');
                    }
                }
                $this->response = @json_encode($out);
                break;
            case 'updateexpert':
                if ($this->user['type'] != Setup::$USER_TYPE_ADMIN) {
                    $out = array('status' => 'error','message'=>'Вам нельзя так делать.');
                } else {
                    require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/access/c_access_API.php";
                    $out = c_access_API::updateExpert($this->db,URL::$POST['userid'],URL::$POST['action']); 
                    if (!isset($out)) {
                        $out = array('status' => 'error','message'=>'Наш сервер скрывается от администраторов, наверное чувствует вину. Мы его ищем.');
                    }
                }
                $this->response = @json_encode($out);
                break;
            case 'findexpert':
                if ($this->user['type'] != Setup::$USER_TYPE_ADMIN) {
                    $out = array('status' => 'error','message'=>'Вам нельзя так делать.');
                } else {
                    require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/access/c_access_API.php";
                    $out = c_access_API::findExpert($this->db,URL::$POST['eemail']);
                    if (!isset($out)) {
                        $out = array('status' => 'error','message'=>'Наш сервер скрывается от администраторов, наверное чувствует вину. Мы его ищем.');
                    }
                }
                $this->response = @json_encode($out);
                break;
            case 'editchief':
                if ($this->user['type'] != Setup::$USER_TYPE_ADMIN) {
                    $out = array('status' => 'error','message'=>'Вам нельзя так делать.');
                } else {
                    require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/posts/c_posts_API.php";
                    $out = c_posts_API::editChief($this->db,URL::$POST,$this->user['userid']);
                    if (!isset($out)) {
                        $out = array('status' => 'error','message'=>'Наш сервер скрывается от администраторов, наверное чувствует вину. Мы его ищем.');
                    }
                }
                $this->response = @json_encode($out);
                break;
            case 'fileupload':
                if (($this->user['type'] != Setup::$USER_TYPE_ADMIN) && ($this->user['type'] != Setup::$USER_TYPE_EXPERT) ) {
                    $out = array('status' => 'error','message'=>'Вам нельзя так делать.');
                } else {
                    $unid = trim(URL::$POST['unid']);
                    $file = isset(URL::$POST['type'])?trim(URL::$POST['type']):0;
                    $final = isset(URL::$POST['final'])?true:false;
                    require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/posts/c_posts_API.php";
                    $out = c_posts_API::uploadTmpFile($unid,($file == 1),$final);
                }
                $this->response = @json_encode($out);
                break;
            case 'itemdel':
            case 'deletelead':
                if ($this->user['type'] != Setup::$USER_TYPE_ADMIN) {
                    $out = array('status' => 'error','message'=>'Вам нельзя так делать.');
                } else {
                    require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/posts/c_posts_API.php";
                    $out = c_posts_API::deleteLead($this->db,URL::$POST,$this->user['userid']);
                    if (!isset($out)) {
                        $out = array('status' => 'error','message'=>'Наш сервер скрывается от администраторов, наверное чувствует вину. Мы его ищем.');
                    }
                }
                $this->response = @json_encode($out);
                break;
            case 'commentdel':
                if ($this->user['type'] != Setup::$USER_TYPE_ADMIN) {
                    $out = array('status' => 'error','message'=>'Вам нельзя так делать.');
                } else {
                    require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/posts/c_posts_API.php";
                    $out = c_posts_API::deleteComment($this->db,URL::$POST,$this->user['userid']);
                    if (!isset($out)) {
                        $out = array('status' => 'error','message'=>'Наш сервер скрывается от администраторов, наверное чувствует вину. Мы его ищем.');
                    }
                }
                $this->response = @json_encode($out);
                break;
            case 'comment':
                require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/posts/c_posts_API.php";
                $cancomment = c_posts_API::canComment();
                if (!$cancomment) {
                    $out = array('status' => 'error','message'=>'Вы слишком часто комментируете. Мы встревожены. Подождите немного. ');
                } else {
                    // $valid = $this->checkRecaptcha(); // @TODO: monitor comments quality, may activate later
                    $valid = true;
                    if ($valid) {
                        $out = c_posts_API::addComment($this->db,URL::$POST);
                    } else {
                        $out = array('status' => 'error','message'=>'Символы с картинки введены неправильно. Попробуйте еще раз.');
                    }
                    if (!isset($out)) {
                        $out = array('status' => 'error','message'=>'Наш сервер скрывается от администраторов, наверное чувствует вину. Мы его ищем.');
                    }
                }

//Logger::log("status: {$out['status']}, message: {$out['message']}");
                $this->response = @json_encode($out);
                break;
            case 'publish':
                if ($this->user['type'] != Setup::$USER_TYPE_ADMIN) {
                    $out = array('status' => 'error','message'=>'Вам нельзя так делать.');
                } else {
                    require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/posts/c_posts_API.php";
                    $out = c_posts_API::publishLead($this->db,URL::$POST,$this->user['userid']);
                    if (!isset($out)) {
                        $out = array('status' => 'error','message'=>'Наш сервер скрывается от администраторов, наверное чувствует вину. Мы его ищем.');
                    }
                }
                $this->response = @json_encode($out);
                break;

            default:
                $this->response = 'f';
                exit(0);
                break;
        }
    }

    function processUnAuth() {
        switch ($this->function) {

            case 'login':
                require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/access/c_access_API.php";
                    $out = c_access_API::loginUser($this->db,URL::$POST);
                    if (!isset($out)) {
                        $out = array('status' => 'error','message'=>'Наш сервер скрывается от администраторов, наверное чувствует вину. Мы его ищем.');
                    }
                    $this->response = @json_encode($out);
                break;
            case 'register':
                require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/access/c_access_API.php";
                    $valid = $this->checkRecaptcha();
                    if ($valid) {
                        $out = c_access_API::registerUser($this->db,URL::$POST);
                    } else {
                        $out = array('status' => 'error','message'=>'Символы с картинки введены неправильно. Попробуйте еще раз.');
                    }
                    if (!isset($out)) {
                        $out = array('status' => 'error','message'=>'Наш сервер скрывается от администраторов, наверное чувствует вину. Мы его ищем.');
                    }
                    $this->response = @json_encode($out);
                break;
            case 'report': // report a corruption issue
                require_once $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH."/posts/c_posts_API.php";
                $canpost = c_posts_API::canPost();
                if (!$canpost) {
                    $out = array('status' => 'error','message'=>'Вы слишком часто постите. Мы встревожены. Подождите немного. ');
                } else {
                    $valid = $this->checkRecaptcha();
                    if ($valid) {        
                        $out = c_posts_API::postCorruptionLead($this->db,URL::$POST);
                    } else {
                        $out = array('status' => 'error','message'=>'Символы с картинки введены неправильно. Попробуйте еще раз.');
                    }
                    if (!isset($out)) {
                        $out = array('status' => 'error','message'=>'Наш сервер скрывается от администраторов, наверное чувствует вину. Мы его ищем.');
                    }
                }

//Logger::log("status: {$out['status']}, message: {$out['message']}");
                $this->response = @json_encode($out);
                break;


            default:
                $this->response = 'f';
                exit(0);
        }
    }

    public function getContent() {
       return $this->response;
    }


}
