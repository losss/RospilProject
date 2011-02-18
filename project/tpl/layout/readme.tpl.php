<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ration.me - free nutrition tracking tool</title>
<meta name="description" content="free nutrition tracking, food log, calories counting, nutrition calculator" />
<link rel="stylesheet" type="text/css" media="screen" href="/project/css/style.css" />
<?= Setup::$GA; ?>
</head>
<body>

<div id="page">
    <div class="header">
        <div class="header-inner">
            <div id="logo"><a href="/"><img src="/project/i/logo3.png" alt="rationme"/></a></div>

            <? if (Setup::$FACEBOOK_CONNECTION) { ?>
            <div class="fblike"><?= Setup::$FB_LIKE; ?></div>
            <? } else { ?>
            <div class="fblike"><?= Setup::$FB_LIKE_IFRAME; ?></div>
            <? } ?>

            <div class="user-info">
            </div>
            <div class="clear"></div>
        </div>
    </div>


<div id="body">

<div class="stream">
<?= $content; ?>
<div class="clear"></div>
</div>

<div class="clear"></div>
</div>
<div class="clear"></div>
<div class="footer grey">&copy; 2010 <a target="blank" href="http://twitter.com/senko">Pavel Senko</a> <br/><br/>
    <b>About:</b> Ration.me is a nutrition tracking tool.
    <br/><br />
    <b><a href="/terms">Terms of Use</a></b> | <b><a href="/privacy">Privacy Policy</a></b>
</div>


</div>

</body>
</html>
