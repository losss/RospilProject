<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>РосПил</title>
<meta name="description" content="госзакупки, конкурс, тендер, деньги, коррупция, чиновники" />
<meta HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE" />
<link rel="stylesheet" type="text/css" media="screen" href="/project/css/style.css" />
<!--
<script type="text/javascript" src="/core/js/jquery.js"></script>
-->

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js"></script>

<script type="text/javascript" src="/project/js/ts.js"></script>
<script type="text/javascript" >
    var siteurl = 'http://<?= Setup::$BASE_DOMAIN; ?>';
    var comment_tpl = '<?= Setup::$COMMENT_TPL; ?>';
</script>

<?= Setup::$VKONTAKTE_SHARE_JS; ?>
<?= Setup::$GA; ?>

</head>

<body>
    <div class="page">
        <div class="inner wrapper">
            <div class="header">
                <div class="logo left">
                    <a href="/"><img src="/project/i/logo.png" /></a>
                </div>
                <? if (isset($this->user) && is_array($this->user) && isset($this->user['name'])) { ?>
                <div class="userhi left">
                    <?= $this->user['name']; ?>, мы должны это остановить.
                </div>
                <? } ?>

                <? if (
                        (isset($this->user['type']) && ($this->user['type']!=Setup::$USER_TYPE_ADMIN) && ($this->user['type']!=Setup::$USER_TYPE_EXPERT)) ||
                        (!isset($this->user['type']))
                        ) { ?>

                <div class="regexperts">
                    <a href="/regexpert">Нужны Эксперты</a>
                </div>
                <? } ?>

                <div class="reportlink">
                    <?= $reportlink; ?>
                </div>
                <div class="clear"></div>
            </div>
            <div class="redbackground">
                <div class="menu">
                    <?= $menu; ?>
                </div>
                <div class="search right">
                    <?= $search; ?>
                </div>
            </div>

            <div class="content">
                <div class="notification displaynone">Через несколько часов, рано утром 15 февраля, сайт переезжает на новый сервер. Мы не обещаем, но теоретически возможны какие-то странности.  </b>
                </div>
                <div class="notification displaynone">Cайт работает в режиме тестирования, возможны любые изменения. Основная версия будет доступна по адресу
                    <b><a href="http://rospil.net">rospil.net</a></b>
                </div>
                <?= $content; ?>
            </div>
            <div class="bottom">
                <?= $bottom; ?>
            </div>
        </div>
    </div>
</body>
</html>
