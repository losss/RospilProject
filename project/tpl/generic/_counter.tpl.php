<div class="localheader grey">
    <? if ($experts) { ?>
    Сумма подозрительных конкурсов
    <? } else { ?>
    Обнаружено махинаций на
    <? } ?>
</div>
<div class="special">
    <div class="inside">
        <span class="bignum <?= ($experts?'black':'red');  ?>"><?= $sum; ?></span><span>руб</span>
    </div>
</div>

