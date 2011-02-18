<? foreach ($menu as $m => $name)  { ?>

<div class="tab <?= ($m == $caller->myRootClassAlias?'active':''); ?>">
    <a href="/<?= ($m==Settings::$DEFAULT_URL_PATH?'':$m); ?>"><?= $name; ?></a>
</div>

<? } ?>

