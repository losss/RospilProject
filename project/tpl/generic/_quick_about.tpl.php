<div class="localheader">Зачем все это нужно?</div>
<div>
    <p>
    Затем, что пенсионеры, врачи, учителя находятся на грани выживания в то время как жулики у власти покупают очередную виллу, яхту или еще черт знает что.
    </p>
    <p>
    Это наши деньги.
    </p>
    <p>
    Это нормальное медицинское обслуживание. Это качественное образование.
    Это дороги, по которым можно ездить. Это чистые улицы.
    Это возможность всем нам жить лучше.
    </p>
</div>

<? if (Setup::$ENABLE_DONATE) { ?>
<div class="donate">
    <div class="big">
        Нужна ваша помощь
    </div>
    <div>
        Яндекс-кошелек:<br/>
        <span><?= Setup::$YANDEX_MONEY_ACCOUNT; ?></span>
        <div class="right"><a href="/donate">подробнее</a> »</div>
    </div>
</div>

<? } ?>

<? if (Setup::$ENABLE_SHARE) { ?>
<br>
    <div class="sharecase" id="sharearea">
        <div id="share_twitter"><?= Setup::$TWITTER_BUTTON; ?></div>
        <div id="share_facebook"><?= Setup::$FACEBOOK_RECOMMEND; ?></div>
        <div id="share_vkontakte"><?= Setup::$VKONTAKTE_SHARE_BUTTON; ?></div>
        <div class="clear"></div>
    </div>
<? } ?>

