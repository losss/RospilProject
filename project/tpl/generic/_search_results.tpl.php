<div class="searchtitle">
    <h1>Поиск</h1>
</div>

<div class="searcharea">
    <form name="big_search" id="big_search" action="/search/" method="GET">
        <input type="text" name="q" id="q" value="<?= $q; ?>">
        <button id="searchb2">Найти</button>
    </form>
</div>

<div class="searchresults">
    <div class="summary">
        Нашли - организаций: <?= $found_orgs; ?>, махинаций: <?= $found_leads; ?>
    </div>
    <div class="orgs_results">
        <? if (count($orgs)) { ?>
        
        <h3>Организации</h3>

        <? foreach ($orgs as $o) { ?>
            <div class="searchorg">
                <div class="tpic left">
                    <? if (isset($o['chief_pic_t'])) {
                        echo '<img src="'.$o['chief_pic_t'].'" />';
                    }
                    ?>
                </div>
                <div class="body left">
                    <div class="title"><a href="/orgs/<?= $o['orgid']; ?>"><?= str_ireplace($q, "<span class='hl'>$q</span>", $o['name']); ?></a></div>
                    <div class="chief"><?= str_ireplace($q, "<span class='hl'>$q</span>", $o['chief_name']); ?></div>
                    <div class="description grey"><?= str_ireplace($q, "<span class='hl'>$q</span>", $o['chief_contact']); ?></div>
                </div>
                <div class="clear"></div>
            </div>

        <? } }   ?>

        
    </div>
    <div class="leads_results">
        <? if (count($leads)) { ?>

        <h3>Махинации</h3>

        <? foreach ($leads as $l) { ?>
            <div class="searchleads">
                <div class="body left">
                    <div class="title"><a href="/corruption-case/<?= $l['leadid']; ?>"><?= str_ireplace($q, "<span class='hl'>$q</span>", $l['title']); ?></a></div>
                    
                    <div class="description grey">
                        Контактная информация: <?= str_ireplace($q, "<span class='hl'>$q</span>", $l['contact_name']); ?>
                        <?= str_ireplace($q, "<span class='hl'>$q</span>", $l['contact_email']); ?>
                        <?= str_ireplace($q, "<span class='hl'>$q</span>", $l['contact_phone']); ?>
                    </div>
                    <div class="description grey">
                        Адресат заявления о коррупции: <?= str_ireplace($q, "<span class='hl'>$q</span>", $l['petition_org_name']); ?>
                    </div>

                    <div class="description"><?= str_ireplace($q, "<span class='hl'>$q</span>", $l['description']); ?></div>
                </div>
                <div class="clear"></div>
            </div>

        <? } }   ?>

    </div>
</div>

