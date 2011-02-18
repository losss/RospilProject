<?
    $cancelled = (isset($p['cancelled_ts']) && ($p['cancelled_ts'] > 0));
    $whendate = $p['scheduled'] ? 'конкурс назначен на '.ru_date(strtotime($p['scheduled'])) : 'дата проведения конкурса уточняется';
    $status = ($cancelled?'<span class="green">конкурс отменен</span>':'<b>'.$whendate.'</b>');
    $discovered = date("j.m.Y", $p['discovered_ts']);
    $resolved = ($status == 'greenbg'?'конкурс прерван':'пилят');
    $ccount = ((isset($p['comments_count'])&&$p['comments_count'])?$p['comments_count'].getCommentsLabel($p['comments_count'], ' комментари'):'');
    $newcomment_title = 'Выскажи свое мнение:';
    $comments_title = 'Мнение экспертов:';
    $no_comments = '<span id="no_comments">пока нет<span>';
    $why = $p['whyfraud'] ? $p['whyfraud'] : '';
    $expert = $p['expertname']?$p['expertname']:'';
    $edoc = $p['expertdoc']?"<span class='expertdoc'><a href='".$p['expertdoc']."'>Экспертное заключение ($expert)</a></span>":'';
    $pic = $p['pic_b']?$p['pic_b']:'';

    $allcomments = '';
    if (isset($p['comments']) && (is_array($p['comments'])) && (count($p['comments']) > 0)) {
        foreach ($p['comments'] as $c) {
            if (isset($this->user['type']) && ($this->user['type'] == Setup::$USER_TYPE_ADMIN)) {
                $utype = 'admin';
                $delete_link = '<div class="tiny orange"><a href="#" class="adminaction" action="deletecomment" item="'.$c['commentid'].'">Удалить</a></div>';
            } else if (isset($this->user['type']) && ($this->user['type'] == Setup::$USER_TYPE_EXPERT)) {
                $utype = 'expert';
                $delete_link = '';
            } else {
                $utype = '';
                $delete_link = '';
            }
            $allcomments.= preg_replace(
                        Setup::$COMMENT_VARS,
                        array($c['commentid'],$c['user_name'],nl2br(date("j.m.Y\nH:i",$c['created_ts'])),
                            nls2p(wordwrapUTF($c['comment'], Setup::$MAX_WORD_LENGTH, " ", true),"\n",true),
                            $delete_link
                            ),
                        Setup::$COMMENT_TPL); 
        }
    }
    $fixed = (isset($this->user['name'])?'disabled':'');
    $uname = ($fixed?$this->user['name']:'Имя');
    $userid = ($fixed?$this->user['userid']:'');

    $chief = (isset($orginfo['chief_name'])?$orginfo['chief_name']:'<span class="grey">информация уточняется</span>');
    $contacts = (isset($orginfo['chief_contact'])?$orginfo['chief_contact']:'');
    if (isset($p['petition_sent_count']) && (count($p['petition_sent_count']) > 0)) {
        $sent = '<span id="petitioncount">'.$p['petition_sent_count'].'</span> '.getSolutionsLabel($p['petition_sent_count'],'обращени').' отправлено';
    } else {
        $sent = '<span id="petitioncount">0</span> человек отправили обращение';
    }
    if (isset($p['petition_users']) && $userid) {
        $allusers = explode(',',$p['petition_users']);
        $isentpetition = (in_array($userid,$allusers));

    } else {
        $allusers = '';
        $isentpetition = false;
    }

?>

<table width="100%">
    <tr>
        <td>
            <div class="case">
                <? if ($why) { ?>
                <div class="row">
                    <div class="whyfraud bottom20"><?= $why; ?></div>
                    <div class="clear"></div>
                </div>
                <? } ?>
                <? if ($pic) { ?>
                <div class="leadpic">
                    <img src="<?= $pic; ?>" />
                </div>
                <? } ?>
                <div class="description"><?= $p['description']; ?></div>
                <div class="link"><b><a target="blank" href="<?= $p['link']; ?>">Ссылка на источник →</a></b></div>
                <div class="statusbar">
                    <?= $status; ?>
                    <?= $edoc; ?>
                    <span class="commstat right"><?= $ccount; ?></span>
                </div>

                <? if (count($adds)) { 
                        foreach ($adds as $a) {
                            if ((isset($this->user['type'])) && ($this->user['type'] == Setup::$USER_TYPE_ADMIN) ) {
                                $remove = ' - <span class="orange"><a href="#" class="adminaction" action="deleteadd" item="'.$a['addid'].'">Удалить</a></span>';
                            } else {
                                $remove = '';
                            }
                            echo '<div id="add'.$a['addid'].'"><div class="addts">Добавлено '.date("j.m.Y H:i",$a['addts']).$remove.':</div>';
                            echo '<div class="added">'.nls2p(wordwrapUTF($a['addtext'], 36, " ", true),"\n",true).'</div></div>';
                        }
                    }
                ?>

                <? if ((isset($this->user['type'])) && ($this->user['type'] == Setup::$USER_TYPE_ADMIN) ) { ?>
                    <div class="tiny orange" id="addingtolead">
                        <a href="#" class="adminaction" action="showadd" item="<?= $p['leadid']; ?>">Добавить</a>
                    </div>

                <?= $this->render($this->getTpl('publisher', 'posts', true),
                        array('p' => $p,
                              'show'=>'displaynone',
                              'textareaid' => 'addtoleadtext',
                              'wrapperid' => 'addtolead',
                              'title' => '',
                              'formname' => 'addtoleadform',
                              'formid' => 'addtoleadform',
                              'func' => 'addtolead',
                              'button' => 'Готово!',
                              'buttonid' => 'postadd',
                              'leadid' => $p['leadid'],
                              'errorbox' => ''
                             )
                        );?>
                    
                <? } ?>
                

                <div class="comments">
                    <div class="subtitle"><h3><?= $comments_title; ?></h3></div>
                    
                    <div class="list" id="commentslist">
                    <? if ($ccount) { 
                            echo $allcomments;
                        } else {
                            echo $no_comments;
                        } ?>

                    </div>

                    <? if (isset($this->user)) {
                        echo $this->render($this->getTpl('new_comment', 'posts', true),
                                array('p' => $p,'show'=>''));
                    } else {
                        header('location:/404');
                        exit(0);
                    }
                    ?>

                </div>
            </div>
        </td>
        <td>
        <div class="right side">
 
            <div class="whoiswho">
                Бюджет: <span class="red"><b><?= $p['amount'] ?> млн рублей</b></span><br>
                Срок выполнения проекта (в днях): <b><?= $p['days'] ?></b>
            </div>
            
            <div class="todo">
                <h2>План действий:</h2>
                <ol>
                    <li>Зайти на <b><a target="blank" href="<?= $p['link']; ?>">страницу конкурса →</a></b></li>
                    <li>Скачать ТЗ</li>
                    <li>Внимательно прочитать ТЗ</li>
                    <li>Аргументированно высказаться почему в указанный срок невозможно выполнить задание конкурса</li>
                    <li>Если конкурс - не распил, аргументированно высказаться почему так</li>
                </ol>
            </div>

            <div class="petition grey">
                Сюда мы добавим еще кое-что
            </div>
        </div>
        </td>
    </tr>
</table>