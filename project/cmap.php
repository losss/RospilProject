<?php

class CMap {
	
	public static $MAP = array(
        '_____'     => 'c_generic_Main', 		// default path (no path)
        'search'    => 'c_generic_Search',
        'orgs'      => 'c_generic_Orgs',
        'donate'    => 'c_generic_Donate',
        'about'     => 'c_readme_Main',
        'report-a-corruption' => 'c_posts_PostLead',     // sync with Setup::$REPORT_LINK
        'corruption-case'   => 'c_posts_Post',           // sync with Setup::$POST_BASE_URL
        'admin'     => 'c_access_Admin',
        'expert'    => 'c_access_Expert',
        'login'     => 'c_access_Login',
        'register'  => 'c_access_Register',
        'regexpert' => 'c_access_Register',
        'logout'    => 'c_access_Logout',
        'api'       => 'c_api_Main',
        '404'       => 'c_404',
        'a'         => 'c_ajax_Root',
        'expertise' => 'c_generic_Main',
	);

}
