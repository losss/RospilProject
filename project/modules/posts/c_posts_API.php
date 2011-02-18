<?php

class c_posts_API extends API {

    public static function sentPetition($db,$leadid,$userid) {
        $leadid = $db->escapeString($leadid);
        $userid = $db->escapeString($userid);
        $ts = time();
        $cnt = self::getSingleValue($db, "SELECT petition_sent_count as value FROM leads WHERE leadid=$leadid ");
        if (!$cnt) $cnt = 0;
        $users = self::getSingleValue($db, "SELECT petition_users as value FROM leads WHERE leadid=$leadid ");
        $allusers = explode(',',$users);
        if (!in_array($userid,$allusers)) {
            $cnt++;
            array_push($allusers, $userid);
            $users = implode(',',$allusers);
            $sql = "UPDATE leads SET petition_sent_count=$cnt,petition_users='$users' WHERE leadid=$leadid";
            self::q($db,$sql);
        } 
        return array('status'   => 'OK',
                     'message'  => 'Отлично!',
                     'count'    => $cnt
                );
    }

    public static function getAdditions($db,$leadid) {
        
        $leadid = $db->escapeString($leadid);
        $sql = "SELECT * FROM additions
                WHERE  leadid=$leadid AND deleted_ts IS NULL
                ORDER BY addts ASC";
        $res = API::getSqlArray($db, $sql);
        return $res;
    }

    public static function markAsPreselected($db,$leadid) {
        $leadid = $db->escapeString($leadid);
        $ts = time();
        $sql = "UPDATE leads SET preselected='Y' WHERE leadid=$leadid";
        self::q($db,$sql);
        return array('status'   => 'OK',
                     'message'  => 'Отлично!',
                );
    }

    public static function deleteScreen($db,$leadid) {
        $leadid = $db->escapeString($leadid);
        $ts = time();
        $sql = "UPDATE leads SET pic_o='',pic_b='',pic_m='',pic_s='',pic_t='' WHERE leadid=$leadid";
        self::q($db,$sql);
        return array('status'   => 'OK',
                     'message'  => 'Отлично!',
                );
    }

    public static function markAsCancelled($db,$leadid,$reset=false) {
        $leadid = $db->escapeString($leadid);
        $ts = $reset?0:time();
        $sql = "UPDATE leads SET cancelled_ts=$ts WHERE leadid=$leadid";
        self::q($db,$sql);
        return array('status'   => 'OK',
                     'message'  => 'Отлично!',
                );
    }

    public static function deleteExpertDoc($db,$leadid) {
        $leadid = $db->escapeString($leadid);
        $sql = "UPDATE leads SET expertdoc='',booked_expertid=NULL WHERE leadid=$leadid";
        self::q($db,$sql);
        return array('status'   => 'OK',
                     'message'  => 'Отлично!',
                );
    }

    public static function deleteAdd($db,$addid,$uid) {
        $addid = $db->escapeString($addid);
        $uid = $db->escapeString($uid);
        $ts = time();
        $sql = "UPDATE additions SET deleted_ts=$ts,deleted_by=$uid WHERE addid=$addid";
        self::q($db,$sql);
        return array('status'   => 'OK',
                     'message'  => 'Отлично!',
                );
    }

    public static function attachFile($db,$leadid,$file,$ename,$eid) {
        $leadid = $db->escapeString($leadid);
        $file = $db->escapeString($file);
        $ename = $db->escapeString($ename);
        $eid = $db->escapeString($eid);
        $sql = "UPDATE leads SET expertdoc='$file',expertname='$ename',booked_expertid=$eid WHERE leadid=$leadid";
        self::q($db,$sql);

        return array('status'   => 'OK',
                     'message'  => 'Отлично!',
                     'url'      => $file,
                     'leadid'   => $leadid
                );
    }

    public static function attachPic($db,$leadid,$file) {
        $leadid = $db->escapeString($leadid);
        $file = $db->escapeString($file);
        $filename = $_SERVER['DOCUMENT_ROOT'].$file;

        if (file_exists($filename)) {
            $pi = pathinfo($filename);
            // ($db,$id,$tmpname,$ext,$table='orgs')
            $pics = self::preparePics($db, $leadid, $filename, $pi['extension'], 'pic');
            $picpath = Settings::$IMG_ROOT.'/pic/';

            $b = $picpath.$pics['b'];
            $m = $picpath.$pics['m'];
            $s = $picpath.$pics['s'];
            $t = $picpath.$pics['t'];
            $o = $file;

            // update pics
            self::q($db,"UPDATE leads SET pic_b='$b',pic_m='$m',pic_s='$s',pic_t='$t',pic_o='$o' WHERE leadid=$leadid ");

            return array('status'   => 'OK',
                         'message'  => 'Отлично!',
                         'url'      => $b,
                         'leadid'   => $leadid
                    );
        } else {
            return array(
                'status'   => 'error',
                'message'  => 'file not found'
            );
        }
    }

    public static function search($db,$q='') {

        $q = $db->escapeString($q);

        if ((!$q) || (strlen($q) < Setup::$MIN_SEARCH_LENGTH)) return;

        $qa = explode(" ",$q);
        $rxp_open = '[[:<:]]';
        $rxp_close = '[[:>:]]';
        
        //$rxp_open = '';
        //$rxp_close = '';

        // search in leads (title + text)
        $leads_fields = array('title','description','contact_name','contact_phone','contact_email','org_name','petition_org_name');
        $orgs_fields = array('chief_name','chief_contact');

        $sqll = "SELECT * FROM leads WHERE (deleted_ts IS NULL) AND (published_ts IS NOT NULL) AND (0 ";
        $sqlo = "SELECT * FROM orgs WHERE 1 AND (0 ";
        foreach ($qa as $qw) {
                if (strlen(utf8_decode($qw)) < Setup::$MIN_SEARCH_LENGTH) { continue; }
                foreach ($leads_fields as $f) {
                    $sqll.= " OR ( $f REGEXP '$rxp_open$qw$rxp_close' ) ";
                }
                foreach ($orgs_fields as $f) {
                    $sqlo.= " OR ( $f REGEXP '$rxp_open$qw$rxp_close' ) ";
                }
        }
        $sqll.= ' ) ';
        $sqlo.= ' ) ';

        $leads = self::getSqlArray($db, $sqll);
        $orgs = self::getSqlArray($db, $sqlo);

        return array('orgs'=>$orgs,'leads'=>$leads);
    }

    public static function editChief($db,$post) {

        $imgdir = 'orgs';
        $picpath = Settings::$IMG_ROOT.'/'.$imgdir.'/';
        $tmppath = Settings::$IMG_TMP;          // has trailing spash!

        $orig_contact = $post['chief_contact'];
        $contact = $db->escapeString($post['chief_contact']);
        $name = $db->escapeString($post['chief_name']);
        $orgid = $db->escapeString($post['orgid']);
        $unid = $db->escapeString($post['unid']);
        $fd = (isset($post['filedata'])?$db->escapeString($post['filedata']):'');

        // update chief data
        self::q($db,"UPDATE orgs SET chief_contact='$contact', chief_name='$name' WHERE orgid=$orgid ");

        if ($fd) {
            $filedata = (array)json_decode($fd);
            if (isset($filedata['size']) && ($filedata['size'] <= Settings::$MAX_FILE_SIZE)) {
                list($filename,$ext) = explode('.',$filedata['file']);
                $pics = self::preparePics($db, $orgid, $filedata['tmp'], $ext, $imgdir);
                
//Logger::log(var_export($pics,true));

                $b = $picpath.$pics['b'];
                $m = $picpath.$pics['m'];
                $s = $picpath.$pics['s'];
                $t = $picpath.$pics['t'];
                $o = $tmppath.$pics['o'];

                // update chief pics
                self::q($db,"UPDATE orgs SET chief_pic_b='$b',chief_pic_m='$m',chief_pic_s='$s',chief_pic_t='$t',chief_pic_o='$o' WHERE orgid=$orgid ");

            } else {
                if (isset($filedata['size']))
                return array(
                    'status'   => 'error',
                    'message'  => 'Картинка слишком большая, можно до 1М.'
                );
            }
        }


        return array('status'   => 'OK',
                     'message'  => 'Отлично!',
                     'pic'      => (isset($m)?$m:''),
                     'chief_name' => $name,
                     'chief_contact'=> nl2br($orig_contact)
                );
    }

    public static function getMainCounter($db,$orgid=0,$upcoming=false) {
        $orgid = $db->escapeString($orgid);
        $orgfilter = ($orgid?" AND (orgid=$orgid) ":"");
        $not = ($upcoming?'':'NOT');
        $res = self::getSingleValue($db, "SELECT SUM(amount) as value FROM leads WHERE (deleted_ts IS NULL) $orgfilter AND (published_ts IS $not NULL) ");
        $num = number_format($res*1000000, 0,'.',' ');
        return $num;
    }

    public static function getTotalLeadsPages($db,$review=false,$orgid=0) {
        $orgid = $db->escapeString($orgid);
        $orgfilter = ($orgid?" AND (orgid=$orgid) ":"");
        $forreview = ($review?"(published_ts IS NULL) AND ":"(published_ts IS NOT NULL) AND ");
        $sql = "SELECT count(*) as value FROM leads
                WHERE  $forreview (deleted_ts IS NULL) $orgfilter ";

        $amount = API::getSingleValue($db, $sql);
        return ceil($amount / Setup::$POSTS_PER_PAGE);
    }

    public static function getMainPage($db,$review=false,$orgid=0,$currentPage=1,$ids='') {

        $list = self::getLeadsList($db, $review, $orgid, $currentPage, $ids);
        $sum = self::getMainCounter($db,$orgid,false);

        return array(
            'list' => $list,
            'sum' => $sum
        );

    }

    public static function getLeadsList($db,$review=false,$orgid=0,$currentPage=1,$ids='') {

        $orgid = $db->escapeString($orgid);
        $currentPageid = $db->escapeString($currentPage);
        $selectIds = $db->escapeString(preg_replace('/[^\d,]+/','',$ids));

        $itemsPerPage = Setup::$POSTS_PER_PAGE;
        $offset = (($currentPage > 1)?(($currentPage-1)*$itemsPerPage):0);
        $paging = "LIMIT ".($offset?$offset.', ':'').$itemsPerPage;

        $orgid = $db->escapeString($orgid);
        $orgfilter = ($orgid?" AND (orgid=$orgid) ":"");
        $forreview = ($review?"(published_ts IS NULL) AND ":"(published_ts IS NOT NULL) AND");
        $orderby = ($review?"commented_ts DESC":"published_ts DESC");
        $sqlIds = ($selectIds?" AND leadid IN ($selectIds)":'');
        $sql = "SELECT * FROM leads
                WHERE  $forreview (deleted_ts IS NULL) $orgfilter $sqlIds
                ORDER BY $orderby $paging";
        
        $res = API::getSqlArray($db, $sql);
        return $res;
    }

    public static function addToLead($db,$post) {
        $data = $db->escapeArray($post);
        if (strlen($data['addtoleadtext']) > Setup::$COMMENT_LIMIT) {
            return array('status'=>'error','message'=>'Слишком длинный комментарий');
        }
        $text = $data['addtoleadtext'];
        $name = $data['user_name'];
        $leadid = $data['leadid'];
        $userid = $data['userid'];
        $ts = time();
        $cid = self::q($db,"INSERT INTO additions (addtext,addts,added_by,leadid) VALUES ('$text',$ts,$userid,$leadid) ");
        return array('status'   => 'OK',
                     'message'  => 'OK!',
                     'userid'   => $userid,
                     'text'     => nls2p(wordwrapUTF($text, 36, " ", true),'\n', true),
                     'leadid'   => $leadid,
                     'cid'      => $cid,
                     'ts'       => date("j.m.Y H:i",$ts)
                );
    }

    public static function addComment($db,$post) {
        $data = $db->escapeArray($post);
        if (strlen($data['comment']) > Setup::$COMMENT_LIMIT) {
            return array('status'=>'error','message'=>'Слишком длинный комментарий');
        }

        $comm = $data['comment'];
        $name = $data['user_name'];
        $leadid = $data['leadid'];
        $userid = $data['userid'];

        $lead = self::getLead($db,$leadid);
        $internal = ($lead['published_ts']?'':'Y');

        $ts = time();
        $cid = self::q($db,"INSERT INTO comments (userid,comment,created_ts,leadid,user_name,internal) VALUES ($userid,'$comm',$ts,$leadid,'$name','$internal') ");
        if (isset($cid) && ($cid > 0)) {
            $intsql = ($internal?" AND internal='Y' ":" AND ((internal IS NULL) OR (internal = '')) ");
            $cc = self::getSingleValue($db, "SELECT count(*) as value FROM comments WHERE leadid=$leadid AND deleted_ts IS NULL $intsql");
            self::q($db,"UPDATE leads SET comments_count = $cc,commented_ts=$ts WHERE leadid=$leadid");
        }
        self::writeTime(Setup::$COMMENT_COOKIE);
        return array('status'   => 'OK',
                     'message'  => 'OK!',
                     'userid'   => $userid,
                     'user_name'=> $name,
                     'comment'  => nls2p(wordwrapUTF($comm, 36, " ", true),'\n'),
                     'leadid'   => $leadid,
                     'cid'      => $cid,
                     'ts'       => date("j.m.Y H:i",$ts)
                );
    }

    public static function canComment() {
        // get last post time from cookie @TODO: record last post time in cookie
        if (isset($_COOKIE[Setup::$COMMENT_COOKIE])) {
            $ts = $_COOKIE[Setup::$COMMENT_COOKIE];
            $diff = time() - $ts;
            return ($diff > Setup::$COMMENT_TIMEOUT);  // last post was made less than XXX sec ago
        }
        return true;
    }

    public static function getOrgs($db,$order='name ASC',$filter='') {
        $sql = "SELECT * FROM orgs $filter ORDER BY $order";
        $res = API::getSqlArray($db, $sql);
        return $res;
    }

    public static function getLead($db,$id,$withcomments=true) {
        if (!isset($id) || !is_object($db)) return array();
        $leadid = $db->escapeString($id);
        if (!$leadid || !is_numeric($leadid)) {
            return array();
        }
        $sql = "SELECT * FROM leads WHERE leadid=$leadid";
        $res = API::getSqlHash($db, $sql);

        // get comments for the lead
        if ($withcomments) {
            $internal = ($res['published_ts']?'':'Y'); 
            $intsql = ($internal?" AND internal='Y' ":" AND (internal IS NULL || internal = '') ");
            $sql = "SELECT * FROM comments WHERE (leadid=$leadid) AND (deleted_ts IS NULL) $intsql ORDER BY commentid ASC";

            $comments = API::getSqlArray($db, $sql);
            $res['comments'] = $comments;
            $res['comments_count'] = count($comments);
        }
        return $res;
    }

    public static function getComments($db,$leadid,$internal=false,$page=1,$perpage=0) {

        $leadid = $db->escapeString($leadid);
        $page = $db->escapeString($page);
        $perpage = $db->escapeString($perpage);

        if (!$perpage) $perpage = Setup::$COMMENTS_PER_PAGE;

        $offset = (($page > 1)?(($page-1)*$perpage):0);
        $paging = "LIMIT ".($offset?$offset.', ':'').$perpage;
        $intsql = ($internal?" AND internal='Y' ":" AND (internal != 'Y') ");

        $sql = "SELECT * FROM comments WHERE (leadid=$leadid) AND (deleted_ts IS NULL) $intsql ORDER BY commentid ASC $paging";

        $comments = API::getSqlArray($db, $sql);
        $res['comments'] = $comments;
        $res['comments_count'] = count($comments);

        return $res;
    }

    public static function assignToExpert($db,$leadid,$me,$name) {

        $name = $db->escapeString($name);
        $id = $db->escapeString($leadid);
        $me = $db->escapeString($me);
        $lead = self::getLead($db, $id, false);

        if (!$lead['leadid']) {
            return array('status'=>'error','message'=>'Не найдено');
        }

        if (!$lead['preselected']) {
            $sqlSelected = ", preselected='Y'";
        } else {
            $sqlSelected = "";
        }

        $ts = time();
        $sql = "UPDATE leads SET booked_expertid=$me, expertname='$name', booked_ts=$ts $sqlSelected WHERE leadid=$id";
        API::q($db, $sql);
        return array('status'=>'OK','message'=>'Да все нормально.');
    }

    public static function deleteLead($db,$post,$me) {
        $id = $db->escapeString($post['id']);
        $me = $db->escapeString($me);
        $ts = time();
        $sql = "UPDATE leads SET deleted_ts=$ts, deleted_by=$me WHERE leadid=$id";
        API::q($db, $sql);
        $orgid = self::getSingleValue($db, "SELECT orgid as value FROM leads WHERE (leadid=$id)");
        if ($orgid) { self::updateOrgStats($db, $orgid); }
        return array('status'=>'OK','message'=>'Да все нормально.');
    }

    public static function deleteComment($db,$post,$me) {
        $id = $db->escapeString($post['id']);
        $me = $db->escapeString($me);
        $leadid = self::getSingleValue($db, "SELECT leadid as value FROM comments WHERE (commentid=$id)");
        $ts = time();
        $sql = "UPDATE comments SET deleted_ts=$ts, deleted_by=$me WHERE commentid=$id";
        API::q($db, $sql);
        $comments = self::getSingleValue($db, "SELECT count(*) as value FROM comments WHERE (leadid=$leadid) AND (deleted_ts IS NULL)");
        if (!$comments) $comments = 'NULL';
        API::q($db, "UPDATE leads SET comments_count=$comments WHERE (leadid=$leadid)");
        return array('status'=>'OK','message'=>'Да все нормально.');
    }

    public static function getLeadsForReview($db,$selected,$uid=0) {
        $uid = $db->escapeString($uid);
        $selectfilter = ($selected == 'selected') ? 
                        " AND (preselected = 'Y') " :
                        ( $selected == 'my' ? " AND (booked_expertid = $uid) " :  " AND (preselected IS NULL) " );
        $forreview = "(published_ts IS NULL) AND ";
        $orderby = "discovered_ts DESC";
        $sql = "SELECT * FROM leads
                WHERE  $forreview (deleted_ts IS NULL) $selectfilter
                ORDER BY $orderby ";
        $res = API::getSqlArray($db, $sql);
        return $res;
    }

    public static function canPost() {
        // get last post time from cookie @TODO: record last post time in cookie
        if (isset($_COOKIE[Setup::$POST_COOKIE])) {
            $ts = $_COOKIE[Setup::$POST_COOKIE];
            $diff = time() - $ts;
            return ($diff > Setup::$POST_TIMEOUT);  // last post was made less than XXX sec ago
        }
        return true;
    }

    public static function writeTime($cookie) {
        // save post time in cookie
        $ts = time();
        $expire = time() + 86400; // 1 day
        setcookie($cookie, $ts, $expire, "/", Setup::$BASE_DOMAIN);
    }

    public static function publishLead($db,$post,$uid) {
        $result = array('status'=>'OK','message'=>'/');
        unset($post['f']);
        $numbers = array('days','amount','orgid');
        $bypass = array('petition_text','petition_link','petition_org_name');
        $dataissue = false;
        $tosave = $db->escapeArray($post);
        $leadid = $tosave['leadid'];
        unset($tosave['leadid']);

        // check if it's a new org
        //
        if ($tosave['orgid'])
            $orgid = $tosave['orgid'];
        else
            $orgid = self::getOrgIdByName($db,$tosave['org_name']); 
            // checks if exists and creates a new one, if need be (returns 0 if no org for petition specified)

        // check if it's a new org for petition
        if ($tosave['petition_orgid'])
            $petition_orgid = $tosave['petition_orgid'];
        else //check if exists and creates a new one, if need be
            $petition_orgid = self::getOrgIdByName($db,$tosave['petition_org_name'],$tosave['petition_link']);       

        if (!$tosave['orgid']) $tosave['orgid'] = $orgid;
        if (!$tosave['petition_orgid']) $tosave['petition_orgid'] = $petition_orgid;

        $updates = array();
        foreach ($tosave as $k => $v) {
            if ((strlen($k)==0 || strlen($v)==0) && (!in_array($k, $bypass))) {
                $dataissue = true;
                break;
            }
            if (in_array($k,$numbers)) {
                if (is_numeric($v)) {
                    $updates[] = "$k = $v";
                } else {
                    $dataissue = true;
                    break;
                }
            } else {
                $updates[] = "$k = '$v'";
            }
        }
        if ($dataissue) {
            $result = array(
                'status' => 'error',
                'message'=>'Что-то не то с данными, которые вы ввели.
                    Посмотрите внимательно, ничего не пропустили, ничего не перепутали?
                    Например, срок исполнения и размер контракта должны быть числами.'
                );
            return $result;
        }
        $sqlupdate = implode(',',$updates);
        $ts = time();

        // update the lead & publish it
        self::q($db,"UPDATE leads SET $sqlupdate,published_ts=$ts WHERE leadid=$leadid");

        self::updateOrgStats($db, $orgid);

        $baseurl = Setup::$POST_BASE_URL;
        $result['message'] = "/$baseurl/$leadid";
        return $result;
    }

    public static function updateOrgStats($db,$orgid) {
        $orgid = $db->escapeString($orgid);
        $total_amount = self::getSingleValue($db, "SELECT SUM(amount) as value FROM leads WHERE (orgid=$orgid) AND (deleted_ts IS NULL) AND (published_ts IS NOT NULL)");
        if (!$total_amount) $total_amount = 0;
        $total_cases = self::getSingleValue($db, "SELECT count(*) as value FROM leads WHERE (orgid=$orgid) AND (deleted_ts IS NULL) AND (published_ts IS NOT NULL)");
        self::q($db,"UPDATE orgs SET total_amount=$total_amount,total_cases=$total_cases WHERE orgid=$orgid");
    }

    public static function getOrgIdByName($db,$name,$petition_page='') {
        $name = $db->escapeString($name);
        $petition_page = $db->escapeString($petition_page);
        $org = self::getSqlHash($db, "SELECT * FROM orgs WHERE name LIKE '%$name%'");
        if (count($org) > 0) {
            return $org['orgid'];
        } else {
            if (isset($name) && $name) return self::q($db,"INSERT INTO orgs (name,petition_page_url) VALUES ('$name','$petition_page')");
            else return 0;
        }
    }
    public static function getOrgNameById($db,$id) {
        $orgid = $db->escapeString($id);
        return self::getSingleValue($db,"SELECT name as value FROM orgs WHERE orgid=$orgid");
    }

    public static function getOrgById($db,$id) {
        $orgid = $db->escapeString($id);
        if (!$orgid || !is_numeric($orgid)) {
            return array();
        }
        return self::getSqlHash($db,"SELECT * FROM orgs WHERE orgid=$orgid");
    }
    
    public static function postCorruptionLead($db,$post) {
        $result = array('status'=>'OK','message'=>'Да, все нормально.');

        unset($post['f']);
        unset($post['recaptcha_challenge_field']);
        unset($post['recaptcha_response_field']);

        //$post = $db->escapeArray($post);

        $numbers = array('days','amount');
        $keys = array();
        $values = array();
        
        $dataissue = false;

        foreach ($post as $key => $value) {

            $k = strip_tags($db->escapeString(trim($key)));
            $v = strip_tags($db->escapeString(trim($value)));

            if (strlen($k)==0 || strlen($v)==0) {
                $dataissue = true;
                break;
            }
            array_push($keys, $k);
            if (in_array($key,$numbers)) {
                if (is_numeric($v)) {
                    array_push($values,$v);
                } else {
                    $dataissue = true;
                    break;
                }
            } else {
                array_push($values,"'$v'");
            }   
        }

        if ($dataissue) {
            $result = array(
                'status' => 'error',
                'message'=>'Что-то не то с данными, которые вы ввели.
                    Посмотрите внимательно, ничего не пропустили, ничего не перепутали?
                    Например, срок исполнения и размер контракта должны быть числами.'
                );
            return $result;
        }

        // add timestamps
        $ts = time();
        $allkeys = implode(',',$keys);
        $allvalues = implode(',',$values);
        $sql = "INSERT INTO leads ($allkeys,discovered_ts) VALUES ($allvalues,$ts)";
        self::q($db,$sql);
        self::writeTime(Setup::$POST_COOKIE);

        return $result;
    }

    

}
