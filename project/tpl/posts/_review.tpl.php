<?

$isexpert = ($this->user['type'] == Setup::$USER_TYPE_EXPERT);
$isadmin = ($this->user['type'] == Setup::$USER_TYPE_ADMIN);

?>

<div class="review" id="reviewarea">

    <div class="selection">
        <div class="left <?= $selected != 'selected' ? 'regular': 'selected' ?>"><a href="?tab=selected">Перспективные</a></div>

        <? if ($isexpert) { ?>
        <div class="left <?= $selected != 'my' ? 'regular': 'selected' ?>"><a href="?tab=my">Мои</a></div>
        <? } ?>

        <div class="left <?= $selected != 'alltherest' ? 'regular': 'selected' ?>"><a href="?tab=alltherest">Все остальные</a></div>

        <div class="clear"></div>
    </div>

    <? if (is_array($list) && (count($list) > 0)) { ?>

        <? foreach ($list as $l) { ?>
            <div class="item" id="item<?= $l['leadid']?>">
                <div class="bodyarea left">
                    <div class="row">
                        <div class="label left">Стоимость:</div>
                        <div class="desc enhanced"><?= round($l['amount'], 1)?> млн руб</div>
                        <div class="clear"></div>
                    </div>
                    <div class="row">
                        <div class="label left">Срок:</div>
                        <div class="desc enhanced"><?= round($l['days'], 1)?> дней</div>
                        <div class="clear"></div>
                    </div>
                    <div class="row">
                        <div class="label left">Организация:</div>
                        <div class="desc enhanced"><?= $l['org_name']; ?></div>
                        <div class="clear"></div>
                    </div>
                    <div class="row">
                        <div class="label left">Ссылка:</div>
                        <div class="desc enhanced"><a target="blank" href="<?= $l['link']; ?>">Открывается в новом окне</a></div>
                        <div class="clear"></div>
                    </div>
                    <div class="row">
                        <div class="label left">Описание:</div>
                        <div class="desc"><?= $l['description']; ?></div>
                        <div class="clear"></div>
                    </div>
                </div>
                <div class="adminarea right">

                    <? if ($selected != 'selected') { ?>
                        <div>
                           <a href="#" class="adminaction" action="preselect" item="<?= $l['leadid']; ?>">Перспективный</a>
                        </div>
                    <? } ?>

                    <? if ($isadmin) { ?>
                    <div><a href="/admin/publish/<?= $l['leadid']?>">Опубликовать</a></div>
                    <div class="red"><a href="#" class="adminaction" action="deletelead" item="<?= $l['leadid']?>">Удалить</a></div>
                     <? if ($l['expertdoc']) { ?>
                            <div id="doc<?= $l['leadid']?>">
                                <a href="<?= $l['expertdoc']; ?>">Экспертное заключение</a>
                            </div>
                            <div class="red">
                                <a href="#" class="expertaction" action="delete" item="<?= $l['leadid']; ?>">Удалить файл</a>
                            </div>

                        <? } else if ($l['booked_expertid']) {  ?>
                            <div><?= $l['expertname']; ?> пишет экспертное заключение</div>
                        <? } ?>
                    <? } ?>

                    <? if ($isexpert) { ?>

                        <? if ($l['expertdoc']) { ?>
                            <div id="doc<?= $l['leadid']?>">
                                <a href="<?= $l['expertdoc']; ?>">Экспертное заключение</a>
                            </div>
                            <div class="red">
                                <a href="#" class="expertaction" action="delete" item="<?= $l['leadid']; ?>">Удалить файл</a>
                            </div>

                        <? } else { ?>

                        <? if ($l['booked_expertid'] == $this->user['userid']) { ?>
                        <div>Готовлю экспертное заключение</div>
                        <? } else if ($l['booked_expertid']) {  ?>
                            <div><?= $l['expertname']; ?> пишет экспертное заключение</div>
                        <? } else { ?>
                        <div><a href="#" class="expertaction" action="assign" item="<?= $l['leadid']; ?>">Буду готовить экспертное заключение</a></div>
                        <? } ?>
                        <div><a href="#" class="expertaction" action="upload" item="<?= $l['leadid']; ?>">Загрузить экспертное заключение</a></div>
                    
                        <form name="expertdocform" id="expertdocform" target="utc<?= $l['leadid']; ?>" method="post" action="/a/" enctype="multipart/form-data"  >
                            <input type="hidden" name="f" value="fileupload">
                            <input type="hidden" name="type" value="1">
                            <input type="hidden" name="final" value="1">
                            <input type="hidden" name="filedata" id="filedata<?= $l['leadid']; ?>" value="">
                            <input type="hidden" name="unid" value="<?= $l['leadid']; ?>">
                            <div class="inputbox displaynone" id="fileupload<?= $l['leadid']; ?>">
                                <div class="field" id="field<?= $l['leadid']; ?>">
                                    <div class="sidefield">
                                        <input type="file" name="file<?= $l['leadid']; ?>" id="file<?= $l['leadid']; ?>" onChange="Expert.fileUpload(this,'file');">
                                    </div>
                                </div>
                                <div class="field displaynone" id="pictarget">
                                    <img src="/project/i/al.gif">
                                </div>
                                <div class="clear"></div>
                            </div>
                            <div class="displaynone" id="filelink<?= $l['leadid']?>"></div>
                            <iframe id="utc<?= $l['leadid']?>" name="utc<?= $l['leadid']?>" onload="Expert.fileLoaded('<?= $l['leadid']?>');" src="" class="uploadframe"></iframe>
                        </form>
                        <? } ?>
                    <? } ?>
                </div>
                <div class="clear"></div>
            </div>
        <? } ?>
    <? } else { ?>
    <h2>Пока ничего.</h2>
    <? } ?>

</div>
<div class="errorbox displaynone" id="errorbox">
    <div class="title" id="errortitle">Ошибка!</div>
    <div class="message" id="errormessage"></div>
</div>

