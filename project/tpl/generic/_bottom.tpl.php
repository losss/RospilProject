<div class="area">
    CC 2010 Содержимое сайта может свободно распространяться в соответствии с лицензией <a target="blank" href="http://creativecommons.org/licenses/by/3.0/">Creative Commons</a><br/>
    Идея и смысл сайта — <a target="blank" href="http://navalny.livejournal.com/">А. Навальный</a> и коллеги. Разработка — <a target="blank" href="http://twitter.com/senko">П. Сенько</a>. Автор лого — анонимный пользователь ХабраХабр.ру.
    <div style="margin-top: 20px;">

        <a href="/about">О сайте</a>

        <? if (isset($this->user) && is_array($this->user)) { ?>
        | <a href="/logout">Выйти</a>
        <?} else {?>
        | <a href="/register">Регистрация</a>
        | <a href="/login">Вход</a>
        <? } ?>

        <? if (isset($this->user['type']) && ($this->user['type'] == Setup::$USER_TYPE_ADMIN)) { ?>
        | <a href="/admin/review">Публиковать</a>
        | <a href="/admin/experts">Эксперты</a>
        <? } ?>

        <? if (isset($this->user['type']) && ($this->user['type'] == Setup::$USER_TYPE_EXPERT)) { ?>
        | <a href="/expert/review">Публиковать</a>
        <? } ?>

    </div>

</div>

