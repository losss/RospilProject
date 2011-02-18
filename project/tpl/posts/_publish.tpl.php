<?
    $title = (isset($lead['title'])?$lead['title']:'');
    $link = (isset($lead['link'])?$lead['link']:'');
    $description = (isset($lead['description'])?$lead['description']:'');
    $amount = (isset($lead['amount'])?$lead['amount']:'');
    $days = (isset($lead['days'])?$lead['days']:'');
    $contact_name = (isset($lead['contact_name'])?$lead['contact_name']:'');
    $contact_phone = (isset($lead['contact_phone'])?$lead['contact_phone']:'');
    $contact_emial = (isset($lead['contact_email'])?$lead['contact_email']:'');
    $org_name = (isset($lead['org_name'])?$lead['org_name']:'');
    $leadid = (isset($lead['leadid'])?$lead['leadid']:'');
    $whyfraud = (isset($lead['whyfraud'])?$lead['whyfraud']:'');
    $scheduled = (isset($lead['scheduled'])?$lead['scheduled']:'');

    $petition_text = (isset($lead['petition_text'])?$lead['petition_text']:'');
    $petition_orgid = (isset($lead['petition_orgid'])?$lead['petition_orgid']:'');
    $petition_link = (isset($lead['petition_link'])?$lead['petition_link']:'');
    $petition_orgname = '';

    $plist = '<select name="petition_orgid" onchange="Org.updatePetitionTarget();" id="petition_orgid">';
    $olist = '<select name="orgid" id="orgid" onchange="Org.updateOrgTarget();">';
    $plist.= '<option value="0">Выберите организацию</option>';
    $olist.= '<option value="0">Выберите организацию</option>';
    if (is_array($orgs) && (count($orgs)>0)) {
        foreach ($orgs as $o) {
            $name = $o['name'];
            $id = $o['orgid'];
            $purl = $o['petition_page_url'];
            $ourl = $o['website'];
            if ($petition_orgid == $id) {
                $plist.= "<option value='$id' url='$purl' selected>$name</option>";
                $petition_orgname = $name;
            } else {

            }
            $olist.= "<option value='$id' url='$ourl'>$name</option>";
        }
    }
    $plist.= '</select>';
    $olist.= '</select>';

?>

<div class="publish" id="publisharea">
    <form name="report" id="publishform" action="" method="">
        <input type="hidden" name="f" value="publish" />
        <input type="hidden" name="leadid" value="<?= $leadid; ?>" />
        <div class="leadedit left">
            <div class="inputbox smallpadding">
                <div class="label">Название публикации</div>
                <div class="field"><input type="text" name="title" id="title" value="<?= $title; ?>"></div>
            </div>
            <div class="inputbox smallpadding">
                <div class="label"><a target="blank" href="<?= $link; ?>">Ссылка на первоисточник</a> (открывается в новом окне)</div>
                <div class="field"><input type="text" name="link" id="link" value="<?= $link; ?>"></div>
            </div>
            <div class="row">
                <div class="label left">Стоимость:</div>
                <div class="tinyfield"><input type="text" name="amount" id="amount" value="<?= $amount; ?>"> млн. рублей</div>
                <div class="clear"></div>
            </div>
            <div class="row">
                <div class="label left">Срок:</div>
                <div class="tinyfield"><input type="text" name="days" id="days" value="<?= $days; ?>"> дней</div>
                <div class="clear"></div>
            </div>
            <div class="row">
                <div class="label left">Запланирован:</div>
                <div class="tinyfield"><input type="text" name="scheduled" id="scheduled" value="<?= $scheduled; ?>"></div>
                <div class="clear"></div>
            </div>
            <div class="row">
                <div class="label left">Организация:</div>
                <div class="desc">
                    <div class="">
                        <div class="medfield"><input type="text" name="org_name" id="org_name" value="<?= $org_name; ?>"></div>
                        <div class="medfield"><?= $olist; ?></div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            <!--
            <div class="row">
                <div class="label left">Контакт:</div>
                <div class="desc">
                    <div class="smallfield left"><input type="text" name="contact_name" id="contact_name" value="<?= $contact_name; ?>"></div>
                    <div class="smallfield left"><input type="text" name="contact_phone" id="contact_phone" value="<?= $contact_phone; ?>"></div>
                    <div class="clear"></div>
                </div>
                <div class="clear"></div>
            </div>
            -->
            <div class="row">
                <div class="label left">Описание:</div>
                <div class="desc">
                    <div class="medfield"><textarea name="description" id="description"><?= $description; ?></textarea></div>
                </div>
                <div class="clear"></div>
            </div>
            <div class="row">
                <div class="label left">Обоснование (почему вы считаете что это махинация):</div>
                <div class="desc">
                    <div class="">
                        <div class="medfield"><textarea name="whyfraud" id="whyfraud"><?= $whyfraud; ?></textarea></div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>


        </div>

        <!-- right side -->

        <div class="petitionedit right">
            <div class="inputbox smallpadding">
                <div class="label">Обращение к организации:</div>
                <div class="field">
                    <div class="">
                        <div class="field"><input type="text" name="petition_org_name" id="petition_org_name" value="<?= $petition_orgname; ?>"></div>
                        <div class="field"><?= $plist; ?></div>
                    </div>
                </div>
            </div>
            <div class="inputbox smallpadding">
                <div class="label">Адрес страницы для ввода обращения:</div>
                <div class="field"><input type="text" name="petition_link" id="petition_link" value="<?= $petition_link; ?>"></div>
            </div>
            <!--
            <div class="inputbox smallpadding">
                <div class="label">Инструкции:</div>
                <div class="field"><textarea style="height:153px;" name="petition_instructions" id="petition_instructions"></textarea></div>
            </div>
            -->
            <div class="inputbox smallpadding">
                <div class="label">Текст обращения:</div>
                <div class="field"><textarea name="petition_text" id="petition_text"><?= $petition_text; ?></textarea></div>
            </div>
        </div>
        
        </form>

        <div class="clear"></div>
        
        <div class="row">
            <div class="label left">Скриншот</div>
            <div class="desc">
            <? if ($lead['pic_b']) { ?>
                <div class="red">
                    <a href="#" class="adminaction" action="deletescreen" item="<?= $lead['leadid']; ?>">Удалить</a>
                </div>
            <? } else { ?>
                <div style="width:100%">
                    <div><a href="#" class="adminaction" action="addscreen" item="<?= $lead['leadid']; ?>">Загрузить скриншот</a></div>
                    <div id="adminpicarea<?= $lead['leadid']; ?>">
                    
                    <form name="expertdocform" id="expertdocform" target="utc<?= $lead['leadid']; ?>" method="post" action="/a/" enctype="multipart/form-data"  >
                        <input type="hidden" name="f" value="fileupload">
                        <input type="hidden" name="type" value="0">
                        <input type="hidden" name="final" value="1">
                        <input type="hidden" name="filedata" id="filedata<?= $lead['leadid']; ?>" value="">
                        <input type="hidden" name="unid" value="<?= $lead['leadid']; ?>">
                        <div class="inputbox displaynone" id="fileupload<?= $lead['leadid']; ?>">
                            <div class="field" id="field<?= $lead['leadid']; ?>">
                                <div class="sidefield">
                                    <input type="file" name="file<?= $lead['leadid']; ?>"
                                           id="file<?= $lead['leadid']; ?>" onChange="Expert.fileUpload(this,'image');">
                                    <span class="tiny orange"><a href="#" class="adminaction" action="cancelpic" item="<?= $lead['leadid']; ?>">Нет, картинка не нужна</a></span>
                                </div>
                            </div>
                            <div class="field displaynone" id="pictarget">
                                <img src="/project/i/al.gif">
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="displaynone" id="filelink<?= $lead['leadid']?>"></div>
                        <iframe id="utc<?= $lead['leadid']?>" name="utc<?= $lead['leadid']?>"
                                onload="Admin.fileLoaded('<?= $lead['leadid']; ?>');" src="" class="uploadframe"></iframe>
                    </form>
                </div>
                    <div id="pic<?= $lead['leadid']?>">&nbsp;</div>
                </div>
            <? } ?>
            </div>
        </div>
        

        <? if ($lead['cancelled_ts']) { ?>
        <div class="row">
            <div class="label left">Отмена</div>
            <div class="desc">
                <div class="red">
                    <a href="#" class="adminaction" action="resetcancel" item="<?= $lead['leadid']; ?>">На самом деле конкурс не отменили</a>
                </div>
            </div>
        </div>
        <?} ?>

        <div class="row">
            <div class="label left">Экспертное заключение</div>
            <div class="desc">
                <? if ($lead['expertdoc']) { ?>
                    <div id="doc<?= $lead['leadid']?>">
                        <a href="<?= $lead['expertdoc']; ?>">Экспертное заключение</a>
                    </div>
                    <div class="red">
                        <a href="#" class="expertaction" action="delete" item="<?= $lead['leadid']; ?>">Удалить файл</a>
                    </div>

                <? } else {
                    if ($lead['booked_expertid']) {  ?>
                    <div><?= $lead['expertname']; ?> пишет экспертное заключение</div>
                    <? } ?>

                    <?= $this->render($this->getTpl('upload_file', 'posts', true),array('l' => $lead)) ?>
                    <!--
                    <input type="file" name="file<?= $lead['leadid']; ?>" id="file<?= $lead['leadid']; ?>" value="">
                    -->
                <? } ?>
            </div>
            <div class="clear"></div>
        </div>

        <div class="errorbox displaynone" id="errorbox">
            <div class="title" id="errortitle">Нужно заполнить все поля</div>
            <div class="message" id="errormessage">Обратите внимание на выделенные поля. Их нужно заполнить. Если не знаете, вставьте символ "-".</div>
        </div>
        <div class="inputbox bordertop block">
            <button id="postlead" onclick="$('#publishform').submit()" name="post">Готово!</button>
        </div>

    

</div>



