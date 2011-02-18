<div class="login" id="loginarea">
    <form name="report" id="loginform" action="" method="">
        
        <input type="hidden" name="f" value="login">
        <input type="hidden" name="jc" value="<?= isset($jcallback)?$jcallback:'';  ?>">

        <div class="inputbox">
            <div class="halfbox">
                <div class="label">Имя</div>
                <div class="field"><input type="text" name="name" id="name" value=""></div>
            </div>
            <div class="halfbox">
                <div class="label">Пароль</div>
                <div class="field"><input type="password" name="password" id="password" value=""></div>
            </div>
            <div class="clear"></div>
        </div>

        <div class="errorbox displaynone" id="errorbox">
            <div class="title" id="errortitle">Нужно заполнить все поля</div>
            <div class="message" id="errormessage">Обратите внимание на выделенные поля. Их нужно заполнить..</div>
        </div>

        <div class="inputbox">
            <button id="dologin" name="dologin">Войти</button>
        </div>

    </form>
</div>

