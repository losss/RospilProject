
















<div><a href="#" class="adminaction" action="showupload" item="<?= $l['leadid']; ?>"><?= $label; ?></a></div>
<form name="fileuploadform" id="fileuploadform" target="utc<?= $l['leadid']; ?>" method="post" action="/a/" enctype="multipart/form-data"  >
    <input type="hidden" name="f" value="<?= $f; ?>">
    <input type="hidden" name="type" value="<?= $typenum; ?>">
    <input type="hidden" name="final" value="<?= $final; ?>">
    <input type="hidden" name="filedata" id="filedata<?= $l['leadid']; ?>" value="">
    <input type="hidden" name="unid" value="<?= $l['leadid']; ?>">
    <div class="inputbox displaynone" id="fileupload<?= $l['leadid']; ?>">
        <div class="field" id="field<?= $l['leadid']; ?>">
            <div class="sidefield">
                <input type="file" name="file<?= $l['leadid']; ?>" id="file<?= $l['leadid']; ?>" onChange="Expert.fileUpload(this,'<?= $type; ?>');">
            </div>
        </div>
        <div class="field displaynone" id="pictarget">
            <img src="/project/i/al.gif" alt="loading">
        </div>
        <div class="clear"></div>
    </div>
    <div class="displaynone" id="filelink<?= $l['leadid']?>"></div>
    <iframe id="utc<?= $l['leadid']?>" name="utc<?= $l['leadid']?>" onload="Expert.fileLoaded('<?= $l['leadid']?>');" src="" class="uploadframe"></iframe>
</form>