<?
    $cancelled = (isset($p['cancelled_ts']) && ($p['cancelled_ts'] > 0));
    $whendate = $p['scheduled'] ? 'конкурс назначен на '.ru_date(strtotime($p['scheduled'])) : 'дата проведения конкурса уточняется';
    $status = ($cancelled?'<span class="green">конкурс отменен</span>':'<b>'.$whendate.'</b>');
    $discovered = date("j.m.Y", $p['discovered_ts']);
    $resolved = ($status == 'greenbg'?'конкурс прерван':'пилят');
    $ccount = ((isset($p['comments_count'])&&$p['comments_count'])?$p['comments_count'].getCommentsLabel($p['comments_count'], ' комментари'):'');
    $newcomment_title = 'Выскажи свое мнение:';
    $comments_title = 'Вот что народ говорит по этому поводу:';
    $no_comments = '<span id="no_comments">пока ничего не говорит...<span>';
    $why = $p['whyfraud'] ? $p['whyfraud'] : '';
    $expert = $p['expertname']?$p['expertname']:'';
    $edoc = $p['expertdoc']?"<span class='expertdoc'><a href='".$p['expertdoc']."'>Экспертное заключение ($expert)</a></span>":'';
    $pic = $p['pic_b']?$p['pic_b']:'';

    $allcomments = '';
    $delete_link = '';
    if (isset($p['comments']) && (is_array($p['comments'])) && (count($p['comments']) > 0)) {
        foreach ($p['comments'] as $c) {
            if (isset($this->user['type']) && ($this->user['type'] == Setup::$USER_TYPE_ADMIN)) {
                $utype = 'admin';
                $delete_link = '<div class="tiny orange"><a href="#" class="adminaction" action="deletecomment" item="'.$c['commentid'].'">Удалить</a></div>';
            } else if (isset($this->user['type']) && ($this->user['type'] == Setup::$USER_TYPE_EXPERT)) {
                $utype = 'expert';
            } else {
                $utype = '';
                $delete_link = '';
            }
            $allcomments.= preg_replace(
                        Setup::$COMMENT_VARS,
                        array($c['commentid'],$c['user_name'],nl2br(date("j.m.Y\nH:i",$c['created_ts'])),
                            nls2p(wordwrapUTF($c['comment'], Setup::$MAX_WORD_LENGTH, " ", true),"\n",true),
                            $delete_link, $utype
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
                <div class="title"><h1><?= $p['title']; ?></h1></div>
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
                        echo '<div id="loginregisterarea">
                              <h4>Чтобы комментировать авторизуйтесь или зарегистрируйтесь</h4>';
                        echo '<h2>Логин</h2>';
                        echo $this->render($this->getTpl('login', 'access', true),
                                array('jcallback'=>'Util.stayComment'));
                        echo '<div class="bordertop"></div>';
                        echo '<h2>Регистрация</h2>';
                        echo $this->render($this->getTpl('register', 'access', true),
                                array('recaptcha' => recaptcha_get_html(Setup::$RECAPTCHA_PUBLIC),
                                      'jcallback' => 'Util.stayComment'));
                        echo '</div>';
                        echo $this->render($this->getTpl('new_comment', 'posts', true),
                                array('p' => $p,'show'=>'displaynone'));
                    }
                    ?>

                </div>
            </div>
        </td>
        <td>
        <div class="right side">
            <? if (Setup::$ENABLE_SHARE) { ?>
                <div class="sharecase" id="sharearea">
                    <div class="left" id="share_twitter"><?= Setup::$TWITTER_BUTTON; ?></div>
                    <div class="left" style="margin-top: 1px;" id="share_facebook"><?= Setup::$FACEBOOK_SHARE; ?></div>
                    <div class="clear"></div>
                </div>
            <? } ?>
            <div class="whoiswho">
                <p>
                    Кто-то пилит <span class="red"><b><?= $p['amount'] ?> млн рублей</b></span>
                    установив срок выполнения проекта в <b><?= $p['days'] ?> дней</b>.
                </p>
                <p>
                    Организация, где это творится:<br>
                    <a href="/orgs/<?= $p['orgid']; ?>"><b><?= $p['org_name']; ?></b></a>
                </p>
                
                <div class="" id="chief">
                    <div class="pic left right10" id="chief_pic">
                        <? if (isset($orginfo['chief_pic_s'])) {
                            echo '<img src="'.$orginfo['chief_pic_s'].'" />';
                        }
                        ?>
                    </div>
                    <div class="" id="chief_name_area">Руководитель:<br><?= $chief; ?></div>
                    
                    <div class=" small grey" id="chief_contact_area"><?= nl2br($contacts); ?></div>
                    <div class="clear"></div>
                </div>
               
            </div>

            <? if (strlen($p['petition_text']) > 10) { ?>

            <div class="petition">
                <h1 class="red">Действуй!</h1>
                <div class="todo">
                    <ol>
                        <li>Идем сюда: <b><a target="blank" href="<?= $p['petition_link']?>"><?= $p['petition_org_name']?></a></b></li>
                        <li>Заполняем форму или находим контактную информацию если нет формы для обращений</li>
                        <li>Копируем обращение отсюда
                            <textarea id="text2copy"><?= $p['petition_text']; ?></textarea></li>
                        <li>Отправляем и записываем дату</li>
                    </ol>
                </div>
                <div class="pcounter">
                    <?= $sent; ?>
                </div>
                <div class="biglink" id="isentpetition">
                    <? if ($userid) { ?>
                        <? if ($isentpetition) { ?>
                        Спасибо за отправленное обращение!
                        <? } else { ?>
                            <a href="#" onclick="User.sentPetition(<?= $p['leadid']; ?>);return false;">Отправлено!</a>
                        <? } ?>
                    <? } else { ?>
                            <a href="/login">Залогинься</a> или <a href="/register">зарегистрируйся</a> чтобы отметиться
                    <? } ?>
                </div>
            </div>

            <? } else { ?>

            <div class="petition">
                <h1 class="red">Нужна помощь!</h1>
                <div class="text">
                    Нужен эксперт в этой области, который мог бы грамотно и доходчиво написать экспертное заключение,
                    поясняющее, почему вот этот конкретный конкурс является распилом. <br><br>
                    <b><a target="blank" href="/regexpert">Записаться в эксперты »</a></b><br><br>
                    Когда будете заполнять форму, упомяните, что вы обращаетесь относительно этого конкурса.
                </div>
            </div>

            <? } ?>
        </div>
        </td>
    </tr>
</table>