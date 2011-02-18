<?php

function ru_date($ts) {
    return preg_replace("/".date("M",$ts)."/",Setup::$MONTH_MAP[date("M",$ts)],date("j M Y",$ts));
}

function makeCookie($user,$password) {
    return md5($user.'+'.$password);
}

function wordwrapUTF($str, $width, $break) {
    $return = '';
    $br_width = mb_strlen($break, 'UTF-8');
    for($i = 0, $count = 0; $i < mb_strlen($str, 'UTF-8'); $i++, $count++) {
        if (mb_substr($str, $i, $br_width, 'UTF-8') == $break) {
            $count = 0;
            $return .= mb_substr($str, $i, $br_width, 'UTF-8');
            $i += $br_width - 1;
        }
        if ($count > $width) {
            $return .= $break;
            $count = 0;
        }
        $return .= mb_substr($str, $i, 1, 'UTF-8');
    }
    return $return;
}

function timeDiff($diff) {
    if ($diff < 1000) {
        return '< 1 sec';
    } else {
        $days = floor($diff/1000/60/60/24);
        $diff -= $days*1000*60*60*24;
        if ($days) return "$days days";
        $hours = floor($diff/1000/60/60);
        $diff -= $hours*1000*60*60;
        $minutes = floor($diff/1000/60);
        $diff -= $minutes*1000*60;
        if ($hours) return "$hours hrs, $minutes min";
        $seconds = floor($diff/1000);
        if ($minutes) return "$minutes min, $seconds sec";
        return "$seconds sec";
    }
}

function preparePic($tmpf,$id,$ps,$upload_final_dir,$avatar=true,$e='') {
    $pic = array(
        'maxw'	=> $ps,
        'maxh'	=> $ps,
        'name'	=> ''
    );
    $quality = 100;
    $ext = '';
    $errmsg = '';
    $size = getimagesize($tmpf);
    if ($size[0] && $size[1]) {     	
        $ratio = $size[0] / $size[1];
        if ($ratio < 1) {
            // A)
            if ($avatar) {
                $pic['scale'] 	= $pic['maxw'] / $size[0];
            } else {
                $pic['scale'] = $pic['maxw'] / $size[0];
                $pic['maxh'] = $pic['maxh'] / $ratio;
            }
        } else {
            // B)
            if ($avatar) {
                $pic['scale'] = $pic['maxh'] / $size[1];
            } else {
                $pic['scale'] = $pic['maxw'] / $size[0];
                $pic['maxh'] = $pic['maxh'] / $ratio;
            }
        }
        if (($pic['maxw'] > $size[0]) && ($pic['maxh'] > $size[1])) {
            $pic['scale'] = 1;
        }
        if (!$avatar) {
            if ($pic['maxw'] > $size[0]) $pic['maxw'] = $size[0];
            if ($pic['maxh'] > $size[1]) $pic['maxh'] = $size[1];
        }
        $r = substr(md5(time().' '.rand(19,99)),rand(5,10),7);
        switch ($size['mime']) {
            case 'image/gif':
                if (imagetypes() & IMG_GIF)  {
                    $ext = "gif";
                    $im_in = imagecreatefromgif($tmpf);
                    $pic['target'] = $upload_final_dir.$id."_".$ps."_"."$r.gif";
                    $pic['name'] = $id."_".$ps."_"."$r.gif";
                    $pic['im_out'] = imagecreatetruecolor($pic['maxw'], $pic['maxh']);
                    $pic['transparent'] = imagecolorallocate($pic['im_out'], 255, 255, 255);
                    imagefilledrectangle($pic['im_out'], 0, 0, $pic['maxw'], $pic['maxh'], $pic['transparent']);
                    $scale1 = $pic['scale'];
                    $src_x = ($ratio < 1?0:(int)(($size[0] - $pic['maxw']/$scale1)/2));
                    $src_y = ($ratio < 1?(int)(($size[1] - $pic['maxh']/$scale1)/2):0);
                    $src_w = (int)($pic['maxw']/$scale1);
                    $src_h = (int)($pic['maxh']/$scale1);
                    imagecopyresampled($pic['im_out'], $im_in, 0, 0, $src_x, $src_y, $pic['maxw'], $pic['maxh'], $src_w, $src_h);
                    imagegif($pic['im_out'], $pic['target'], $quality);
                    } else {
                        $errmsg = 'error: формат GIF не поддерживается';
                    }
            break;
            case 'image/jpeg':
                if (imagetypes() & IMG_JPG)  {
                    $pic['target'] = $upload_final_dir.$id."_".$ps."_"."$r.jpg";
                    $pic['name'] = $id."_".$ps."_"."$r.jpg";
                    $ext = "jpg";
                    $im_in = imageCreateFromJPEG($tmpf);
                    $pic['im_out'] = imagecreatetruecolor($pic['maxw'], $pic['maxh']);
                    $scale1 = $pic['scale'];
                    $src_x = ($ratio < 1?0:(int)(($size[0] - $pic['maxw']/$scale1)/2));
                    $src_y = ($ratio < 1?(int)(($size[1] - $pic['maxh']/$scale1)/2):0);
                    $src_w = (int)($pic['maxw']/$scale1);
                    $src_h = (int)($pic['maxh']/$scale1);
                    imagecopyresampled($pic['im_out'], $im_in, 0, 0, $src_x, $src_y, $pic['maxw'], $pic['maxh'], $src_w, $src_h);
                    imagejpeg($pic['im_out'], $pic['target'], $quality);
                } else {
                    $errmsg = 'error: формат JPG не поддерживается';
                }
            break;
            case 'image/png':
                if (imagetypes() & IMG_PNG)  {
                    $pic['target'] = $upload_final_dir.$id."_".$ps."_"."$r.png";
                    $pic['name'] = $id."_".$ps."_"."$r.png";
                    $ext = "png";
                    $im_in = imageCreateFromPNG($tmpf);
                    $pic['im_out'] = imagecreatetruecolor($pic['maxw'], $pic['maxh']);
                    $scale1 = $pic['scale'];
                    $src_x = ($ratio < 1?0:(int)(($size[0] - $pic['maxw']/$scale1)/2));
                    $src_y = ($ratio < 1?(int)(($size[1] - $pic['maxh']/$scale1)/2):0);
                    $src_w = (int)($pic['maxw']/$scale1);
                    $src_h = (int)($pic['maxh']/$scale1);
                    imagecopyresampled($pic['im_out'], $im_in, 0, 0, $src_x, $src_y, $pic['maxw'], $pic['maxh'], $src_w, $src_h);
                    imagepng($pic['im_out'], $pic['target'], 9); // for PNG quality is
                } else {
                    $ermsg = 'error: формат PNG не поддерживается';
                }
            break;
            default:
                $errmsg = 'error: '.$size['mime'].' не поддерживается';
            break;
        }
    }
    return ($errmsg?$errmsg:$pic['name']);
}

function getPics($type,$tmp_name,$ext,$id,$sizes=array()) {
	
    $pics = array();
    if (!count($sizes)) return $pics;
    $dir = 'img';

    $upload_tmp_dir = $_SERVER['DOCUMENT_ROOT'].'/'.$dir.'/'.$type.'/tmp/';
    $upload_final_dir = $_SERVER['DOCUMENT_ROOT'].'/'.$dir.'/'.$type.'/';
    $ts = time();

    if ($tmp_name) {
        $avatar = false;
        foreach ($sizes as $s) {
            $pname = preparePic($tmp_name,$id,$s,$upload_final_dir,$avatar,$ext); // avatar = true by default
// Logger::log("pname: $pname | $ext");
            array_push($pics,$pname);
        }
    }
    // append original image
    //
    // tmp_name = comment_id in temp directory
    rename($tmp_name,$tmp_name.$ts.'.'.$ext);
    $w_pos = strrpos($tmp_name,'\\');
    $u_pos = strrpos($tmp_name,'/');

//Logger::log("w: $w_pos, u: $u_pos | $tmp_name | $ext");

    if ($w_pos > 0) {
        $s_name = substr($tmp_name,$w_pos+1);
    } else {
        $s_name = substr($tmp_name,$u_pos+1);
    }
    $pics[] = $s_name.$ts.'.'.$ext;

    return $pics;
}

function makePics($field,$id,$sizes=array(100,30),$dir='pic') {
    $pics = array();
    $inp = $dir;
    //$dir = ($avatar?'avatar':'pic');
    //$inp = ($avatar?'avatar':'pic');
    $upload_tmp_dir = $_SERVER['DOCUMENT_ROOT'].'/img/'.$dir.'/tmp/';
    $upload_final_dir = $_SERVER['DOCUMENT_ROOT'].'/img/'.$dir.'/';
    if (!isset($_FILES[$field])) return array('wrong field');
    $fname = $_FILES[$field]['name'];
    if ($fname) {
        $md5id = md5($id);
        $tmpf = $upload_tmp_dir.''.$md5id.'_'.$fname;
        $moved = move_uploaded_file($_FILES[$field]['tmp_name'], $tmpf);
        if ($moved) {
            foreach ($sizes as $s) {
                $pname = preparePic($tmpf,$id,$s,$upload_final_dir,$avatar); // avatar = true by default
                array_push($pics,$pname);
            }
            $pics[] = $md5id.'_'.$fname;
        }
    }
    return $pics;
}

function render(/* $file, $model = Array() */) {
    if (!is_string(func_get_arg(0))) {
        throw new Exception("Wrong argument type. Expected string as first parameter");
    }
    if (func_num_args() > 1) {
      	extract(func_get_arg(1));
    }
    $file = func_get_arg(0);
    if (!is_file($file)) {
      	throw new Exception("Failed opening '".$file."' for inclusion.");
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

if ( false === function_exists('lcfirst') )
{
    function lcfirst( $str )
    { return (string)(strtolower(substr($str,0,1)).substr($str,1));}
} 

$FUNC_ESCAPE_CHARS = array("/" => "-_", "\\" => "-~", '+' => '-!', '-' => '--');
$FUNC_UNESCAPE_CHARS = array_reverse($FUNC_ESCAPE_CHARS, true);

function escapeSlashes($text) {
    global $FUNC_ESCAPE_CHARS;
    return str_replace(array_keys($FUNC_ESCAPE_CHARS), array_values($FUNC_ESCAPE_CHARS), $text);
}

function unescapeSlashes($text) {
    global $FUNC_UNESCAPE_CHARS;
    return str_replace(array_values($FUNC_UNESCAPE_CHARS), array_keys($FUNC_UNESCAPE_CHARS), $text);
}


$GLOBALS['_global_function_callback_t'] = '';	// to prevent notice of undefined index

// turns 2 or more nl's to <p>
function nls2p($str,$delimiter="\n",$userCanPostLinks=false) {
    $lines = explode($delimiter,$str);
    $text = '';
    $extraline = 0;
    foreach ($lines as $l) {
        $l = trim($l);
        if (!$l) continue;
        if (Settings::$SHOW_LINKS_COMMENTS || $userCanPostLinks) {
            $l = preg_replace('!(^|\s[:\'"\!.,?&)(\[\]{}]*)((?:http(s?)\://([^\s]+?))|((?:[a-z][\-a-z\d]*?\.)+[a-z][\-a-z\d]+?(/.+?)*))([:\'"\!.,?&)(\[\]{}]*(?=\s)|$)!ms', '$1<a target="_blank" id="blue" href="http$3://$4$5">$4$5</a>$7$8', str_replace('>', '&gt;', str_replace('<', '&lt;', preg_replace("/ {2,}/", " ", $l))));
        }
        $text.= "<p>$l</p>";
    }
    return $text;
}

// parse input text
function inputBasicTextParser($string) {
    $string = trim($string);
    $string = strip_tags($string);	// no tags
    return $string;
}

function t($str) {
    return $str; // placeholder for translation
}

// проблема - проблемы - проблем
//
function getPostsLabel($num,$labelBase) {
    if ( ($num != 11) &&
	((($num - 1) % 10) == 0)
	) {
	$label = $labelBase.'а';	
    } else if ( ($num != 12) && ($num != 13) && ($num != 14) &&
	( ((($num - 2) % 10) == 0) || ((($num - 3) % 10) == 0) ||  ((($num - 4) % 10) == 0) ) 
	) {
	$label = $labelBase.'ы';	
    } else $label = $labelBase;
    return $label;
}
    
// решение - решения - решений
// 
function getSolutionsLabel($num,$labelBase) {
   if ( ($num != 11) &&
	((($num - 1) % 10) == 0)
	) {
	$label = $labelBase.'е';	
} else if ( ($num != 12) && ($num != 13) && ($num != 14) &&
	( ((($num - 2) % 10) == 0) || ((($num - 3) % 10) == 0) ||  ((($num - 4) % 10) == 0) ) 
	) {
	$label = $labelBase.'я';	
} else $label = $labelBase.'й';
return $label;
}



// просмотр - просмотра - просмотров
//
function getViewsLabel($num,$labelBase) {
    return getVotesLabel($num,$labelBase);
}

// голос - голоса - голосов
//
function getVotesLabel($num,$labelBase) {
    if ( ($num != 11) &&
	((($num - 1) % 10) == 0)
	) {
	$label = $labelBase;	
} else if ( ($num != 12) && ($num != 13) && ($num != 14) &&
	( ((($num - 2) % 10) == 0) || ((($num - 3) % 10) == 0) ||  ((($num - 4) % 10) == 0) ) 
	) {
	$label = $labelBase.'а';	
} else $label = $labelBase.'ов';
return $label;
}

// комментарий - комментария - комментариев
// "комментари" - база
function getCommentsLabel($num,$labelBase) {
    if ( ($num != 11) &&
	((($num - 1) % 10) == 0)
	) {
	$label = $labelBase.'й';
} else if ( ($num != 12) && ($num != 13) && ($num != 14) &&
	( ((($num - 2) % 10) == 0) || ((($num - 3) % 10) == 0) ||  ((($num - 4) % 10) == 0) )
	) {
	$label = $labelBase.'я';
} else $label = $labelBase.'ев';
return $label;
}

function ago($tm,$rcs = 0) {
    $cur_tm = time(); $dif = $cur_tm-$tm;
    $pds = array();
    $pds[1] = array('секунда','минута','час','день','неделя','месяц','год','десятилетие');
    $pds[2] = $pds[3] = $pds[4] = array('секунды','минуты','часа','дня','недели','месяца','года','десятилетия');
    $pds[5] = $pds[6]= $pds[7] = $pds[8] = $pds[9] = $pds[10] = $pds[0] = array('секунд','минут','часов','дней','недель','месяцев','лет','десятилетий');
    
    $lngh = array(1,60,3600,86400,604800,2630880,31570560,315705600);
    for($v = sizeof($lngh)-1; ($v >= 0)&&(($no = $dif/$lngh[$v])<=1); $v--); if($v < 0) $v = 0; $_tm = $cur_tm-($dif%$lngh[$v]);
    
    $no = floor($no); 
    // if($no <> 1) $pds[$v] .='s';
    
    if ($no <= 10) {
    	$x=sprintf("%d %s назад",$no,$pds[$no][$v]);
    } else if ($no >= 20) {
    	$x=sprintf("%d %s назад",$no,$pds[$no%10][$v]);
    } else {
    	$x=sprintf("%d %s назад",$no,$pds[0][$v]);
    }
    if(($rcs == 1)&&($v >= 1)&&(($cur_tm-$_tm) > 0)) $x .= time_ago($_tm);
    return $x;
}

function strlen_cyr($str) {
	$line = iconv("UTF-8", "Windows-1251//TRANSLIT", $str); // convert to windows-1251 
	return strlen($line); 
}

function substr_cyr($str,$start,$len) {
	$line = iconv("UTF-8", "Windows-1251//TRANSLIT", $str); // convert to windows-1251 
	$line1 = substr($line,$start,$len); 
	$line2 = iconv("Windows-1251", "UTF-8", $line1); // convert back to utf-8 
	return $line2;
}

function purifySimple($string) {
	$clean_html = strip_tags(str_replace("\n", '', trim($string)));
	$clean_html = strip_tags(preg_replace('/[#][\w]*/', '', trim($string)));
	return $clean_html;
}

function purify($string, $replaceNl2Br=true) {
	$clean_html = '';
	$string = trim($string);        
	if ($string) {
		$clean_html = nls2p(strip_tags($string));
		if ($replaceNl2Br) {
			$clean_html = nl2br($clean_html);
		}
	}
    return $clean_html;
}
function convertToText($str,$highlightSearchTerms=null) {
	$text = preg_replace('!(^|\s[:\'"\!.,?&)(\[\]{}]*)((?:http(s?)\://([^\s]+?))|((?:[a-zA-Z][a-zA-Z\d]*?\.)+[a-zA-Z][a-zA-Z\d]+?(/.+?)*))([:\'"\!.,?&)(\[\]{}]*(?=\s)|$)!msi', '$1<a target="_blank" href="http$3://$4$5">$4$5</a>$7$8', str_replace('>', '&gt;', str_replace('<', '&lt;', preg_replace("/ {2,}/", " ", $str))));
    return $text;
}

function convertToPlainText($str) {
	return htmlentities($str);
}

function startsWith($haystack,$needle,$case=true) {
    if($case){return (strcmp(substr($haystack, 0, strlen($needle)),$needle)===0);}
    return (strcasecmp(substr($haystack, 0, strlen($needle)),$needle)===0);
}

function endsWith($haystack,$needle,$case=true) {
    if($case){return (strcmp(substr($haystack, strlen($haystack) - strlen($needle)),$needle)===0);}
    return (strcasecmp(substr($haystack, strlen($haystack) - strlen($needle)),$needle)===0);
}






