<?
$chief = (isset($orginfo['chief_name'])?$orginfo['chief_name']:'<span class="grey">информация уточняется</span>');
$contacts = (isset($orginfo['chief_contact'])?$orginfo['chief_contact']:'');
$chief_name = (isset($orginfo['chief_name'])?$orginfo['chief_name']:'');
$unid = md5(rand(1,99).time());
$total = ($orginfo['total_cases']?$orginfo['total_cases']:0);
?>

<div class="localheader"><?= $total.getViewsLabel( $total, ' проект'); ?></div>
<div class="special">
    <div class="inside">
        <span class="bignum"><?= number_format($orginfo['total_amount']*1000000, 0,'.',' '); ?></span><span>руб</span>
    </div>
</div>

 <div class="chief" id="chief">
    <div class="chief" id="chief_name_area">Руководитель:<br><?= $chief; ?></div>
    <div class="pic" id="chief_pic">
        <? if (isset($orginfo['chief_pic_m'])) {
            echo '<img src="'.$orginfo['chief_pic_m'].'" />';
        }
        ?>
    </div>
    <div class="chief small grey" id="chief_contact_area"><?= nl2br($contacts); ?></div>
    <div class="clear"></div>
</div>

<? if (isset($this->user['type']) && ($this->user['type'] == Setup::$USER_TYPE_ADMIN)) {  ?>

<div class="editchief" id="editchief">
    <a href="#">редактировать</a>
</div>

<div class="chiefform displaynone" id="chiefform">
    <form name="chiefeditform" id="chiefeditform" target="utc<?= $unid; ?>" method="post" action="/a/" enctype="multipart/form-data">
        <input type="hidden" name="f" value="editchief">
        <input type="hidden" name="filedata" id="filedata<?= $unid; ?>" value="">
        <input type="hidden" name="unid" value="<?= $unid; ?>">
        <input type="hidden" name="orgid" value="<?= $orginfo['orgid']; ?>">
        <div class="inputbox smallpadding">
            <div class="label">Руководитель:</div>
            <div class="sidefield"><input type="text" name="chief_name" id="chief_name" value="<?= $chief_name; ?>"></div>
        </div>
        <div class="inputbox smallpadding">
            <div class="label">Контактная информация:</div>
            <div class="sidefield"><textarea style="height:80px;" name="chief_contact" id="chief_contact"><?= $contacts; ?></textarea></div>
        </div>
        <div class="inputbox">
            <div class="field" id="picfield">
                <div class="sidefield">
                    <input type="file" canbe="empty" name="file<?= $unid; ?>" id="img<?= $unid; ?>" onChange="Chief.picUpload(this,'<?= $unid; ?>','editchief');">
                </div>
            </div>
            <div class="field displaynone" id="pictarget">
                <img src="/project/i/al.gif">
            </div>
            <div class="clear"></div>
        </div>
        <div class="errorbox displaynone" id="errorbox">
            <div class="title" id="errortitle">Нужно заполнить все поля</div>
            <div class="message" id="errormessage">Обратите внимание на выделенные поля. Их нужно заполнить.</div>
        </div>
        <div class="clear"></div>
        <div class="inputbox">
            <button id="savechief" name="post">Готово!</button>
        </div>
        <iframe id="utc<?= $unid; ?>" name="utc<?= $unid; ?>" onload="Chief.picLoaded('<?= $unid; ?>');" src="" class="uploadframe"></iframe>
    </form>
</div>

<? } ?>


