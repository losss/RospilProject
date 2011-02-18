<?php
/**
 * Main readme class
 *
 */

class c_readme_Main extends c_class {
	
    function __construct ($caller) {
        $this->caller = $caller;
    }
	
    public function getContent($user=null) {
    	$this->user = $user;
    	return $this->execute();
    }

    public function execute() {
    	switch (strtolower($this->caller->myRootClassAlias)) {
            //case 'privacy':
            //    $content = $this->render($this->caller->tplroot.'/readme/_privacy.tpl.php');
            //    break;
            case 'about':
                $content = $this->render($this->caller->tplroot.'/readme/_about.tpl.php');
                break;
            //case 'terms':
            //    $content = $this->render($this->caller->tplroot.'/readme/_terms.tpl.php');
            //    break;
            default:
                $content = 'not found';
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
	
}
