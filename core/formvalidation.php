<?php

class FormValidation {
	
	const MAXLEN_TOPIC_NAME = 64;
	const MAXLEN_POST_TITLE = 255;
	const MAXLEN_POST_BODY = 39600; //19800*2
	const MAXLEN_USERNAME = 20;
	const MINLEN_USERNAME = 4;
	const MAXLEN_PASSWORD = 20;
	const MINLEN_PASSWORD = 4;
	const MINLEN_CITY = 3;
	const MAXLEN_CITY = 50;
	
	static public $msg;
	static public $optional;
	

	static public function field($validator, $value, $field='', $optional=false) {
	
		self::$optional = $optional;
		if (method_exists('Validate', $validator) 
			&& !call_user_func(array('Validate', $validator), $value)) 		
			throw new Response(0, array('msg' => self::$msg, 'name' => $field));	
	}
	
	static public function numeric($value, $field='', $min=null, $max=null) {
		if (!(is_numeric($value) && $msg = t('Please enter a valid number.')) 
			|| ($min !== null && $value < $min && $msg = t("Please enter a number greater or equal to $min")) 
			|| ($max !== null && $value > $max && $msg = t("Please enter a number less than or equal to $max")) 
			) {
			throw new Response(0, array('msg' => $msg, 'name' => $field));
		}	
	}
	
	static public function blank($value) {
		self::$msg = ''; 
		if (trim($value) == '') {
			self::$msg = t('This field cannot be empty.  Please enter a valid value.');
		}
		return self::$msg == ''; 
	}
	
	static public function postTitle($value){
		self::$msg = ''; 
		if (!self::$optional && trim($value) == '') {
			self::$msg = t('Title cannot be empty.  Please enter a valid title.');
		}
		else if (strlen($value) > self::MAXLEN_POST_TITLE) {
			self::$msg = t('Title must be %d characters or less. Please enter a valid title.', self::MAXLEN_POST_TITLE);
		}
		return self::$msg == ''; 
	}

	static public function postBody($value){
		self::$msg = ''; 
		if (!self::$optional && trim($value) == '') {
			self::$msg = t('Text cannot be empty.  Please enter a valid text.');
		}
		else if (strlen($value) > self::MAXLEN_POST_BODY) {
			self::$msg = t('Text must be %d characters or less. Please enter a valid text.', self::MAXLEN_POST_BODY);
		}
		return self::$msg == ''; 
	}
	
	static public function city($value){
		self::$msg = ''; 
		if (!self::$optional && trim($value) == '') {
			self::$msg = t('City cannot be empty.  Please enter a valid city.');
		}
		else if ((strlen($value) > self::MAXLEN_CITY)|| (strlen($value) < self::MINLEN_CITY))  {
			self::$msg = t('City must be %d characters or less and %d characters or more. Please enter a valid city.', self::MAXLEN_CITY,self::MINLEN_CITY);
		}		
		return self::$msg == ''; 
	}
	
	
	static public function cell($value){
		self::$msg = ''; 
		if (strlen(trim($value)) > 0) {
			$digits = preg_replace('/[^0-9\+]/','',$value);
			if (!in_array(strlen($digits),array(10,11,12))) self::$msg = t('Wrong cell phone number, check format: +7(XXX)XXX-XX-XX.');
		}
		return self::$msg == ''; 
	}
	
		
	static public function email($value){
		self::$msg = ''; 
		if (trim($value) == '') {
			self::$msg = t('Email cannot be empty.  Please enter a valid email.');
		}
		//else if (!preg_match('/^[a-z0-9]+([-+.][a-z0-9]+)*@[a-z0-9]+([-.][a-z0-9]+)*\.[a-z0-9]+([-.][a-z0-9]+)*$/',$value) ) {
        else {
            $reg = '/(?:[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/i';
            if (!preg_match($reg, $value)) {
                self::$msg = t('This doesn\'t appear to be a valid email. Please enter a valid email.');
            }
        }
        
		
		return self::$msg == ''; 
	}
	
		
	static public function username($value){
		self::$msg = ''; 
		if (trim($value) == '') {
			self::$msg = t('Name cannot be empty.  Please enter a valid full name.');
		}
		//else if (preg_match("/\s/",$value)) { // commented: no spaces validation required
		else if (false) {
			self::$msg = t('Name cannot contain any white space characters. Please enter a valid name.');
		}
		else if (strlen($value) > self::MAXLEN_USERNAME || strlen($value) < self::MINLEN_USERNAME) {
			self::$msg = t('Please select a name between %d and %d characters.', self::MINLEN_USERNAME, self::MAXLEN_USERNAME);
		}

		return self::$msg == ''; 
	}
		
	static public function password($value){
		self::$msg = ''; 
		if (trim($value) == '') {
			self::$msg = t('Password cannot be empty.  Please enter a valid password.');
		}
		else if (strlen($value) > self::MAXLEN_PASSWORD || strlen($value) < self::MINLEN_PASSWORD) {
			self::$msg = t('Please select a password between %d and %d characters.', self::MINLEN_PASSWORD, self::MAXLEN_PASSWORD);
		}
		return self::$msg == ''; 
	}
	

}
?>