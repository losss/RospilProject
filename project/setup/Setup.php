<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/core/config/Passwords.php';

class Setup {

    // general settings
    //
    public static $BASE_DOMAIN 	= 'rospil.info';
    public static $SITE_NAME	= 'РосПил';
    public static $LOGO		= '/project/i/logo.png';

    public static $POST_COOKIE  = 'postts';
    public static $COMMENT_COOKIE  = 'commts';
    public static $STAT_COOKIE  = 'stats';
    public static $AUTH_COOKIE  = 'auth';
    public static $ENABLE_DONATE = true;
    public static $YANDEX_MONEY_ACCOUNT = 41001859832724;

    public static $EMAIL_FROM   = 'noreply@rospil.info';

    public static $SERVER_TIMEZONE_DELTA = -28800;  // what it would take to
                                                    // nullify server timezone
                                                    // and bring it up to UTC
                                                    // (will need to adjust experimentally)
    public static $DEFAULT_TIMEZONE = 28800;        // Pacific Time @TODO: change to Moscow Time
    public static $DEFAULT_TIMEZONE_STR = "Europe/Moscow";


    // limits
    //
    public static $COMMENT_LIMIT    = 20000;
    public static $POST_LIMIT       = 30000;
    public static $COMMENTS_PER_MINUTE_LIMIT = 5;
    public static $COMMENTS_PER_HOUR_LIMIT = 20;
    public static $COMMENTS_PER_DAY_LIMIT = 100;
    public static $POST_TIMEOUT = 120;              // 2 min
    public static $COMMENT_TIMEOUT = 10;            // 10 seconds
    public static $POSTS_PER_PAGE = 20;
    public static $COMMENTS_PER_PAGE = 100;
    public static $PAGE_ARG = 'page';
    public static $MIN_SEARCH_LENGTH = 3;
    public static $MAX_WORD_LENGTH = 90;

    // image sizes
    public static $BIG_IMG_SIZE = 750;
    public static $MED_IMG_SIZE = 300;
    public static $SML_IMG_SIZE = 90;
    public static $TNY_IMG_SIZE = 50;

    // menu
    //
    public static $MENU = array(
        '_____'     => 'Главная',
        'orgs'      => 'Организации',
        'about'     => 'О сайте'
    );
    public static $REPORT_LINK = 'report-a-corruption';
    public static $POST_BASE_URL = 'corruption-case';
    public static $USER_TYPE_ADMIN = 100;
    public static $USER_TYPE_EXPERT = 110;
    public static $USER_TYPE_DEVELOPER = 120;
    public static $USER_TYPE_EXPERT_CANDIDATE = 10;
    public static $USER_TYPE_EXPERT_REJECTED = 11;

    public static $COMMENT_TPL = '<div class="comment" id="comment__CID__"><div class="who left"><div class="name">__USER_NAME__</div><div class="when">__DATE__</div>__DELETE__</div><div class="what">__COMMENT__</div><div class="clear"></div></div>';
    public static $COMMENT_VARS = array('/__CID__/','/__USER_NAME__/','/__DATE__/','/__COMMENT__/','/__DELETE__/');
    // features
    //
    public static $DEBUG = true;
    public static $RECORD_STATS = false;

    public static $MONTH_MAP = array(
        'Jan' => 'октября',
        'Feb' => 'февраля',
        'Mar' => 'марта',
        'Apr' => 'апреля',
        'May' => 'мая',
        'Jun' => 'июня',
        'Jul' => 'июля',
        'Aug' => 'августа',
        'Sep' => 'сентября',
        'Oct' => 'октября',
        'Nov' => 'ноября',
        'Dec' => 'декабря',
    );

    public static $CATEGORIES = array(
        'software'      => 'Разработка Программного Обеспечения',
        'computers'     => 'Компьютеры и Комплектующие',
        'drugs'         => 'Лекарства и Фармакология',
        'medicine'      => 'Медицинская Техника и Услуги',
        'construction'  => 'Строительство',
        'education'     => 'Образование',
        'providers'     => 'Услуги Связи',
        'defense'       => 'Рособоронпоставка',
        'realestate'    => 'Недвижимость',
        'food'          => 'Общепит и Продукты Питания',
        'oilgas'        => 'Нефть и Газ',
        'banking'       => 'Банки и Финансы',
        'forrest'       => 'Лесная Промышленность',
        'electricity'   => 'Электроэнергетика',
        'geo'           => 'Геодезия и Картография',
        'ecology'       => 'Экология',
        'metal'         => 'Металлобработка и Металургия',
        'machinery'     => 'Машинное Оборудование',
        'fire'          => 'Пожарная Техника',
        'engineering'   => 'Инженерные Системы',
        'other'         => 'Другое'
    );

    // directories
    //
    public static $CSSROOT = '';
    public static $JSROOT = '';
    public static $IMGROOT = '';
    public static $WEBROOT = '';
    public static $APPURL = '';
    
    public static $GA = '';

    public static $FACEBOOK_APP_ID = '';
    public static $FACEBOOK_SECRET = '';

    public static $FACEBOOK_RECOMMEND = '<iframe src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Frospil.info&amp;layout=button_count&amp;show_faces=false&amp;width=150&amp;action=recommend&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:150px; height:21px;" allowTransparency="true"></iframe>';
    public static $FACEBOOK_LIKE = '<iframe src="http://www.facebook.com/plugins/like.php?href=rospil.info&amp;layout=button_count&amp;show_faces=false&amp;width=150&amp;action=like&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:150px; height:21px;" allowTransparency="true"></iframe>';
    public static $TWITTER_BUTTON = '<a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal" data-via="navalny">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>';

    public static $SHARE_BUTTON = '<span class="st_twitter_hcount" displayText="Tweet"></span><span class="st_facebook_hcount" displayText="Share"></span><span class="st_email_hcount" displayText="Email"></span><span class="st_sharethis_hcount" displayText="Share"></span>';
    public static $SHARE_JS ='<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script><script type="text/javascript">stLight.options({publisher:\'897c3993-783f-4ad1-8560-4bff3f15f9b3\'});</script>';

    public static $FACEBOOK_SHARE ='<a name="fb_share"></a><script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script>';

    public static $RECAPTCHA_PUBLIC = '';
    public static $RECAPTCHA_PRIVATE = '';

    public static $VKONTAKTE_SHARE_JS = '<script type="text/javascript" src="http://vkontakte.ru/js/api/share.js?10" charset="windows-1251"></script>';
    public static $VKONTAKTE_SHARE_BUTTON = '<script type="text/javascript">document.write(VK.Share.button(false,{type: "button", text: "Сохранить"}));</script>';

    // default for .net
    public static $MAILRU_SHARE = '<a target="_blank" class="mrc__plugin_like_button" href="http://connect.mail.ru/share?share_url=http%3A//rospil.net" rel="{\'type\' : \'button\', \'width\' : \'150\'}">Рекомендую</a><script src="http://cdn.connect.mail.ru/js/loader.js" type="text/javascript" charset="UTF-8"></script>';

    public static $ENABLE_SHARE = true;



}

if (preg_match('/rospil\.info/',$_SERVER['SERVER_NAME'])) {

    // do something custom

    Setup::$FACEBOOK_LIKE = '<iframe src="http://www.facebook.com/plugins/like.php?href=rospil.info&amp;layout=button_count&amp;show_faces=false&amp;width=450&amp;action=like&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:150px; height:21px;" allowTransparency="true"></iframe>';

    Setup::$MAILRU_SHARE = '<a target="_blank" class="mrc__plugin_like_button" href="http://connect.mail.ru/share?share_url=http%3A//rospil.info" rel="{\'type\' : \'button\', \'width\' : \'150\'}">Рекомендую</a><script src="http://cdn.connect.mail.ru/js/loader.js" type="text/javascript" charset="UTF-8"></script>';

    Setup::$GA = "  <script type=\"text/javascript\">
                      var _gaq = _gaq || [];
                      _gaq.push(['_setAccount', 'UA-248033-12']);
                      _gaq.push(['_trackPageview']);
                      (function() {
                        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
                      })();
                    </script>";

    Setup::$RECAPTCHA_PRIVATE   = '6Lc8Ur4SAAAAADFocBVTBepIpeBeTrLttvS3idkw';
    Setup::$RECAPTCHA_PUBLIC    = '6Lc8Ur4SAAAAAC5NB7vc3hS3tC7u85vVh0ARynKU';


} else if (preg_match('/rospil\.net/',$_SERVER['SERVER_NAME'])) {

    Setup::$FACEBOOK_RECOMMEND = '<iframe src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Frospil.net&amp;layout=button_count&amp;show_faces=false&amp;width=150&amp;action=recommend&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:150px; height:21px;" allowTransparency="true"></iframe>';
    Setup::$FACEBOOK_LIKE = '<iframe src="http://www.facebook.com/plugins/like.php?href=rospil.net&amp;layout=button_count&amp;show_faces=false&amp;width=90&amp;action=like&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:90px; height:21px;" allowTransparency="true"></iframe>';

} else if (preg_match('/rospil\.org/',$_SERVER['SERVER_NAME'])) {

    Setup::$FACEBOOK_RECOMMEND = '<iframe src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Frospil.org&amp;layout=button_count&amp;show_faces=false&amp;width=150&amp;action=recommend&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:150px; height:21px;" allowTransparency="true"></iframe>';
    Setup::$FACEBOOK_LIKE = '<iframe src="http://www.facebook.com/plugins/like.php?href=rospil.org&amp;layout=button_count&amp;show_faces=false&amp;width=90&amp;action=like&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:90px; height:21px;" allowTransparency="true"></iframe>';
    Setup::$MAILRU_SHARE = '<a target="_blank" class="mrc__plugin_like_button" href="http://connect.mail.ru/share?share_url=http%3A//rospil.org" rel="{\'type\' : \'button\', \'width\' : \'150\'}">Рекомендую</a><script src="http://cdn.connect.mail.ru/js/loader.js" type="text/javascript" charset="UTF-8"></script>';

    Setup::$BASE_DOMAIN = 'rospil.org';
    Setup::$RECAPTCHA_PRIVATE   = '6Lc-Ur4SAAAAAL1S6kuIet13_WCYZdG_HbRVkMmq';
    Setup::$RECAPTCHA_PUBLIC    = '6Lc-Ur4SAAAAAOQb7UKDNlpzLfrqefTY_xCdaNxW';
} else {
    Setup::$BASE_DOMAIN = $_SERVER['SERVER_NAME'];
}