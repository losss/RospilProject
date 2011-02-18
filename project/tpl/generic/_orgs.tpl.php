<div class="orglist" >
    <? if (is_array($list) && (count($list) > 0)) { ?>

        <? foreach ($list as $o) {

            $cases = (isset($o['total_cases'])?$o['total_cases']:0);
            $total = (isset($o['total_amount'])?$o['total_amount']:0);
            $chief = (isset($o['chief_name'])?$o['chief_name']:'<span class="grey">информация уточняется</span>');

           
        ?>
            <div class="org">
                <div class="pic left">
                    <? if (isset($o['chief_pic_s'])) {
                        echo '<img src="'.$o['chief_pic_s'].'" />';
                    } 
                    ?>
                </div>
                <div class="body">
                    <div class="title"><a href="/orgs/<?= $o['orgid']; ?>"><?= $o['name']; ?></a></div>
                    <div class="score"><?= $cases.' '.getVotesLabel($cases,'проект'); ?>  (<?= $total; ?> млн руб)</div>
                    <div class="chief">Руководитель:<br><?= $chief; ?></div>
                </div>
                <div class="clear"></div>
            </div>
    
        <? } ?>
    <? } else { ?>
    <h2>Пока ничего.</h2>
    <? } ?>
</div>


