<script type='text/javascript'>
        $(function() {
          $('textarea#<?= $textareaid; ?>').autogrow();
        });
</script>

                    
<div class="adding <?= $show; ?>" id="<?= $wrapperid; ?>">
    <? if ($title) { ?>
    <div class="subtitle"><h3><?= $title; ?></h3></div>
    <? } ?>

    <form name="<?= $formname; ?>" id="<?= $formid; ?>" method="" action="">
        <input type="hidden" name="userid" value="<?= $this->user['userid']; ?>">
        <input type="hidden" name="f" value="<?= $func; ?>">
        <input type="hidden" name="leadid" value="<?= $leadid; ?>">
        <input type="hidden" name="user_name" value="<?= $this->user['name']; ?>">

        <div class="addtolead">
            <textarea name="<?= $textareaid; ?>" id="<?= $textareaid; ?>"></textarea>
        </div>
        <? if ($errorbox) { ?>
        <div class="errorbox displaynone" id="errorbox">
            <div class="title" id="errortitle">Нужно заполнить все</div>
            <div class="message" id="errormessage">Обратите внимание на выделенные поля. Их нужно заполнить.</div>
        </div>
        <? } ?>
        <div class="inputbox">
            <button id="<?= $buttonid; ?>" name="post"><?= $button; ?></button>
        </div>
    </form>
</div>
               