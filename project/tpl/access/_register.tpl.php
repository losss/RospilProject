<div class="register" id="registerarea">
    <form name="registerform" id="registerform" action="" method="">
        
        <input type="hidden" name="f" value="register">
        <input type="hidden" name="jc" value="<?= isset($jcallback)?$jcallback:'';  ?>">

        <div class="inputbox">
            <div class="halfbox">
                <div class="label">Имя (псевдоним)</div>
                <div class="field"><input type="text" name="name" id="name" value=""></div>
            </div>
            <div class="halfbox">
                <div class="label">Электронная почта (опционально)</div>
                <div class="field"><input type="text" name="email" id="email" value=""></div>
            </div>
            <div class="clear"></div>
        </div>

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

        <div class="errorbox displaynone" id="errorbox">
            <div class="title" id="errortitle">Нужно заполнить все поля</div>
            <div class="message" id="errormessage">Обратите внимание на выделенные поля. Их нужно заполнить..</div>
        </div>

        <div class="inputbox">
            <button id="doregister" name="post">Создать Аккаунт</button>
        </div>

    </form>
</div>

