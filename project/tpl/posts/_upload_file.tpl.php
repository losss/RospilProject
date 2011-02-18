<div><a href="#" class="expertaction" action="upload" item="<?= $l['leadid']; ?>">Загрузить экспертное заключение</a></div>
<form name="expertdocform" id="expertdocform" target="utc<?= $l['leadid']; ?>" method="post" action="/a/" enctype="multipart/form-data"  >
    <input type="hidden" name="f" value="fileupload">
    <input type="hidden" name="type" value="1">
    <input type="hidden" name="final" value="1">
    <input type="hidden" name="filedata" id="filedata<?= $l['leadid']; ?>" value="">
    <input type="hidden" name="unid" value="<?= $l['leadid']; ?>">
    <div class="inputbox displaynone" id="fileupload<?= $l['leadid']; ?>">
        <div class="field" id="field<?= $l['leadid']; ?>">
            <div class="sidefield">
                <input type="file" name="file<?= $l['leadid']; ?>" id="file<?= $l['leadid']; ?>" onChange="Expert.fileUpload(this,'file');">
            </div>
        </div>
        <div class="field displaynone" id="pictarget">
            <img src="/project/i/al.gif">
        </div>
        <div class="clear"></div>
    </div>
    <div class="displaynone" id="filelink<?= $l['leadid']?>"></div>
    <iframe id="utc<?= $l['leadid']?>" name="utc<?= $l['leadid']?>" onload="Expert.fileLoaded('<?= $l['leadid']?>');" src="" class="uploadframe"></iframe>
</form>