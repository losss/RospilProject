<div class="experts" id="expertsarea">

    <div class="findexpert">
        <div class="searchtitle">
            <b>Поиск по электронной почте</b>
        </div>
        <div class="searcharea">
            <form name="expertsearchform" id="expertsearchform" action="" method="GET">
                <input type="hidden" name="f" value="findexpert">
                <input type="text" name="eemail" id="eemail" value="">
                <button id="findexpertbtn">Найти</button>
            </form>
        </div>
        <div class="expert displaynone" id="foundexpert">
            <div class="row">
                    <div class="left" id="foundname"></div>
                    <div class="left" id="foundemail"></div>
                    <div class="left" id="foundlink"></div>
                    <div class="clear"></div>
                </div>
                <div class="clear"></div>
        </div>
        <div class="errorbox displaynone" id="errorbox">
            <div class="title" id="errortitle">Ошибка!</div>
            <div class="message" id="errormessage"></div>
        </div>
    </div>

    <div class="listcontainer borderbottom">
    <h3>Кандидаты</h3>

    <? if (is_array($candidates) && (count($candidates) > 0)) { ?>

        <? foreach ($candidates as $с) { ?>

            <div class="expert" id="expert<?= $с['userid']; ?>">
                <div class="row">
                    <div class="left"><?= $с['name']; ?></div>
                    <div class="left"><?= ($с['email']?$с['email']:'&nbsp;'); ?></div>
                    <div class="left"><a href="#" id="elink<?= $с['userid']; ?>" onclick="Expert.updateExpert(2,<?= $с['userid']; ?>,0);return false;">сделать экспертом</a> &nbsp;|&nbsp; <a href="#" id="elink<?= $с['userid']; ?>" class="red" onclick="Expert.updateExpert(2,<?= $с['userid']; ?>,2);return false;">отказать</a></div>
                    <div class="clear"></div>
                    <div class="grey left"><?= $с['specialty']; ?></div>
                    <div class="grey left"><?= $с['protext']; ?></div>
                    <div class="clear"></div>
                </div>
                <div class="clear"></div>
            </div>

        <? } ?>
    <? } else { ?>
    <h2>Пока никого.</h2>
    <? } ?>
    </div>

    <div class="listcontainer">
    <h3>Зарегистрированные эксперты</h3>

    <? if (is_array($list) && (count($list) > 0)) { ?>

        <? foreach ($list as $l) { ?>

            <div class="expert" id="expert<?= $l['userid']; ?>">
                <div class="row">
                    <div class="left"><?= $l['name']; ?></div>
                    <div class="left"><?= ($l['email']?$l['email']:'&nbsp;'); ?></div>
                    <div class="left"><a href="#" id="elink<?= $l['userid']; ?>" onclick="Expert.updateExpert(2,<?= $l['userid']; ?>,1);return false;">лишить звания эксперта</a></div>
                    <div class="clear"></div>
                    <div class="grey left"><?= $l['specialty']; ?></div>
                    <div class="grey left"><?= $l['protext']; ?></div>
                    <div class="clear"></div>
                </div>
                <div class="clear"></div>
            </div>
    
        <? } ?>
    <? } else { ?>
    <h2>Пока никого.</h2>
    <? } ?>
    </div>
</div>


