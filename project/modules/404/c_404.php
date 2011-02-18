<?php

class c_404 extends c_class {
	
    function __construct ($caller) {
            $this->caller = $caller;
            if (isset($caller->user)) $this->user = $caller->user;
    }
	
    public function getContent($user=null) {
    	$this->user = $user;
    	return $this->execute();
    }

    public function execute() {

        $index = $this->caller->tplroot.'/generic/default.tpl.php';
        return $this->render($index,array(
                'reportlink'    => $this->getReportLink(),
                'menu'          => '<div class="bigtitle">404</div>',
                'search'        => '',
                'content'       => '<div style="margin-left:135px;min-height:400px;"><h1>Такого тут нет</h1></div>',
                'bottom'        => $this->getBottom() // called from parent (no changes to bottom, it's pretty static)
        ));

    }

}

