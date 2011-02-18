<?php 

class URL {
	
    public static $ENV;
    public static $HEADERS;
    public static $INPUT;
    public static $SUBSPACE;
    public static $GET;
    public static $POST;
    public static $FILES;
    public static $URLPATH;
    public static $URLVARS;
    public static $FIXEDPATH; // example: /orgs/6?page=2 => /orgs/6/?page=2

    private static $instance = null;
	
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new URL();
        }
        return self::$instance;
    }
	
    public static function setPath() {
    	$parseurl = self::$SUBSPACE;
    	if (preg_match("/[^\/]\?/",$parseurl)) {
            $parseurl = preg_replace("/\?/","/?",$parseurl);
        }
        self::$FIXEDPATH = $parseurl;
        if (self::$FIXEDPATH != self::$SUBSPACE) {
            self::$SUBSPACE = self::$FIXEDPATH;
        }

    	$path = explode("/",$parseurl);
            $num = count($path);
            if (preg_match("/\?/",$path[$num-1])) {
                $path[$num-1] = preg_replace("/\?/","",$path[$num-1]);
                array_pop($path);
            }
            return $path;
	}

    public static function normalizePath($path) {
        $path = trim($path, "/");
        $parts = Array();
        foreach (explode("/", $path) as $part) {
            $part = urldecode($part);
            if ($part == '..') {
                if (count($parts) == 0) {
                    throw new Exception("Illegal path. Relative level extends below root.");
                }
                array_pop($parts);
            } else {
                $parts[] = urlencode($part);
            }
        }
        return implode('/', $parts);
    }

    public static function redirect($url) {
            $content =  '<html><head>'
                                    .'<meta http-equiv="Refresh" content="1;url='.$url.'">'
                                    .'</head><body>'
                                    .'<a href="'.$url.'">'.$url.'</a>'
                                    .'</body></html>';
            header("Location:".$url);
            echo $content;
    }
	
    function __construct() {

        

        // workaround for wierd "undocumented feature" of IE
        self::$HEADERS = Array();
        if (function_exists('apache_request_headers')) {
            foreach (apache_request_headers() as $key => $value) {
                for ($tmp = explode("-", strtolower($key)), $i=0; $i<count($tmp); ++$i) {
                    $tmp[$i] = ucfirst($tmp[$i]);
                }
                self::$HEADERS[implode("-", $tmp)] = $value;
            }
        }
        self::$ENV = $_SERVER;
        if (isset($HEADERS['Http-Method-Equivalent'])) {
            self::$ENV['HTTP_METHOD'] = self::$HEADERS['Http-Method-Equivalent'];
        } else {
            self::$ENV['HTTP_METHOD'] = self::$ENV['REQUEST_METHOD'];
        }
        self::$INPUT = file_get_contents('php://input');
        $protocol = (isset(self::$ENV['HTTPS']) && self::$ENV['HTTPS'] == "on") ? "https" : "http";
        if (isset(self::$ENV['HTTP_HOST'])) {
            self::$ENV['HTTP_ROOT'] = rtrim($protocol."://".self::$ENV['HTTP_HOST'], "/");
        } else {
            self::$ENV['HTTP_ROOT'] = $protocol."://".self::$ENV['SERVER_NAME'];
        }
        $root = rtrim(str_replace("\\", "/", dirname(self::$ENV['PHP_SELF'])), "/")."/";
        self::$ENV['URL_BASE'] = self::$ENV['HTTP_ROOT'] . $root;
        self::$SUBSPACE = rawurldecode(trim(preg_replace("/^(".preg_quote($root,"/").")([^\?]*)(.*)/", "\$2", self::normalizePath(self::$ENV['REQUEST_URI'])), "/"));
        self::$URLPATH = self::setPath();
        self::$GET = self::unMagic($_GET);
        self::$POST = self::unMagic($_POST);
        self::$FILES = self::decodeCharset($_FILES);
        self::$HEADERS = self::decodeCharset(self::$HEADERS);
        self::$ENV = self::decodeCharset(self::$ENV);
        self::$INPUT = self::decodeCharset(self::$INPUT);
        
    }
	
    protected static function unMagic($input) {
            if (is_array($input)) {
                    $output = Array();
                    foreach ($input as $key => $value) {
                            $output[$key] = self::unMagic($value);
                    }
            return $output;
    }
            if (get_magic_quotes_gpc()) {
                    return stripslashes($input);
            }
            return $input;
    }

    protected static function decodeCharset($input) {
            if (is_array($input)) {
                    $output = Array();
                    foreach ($input as $key => $value) {
                            $output[$key] = self::decodeCharset($value);
                    }
            return $output;
            }
            return utf8_decode($input);
    }

    function __destruct() {
    }
	
}