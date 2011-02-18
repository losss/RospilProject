<?php

class c_class {
	
    public $listCSS;
    public $listJS;
    public $tplroot;

    public function getSearch() {
        return $this->render($this->getTpl('search', 'generic', true),array('caller'=>$this->caller));
    }

    public function getMenu() {
        return $this->render($this->getTpl('menu', 'generic', true),array('menu'=>Setup::$MENU,'caller'=>$this->caller));
    }

    public function getReportLink() {
        return $this->render($this->getTpl('reportlink', 'generic', true));
    }

    function __construct() {
        $this->listCSS = array();
        $this->listJS = array();
        $this->tplroot = $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_TPL_PATH;
    }

    public function addCSS($name) {
            $full_name = Settings::$PROJECT_CSS_PATH.'/'.$name.'.css';
            $this->listCSS[] = $full_name;
    }

    public function addJS($name) {
            $full_name = Settings::$PROJECT_JS_PATH.'/'.$name.'.js';
            $this->listJS[] = $full_name;
    }

    public function render(/* $file, $model = Array() */) {
            if (!is_string(func_get_arg(0))) {
                    throw new Exception("[render]: first argument is not a string");
            }
        if (func_num_args() > 1) {
            extract(func_get_arg(1));
        }
            $file = func_get_arg(0);
            if (!is_file($file)) {
            throw new Exception("[render]: cannot open '".$file."'.");
        }
            ob_start();
            try {
            include($file);
            $buffer = ob_get_clean();
                    return $buffer;
            } catch (Exception $ex) {
                    ob_end_clean();
                    throw $ex;
            }
    }

    public function getBottom() {
        return $this->render($this->getTpl('bottom','generic',true));
    }


    public function getTpl($tplName,$folder,$partial='') {
            return $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_TPL_PATH.'/'.$folder.'/'.($partial?'_':'').$tplName.'.tpl.php';
    }

    public function addClass($cname,$parent=null,$user=null) {
        $namearr = explode('_',CMap::$MAP[$cname]); // c_path_Classname is a standard for project class naming
        $classPath = '/'.$namearr[1].'/';
        $toinclude = $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_MODULES_PATH.$classPath.CMap::$MAP[$cname].'.php';
        if (!file_exists($toinclude)) {
                // var_dump($toinclude);
                return null;
        }
        try {
                require_once $toinclude;
        } catch (Exception $ex) {
                throw ex;
        }
        return new CMap::$MAP[$cname]($parent?$parent:$this,$user);
    }

}
