<?php

class c_users_API extends API {


    public function resetUserCookieTo2($usercode) {
        $this->deleteCookie();
        setcookie(Setup::$USER_COOKIE, "$usercode", time() + 12096000, '/', Setup::$BASE_DOMAIN);
    }

    public function checkUser($usercode) {

        // check if this one gas account
        $user = $this->getUser($usercode);

        // if not - create one
        if (!is_array($user)) {
            $res = $this->createNewUser(null, false);
            if (Setup::$DEBUG) Logger::log("[checkUser] res: ".  var_export($res, true));
            $user['usercode'] = isset($res['message'])?$res['message']:'';
            setcookie(Setup::$USER_COOKIE, $user['usercode'], time() + 12096000, '/', Setup::$BASE_DOMAIN);
        }
        return $user;

    }

    // can get user by id, usercode or fb_userid
    //
    public function getUser($usercode,$field='usercode') {
        $usercode = $this->cleanInput($usercode);
        $user = $this->getSqlArray("SELECT * FROM users WHERE $field = '$usercode'");
        if (isset($user[0]) && is_array($user[0])) {
            // add IP address to user array
            //
            $user[0]['ip'] = $_SERVER['REMOTE_ADDR'];
            return $user[0];
        } else
            return false;
    }

    public function deleteCookie() {
        setcookie(Setup::$USER_COOKIE, "", time() - 3600, '/', Setup::$BASE_DOMAIN);
    }

    public function readCookie() {

        $usercode = '';
        $timezone = 0;

        if (isset($_COOKIE[Setup::$USER_COOKIE])) {
            $cookie = $_COOKIE[Setup::$USER_COOKIE];
            if (!strrpos($cookie, '|')) {
                // remove wrong cookie
                setcookie($_COOKIE[Setup::$USER_COOKIE],"",time()-3600);

                // create correct one
                $usercode = $cookie;
                $timezone = Setup::$DEFAULT_TIMEZONE;
                setcookie(Setup::$USER_COOKIE, "{$usercode}|{$timezone}", time() + 12096000, '/', Setup::$BASE_DOMAIN);
            } else {
                list($usercode,$timezone) = explode('|',$_COOKIE[Setup::$USER_COOKIE]);
            }   
        }
        return array($usercode,$timezone);
    }


    // $post['uc'] should contain 32 char unique usercode
    //
    public function createNewUser($post,$ajax=true) {

        $response = array(
            'status' => '',
            'message'=> ''
            );
        // validate post
        //
        if (!isset($post['uc']) && $ajax) {
            $response['status'] = 'error';
            $response['message']= 'request failure';
        } else {
            $db = $this->db;
            $uc = $db->escapeString(trim($post['uc']));

            if ((strlen($uc)!=32) && $ajax) {
                $response['status'] = 'error';
                $response['message']= 'corrupt input';
            } else {
                $unique = false;
                $reps = 3;

                if (!isset($uc) || (strlen($uc)!=32)) {
                    $ts = time();
                    $uc = md5($ts.'new');
                }

                // check if this user aleady exists
                //
                while (!$unique && $reps-- > 0) {
                    $sql = "SELECT count(*) as value
                            FROM users
                            WHERE usercode = '$uc'";
                    if ($this->getSingleValue($sql) > 0) {
                        $uc = substr(rand(1,9).$uc,0,32);
                    } else {
                        $unique = true;
                    }
                }

                $ts = time();
                $this->q("INSERT INTO users (usercode,ts,utc_delta) VALUES ('$uc',$ts,0)");
                if (!$ajax) {
                    $response['usercode'] = $uc;
                }
                $response['status'] = 'ok';
                $response['message']= $uc;
            }
        }
        return $response;

    }




    /*
     *
     *
     * **************************************************************
     *
     *
     *
     */



	
	
	public function getTopCities($nu=50) {
		$db = $this->db;
		$top = array();
		$sql = 
			" SELECT c.`city_id`,c.`city_name`,count(v.`city_id`) as venues ".
			" FROM `city` c LEFT JOIN `venue` v ON (v.`city_id`=c.`city_id`) ".
			" GROUP BY c.`city_id` ORDER BY venues DESC LIMIT $nu";

		$dbResult = $db->runSQL($sql);
		if ($dbResult) {
            $resultObj = $dbResult->fetch_object();
            while ($resultObj) {
            	$top[] = array(
				'id' => $resultObj->city_id,
				'name' => $resultObj->city_name,
				'venues' => $resultObj->venues
            	);
				$resultObj = $dbResult->fetch_object();
            }
            $dbResult->free();
        }
		return $top;
	}
	
	public function getLeaders($days=1,$num=10) {
		$db = $this->db;
		$ago = time() - $days*86400;
		$top = array();
		$sql = 
			" SELECT a.`user_id` as user_id,SUM(a.`points`) as points,u.`user_first_name` as name,".
			" SUM(a.`checkin`) as checkins ".
			" FROM `action` a LEFT JOIN `user` u ON (a.`user_id`=u.`user_id`) ".
			" WHERE a.`ts`>$ago GROUP BY a.`user_id` ORDER BY points DESC,checkins DESC LIMIT $num";

		$dbResult = $db->runSQL($sql);
		if ($dbResult) {
            $resultObj = $dbResult->fetch_object();
            while ($resultObj) {
            	$top[] = array(
				'id' => $resultObj->user_id,
				'points' => $resultObj->points,
				'checkins' => $resultObj->checkins,
				'name' => $resultObj->name    	
            	);
				$resultObj = $dbResult->fetch_object();
            }
            $dbResult->free();
        }
		return $top;
		
	}
	
	public function getTopUser($days=1) {
		$db = $this->db;
		$ago = time() - $days*86400;
		$top = array();
		$sql = "SELECT a.`user_id` as user_id,SUM(a.`points`) as points FROM `action` a WHERE a.`ts`>$ago GROUP BY a.`user_id` LIMIT 1";
		$dbResult = $db->runSQL($sql);
		if ($dbResult) {
            $resultObj = $dbResult->fetch_object();
            if ($resultObj) {
				$top['id'] = $resultObj->user_id;
				$top['points'] = $resultObj->points;
            }
            $dbResult->free();
        }
		return $top;
	}
	
	public function recordClaim($logged,$uid,$vid,$c='',$n='',$s='') {
		
		if (!$logged || ($logged != $uid) || !$vid) return false;
		
		$db = $this->db;
		
		$vid = $db->escapeString($vid);
		$uid = $db->escapeString($uid);

		if ($c) $c = $db->escapeString(strip_tags($c));
		if ($n) $n = $db->escapeString(strip_tags($n));

		// insert into claims
		if ($s) {
			$s = $db->escapeString(strip_tags($s));
		}
		$ts = time();
		@$this->q("INSERT INTO `claims` (`venue_id`,`user_id`,`phone`,`name`,`special`,`ts`) VALUES ( ".
		"$vid,$uid,'$c','$n','$s',$ts".
		") ");
		$cid = $db->getGeneratedId();
		
		// email to our email list a message with approve/deny links
		//
		$subject = 'Кто-то хочет прав владельца';
		$from = 'noreply@'.Setup::$BASE_DOMAIN;
		$body = ' Пользователь ('.$n.', '.$c.'): http://'.Setup::$BASE_DOMAIN.'/user/'.$uid.' '.
				' Место: http://'.Setup::$BASE_DOMAIN.'/venue/'.$vid.' '.
				' СП: ['.$s.'] '.
				' http://'.Setup::$BASE_DOMAIN.'/claim_approve/'.$cid.
				' --------- '.
				' http://'.Setup::$BASE_DOMAIN.'/claim_deny/'.$cid; 
		$headers = "From: {$from}";
		$good = true;
		try {
			$result = @mail(Setup::$CLAIM_EMAIL,$subject,$body,$headers);
			Logger::log("[recordClaim] claim email sent [cid=$cid,vid=$vid,result=$result] ");
		} catch(Exception $ex) {
			Logger::log("[recordClaim] cannot send email to us");
			$good = false;
		}
		
		// send also confirmation email to user who claimed the venue
		//
		if ($cid && $good) {
			
			$email = $this->getSingleValue("SELECT `user_email` as value FROM `user` WHERE `user_id`=$uid ");
			
			$subject = 'Ratingo: мы приняли вашу заявку';
			$from = 'noreply@'.Setup::$BASE_DOMAIN;
			$body = ' Пользователь: http://'.Setup::$BASE_DOMAIN.'/user/'.$uid.' '.
					' Место: http://'.Setup::$BASE_DOMAIN.'/venue/'.$vid.' '.
					' Как только мы закончим ее рассматривать, дадим знать. С уважением, робот.'; 
			$headers = "From: {$from}";
			$good = true;
			try {
				@mail($email,$subject,$body,$headers);
			} catch(Exception $ex) {
				Logger::log("[recordClaim:notification] Cannot send email to $email");
				$good = false;
			}
		}
		
		// update venue to make sure this user cannot be neither king nor opener
		//
		$king = $this->getSingleValue("SELECT `best_customer_id` as value FROM `venue` WHERE `id`=$vid ");
		$first = $this->getSingleValue("SELECT `first_customer_id` as value FROM `venue` WHERE `id`=$vid ");
		
		if (($king == $uid) || ($first == $uid)) {
			try {
			@$this->q("UPDATE `venue` SET ".
			(($king==$uid)?"`best_customer_id`=0 ":"").
			(($first==$uid)?(($king==$uid)?",":"")."`first_customer_id`=0 ":"").
			"WHERE `id`=$vid ");
			} catch (Exception $ex) {
				Logger::log("[recordClaim: update] Cannot update $vid with $uid");
				$good = false;
			}
		}
		
		return $good;
	}
	
	public function getMyIdols($user) {
		$db = $this->db;
		$idols = array();
		if (!is_object($user)) return $idols;
		$me = $user->get_user_id();
		
		$sql = 
		"SELECT f.`friend_id`,u.`user_avatar_small`,u.`user_first_name`,u.`user_last_name` ".
		"FROM `friendship` f ".
		"LEFT JOIN `user` u ON (f.`friend_id` = u.`user_id`) ".
		"WHERE ((f.`user_id`=$me) AND (f.`confirmed_ts`=0) AND (f.`requested_ts`!=0) ) ORDER BY u.`user_score` DESC";
		$dbResult = $db->runSQL($sql);
		if ($dbResult) {
            $resultObj = $dbResult->fetch_object();
            while ($resultObj) {
				$idols[] = array(
					'id'		=> $resultObj->friend_id,
					'first_name'=> $resultObj->user_first_name,
					'last_name'	=> $resultObj->user_last_name,
					'avatar'	=> $resultObj->user_avatar_small
				);
                
				$resultObj = $dbResult->fetch_object();
            }
            $dbResult->free();
        }
		return $idols;
	}
	
	public function getMyFans($user) {
		$db = $this->db;
		$fans = array();
		if (!is_object($user)) return $fans;
		$me = $user->get_user_id();
		
		$sql = 
		"SELECT f.`user_id`,u.`user_avatar_small`,u.`user_first_name`,u.`user_last_name` ".
		"FROM `friendship` f ".
		"LEFT JOIN `user` u ON (f.`user_id` = u.`user_id`) ".
		"WHERE ((f.`friend_id`=$me) AND (f.`confirmed_ts`=0) AND (f.`requested_ts`!=0) ) ORDER BY u.`user_score` DESC";
		$dbResult = $db->runSQL($sql);
		if ($dbResult) {
            $resultObj = $dbResult->fetch_object();
            while ($resultObj) {
				$fans[] = array(
					'id'		=> $resultObj->user_id,
					'first_name'=> $resultObj->user_first_name,
					'last_name'	=> $resultObj->user_last_name,
					'avatar'	=> $resultObj->user_avatar_small
				);
                
				$resultObj = $dbResult->fetch_object();
            }
            $dbResult->free();
        }
		return $fans;
	}
	
	private function removeFriend($me,$him) {
		$myfriends = $this->getSingleValue("SELECT `friends` as value FROM `user` WHERE `user_id`=$me");
		if ($myfriends) {
			$mf = explode(',',$myfriends);
			$newf = array();
			foreach ($mf as $f) {
				if ($f != $him) {
					array_push($newf,$f);
				}
			}
			$myf = implode(',',$newf);
			@$this->q("UPDATE `user` SET `friends`='$myf' WHERE `user_id`=$me");
		}
		@$this->q("UPDATE `friendship` SET `confirmed_ts`=0 WHERE `user_id`=$me AND `friend_id`=$him");
		@$this->q("UPDATE `friendship` SET `requested_ts`=0 WHERE `user_id`=$me AND `friend_id`=$him");
	}
	
	public function deleteFriend($me,$him) {
		if (!$me || !$him) return 0;
		$this->removeFriend($me,$him);
		$this->removeFriend($him,$me);
	}
	
	public function confirmFriendshipRequest($from,$to) {
		if (!$from || !$to) return 0;
		$ts = time();
		$this->q("UPDATE `friendship` SET `confirmed_ts`=$ts WHERE (`user_id`=$to AND `friend_id`=$from) OR (`user_id`=$from AND `friend_id`=$to) ");
		
		$this->q("UPDATE `user` SET `friends`=concat(`friends`,',$from') WHERE `user_id`=$to");
		$this->q("UPDATE `user` SET `friends`=concat(`friends`,',$to') WHERE `user_id`=$from");
	}
	

	
	public function sendFriendshipRequest($from,$to) {
		if (!$from || !$to) return 0;
		$ts = time();
		$this->q(
			"INSERT INTO `friendship` (`user_id`,`friend_id`,`requested_ts`,`confirmed_ts`) VALUES ".
			"($from,$to,$ts,0)");
		 	
		return 1;
	}
	
	public function wasFriendshipRequestSent($from,$to) {
		if (!$from || !$to) return 0;
		return 
		$this->getSingleValue(
		"SELECT `requested_ts` as value FROM `friendship` WHERE ((`user_id`=$from) AND (`friend_id`=$to))");
	}
	
	
	function setAward($award,$uid,$aid,$vid,$add_points,$awards,$action=0) {
		$award_id = $award['id'];
		$icon = $award['icon'];
		$about = $award['about'];
		$ts = time();
		
		if ($action) {
			@$this->q(
			"INSERT INTO `action` (`venue_id`,`user_id`,`recommend`,`body`,`ts`,`icon`,`checkin`,`mobile`,`points`,`award`) VALUES ".
			"($vid,$uid,0,'$about',$ts,'$icon',0,0,0,1)");
		} else {
			@$this->q(
			"INSERT INTO `user_awards` (`award_id`,`user_id`,`ts`,`action_id`,`venue_id`,`icon`) VALUES ".
			"($award_id,$uid,$ts,$aid,$vid,'$icon')");	
		}
		
		// TODO 'wow'	=> ($user->get_gender() == 'female'?Setup::$AWARD_FIRST_CUSTOMER['wow2']:Setup::$AWARD_FIRST_CUSTOMER['wow']));
		
		$add_points+= $award['points'];
		$awards['awards'][] = array(
			'icon' 	=> $award['icon'],
			'wow'	=> $award['wow']);
		$awards['points'][] = array(
			'about' => $award['about'],
			'points'=> $award['points']);
		return array($add_points,$awards);
	}
	
	public function checkAwards($user,$aid,$vid,$type='место') {
		
		$awards = array();
		$addpoints = 0;
		
		if (!(is_object($user)) || !$aid || !$vid) return 0;	
		
		$ts = time();
		$day = date("j",$ts);	// 1-31
		$month = date("n",$ts); // 1-12
		$year = date("Y",$ts);	// YYYY

		$add_points = Setup::$AWARD_CHECKIN['points']; // checkin 
		$uid = $user->get_user_id();
		
		$total_venues = $this->getSingleValue("SELECT count(distinct(`venue_id`)) as value FROM `action` WHERE `user_id`=$uid AND `checkin`=1");
		$total = $this->getSingleValue("SELECT count(`id`) as value FROM `action` WHERE `user_id`=$uid AND `checkin`=1");
		$checkins = $this->getSingleValue("SELECT count(`id`) as value FROM `action` WHERE `user_id`=$uid AND `venue_id`=$vid AND `checkin`=1");
	
		$awards['checkins'] = $checkins;
		
		$awards['points'][] = array(
			'about' => Setup::$AWARD_CHECKIN['about'],
			'points'	=> Setup::$AWARD_CHECKIN['points']);
		$icon = '';
		
		switch ($total) {
			case 1:	 list($add_points,$awards) = $this->setAward(Setup::$AWARD_FIRST_CHECKIN,$uid,$aid,$vid,$add_points,$awards); break;
			case 10: list($add_points,$awards) = $this->setAward(Setup::$AWARD_10_CHECKINS,$uid,$aid,$vid,$add_points,$awards); break;
			case 25: list($add_points,$awards) = $this->setAward(Setup::$AWARD_25_CHECKINS,$uid,$aid,$vid,$add_points,$awards); break;
			case 50: list($add_points,$awards) = $this->setAward(Setup::$AWARD_50_CHECKINS,$uid,$aid,$vid,$add_points,$awards); break;
			case 100:list($add_points,$awards) = $this->setAward(Setup::$AWARD_100_CHECKINS,$uid,$aid,$vid,$add_points,$awards); break;
		}
		
		if (in_array($total,array(1,10,25,50,100)))
			@$this->q(
				"INSERT INTO `action` (`venue_id`,`user_id`,`recommend`,`body`,`ts`,`icon`,`checkin`,`mobile`,`points`,`award`) VALUES ".
				"($vid,$uid,0,'$about',$ts,'$icon',0,0,0,1)");
		
		// FIRST/BEST CUSTOMER
		//
		if ($user->get_avatar_small()) { // has photo
			$users = $this->getPastVisitors($vid,3000); // get all user_ids in this venue for the past 3000 days
			if ((count($users) == 1) && ($users[$uid] == 1)) { // FIRST CUSTOMER
				list($add_points,$awards) = $this->setAward(Setup::$AWARD_FIRST_CUSTOMER,$uid,$aid,$vid,$add_points,$awards,1); // action=1, not award
				@$this->q("UPDATE `venue` SET `first_customer_id`=$uid WHERE `id`=$vid");
			} 
			// reset users for the past month for best user check
			$users = $this->getPastVisitors($vid,30); // get all user_ids in this venue for the past month
			arsort($users);
			$leaders = array_keys($users);
			if (is_array($leaders) && ($leaders[0] == $uid) && ($users[$uid] > 4)) { // FOUND BEST CUSTOMER
				list($add_points,$awards) = $this->setAward(Setup::$AWARD_BEST_CUSTOMER,$uid,$aid,$vid,$add_points,$awards,1);
				$this->q("UPDATE `venue` SET `best_customer_id`=$uid WHERE `id`=$vid");
			} 
		}
		
		if (($day==17)&&($month==3)&&in_array($type,array('бар','паб'))) {
			// see if this user already has this award
			$hasit = $this->getSingleValue("SELECT `id` AS value FROM `user_awards` WHERE (`user_id`=$uid) AND (`award_id`=".Setup::$AWARD_IRISH['id'].")");
			if(!$hasit) list($add_points,$awards)=$this->setAward(Setup::$AWARD_IRISH,$uid,$aid,$vid,$add_points,$awards); 
		}	// ST. PATRICK'S DAY!
		
		
		

		
		// LOVING CUSTOMER
		
		// SUPER CUSTOMER
		
		// PIONEER
		
		// <- TBD
		
		$awards['addpoints'] = $add_points;
		
		$this->q("UPDATE `action` SET `points`=$add_points WHERE `id`=$aid"); // total points for this action
		$this->q("UPDATE `user` SET `user_score`=`user_score`+$add_points,`user_total_checkins`=$total,`user_total_venues`=$total_venues WHERE `user_id`=$uid");	

		return $awards;
	}
	
	public function getPastVisitors($vid,$days) {
		$db = $this->db;
		if (!$vid) return 0;
		
		$users = array();
		$then = time() - $days*86400;
		$sql = "SELECT `user_id` as id FROM `action` WHERE `venue_id`=$vid AND `ts` > $then AND `checkin`=1";
		$dbResult = $db->runSQL($sql);
        if ($dbResult) {
            $resultObj = $dbResult->fetch_object();
            while ($resultObj) {
            	if (isset($users[$resultObj->id])) $users[$resultObj->id]++;
            	else $users[$resultObj->id] = 1;
				$resultObj = $dbResult->fetch_object();
            }
            $dbResult->free();
        }
		return $users;
	}
	
	public function getAwardByName($name) {
		
		$db = $this->db;
		$award = array();
		if (!$user) return 0;
		$name = $db->escapeString($name);
		
		$sql = "SELECT * FROM `award` WHERE `award_name`='$name' ";
		
		if ($dbResult) {
            $resultObj = $dbResult->fetch_object();
            if ($resultObj) {
				$award = array(
					'id'		=> $resultObj->award_id,
					'name'		=> $resultObj->award_name,
					'icon'		=> $resultObj->award_icon,
					'points'	=> $resultObj->award_add_points
				);
            }
            $dbResult->free();
        }
		return $award;
	}
	
	public function getMyAwards($user) {
		
		$allawards = array(
			Setup::$AWARD_CHECKIN['id']=>Setup::$AWARD_CHECKIN,
			Setup::$AWARD_FIRST_CHECKIN['id']=>Setup::$AWARD_FIRST_CHECKIN,
			Setup::$AWARD_BEST_CUSTOMER['id']=>Setup::$AWARD_BEST_CUSTOMER,
			Setup::$AWARD_FIRST_CUSTOMER['id']=>Setup::$AWARD_FIRST_CUSTOMER,
			Setup::$AWARD_LOVING_CUSTOMER['id']=>Setup::$AWARD_LOVING_CUSTOMER,
			Setup::$AWARD_PIONEER['id']=>Setup::$AWARD_PIONEER,
			Setup::$AWARD_SUPER_CUSTOMER['id']=>Setup::$AWARD_SUPER_CUSTOMER,
			Setup::$AWARD_10_CHECKINS['id']=>Setup::$AWARD_10_CHECKINS,
			Setup::$AWARD_25_CHECKINS['id']=>Setup::$AWARD_25_CHECKINS,
			Setup::$AWARD_50_CHECKINS['id']=>Setup::$AWARD_50_CHECKINS,
			Setup::$AWARD_100_CHECKINS['id']=>Setup::$AWARD_100_CHECKINS,
			Setup::$AWARD_WRITER['id']=>Setup::$AWARD_WRITER,
			Setup::$AWARD_BEER['id']=>Setup::$AWARD_BEER,
			Setup::$AWARD_SPORT['id']=>Setup::$AWARD_SPORT,
			Setup::$AWARD_CULT['id']=>Setup::$AWARD_CULT,
			Setup::$AWARD_NIGHT['id']=>Setup::$AWARD_NIGHT,
			Setup::$AWARD_IRISH['id']=>Setup::$AWARD_IRISH,
			Setup::$AWARD_SHOP['id']=>Setup::$AWARD_SHOP,
			Setup::$AWARD_TRAVEL['id']=>Setup::$AWARD_TRAVEL,
			Setup::$AWARD_MULTI['id']=>Setup::$AWARD_MULTI,
			Setup::$AWARD_COFFEE['id']=>Setup::$AWARD_COFFEE
		);

		$db = $this->db;
		$awards = array();
		
		if (!is_object($user)) return $ranks;
		
		$sql = "SELECT u.`award_id`,u.`icon`,u.`venue_id` ".
		" FROM `user_awards` u ".
		" WHERE u.`user_id`=".$user->get_user_id().
		" ORDER BY u.`ts` DESC";
		$dbResult = $db->runSQL($sql);
		
        if ($dbResult) {
            $resultObj = $dbResult->fetch_object();
            while ($resultObj) {
				$awards[] = array(
					'title'	=> $allawards[$resultObj->award_id]['name'],
					'icon'	=> $resultObj->icon,
					'venue'	=> $resultObj->venue_id
				);
                
				$resultObj = $dbResult->fetch_object();
            }
            $dbResult->free();
        }
        
        return $awards;
	}
	
	public function getMyFriends($user) {
		
		$db = $this->db;
		$friends = array();
		if (!is_object($user)) return $friends;
		$f = $user->get_friends();
		if (!$f) return $friends;
		
		$sql = "SELECT `user_id`,`user_avatar_small`,`user_first_name`,`user_last_name` FROM `user` WHERE `user_id` IN ($f) ORDER BY `user_score` DESC";
		$dbResult = $db->runSQL($sql);
		if ($dbResult) {
            $resultObj = $dbResult->fetch_object();
            while ($resultObj) {
				$friends[] = array(
					'id'		=> $resultObj->user_id,
					'first_name'=> $resultObj->user_first_name,
					'last_name'	=> $resultObj->user_last_name,
					'avatar'	=> $resultObj->user_avatar_small
				);
                
				$resultObj = $dbResult->fetch_object();
            }
            $dbResult->free();
        }
		return $friends;
	}
	
	public function getMyPlaces($user) {
		$db = $this->db;
		$places = array();
		
		if (!is_object($user)) return $places;
		
		$sql = 
		"SELECT DISTINCT(a.`venue_id`) as vid,v.`name`,v.`type`,c.`city_name`,c.`city_id`, ".
		" v.`first_customer_id`,v.`best_customer_id` ".
		" FROM `action` a ".
		" LEFT JOIN `venue` v ON (a.`venue_id`=v.`id`) ".
		" LEFT JOIN `city` c ON (v.`city_id`=c.`city_id`) ".
		" WHERE a.`user_id`=".$user->get_user_id().
		" ORDER BY a.`ts` DESC";
		$dbResult = $db->runSQL($sql);
		
        if ($dbResult) {
            $resultObj = $dbResult->fetch_object();
            while ($resultObj) {
				$places[] = array(
					'venue'		=> $resultObj->name,
					'venue_id'	=> $resultObj->vid,
					'type'		=> $resultObj->type,
					'city'		=> $resultObj->city_name,
					'city_id'	=> $resultObj->city_id,
					'first'		=> $resultObj->first_customer_id,
					'best'		=> $resultObj->best_customer_id
				);
                
				$resultObj = $dbResult->fetch_object();
            }
            $dbResult->free();
        }
		
		return $places;
	} 
	
	public function updateSetting($uid,$setting,$checked) {
		
		$db = $this->db;
		$uid = $db->escapeString($uid);
		$setting = $db->escapeString($setting);
		
		$sql = "UPDATE `user` SET `settings_$setting` = ".($checked=='true'?1:0)." WHERE `user_id`=$uid";
		$db->runSQL($sql);
		
	}
	

    public function saveUser($userName,$email,$password,$regMethod,$city,$gender,$lastName='',$cell='',$cell_entered='',$photo='',$id=0) {
        $db = $this->db;
        $userName = $db->escapeString($userName);
        $passwd = $db->escapeString($password);
        $email = $db->escapeString($email);
        $gender = $db->escapeString($gender);
        $lastName = $db->escapeString($lastName);
        $cell = $db->escapeString($cell);
        $cell_entered = $db->escapeString($cell_entered);
		$registrationMethod = $db->escapeString($regMethod);
		$photo = $db->escapeString($photo);

        $sql = '';
        if ($id) {
            $sql = "UPDATE `user` set `user_email`='$email', `user_first_name`='$userName'"
                    .($passwd ? ", `user_password`='$passwd'" : '')
                    ." where `user_id`=$id";
        } else {
        	
        	// process city
        	//
        	if (!$city) {
        		return false;
        	}
        	
        	// check if city exists, if not - create
        	//
        	
        	// workaround for St. Petersburg (@TODO: add more workrounds as we go in separated function)
        	//
        	if (($city == 'Питер') ||
        		($city == 'С Петербург') ||
        		($city == 'Ст Петербург') ||
        		($city == 'Ст.-Петербург') ||
        		($city == 'Санкт Петербург') ||
        		($city == 'С.-Петербург') ||
        		($city == 'Петербург')
        		
        	){
        		$city = 'Санкт-Петербург';
        	}
        	
        	$city_id = $this->checkCity($city);
        	
        	
            $sql = "INSERT INTO `user` ("
            		."`user_first_name`, `user_email`, `user_password`,`user_city_id`,`user_gender`,"
            		."`user_last_name`,`user_cell`,`user_cell_entered`,"
                    ."`user_reg_date`, `user_status`,`reg_method`,`settings_send_monthly_email`,"
                    ."`settings_show_phone`,`settings_show_sn`,`settings_send_email_friends_checkin`,"
                    ."`settings_send_sms_friends_checkin`,`settings_send_sms_friendship_request`,`friends`,`user_score`)"
                    ." VALUES ("
                    ."'$userName','$email','$passwd',$city_id,'$gender','$lastName','$cell','$cell_entered',now(), "
                    .(Setup::$VERIFICATION_STATUS_NONE+0).",'$registrationMethod',1,1,1,1,1,1,'',0)";
        }
        
        return $db->runSQL($sql);
    }
    
    public function changeCity($uid,$city) {
    	$db = $this->db;
    	$uid = $db->escapeString(trim($uid));
    	$city = $db->escapeString(trim($city));
    	$city_id = $this->checkCity($city);
    	@$this->q("UPDATE `user` SET `user_city_id`=$city_id WHERE `user_id`=$uid ");
    }
    
    public function checkCity($city) {
    	$db = $this->db;
    	$c = strtolower($db->escapeString(trim($city)));
    	$sql = "SELECT city_id,LOWER(`city_name`) as cityname FROM `city` WHERE LOWER(`city_name`) = '$c'";
		$dbResult = $db->runSQL($sql);
		$id = 0;
        if ($dbResult) {
        	$resultObj = $dbResult->fetch_object();
            if ($resultObj) {
            	$id = $resultObj->city_id;
            }
            $dbResult->free();
            if ($id) return $id;
        }
        
        // city does not exist, create it
        //
		$c1 = $db->escapeString(trim($city));
        $sql = "INSERT INTO `city` (`city_name`) VALUES ('$c1')";
        $dbResult = $db->runSQL($sql);
        
        return $db->getGeneratedId();
    }
    
    public function saveVerificationCode($verificationCode, $userId, $status) {
        $db = $this->db;
        $userId = (int)$userId;
        $code = $db->escapeString($verificationCode);
        $status = (int)$status;
        $ts = time();
        $sql = "UPDATE `user` SET `email_ver_code`='$code', ".
			   "`email_ver_code_ts`=$ts, `user_status`=$status  ".
			   " where `user_id`=$userId";
        return $db->runSQL($sql);
    }
    
    public function checkNewUser($userName='', $email=null) {
    	
        $result = 0;
        $db = $this->db;

        $sql = '';
        if (Setup::$SCREEN_NAME_UNIQUE) {
            $email = strtolower($db->escapeString($email));
            $userName = strtolower($db->escapeString($userName));
            $sql = "SELECT LOWER(`user_first_name`) as username, `user_email` FROM `user` u WHERE "
                ." `user_first_name`='$userName'".($email ? " or `user_email`='$email'" : '');
        } else {
            $email = strtolower($db->escapeString($email));
            $sql = "SELECT `user_first_name`, `user_email` FROM `user` u WHERE "
                    ." `user_email`='$email'";
        }
        $dbResult = $db->runSQL($sql);
        if ($dbResult) {
            $resultObj = $dbResult->fetch_object();
            while ($resultObj) {
                if (Setup::$SCREEN_NAME_UNIQUE) {
                    if ($resultObj->username == $userName) {
                        $result |= Settings::$NAME_EXISTS;
                    }
                }
                if ($email && strtolower($resultObj->user_email) == strtolower($email)) {
                    $result |= Setup::$EMAIL_EXISTS;
                }
                $resultObj = $dbResult->fetch_object();
            }
            $dbResult->free();
        }
        return $result;
    }
	
    public function getUsersFromIpWithStatus($ip, $status) {
    	
        $result = null;
        $db = $this->db;
        $ip = $db->escapeString($ip);
        $status = (int)$status;

        $sql = "SELECT `user_id`,`user_first_name`, `user_email`, `user_password`,".
               "`user_status`, `ip_address`, `user_reg_date` ".
               " from `user` where `ip_address`='$ip' and `user_status`=$status";

        $dbResult = $db->runSQL($sql);
        if ($dbResult) {
            $resultObj = $dbResult->fetch_object();
            if ($resultObj && $returnArray) {
                $result = array();
            }
            while ($resultObj) {
            	$id = $resultObj->id;
                $pwd = (property_exists($resultObj, 'password') ? $resultObj->password : null);
                $email = (isset($resultObj->email) ? $resultObj->email : null);
                
            	if ($returnArray) {
                    $result[] = array('id'=>$id,'email'=>$email,'password'=>$pwd);
                    $resultObj = $dbResult->fetch_object();
                } else {
                    $result = array('id'=>$id,'email'=>$email,'password'=>$pwd);
                    break;
                }
            }

            $dbResult->free();
        }

        return $result;
    }
	
	public function updateProfile($userId,$email,$username,$password='',$aboutme='',$sendupdates=0) {
		
		$db = $this->db;
		$avatar = '';
		$icon = '';
		
		$email = $db->escapeString(trim($email));
		$username = $db->escapeString(trim($username)); 
		$username = ''; // users cannot change username
		
		$password = $db->escapeString(trim($password));
		$aboutme = $db->escapeString(trim($aboutme));
		$sendupdates = $db->escapeString(trim($sendupdates));
		
		$pics = makePics('users',$userId,array(Settings::$AVATAR_SIZE,Settings::$ICON_SIZE));
		
		if (isset($pics[0]) && strpos($pics[0],'error') === false) { $avatar = $pics[0]; }
		if (isset($pics[1]) && strpos($pics[1],'error') === false) { $icon = $pics[1]; }
		
		if (!$email && !$username && !$password && !$aboutme) return;
		
		$sql = 
		"UPDATE `user` SET ".
		"`sendupdates`=$sendupdates ".
		($email?", `email`='$email'":"").
//		($username?"`username`='$username'".($password||$aboutme||$avatar?", ":""):"").
		($password?", `password`='$password'":"").
		($aboutme?", `aboutme`='$aboutme'":"").
		($avatar?", `avatar`='$avatar',`icon`='$icon' ":"").
		"WHERE `id`=$userId ";
		
		$dbResult = $db->runSQL($sql);
	}
	
    public function getUsersList($itemsPerPage=1,$currentPage=1) {
    	
    	$db = $this->db;
		$orderBy = "rating";
		$offset = (($currentPage > 1)?(($currentPage-1)*$itemsPerPage):0);
		
		$sql = 
		"SELECT ".$this->USER_PROFILE_FIELDS." FROM `user` u ".
		"WHERE 1 ".
		"ORDER BY u.`".$orderBy."` DESC ".
		"LIMIT ".($offset?$offset.', ':'').$itemsPerPage;
		
		$sqlCount =  "SELECT count(*) as number FROM `user` u ";
		
		return $this->getUsersSQL($sql,$sqlCount);
    }
    
    private function getUsersSQL($sql,$sqlCount='') {
    	
    	$db = $this->db;
    	$result = null;

        $dbResult = $db->runSQL($sql);
        if ($dbResult) {
            $resultObj = $dbResult->fetch_object();
            if ($resultObj) {
                $result = array();
            }
            $post = null;
            while ($resultObj) {
				$id = $resultObj->id;
                $username = $resultObj->username;
                $email = $resultObj->email;

                $user = new c_users_GenericProfile($id,$username,$email);
                
                $user->setPostsCount($resultObj->submitted);
                $user->setCommentsCount($resultObj->commented);
                $user->setVotesCount($resultObj->voted);
                $user->setRating($resultObj->rating);
                $user->setRank($resultObj->rank);
                $user->setAboutMe($resultObj->aboutme);
                $user->setRegisteredOn($resultObj->registered_on);
                $user->setStatus($resultObj->status);
                $user->setAvatar($resultObj->avatar);
                $user->setIcon($resultObj->icon);
                $user->setLastLoginOn($resultObj->last_login_on);
                
                $result[] = $user;
                
                $resultObj = $dbResult->fetch_object();
            }
        }
        $dbResult->free();
        
		return new GenericList($result,($sqlCount?$this->getSingleValue($sqlCount):count($result)),0);
    }

    public function saveProfile($user,$picfield) {
    	
    	if (!is_object($user)) return null;
    	
    	// read ref cookie
    	$ref = 0; 
    	$src = '';
        $arr = array();
		if (isset($_COOKIE["ref"]) ) {
			parse_str($_COOKIE["ref"], $arr);
			if (is_array($arr)) {
				$ref = $arr['ref'];
				$src = $arr['src'];
			}
		}
    	
    	$db = $this->db;
    	
    	$fn = $user->get_first_name();
        $ln = $user->get_last_name();
        $city = $user->get_city();

        if ($user->get_city_id()!=999) {
        	$city_id = $user->get_city_id(); // old or new city id
        }
        if ($city) {
        	// city was manually reset, checking
        	$city_id = $this->checkCity($city);
        }
        $email = $user->get_email();
        $g = $user->get_gender();
        $ce = $user->get_cell_entered();
        $c = preg_replace('/[^0-9\+]/','',$ce);

    	// workaround for St. Petersburg (@TODO: add more workrounds as we go in separated function)
        //
        if (($city == 'Питер') ||
        	($city == 'С Петербург') ||
        	($city == 'Ст Петербург') ||
        	($city == 'Ст.-Петербург') ||
        	($city == 'Санкт Петербург') ||
        	($city == 'С.-Петербург') ||
        	($city == 'Петербург')
        	
        ){
        	$city = 'Санкт-Петербург';
        }
        
        if (!$city_id) $city_id = $this->checkCity($city);
        $id = $user->get_user_id();
        $pwd = '';
        $piconly = false;
        if ($user->get_password_updated()) {
        	$pwd = md5($user->get_password());
        }
        
        $avatar = '';
        $icon = '';
        $ts = time();
        
        if (!$id) {
        	$sql = "INSERT INTO `user` ("
				."`user_first_name`, `user_email`, `user_password`,`user_city_id`,`user_gender`,"
				."`user_last_name`,`user_cell`,`user_cell_entered`,"
				."`user_reg_date`, `user_status`,`reg_method`,`settings_send_monthly_email`,"
				."`settings_show_phone`,`settings_show_sn`,`settings_send_email_friends_checkin`,"
				."`settings_send_sms_friends_checkin`,`settings_send_sms_friendship_request`,"
				."`friends`,`user_score`,`ref`,`src`)"
				." VALUES ("
				."'$fn','$email','$pwd',$city_id,'$g','$ln','$c','$ce',now(), "
				.(Setup::$VERIFICATION_STATUS_NONE+0).",'web',1,1,1,1,1,1,'',0,$ref,'$src')";
			$dbResult = $db->runSQL($sql);
			$id = $db->getGeneratedId();
        	$piconly = true;
        }

    	$pics = makePics($picfield,$id,array(Setup::$AVATAR_SIZE,Setup::$ICON_SIZE));
		
		if (isset($pics[0]) && strpos($pics[0],'error') === false) { $avatar = $pics[0]; }
		if (isset($pics[1]) && strpos($pics[1],'error') === false) { $icon = $pics[1]; }	
    	
        if ($id) {
	    	$sql = 
	    		"UPDATE `user` SET ".
	    		(!$piconly?"`user_first_name`='$fn',`user_last_name`='$ln',`user_city_id`=$city_id,`user_email`='$email',":"").
	    		(!$piconly?"`user_cell_entered`='$ce',`user_cell`='$c',`user_gender`='$g'":"").
	    		(!$piconly?($pwd?",`user_password`='$pwd'":""):"").
	    		($piconly?"`settings_updated_ts`=$ts":"").
	    		($avatar?",`user_avatar_big`='$avatar'":"").
	    		($icon?",`user_avatar_small`='$icon'":"").
	    		" WHERE `user_id`=$id";
	    		
	    	$dbResult = @$db->runSQL($sql);
        } 
    }
    
    public function recordLogin($user) {
    	if (!is_object($user)) return;
    	if (strpos($_SERVER['REMOTE_ADDR'],':')) { 
    		list($ip,$port) = explode(':',(isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:':'));
    	} else {
    		$ip = $_SERVER['REMOTE_ADDR'];
    	}
    	$ts = time();
    	$id = $user->get_user_id();
    	@$this->q("UPDATE `user` SET `ip_address`='$ip',`user_last_login_ts`=$ts WHERE `user_id`=$id");
    }
    
	public function getProfile($userId,$cookie='',$me=null) {

		if (!$userId) return null;

		$db = $this->db;
		
		$userId = $db->escapeString(trim($userId));
		$cookie = $db->escapeString(trim($cookie));
		
		$mid = 0;
		if (is_object($me)) $mid = $me->get_user_id();

		$sql = 
		"SELECT ".$this->USER_PROFILE_FIELDS.", ".$this->USER_SETTINGS_FIELDS.", ".
		"c.`city_name` ".
		($mid?", f.`requested_ts`,f.`confirmed_ts` ":"").
		"FROM `user` u ".
		"LEFT JOIN `city` c ON (u.`user_city_id` = c.`city_id`) ".
		($mid?"LEFT JOIN `friendship` f ON (u.`user_id` = f.`friend_id`) AND (f.`user_id`=$mid) ":"").
		"WHERE u.`user_id`=$userId ".($cookie?" AND u.`cookie` = '$cookie' ":"").
		"";
// Logger::log("GETUSER: $sql");
		$user = null;
		$friends = '';

        $dbResult = $db->runSQL($sql);
        if ($dbResult) {
            $resultObj = $dbResult->fetch_object();
           
            if ($resultObj) {
                $id = $resultObj->user_id;
                $first_name = $resultObj->user_first_name;
                $email = $resultObj->user_email;

                $user = new c_users_GenericProfile($id,$first_name,$email);
                
				$user->set_last_name($resultObj->user_last_name); 
				$user->set_avatar_big($resultObj->user_avatar_big);
				$user->set_avatar_small($resultObj->user_avatar_small);
				$user->set_score($resultObj->user_score); 
				$user->set_reg_date($resultObj->user_reg_date); 
				$user->set_last_login_ts($resultObj->user_last_login_ts); 
				$user->set_status($resultObj->user_status); 
				$user->set_email($resultObj->user_email);
				$user->set_cell($resultObj->user_cell);
				$user->set_cell_entered($resultObj->user_cell_entered); 
				$user->set_city_id($resultObj->user_city_id);
				$user->set_city($resultObj->city_name);
				$user->set_gender($resultObj->user_gender); 
				$user->set_total_checkins($resultObj->user_total_checkins); 
				$user->set_total_venues($resultObj->user_total_venues);
				$user->set_friends($resultObj->friends);
				
				// friendship
				if ($mid) {
					$user->set_frequested($resultObj->requested_ts+0);
					$user->set_fconfirmed($resultObj->confirmed_ts+0);
				} else {
					$user->set_frequested(0);
					$user->set_fconfirmed(0);
				}
				
				// settings
				//	
				$user->set_show_phone($resultObj->settings_show_phone); 
				$user->set_show_sn($resultObj->settings_show_sn); 
				$user->set_send_email_friends_checkin($resultObj->settings_send_email_friends_checkin); 
				$user->set_send_sms_friends_checkin($resultObj->settings_send_sms_friends_checkin); 
				$user->set_send_sms_friendship_request($resultObj->settings_send_sms_friendship_request);
				$user->set_send_monthly_email($resultObj->settings_send_monthly_email); 

			}
        }
        $dbResult->free();
        
		return $user;
		
	}
}
?>