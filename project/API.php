<?php

class API  {

    // move pic to tmp directory, return properties array
    //
    public static function uploadTmpFile($id,$file=false,$final=false) {
        if ($file) $dir = Settings::$FILES_DIR;
        else $dir = Settings::$IMG_TMP;
        $fid = "file$id";
        $fname = '';
        if (isset($_FILES[$fid]['name'])) {
            $upload_tmp_dir = $_SERVER['DOCUMENT_ROOT'].$dir;
            if ($final) {
                $pi = pathinfo($_FILES[$fid]['name']);
                $ts = time();
                $fname = md5("$id - $ts").'.'.$pi['extension'];
                $tmpf = $upload_tmp_dir.$fname;
            } else {
                $tmpf = $upload_tmp_dir.$fid;
            }
            $moved = move_uploaded_file($_FILES[$fid]['tmp_name'], $tmpf);
            if ($moved)
                return array(
                    'file'  => $_FILES[$fid]['name'],
                    'url'   => $dir.$fname,
                    'id'    => $id,
                    'tmp'   => $tmpf,
                    'size'  => $_FILES[$fid]['size']
                    );
            else return NULL;
        } else {
            return NULL;
        }
    }

    // pitcure processing (original uploaded -> set of pics with predefined dimensions)
    //
    // db       - database handler
    // type     - type of entity = img sub-directory
    // id       - unique id (to be used in naming?)
    // tmpname  - temp name of original file
    // ext      - file extention?
    //
    public static function preparePics($db,$id,$tmpname,$ext,$table='orgs') {

        $result = array();

        $pics = getPics($table,$tmpname,$ext,$id,array(Setup::$BIG_IMG_SIZE,Setup::$MED_IMG_SIZE,Setup::$SML_IMG_SIZE,Setup::$TNY_IMG_SIZE));
        if (!isset($pics) || (!is_array($pics)) || (count($pics)<4)) return false;
        if (strpos($pics[0],'error') === false) { $result['b'] = $pics[0]; } else { Logger::log('[updatePics] 0:'.$pics[0]); } // big
        if (strpos($pics[1],'error') === false) { $result['m'] = $pics[1]; } else { Logger::log('[updatePics] 1:'.$pics[1]); } // medium
        if (strpos($pics[2],'error') === false) { $result['s'] = $pics[2]; } else { Logger::log('[updatePics] 2:'.$pics[2]); } // small
        if (strpos($pics[3],'error') === false) { $result['t'] = $pics[3]; } else { Logger::log('[updatePics] 3:'.$pics[3]); } // tiny
        if (strpos($pics[4],'error') === false) { $result['o'] = $pics[4]; } else { Logger::log('[updatePics] 4:'.$pics[4]); } // original

        return $result;

    }

    public static function getGeneratedId($db) {
        return $db->getGeneratedId();
    }

    public static function isNum($n) {
        if (preg_match('/\D/', $n)) {
            return false;
        } else {
            return true;
        }
    }

    public static function q($db,$sql) {
        $db->runSQL($sql);
        if (strpos(strtolower($sql),'insert') === 0) {
            return $db->getGeneratedId();
        }
    }

    public static function cleanInput($db,$s) {
        return $db->escapeString(trim($s));
    }

    public static function getSqlArray($db,$sql,$setkey=false,$key=null) {
        $result = array();
        $dbResult = $db->runSQL($sql);
        if ($dbResult) {
            $resultObj = $dbResult->fetch_object();
            while ($resultObj) {
                if ($setkey) {
                   $result[$resultObj->{$key}] = (array)$resultObj;
                } else {
                    $result[] = (array)$resultObj;
                }
                $resultObj = $dbResult->fetch_object();
            }
            $dbResult->free();
        }
        return $result;
    }

    // gets SINGLE row
    public static  function getSqlHash($db,$sql) {
        $result = array();
        $dbResult = $db->runSQL($sql);
        if ($dbResult) {
            $resultObj = $dbResult->fetch_object();
            if ($resultObj) {
                $result = (array)$resultObj;
            }
            $dbResult->free();
        }
        return $result;
    }

    public static function getSingleValue($db,$sql) {

    	$res = $db->runSQL($sql);
    	$result = 0;

    	if (!$res) {
	// do nothing
        } else {
            if ($res) {
                $resultObj = $res->fetch_object();
            	if ($resultObj) { $result = array(); }
                while ($resultObj) {
                    $result = $resultObj->value;
                    $resultObj = $res->fetch_object();
                    break;
                }
                $res->free();
            }
        }
    	return $result;
    }

    public static function bindVars($sql,$vars) {
    	foreach (array_keys($vars) as $v) {
            $sql = preg_replace("/:$v/",$vars[$v],$sql);
    	}
    	return $sql;
    }
    
    public static function checkRecaptcha() {
        require_once $_SERVER['DOCUMENT_ROOT'].Settings::$CORE_LIB_PATH."/recaptchalib.php";
        $pkey = Setup::$RECAPTCHA_PRIVATE;
        $resp = recaptcha_check_answer ($pkey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

        return $resp->is_valid;
    }
    
}

