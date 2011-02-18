<div class="pages-box">
    <ul>
        <? if ($newerPage): ?>
            <li><a href="<?= $baseUrl."page=".$newerPage ?>">&#x2190;</a></li>
        <? else: ?>
            <li id="gray">&#x2190;</li>
        <? endif; ?>
        <? if ($olderPage): ?>
            <li><a href="<?= $baseUrl."page=".$olderPage ?>">&#x2192;</a></li>
        <? else: ?>
            <li id="gray">&#x2192;</li>
        <? endif; ?>
    </ul>
</div>