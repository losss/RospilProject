<?

$logged = (isset($this->user['userid']) && ($this->user['userid']>0));
$regbutton = ($logged?'Записаться в Эксперты':'Создать Аккаунт и Записаться в Эксперты');
$applied = (isset($this->user['userid']) && ($this->user['userid']>0) && ($this->user['type']==Setup::$USER_TYPE_EXPERT_CANDIDATE ));
$expert = (isset($this->user['userid']) && ($this->user['userid']>0) && ($this->user['type']==Setup::$USER_TYPE_EXPERT ));
$rejected = (isset($this->user['userid']) && ($this->user['userid']>0) && ($this->user['type']==Setup::$USER_TYPE_EXPERT_REJECTED ));
$admin = (isset($this->user['userid']) && ($this->user['userid']>0) && ($this->user['type']==Setup::$USER_TYPE_ADMIN ));

?>

<div class="register text w550 borderbottom">
    <p>
        Для грамотного противодействия распилам нам нужны эксперты.
    </p>

    <p>
        Нам нужны специалисты в различных областях (в первую очередь, в области информационых технологий),
        которые могли бы грамотно и доходчиво написать экспертное заключение, поясняющее, почему тот или иной конкурс
        является распилом.
        Необходимо на конкретных примерах из ТЗ показать почему за положенное время невозможно выполнить необходимую работу.
    </p>
    <p>
        Нам нужны также программисты для развития ресурса. Знание PHP, CSS, JavaScript (jQuery), MySQL обязательно.
        Навыки работы с открытым кодом приветствуются.
        Опыт работы...
        Да любой опыт работы пойдет. Главное - желание помочь своей стране.
    </p>
    <p>
        Мы не обещаем оплату. Скорее так: мы обещаем отсутствие денежного вознаграждения.
        Мы работаем за идею и верим, что вы можете тоже.
    </p>

</div>

<div class="register paddingtop" id="registerarea">

    <? if ($admin) { ?>

    <h2><a href="/admin/experts">Эксперты</a></h2>

    <? } else if($expert) { ?>

    <h2><a href="/expert/review">Кандидаты на публикацию</a></h2>

    <? } else if($applied) { ?>

    <h2>Спасибо за инициативу. Мы рассматриваем вашу заявку.</h2>

    <? } else if($rejected) { ?>

    <h2>Спасибо за инициативу. Мы пока не можем назначить вас экспертом. Приходите позже, может что изменится.</h2>

    <? } else { ?>

    <form name="regexpertform" id="regexpertform" action="" method="">    
        <input type="hidden" name="f" value="regexpert">
        <input type="hidden" name="jc" value="<?= isset($jcallback)?$jcallback:'';  ?>">

        <? if ($logged) { ?>
            <input type="hidden" name="userid" value="<?= $this->user['userid']; ?>">
        <? } else { ?>
            <input type="hidden" name="type" value="<?= Setup::$USER_TYPE_EXPERT_CANDIDATE; ?>">
        <? } ?>


        <? if (!$logged) { ?>

        <div class="inputbox">
            <div class="halfbox">
                <div class="label">Имя (псевдоним)</div>
                <div class="field"><input type="text" name="name" id="name" value=""></div>
            </div>
            <div class="halfbox">
                <div class="label">Электронная почта</div>
                <div class="field"><input type="text" name="email" id="email" value=""></div>
            </div>
            <div class="clear"></div>
        </div>

        <? } ?>


        <div class="inputbox">
            <div class="halfbox">
                <div class="label">Имя и фамилия (настоящие)</div>
                <div class="field"><input type="text" name="realname" id="realname" value=""></div>
            </div>
            <div class="halfbox" style="padding-top:30px;">
                <span class="tiny grey" >Для указания авторства экспертного заключения</span>
            </div>
            <div class="clear"></div>
        </div>

        <div class="inputbox">
            <div class="label">Профессиональная область</div>
            <div class="field"><input type="text" name="specialty" id="specialty" value=""></div>
        </div>

        <div class="inputbox">
            <div class="label">Опишите свои достижения (работы, публикации, ссылки и т.п.)</div>
            <div class="field"><textarea name="protext" id="protext"></textarea></div>
        </div>

        <? if (!$logged) { ?>

        <div class="inputbox">
            <div class="halfbox">
                <div class="label">Пароль</div>
                <div class="field"><input type="password" name="password" id="password" value=""></div>
            </div>
            <div class="halfbox">
                <div class="label">Повторите пароль</div>
                <div class="field"><input type="password" name="password2" id="password2" value=""></div>
            </div>
            <div class="clear"></div>
        </div>

        <div class="inputbox">
            <div class="label">Введите символы с картинки</div>
            <div style="margin-left:-3px;"><?= $recaptcha; ?></div>
        </div>

        <? } ?>

        <div class="errorbox displaynone" id="errorbox">
            <div class="title" id="errortitle">Нужно заполнить все поля</div>
            <div class="message" id="errormessage">Обратите внимание на выделенные поля. Их нужно заполнить..</div>
        </div>

        <div class="inputbox">
            <button id="regexpert" name="post"><?= $regbutton; ?></button>
        </div>

    </form>

    <? } ?>

</div>

