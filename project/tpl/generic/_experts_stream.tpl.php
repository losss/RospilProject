<? 
if (is_array($list) && (count($list) > 0)) {
foreach ($list as $l) { ?>

<?
    $why = $l['whyfraud'] ? $l['whyfraud'] : '';
    $pic = $l['pic_b']?$l['pic_b']:'';
    $pico = $l['pic_o']?$l['pic_o']:'';
    $leadid = $l['leadid'];
    $isadmin = (isset($this->user['type']) && ($this->user['type'] == Setup::$USER_TYPE_ADMIN));
    $whendate = $l['scheduled'] ? 'конкурс назначен на '.ru_date(strtotime($l['scheduled'])) : 'дата проведения конкурса уточняется';
    $cancelled = (isset($l['cancelled_ts']) && ($l['cancelled_ts'] > 0));
    $label_cancelled = '';
    $cancel_link = '<span class="tiny orange"><a href="#" class="adminaction" action="cancel" item="'.$leadid.'">Конкурс отменен</a></span>';
    $cancel_pic = '<span class="tiny orange"><a href="#" class="adminaction" action="cancelpic" item="'.$leadid.'">Нет, картинка не нужна</a></span>';
    $status = ($cancelled?'<span class="green"><b>конкурс отменен</b></span>':'<b>'.$whendate.'</b>');
    $link = "/".Setup::$POST_BASE_URL."/".$l['leadid'];
    $discovered = date("j.m.Y", $l['discovered_ts']);
    $ccount = ((isset($l['comments_count'])&&$l['comments_count'])?"<a href='$link'>".$l['comments_count'].getCommentsLabel($l['comments_count'], ' комментари').'</a>'
            :'<a href="'.$link.'">Комментировать</a>');
    $expert = $l['expertname']?$l['expertname']:'';
    $edoc = $l['expertdoc']?"<span class='expertdoc'><a href='".$l['expertdoc']."'>Экспертное заключение ($expert)</a></span>":'';
?>

    <div class="item" id="item<?= $l['leadid']?>">
        <div class="bodyarea left">
            <div class="row">
                <div class="casedesc"><?= $l['description']?></div>
                <div class="clear"></div>
            </div>
            <div class="row">
                <div class="greyarea left"><?= round($l['amount'], 1)?> млн руб</div>
                <div class="orgarea graybg"><?= $l['org_name']; ?></div>
                <div class="clear"></div>
            </div>

            <? if ($why) { ?>
            <div class="row">
                <div class="whyfraud"><?= $why; ?></div>
                <div class="clear"></div>
            </div>
            <? } ?>

            <div class="row">
                <div class="casedesc"><a target="blank" href="<?= $l['link']; ?>">Страница конкурса →</a></div>
                <div class="clear"></div>
            </div>
            
            <div class="row">
                <div class="leadpic" id="pic<?= $leadid; ?>">
                    <? if ($pic) { ?>
                    <img src="<?= $pic; ?>" />
                    <? } ?>
                </div>
                <div class="clear"></div>
            </div>
            
            <div class="statusbar">
                <span class="found" id="status<?= $leadid; ?>"><?= $status; ?><?= $edoc; ?></span>
                <span class="commstat right"><?= $ccount; ?></span>
                <div class="clear"></div>
            </div>
            <? if ($isadmin) { ?>
            <div class="adminarea">
                <div class="left" id="adminpicarea<?= $l['leadid']; ?>">
                    <?= $cancelled ? $label_cancelled : $cancel_link; ?>
                    <form name="expertdocform" id="expertdocform" target="utc<?= $l['leadid']; ?>" method="post" action="/a/" enctype="multipart/form-data"  >
                        <input type="hidden" name="f" value="fileupload">
                        <input type="hidden" name="type" value="0">
                        <input type="hidden" name="final" value="1">
                        <input type="hidden" name="filedata" id="filedata<?= $l['leadid']; ?>" value="">
                        <input type="hidden" name="unid" value="<?= $l['leadid']; ?>">
                        <div class="inputbox displaynone" id="fileupload<?= $l['leadid']; ?>">
                            <div class="field" id="field<?= $l['leadid']; ?>">
                                <div class="sidefield">
                                    <input type="file" name="file<?= $l['leadid']; ?>"
                                           id="file<?= $l['leadid']; ?>" onChange="Expert.fileUpload(this,'image');">
                                    <?= $cancel_pic; ?>
                                </div>
                            </div>
                            <div class="field displaynone" id="pictarget">
                                <img src="/project/i/al.gif">
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="displaynone" id="filelink<?= $l['leadid']?>"></div>
                        <iframe id="utc<?= $l['leadid']?>" name="utc<?= $l['leadid']?>"
                                onload="Admin.fileLoaded('<?= $l['leadid']; ?>');" src="" class="uploadframe"></iframe>
                    </form>
                </div>
                <div class="tiny orange right" ><a href="#" class="adminaction" action="deletelead" item="<?= $leadid; ?>">Удалить</a></div>
                <div class="tiny orange right" style="padding-right:10px;"><a href="/admin/publish/<?= $leadid; ?>">Опубликовать</a></div>
            </div>
            <? } ?>
        </div>
        
 
        <div class="clear"></div>
    </div>
<? } } else { ?>

    <div class="message">Ничего не найдено.</div>

<? } ?>

<?= $pagination; ?>

