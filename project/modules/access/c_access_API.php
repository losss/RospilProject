<?php

class c_access_API extends API {

    public static function expertApplication($db,$userid,$post) {

        $realname = $db->escapeString(trim($post['realname']));
        $specialty = $db->escapeString(trim($post['specialty']));
        $protext = $db->escapeString(trim($post['protext']));
        $uid = $db->escapeString(trim($userid));

        if (!$uid || !$realname || !$specialty || !$protext) {
            return array('status'=>'error','message'=>'В данных чего-то не хватает, проверьте, все ли поля заполнены.');
        }
        $t = Setup::$USER_TYPE_EXPERT_CANDIDATE;
        $sql = "UPDATE users SET type=$t,realname='$realname',specialty='$specialty',protext='$protext' WHERE userid=$uid";
        self::q($db,$sql);
        return array('status'   => 'OK',
                     'message'  => 'Отлично! Мы записали и будем рассматривать вашу заявку.'
                );
    }

    public function sendNewExpertNotification($name,$email) {

        $to = $name." <".$email.">";
	$subj = 'Rospil.net - ваша заявка удовлетворена';
	$from = Setup::$EMAIL_FROM;
        $message =
        "<html>
            <head>
                <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
                <title>Добро пожаловать</title>
                <style type=\"text/css\">
                body { font-size: 16px; font-family: arial, helvetica, sans-serif; background-color:#fff; }
                p { padding: 1em; }
                </style>
            </head>
            <body>
                <p>
                    <b>Здравствуйте, $name!</b>
                </p>
                <p>
                    Спасибо, что предложили свое время и ресурсы для помощи сайту РосПил. Ваша заявка удовлетворена.
                </p>
                <p>
                    Теперь у вас есть доступ в закрытый раздел \"Экспертиза\" (http://rospil.info/expertise - доступ
                    только после логина, для неавторизованных пользователей такого раздела не существует), где идет
                    будет вестись обсуждение подозрительных конкурсов и координироваться работа по разработке.
                </p>
                <p>
                    - Коллеги.
                </p>
            </body>
        </html>";
        return Communicator::sendEmail($to, $message, $from, $subj);
    }

    public static function getUserById($db, $userid) {
        $uid = $db->escapeString(trim($userid));
        $sql = "SELECT * FROM users WHERE userid = '$uid'";
        $user = (array)API::getSqlHash($db, $sql);
    }

    public static function updateExpert($db,$userid,$action=1) {
        $uid = $db->escapeString(trim($userid));
        if (!$uid) {
            return array('status'=>'error','message'=>'Фигня какая-то с данными');
        }
        $user = self::getUserById($db, $uid);
        switch ($action) {
            case 0:
                $t = Setup::$USER_TYPE_EXPERT;
                if ($user['email']) self::sendNewExpertNotification($user['name'],$user['email']);
                break;
            case 1:
                $t = 0;
                break;
            case 2:
                $t = Setup::$USER_TYPE_EXPERT_REJECTED;
                break;
            default:
                $t = 0;
                break;
        }
        //$t = $remove ? 0 : Setup::$USER_TYPE_EXPERT;
        $sql = "UPDATE users SET type=$t WHERE userid=$uid";
        self::q($db,$sql);
        return array('status'   => 'OK',
                     'message'  => 'Отлично!'
                );
    }

    public static function findExpert($db,$email) {
        $e = $db->escapeString(trim($email));
        if (!$e) {
            return array('status'=>'error','message'=>'Надо указать email');
        }
        $sql = "SELECT * FROM users WHERE email = '$e'";
        $expert = (array)API::getSqlHash($db,$sql);
        return array('status'   => 'OK',
                     'message'  => 'Отлично!',
                     'name'     => (isset($expert['name'])?$expert['name']:''),
                     'email'    => (isset($expert['email'])?$expert['email']:''),
                     'type'     => (isset($expert['type'])?$expert['type']:''),
                     'userid'   => (isset($expert['userid'])?$expert['userid']:''),
                );
    }

    public static function getExperts($db,$approved=true) {
        $utype = ($approved?Setup::$USER_TYPE_EXPERT:Setup::$USER_TYPE_EXPERT_CANDIDATE);
        $sql = "SELECT * FROM users WHERE type = $utype";
        $experts = (array)API::getSqlArray($db, $sql);
        return $experts;
    }

    public static function loginUser($db,$post) {
        $result = array('status'=>'OK','message'=>'Да все нормально.');
        $name = $db->escapeString(trim($post['name']));
        $password = md5($db->escapeString(trim($post['password'])));

        $sql = "SELECT * FROM users WHERE name = '$name' AND password='$password'";
        $user = (array)API::getSqlHash($db, $sql);

        if (!isset($user['userid'])) {
            $result = array('status'=>'error','message'=>'Неверное имя или пароль.');
            return $result;
        }

        $ip = $_SERVER['REMOTE_ADDR'];
        $ts = time();
        $uid = $user['userid'];
        //$cookie = md5(md5($password.$name).'cookie');
        $cookie = makeCookie($name,$password);
        API::q($db,"UPDATE users SET last_ip_address='$ip',cookie_ts=$ts,cookie='$cookie' WHERE userid=$uid ");
        self::saveAuthCookie($cookie);

        $result['userid'] = $user['userid'];
        $result['name'] = $user['name'];

        return $result;
    }

    public static function logout() {
        setcookie(Setup::$AUTH_COOKIE, '', 0, "/", Setup::$BASE_DOMAIN);
        header('location:/');
    }

    public static function getUser($db) {

        // read cookie
        $cookie = '';
        if (isset($_COOKIE[Setup::$AUTH_COOKIE])) {
            $cookie = $_COOKIE[Setup::$AUTH_COOKIE];
        }
        // see if this cookie is in DB
        if ($cookie) {
            $c = $db->escapeString($cookie);
            $sql = "SELECT * FROM users WHERE cookie = '$c'";
            $user = (array)API::getSqlHash($db, $sql);

            $ip = $_SERVER['REMOTE_ADDR'];
            $ts = time();
            API::q($db,"UPDATE users SET last_ip_address='$ip',cookie_ts=$ts WHERE cookie='$c' ");
            return $user;
        } else {
            return null;
        }

    }

    // @ Check security
    public static function registerUser($db,$post) {
        $result = array('status'=>'OK','message'=>'/'); // goto home page after registration
        $result['jc'] = strip_tags($db->escapeString(trim($post['jc'])));

        unset($post['f']);
        unset($post['jc']);
        unset($post['recaptcha_challenge_field']);
        unset($post['recaptcha_response_field']);
        unset($post['password2']);

        $name = strip_tags($db->escapeString(trim($post['name'])));
        $password = $db->escapeString(trim($post['password']));

        // check if username is already taken
        $uid = self::getSingleValue($db, "SELECT userid as value FROM users WHERE name='$name'");
        if (isset($uid) && ($uid)) {
             $result = array(
                'status' => 'error',
                'message'=> 'Пользователь с таким именем уже есть, выберите другое.'
                );
            return $result;
        }

        $keys = array();
        $values = array();
        $dataissue = false;
        foreach ($post as $key => $value) {
            $k = strip_tags($db->escapeString(trim($key)));
            $v = strip_tags($db->escapeString(trim($value)));
            if ((strlen($k)==0 || strlen($v)==0) && ($k!='email')) {
                $dataissue = true;
                break;
            }
            array_push($keys, $k);
            if ($k =='password') {
                $v = md5($v);
                array_push($values, "'$v'");
            } else if ($k =='jc') {
                // skip jc
            } else {
                array_push($values,"'$v'");
            }
        }

        if ($dataissue) {
            $result = array(
                'status' => 'error',
                'message'=>'Что-то не то с данными, которые вы ввели.
                    Посмотрите внимательно, ничего не пропустили, ничего не перепутали?'
                );
            return $result;
        }

        $ts = time();
        $allkeys = implode(',',$keys);
        $allvalues = implode(',',$values);
        //$cookie = md5(md5($password).'cookie');
        $cookie = makeCookie($name,$password);
        $ip = $_SERVER['REMOTE_ADDR'];
        $sql = "INSERT INTO users ($allkeys,registered_ts,cookie,cookie_ts,last_ip_address) VALUES ($allvalues,$ts,'$cookie',$ts,'$ip')";
        self::q($db,$sql);
        self::saveAuthCookie($cookie);

        $result['userid'] = $db->getGeneratedId();
        $result['name'] = $name;

        return $result;
    }

    public static function saveAuthCookie($cookie) {
        $expire = time() + 8640000; // 100 days
        setcookie(Setup::$AUTH_COOKIE, $cookie, $expire, "/", Setup::$BASE_DOMAIN);
    }



}