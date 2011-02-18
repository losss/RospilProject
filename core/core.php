<?php

/******************************************************************************
 *
 * (c) 2009-2010 Pavel Senko, All Rights Reserved
 * PHP Context Engine 5.0
 *
 ******************************************************************************/
 
 /*
  * 
XMUST: modify httpd.conf or vhost.conf accordingly 
(otherwise search bots will receive 404 errors instead of pages)

<IfModule mod_rewrite.c>
RewriteEngine on
# we don't want to redirect requests for images from /graph/ directory
RewriteRule /(graph.*) /$1 [skip=10]
# alternatively we can skip file types by extension
RewriteRule /(.*\.(jpg|gif|swf|pps|ppt|doc|zip|png|pdf)) /$1 [skip=10]
# make webalizer stats accessible the standard way
RewriteRule /(statistics.*) /$1 [skip=10]
# ...and redirect all the rest
RewriteRule /.* /index.php
</IfModule>


Options +FollowSymLinks
RewriteEngine On
RewriteRule /(graph.*) /$1 [skip=10]
RewriteRule /(.*\.(jpg|gif|swf|pps|ppt|doc|zip|png|pdf|css|js|php)) /$1 [skip=10]
RewriteRule /.* /index.php
//
 * 
 * 
 */


class Core extends c_class {
	
	public $myURL;
	public $myDB;
	public $myRootClassAlias;
	public $myRootClass;
	public $myUrlBase;
	public $myProjectRoot;
	
	function __construct () {

		if (Settings::$OFF) header("Location:/off.htm");
				
		parent::__construct();

		// initialize URL handler
		//
		$this->myURL = URL::getInstance();

		// connect to DB
		//
		$this->myDB = new dbConnector();

                if (!is_object($this->myDB)) {
                    header("Location:/off.htm");
                }

		// parse GET variables 
		//
		$this->httpRequest();
		
		// init project root
		//
		$this->myProjectRoot = new root($this);
	}
	
	function __destruct() {	
	}
	
	public function run() {

		// receive and send content from project class
		//
		echo $this->myProjectRoot->getContent();
		
	}

	private function httpRequest() {
		
		// get base
		//
		$this->myUrlBase = URL::$ENV['URL_BASE']; 

		// get root class alias
		//
		$arr = URL::$URLPATH;
		$defpath = Settings::$DEFAULT_URL_PATH;
		$this->myRootClassAlias = strtolower(((is_array($arr)&&(count($arr)>0)&&($arr[0]))?array_shift(URL::$URLPATH):$defpath)); 
	} 
	
	
}
