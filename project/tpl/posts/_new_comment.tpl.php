<?
    $newcomment_title = 'Выскажи свое мнение:';
    $uname = isset($this->user['name'])?$this->user['name']:'';
?>

<script type='text/javascript'>
        $(function() {
          $('textarea#comment').autogrow();
        });
</script>

                    
<div class="addcomment <?= $show; ?>" id="newcomment">
    <div class="subtitle"><h3><?= $newcomment_title; ?></h3></div>

    <form name="addcommentform" id="addcommentform" method="" action="">
        <input type="hidden" name="userid" value="<?= $this->user['userid']; ?>">
        <input type="hidden" name="f" value="comment">
        <input type="hidden" name="leadid" value="<?= $p['leadid']; ?>">
        <input type="hidden" name="user_name" value="<?= $uname; ?>">

        <div class="newcomment">
            <textarea name="comment" id="comment"></textarea>
        </div>
        <div class="errorbox displaynone" id="errorbox">
            <div class="title" id="errortitle">Нужно заполнить все поля</div>
            <div class="message" id="errormessage">Обратите внимание на выделенные поля. Их нужно заполнить.</div>
        </div>
        <div class="inputbox">
            <button id="postcomment" name="post">Комментировать</button>
        </div>
    </form>
</div>
               