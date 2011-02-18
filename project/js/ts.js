// CC 2010 Pavel Senko / rospil.info / rospil.net
// 

var Expert = {

    handleAction: function(link) {
        var action = $(link).attr('action');
        switch (action) {
            case 'upload':Expert.uploadAction(link);break;
            case 'assign':Expert.assignAction(link);break;
            case 'delete':Expert.deleteAction(link);break;
        }
    },
    assignAction: function(link) {
        var leadid = $(link).attr('item');
        var formstr = 'f=assign&leadid='+leadid;
        Util.linkAction(link, formstr,'сделано','');
    },
    deleteAction: function(link) {
        var leadid = $(link).attr('item');
        var formstr = 'f=deletedoc&leadid='+leadid;
        Util.linkAction(link, formstr,'файл удален','');
        $('#doc'+leadid).hide();
    },
    updateExpert: function(m,eid,a) {
        var link;
        if (m == 1) {
            link = $('#foundlink');
        } else if (m == 2) {
            link = $('#elink'+eid);
        }
        var formstr = 'f=updateexpert&userid='+eid+'&action='+a;
        Util.linkAction(link,formstr,'<span class="red">Сделано!</span>','');
    },
    findExpert: function() {
        $('#errorbox').hide();
        var formstr = $('#expertsearchform').serialize();
        $('#foundexpert').hide();
        $.ajax({
            type: "POST",
            url: "/a/",
            data: formstr,
            success: function(r) {
                var res = eval('('+r+')');
                if (res.status == 'OK') {
                    if (res.email.length) {
                        $('#foundname').html(res.name);
                        $('#foundemail').html(res.email);
                        if (res.type == 110) { // @TODO the constant in the global JS variable
                            $('#foundlink').html('<a href="#" onclick="Expert.updateExpert(1,'+res.userid+',1);return false;">лишить звания эксперта</a>');
                        } else {
                            if (res.type == 100) {
                                $('#foundlink').html('администратор');
                            } else {
                                $('#foundlink').html('<a href="#" onclick="Expert.updateExpert(1,'+res.userid+',0);return false;">сделать экспертом</a>');
                            }
                        }
                        $('#foundexpert').slideToggle(150);
                    } else {
                        $('#errortitle').html('Ошибка!');
                        $('#errormessage').html('Такого пользователя не найдено');
                        $('#errorbox').slideToggle(150);
                    }
                } else {
                    // display error from backend
                    $('#errortitle').html('Нелепая ошибка!');
                    $('#errormessage').html(res.message);
                    $('#errorbox').slideToggle(150);
                }
            }
        });
    },
    fileLoaded: function(unid) {
       var fd = window.frames['utc'+unid].document.getElementsByTagName("body")[0].innerHTML;
       $('#pictarget').hide();
       if (fd.length) {
            $('#fileupload'+unid).hide();
            var res = eval('('+fd+')');
            var url = res.url;
            var link = $('#filelink'+unid);
            var confirmstr = '<a href="'+url+'">экспертное заключение</a>';
            var formstr = 'f=attachfile&leadid='+unid+'&file='+url;
            Util.linkAction(link,formstr,confirmstr,'');
       }
    },
    uploadAction: function(link) {
        var leadid = $(link).attr('item');
        $(link).hide();
        $('#fileupload'+leadid).show();
    },
    fileUpload2: function(upload_field,type) {

        $('#pictarget').show();
        var ext = $(upload_field).val().split('.').pop().toLowerCase();
        var allow;

        if (type == 'image') {
            allow = new Array('png','jpg','png');
        } else {
            allow = new Array('doc','docx','ppt','pptx','txt','rtf');
        }

        if(jQuery.inArray(ext, allow) == -1) {
            alert("Поддерживаются только "+allow.join(', '));
            upload_field.form.reset();
            return false;
        }
        $('#expertdocform').submit();
        upload_field.disabled = true;
        return true;
    },
    fileUpload: function(upload_field,type) {
        $('#pictarget').show();
        var ext = $(upload_field).val().split('.').pop().toLowerCase();
        var allow;
        if (type == 'image') {
            allow = new Array('png','jpg','png');
        } else {
            allow = new Array('doc','docx','ppt','pptx','txt','rtf');
        }
        if(jQuery.inArray(ext, allow) == -1) {
            alert("Поддерживаются только "+allow.join(', '));
            upload_field.form.reset();
            return false;
        }
        upload_field.form.submit();
        upload_field.disabled = true;
        return true;
    }
}

var Search = {
  smallFormSubmit: function() {
    $('#smallsearchform').submit();
  }  
};

var User = {
  sentPetition: function(leadid) {
    var link = $('#isentpetition');
    var formstr = 'f=isent&leadid='+leadid;
    Util.linkAction(link,formstr,'Спасибо!','User.updatePetitionCount');
  },
  updatePetitionCount: function(res) {
    var cnt = res.count;
    $('#petitioncount').html(cnt);
    $('#isentpetition').html('Спасибо!');
  }
};

var Chief = {
    showEditForm: function() {
        $('#editchief a').html('редактирование');
        $('#picfield').show();
        $('#savechief').removeAttr('disabled');
        $('#picfield INPUT').removeAttr('disabled');
        $('#chiefform').slideToggle(300);

    },
    hideEditForm: function() {
        $('#chiefform').slideToggle(300);
        $('#editchief A').html('редактировать');
    },
    picLoaded: function(unid) {
       var fd = window.frames['utc'+unid].document.getElementsByTagName("body")[0].innerHTML;
       $('#filedata'+unid).attr('value',fd);
       $('#savechief').removeAttr('disabled');
       $('#pictarget').hide();
    },
    updateChiefArea: function (pic, chief_name, chief_contact) {
        if (pic.length) {$('#chief_pic').html('<img src="'+pic+'">');}
        $('#chief_name_area').html('Руководитель:<br>'+chief_name);
        $('#chief_contact_area').html(chief_contact);
        Chief.hideEditForm();
    },
    picUpload: function(upload_field,unid,restorename) {
        // this is just an example of checking file extensions
        // if you do not need extension checking, remove
        // everything down to line
        // upload_field.form.submit();

        $('#savechief').attr('disabled',true);

        $('#picfield').hide();
        $('#pictarget').show();

        var re_text = /\.png|\.jpg|\.gif/i;
        var filename = upload_field.value;

        /* Checking file type */
        if (filename.search(re_text) == -1) {
            alert("Поддерживаются только PNG, JPG и GIF");
            upload_field.form.reset();
            return false;
        }

        upload_field.form.f.value = 'fileupload'; // temp handler for file upload
        upload_field.form.submit();
        
        upload_field.disabled = true;
        upload_field.form.f.value = restorename; // restore original name of ajax server handler
        return true;
    }
};


var Util = {
    stayComment: function(userid,name) {
        $('#loginregisterarea').hide();
        $('#newcomment input[name="userid"]').val(userid);
        $('#newcomment input[name="user_name"]').val(name);
        $('#newcomment').removeClass('displaynone');
    },
    nl2br: function (str, is_xhtml) {
        var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
        return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
    },
    linkAction: function(link,formstr,confirmstr,callback) {
        $.ajax({
            type: "POST",
            url: "/a/",
            data: formstr,
            success: function(r) {
                var res = eval('('+r+')');
                if (res.status == 'OK') {
                    if (callback.length) {
                        eval(callback+'('+r+')');
                    } else {    
                        $(link).parent().html(confirmstr);
                    }
                } else {
                    $('#errortitle').html('Нелепая ошибка!');
                    $('#errormessage').html(res.message);
                    $('#errorbox').slideToggle(150);
                }
            }
        });
    }
};

var Comment = {
    insertComment: function(userid,user_name,comment,leadid,ts,cid) {
        $('#addcommentform #comment').hide();
        $('#addcommentform #comment').val('');
        
        var cc = comment_tpl;

        cc = cc.replace('__USER_NAME__',user_name);
        cc = cc.replace('__DATE__',ts);
        cc = cc.replace('__COMMENT__',comment);
        cc = cc.replace('__CID__',cid);
        cc = cc.replace('__DELETE__','');

        if ($('#no_comments').length>0) {
            $('#no_comments').hide();
        }
        $(cc).appendTo('#commentslist');
        $('#comment'+cid).css('display','none');
        $('#comment'+cid).slideToggle(200);
        $('#addcommentform #comment').css('height','60px');
        $('#addcommentform #comment').show();
    }
};

var Org = {
    updatePetitionTarget: function() {
        if ($("#petition_orgid option:selected").index() == 0) {
            $('#petition_org_name').removeAttr('readonly');
            $('#petition_org_name').removeClass('grey');
            $('#petition_org_name').attr('value','');
            $('#petition_org_name').focus();
            $('#petition_link').attr('value','');
            $('#petition_link').removeAttr('readonly');
            $('#petition_link').removeClass('grey');
        } else {
            $('#petition_org_name').attr('value',$("#petition_orgid option:selected").text());
            $('#petition_org_name').attr('readonly',true);
            $('#petition_org_name').addClass('grey');
            $('#petition_link').attr('value',$("#petition_orgid option:selected").attr('url'));
            $('#petition_link').attr('readonly',true);
            $('#petition_link').addClass('grey');
        }
    },
    updateOrgTarget: function() {
        if ($("#orgid option:selected").index() == 0) {
            $('#org_name').removeAttr('readonly');
            $('#org_name').removeClass('grey');
            $('#org_name').attr('value','');
            $('#org_name').focus();
        } else {
            $('#org_name').attr('value',$("#orgid option:selected").text());
            $('#org_name').attr('readonly',true);
            $('#org_name').addClass('grey');
        }
    }
}

var Reporting = {
    submitForm: function() {
        var formstr = $('#reportform').serialize();
        $.ajax({
            type: "POST",
            url: "/a/",
            data: formstr,
            success: function(r) {
                var res = eval('('+r+')');
                if (res.status == 'OK') {
                    // replace the whole area with confirmation message
                    $('#reportarea').html('<h2>\n\
                                           Спасибо! Мы записали. Наш редактор проверит информацию и опубликует чуть позже.\n\
                                           </h2><br/>\n\
                                           <a href="/">Вернуться на главную.</a>');
                    $('#reportarea').css('height','400px');
                } else {
                    // display error from backend
                    $('#errortitle').html('Нелепая ошибка!');
                    $('#errormessage').html(res.message);
                    $('#errorbox').slideToggle(150);
                }
            }
        });
    },
    validateForm: function() {
        $.each($('#reportform :input:not(input[type=hidden])'),function(i,field) {
            $('#errorbox').hide();
            if (($(field).attr('id') != 'postreport')) {
                $('#'+$(field).attr('id')).removeClass('highlighted');
            }
        });
        var valid = true;
        $.each($('#reportform').serializeArray(), function (i,field) {
            if (jQuery.trim(field.value).length == 0) {
                valid = false;
            }
        });
        return valid;
    },
    showEmptyFields: function() {
        // show error message
        $('#errorbox').slideToggle(150);

        // highlight empty fields
        //$('#reportform').find('.field :input').attr('class','highlighted');

        $.each($('#reportform :input:not(input[type=hidden])'),function(i,field) {

            //console.log($(field).attr('id'));
            //console.log(jQuery.trim($(field).val()).length);

            if (($(field).attr('id') != 'postreport') &&
                (jQuery.trim($(field).val()).length == 0 )) {
                $('#'+$(field).attr('id')).attr('class','highlighted');
            }
            // console.log(field);
        });

    },
    disableButton: function() {
       $('#postreport').attr('disabled',true);
    },
    enableButton: function() {
        $('#postreport').attr('disabled',false);
    },
    handleReporting: function() {
        // validate form (empty fields)
        var valid = Reporting.validateForm();
        Reporting.disableButton();

        // submit form
        if (valid) {
            Reporting.submitForm();
        } else {
            // error message
            Reporting.showEmptyFields();
        }

        Reporting.enableButton();
    }
};


var Register = {
     submitForm: function() {
        var formstr = $('#registerform').serialize();
        var jc = $('#registerform input[name="jc"]').length;
        var callBack = '';
        if (jc) callBack = $('#registerform input[name="jc"]').val();
        $.ajax({
            type: "POST",
            url: "/a/",
            data: formstr,
            success: function(r) {
                var res = eval('('+r+')');
                if (res.status == 'OK') {

                    if (callBack.length) {
                        eval(callBack+"("+res.userid+",'"+res.name+"')");
                    } else {
                        // replace the whole area with confirmation message
                        $('#registerarea').html('<h2>\n\
                                               Отлично! Вы зарегистрированы.\n\
                                               </h2><br/>\n\
                                               <a href="/">Вернуться на главную.</a>');
                        $('#registerarea').css('height','400px');
                    }
                } else {
                    // display error from backend
                    $('.register #errortitle').html('Нелепая ошибка!');
                    $('.register #errormessage').html(res.message);
                    $('.register #errorbox').slideToggle(150);
                }
            }
        });
    },
    validateForm: function() {
        var valid = true;
        if (
            (jQuery.trim($('.register #name').val()).length == 0) ||
            (jQuery.trim($('.register #password').val()).length == 0) ||
            (jQuery.trim($('.register #password2').val()).length == 0) ||
            ($('.register #password').val() != $('.register #password2').val()) ||
            ($('.register #password').val().lenght < 3)
        ) {
            valid = false;
        }
        return valid;
    },
    disableButton: function() {
       $('#doregister').attr('disabled',true);
    },
    enableButton: function() {
        $('#doregister').attr('disabled',false);
    },
    handleRegistration: function() {
        var valid = Register.validateForm();
        Register.disableButton();
        if (valid) {
            Register.submitForm();
        } else {

        }
        Register.enableButton();
    }
};

var Login = {
     submitForm: function() {
        var formstr = $('#loginform').serialize();
        var jc = $('#loginform input[name="jc"]').length;
        var callBack = '';
        if (jc) callBack = $('#loginform input[name="jc"]').val();
        $.ajax({
            type: "POST",
            url: "/a/",
            data: formstr,
            success: function(r) {
                var res = eval('('+r+')');
                if (res.status == 'OK') {
                    if (callBack.length) {
                        eval(callBack+"("+res.userid+",'"+res.name+"')");
                    } else {
                        // default action: go to home page
                        window.location.replace(siteurl);
                    }
                } else {
                    // display error from backend
                    $('#errortitle').html('Нелепая ошибка!');
                    $('#errormessage').html(res.message);
                    $('#errorbox').slideToggle(150);
                }
            }
        });
    },
    validateForm: function() {
        var valid = true;
        if (
            (jQuery.trim($('#name').val()).length == 0) ||
            (jQuery.trim($('#password').val()).length == 0) 
        ) {
            valid = false;
        }
        return valid;
    },
    disableButton: function() {
       $('#dologin').attr('disabled',true);
    },
    enableButton: function() {
        $('#dologin').attr('disabled',false);
    },

    handleLogin: function() {
        var valid = Login.validateForm();
        Login.disableButton();
        if (valid) {
            Login.submitForm();
        } else {

        }
        Login.enableButton();
    }
};

var Admin = {
    handleAction: function(link) {
        var action = $(link).attr('action');
        switch (action) {
            case 'cancel':Admin.cancelAction(link);break;
            case 'cancelpic':Admin.cancelPic(link);break;
            case 'deletelead':Admin.handleDelete(link,'item');break;
            case 'deletecomment':Admin.handleDelete(link,'comment');break;
            case 'preselect':Admin.handlePreselect(link,'item');break;
            case 'showadd':Admin.handleShowAdd(link);break;
            case 'deleteadd':Admin.handleDeleteAdd(link);break;
            case 'addscreen':Admin.handleAddScreen(link);break;
            case 'deletescreen':Admin.handleDeleteScreen(link);break;
            case 'resetcancel':Admin.handleResetCancel(link);break;
        }
    },
    handleResetCancel: function(link) {
        var leadid = $(link).attr('item');
        var formstr = 'f=resetcancel&leadid='+leadid;
        Util.linkAction(link, formstr,'сброшено','');
    },
    handleAddScreen: function(link) {
        var leadid = $(link).attr('item');
        if ($('[action="addscreen"]').length > 0) {
            $('#adminpicarea'+leadid).show();
        }
        $('#fileupload'+leadid).show();
        $(link).hide();
    },
    handleDeleteScreen: function(link) {
        var leadid = $(link).attr('item');
        var formstr = 'f=deletescreen&leadid='+leadid;
        Util.linkAction(link, formstr,'удалено','');
    },
    handleDeleteAdd: function(link) {
        var addid = $(link).attr('item');
        $('#add'+addid).slideToggle();
        var formstr = 'f=deleteadd&addid='+addid;
        Util.linkAction(link, formstr,'удалено','');
    },
    handleShowAdd: function(link) {
        $('#addtolead').slideToggle();
    },
    handlePreselect: function(link) {
        if (confirm("Уверены?")) {
            var leadid = $(link).attr('item');
            var formstr = 'f=preselect&leadid='+leadid;
            Util.linkAction(link, formstr,'сделано','');
        }
    },
    cancelPic: function(link) {
      var leadid = $(link).attr('item');
      $('#adminpicarea'+leadid).hide();
      $('#file'+leadid).removeAttr('disabled');
      $('#file'+leadid).val('');
      if ($('[action="addscreen"]').length > 0) {
        $('[action="addscreen"]').show();
      }
    },
    updatePic: function(res) {
      $('#pic'+res.leadid).html('<img src="'+res.url+'">');
    },
    updateAddArea: function(res) {
        var addthis = '<div class="addts">Добавлено '+res.ts+':</div>'+
                  '<div class="added">'+res.text+'</div>';
        $(addthis).insertBefore('#addingtolead');
        $('#addtolead').hide();
//      $('#pic'+res.leadid).html('<img src="'+res.url+'">');
    },
    fileLoaded: function(unid) {
       var fd = window.frames['utc'+unid].document.getElementsByTagName("body")[0].innerHTML;
       $('#pictarget').hide();
       if (fd.length) {
            $('#fileupload'+unid).hide();
            var res = eval('('+fd+')');
            var url = res.url;
            var link = $('#filelink'+unid);
            var confirmstr = '';
            var formstr = 'f=attachpic&leadid='+unid+'&file='+url;
            Util.linkAction(link,formstr,confirmstr,'Admin.updatePic');
            $('#adminpicarea'+unid).hide();
       }
    },
    cancelAction: function(link) {
        if (confirm("Уверены, что конкурс отменили?")) {
            var leadid = $(link).attr('item');
            var formstr = 'f=cancel&leadid='+leadid;
            Util.linkAction(link, formstr,'загрузим скриншот?','');
            $('#status'+leadid).html('<span class="green"><b>конкурс отменен</b></span>');
            $('#fileupload'+leadid).show();
        }
    },
    handleDelete: function(lead,item) {
        if (confirm("Точно удалить?")) {
            var itemid = $(lead).attr('item');
            var formstr = "f="+item+"del&id="+itemid;
            $.ajax({
                type: "POST",
                url: "/a/",
                data: formstr,
                success: function(r) {
                    var res = eval('('+r+')');
                    if (res.status == 'OK') {
                        $('#'+item+itemid).slideToggle(200);
                    } else {
                        // display error from backend
                        $('#errortitle').html('Нелепая ошибка!');
                        $('#errormessage').html(res.message);
                        $('#errorbox').slideToggle(150);
                    }
                }
            });
        }
        return false;
    }
};

var Publish = {
    submitForm: function() {
        // disable all form buttons
        var formstr = $('#publishform').serialize();
        $.ajax({
            type: "POST",
            url: "/a/",
            data: formstr,
            success: function(r) {
                var res = eval('('+r+')');   
                if (res.status == 'OK') {
                    // replace the whole area with confirmation message
                    $('#reportarea').html('<h2>\n\
                                          Опубликовано!\n\
                                           </h2><br/>\n\
                                           <a href="/">Вернуться на главную.</a>');
                    $('#reportarea').css('height','400px');
                } else {

                    // display error from backend
                    $('#errortitle').html('Нелепая ошибка!');
                    $('#errormessage').html(res.message);
                    $('#errorbox').slideToggle(150);
                }
            }
        });
    },
    validateForm: function() {
        $.each($('#reportform :input:not(input[type=hidden])'),function(i,field) {
            $('#errorbox').hide();
            if (($(field).attr('id') != 'postreport')) {
                $('#'+$(field).attr('id')).removeClass('highlighted');
            }
        });
        var valid = true;
        $.each($('#reportform').serializeArray(), function (i,field) {
            if (jQuery.trim(field.value).length == 0) {
                valid = false;
            }
        });
        return valid;
    },
    showEmptyFields: function() {
        // show error message
        $('#errorbox').slideToggle(150);

        // highlight empty fields
        //$('#reportform').find('.field :input').attr('class','highlighted');

        $.each($('#reportform :input:not(input[type=hidden])'),function(i,field) {

            //console.log($(field).attr('id'));
            //console.log(jQuery.trim($(field).val()).length);

            if (($(field).attr('id') != 'postreport') &&
                (jQuery.trim($(field).val()).length == 0 )) {
                $('#'+$(field).attr('id')).attr('class','highlighted');
            }
            // console.log(field);
        });

    },
    disableButton: function() {
       $('#postreport').attr('disabled',true);
    },
    enableButton: function() {
        $('#postreport').attr('disabled',false);
    },
    handlePublish: function() {

        var valid = Publish.validateForm();
        Publish.disableButton();

        // submit form
        if (valid) {
            Publish.submitForm();
        } else {
            // error message
            Publish.showEmptyFields();
        }

        Publish.enableButton();
    }
};

var ProcessForm = {
    resetForm: function(id,btn) {
        $.each($('#'+id+' :input'),function(i,field) {
            $('#errorbox').hide();
            if (($(field).attr('id') == btn) ||
                ($(field).attr('value') == 'f') ||
                ($(field).attr('value') == 'orgid') ||
                ($(field).attr('value') == 'leadid')
            ) {
                return true;
            }
            $(field).attr('value','');
        });
    },
    submitForm: function(id,btn) {
        $('#'+btn).hide();
        $('<p id="tmpmsg">Обновляем информацию...</p>').insertAfter('#'+btn);
        var formstr = $('#'+id).serialize();
        $.ajax({
            type: "POST",
            url: "/a/",
            data: formstr,
            success: function(r) {
                $('#tmpmsg').remove();
                $('#'+btn).show();
                var res = eval('('+r+')');
                if (res.status == 'OK') { // either local forward or display message. @TODO: add function calls (show/hide/update areas)
                    $(':input','#'+id)
                         .not(':button, :submit, :reset, :hidden')
                         .val('')
                         .removeAttr('checked')
                         .removeAttr('selected');
                    if (res.message.indexOf('/') == 0) {
                        window.location.replace(res.message);
                    } else {
                        if (id == 'addcommentform') { // special case for comments
                            Comment.insertComment(res.userid, res.user_name, res.comment, res.leadid, res.ts, res.cid);
                        }
                        else if (id == 'chiefeditform') { // special case for chief updates
                            Chief.updateChiefArea(res.pic, res.chief_name, res.chief_contact);
                        }
                        else if (id == 'addtoleadform') { // special case for additions
                            Admin.updateAddArea(res);
                        }
                        else {
                            $('#'+id).html(res.message);
                            $('#'+id).css('height','400px');
                        }
                    }
                } else {
                    $('#errortitle').html('Нелепая ошибка!');
                    $('#errormessage').html(res.message);
                    $('#errorbox').slideToggle(150);
                }
            }
        });
    },
    validateForm: function(id,btn) {
        var valid = true;
        $.each($('#'+id+' :input:not(input[type=hidden])'),function(i,field) {
            $('#errorbox').hide();
            if (($(field).attr('id') != btn)) {
                $('#'+$(field).attr('id')).removeClass('highlighted');
            }
            if ($(field).attr('canbe') == 'empty') {
                return true;
            }
            if (jQuery.trim($(field).attr('value')).lenght == 0) {
                valid = false;
            }
        });

        return valid;
    },
    showEmptyFields: function(id) {
        $('#errorbox').slideToggle(150);
        $.each($('#'+id+' :input:not(input[type=hidden])'),function(i,field) {
            if (($(field).attr('id') != 'postreport') &&
                (jQuery.trim($(field).val()).length == 0 )) {
                $('#'+$(field).attr('id')).attr('class','highlighted');
            }
        });
    },
    disableButton: function(btn) {
       $('#'+btn).attr('disabled',true);
    },
    enableButton: function(btn) {
        $('#'+btn).attr('disabled',false);
    },
    handleForm: function(formid,submitid) {
        var valid = ProcessForm.validateForm(formid,submitid);
        ProcessForm.disableButton(submitid);
        if (valid) {
            ProcessForm.submitForm(formid,submitid);
        } else {
            ProcessForm.showEmptyFields(formid);
        }
        ProcessForm.enableButton(submitid);
        //ProcessForm.resetForm(formid,submitid);
    }
};





$(window).bind("beforeunload", function(){
    // see if we need this
});

$(document).ready(function() {
    $('.delete').click( function(event) {
        event.preventDefault();
        Admin.handleDelete(this);
    });
    $('.expertaction').click( function(event) {
        event.preventDefault();
        Expert.handleAction(this);
    });
    $('.adminaction').click( function(event) {
        event.preventDefault();
        Admin.handleAction(this);
    });
    $('#loginform').submit( function(event) {
        event.preventDefault();
        Login.handleLogin();
    });
    $('#reportform').submit( function(event) {
        event.preventDefault();
        Reporting.handleReporting();
    });
    $('#registerform').submit( function(event) {
        event.preventDefault();
        Register.handleRegistration();
    });
    $('#regexpertform').submit( function(event) {
        event.preventDefault();
        ProcessForm.handleForm('regexpertform','regexpert');
    });
    $('#publishform').submit( function(event) {
        event.preventDefault();
        ProcessForm.handleForm('publishform','dopublish');
    });
    $('#addcommentform').submit( function(event) {
        event.preventDefault();
        ProcessForm.handleForm('addcommentform','postcomment');
    });
    $('#addtoleadform').submit( function(event) {
        event.preventDefault();
        ProcessForm.handleForm('addtoleadform','postadd');
    });
    $('#text2copy').click(function() {
        $('#text2copy').select();
     });
    $('#editchief a').click(function(event) {
        event.preventDefault();
        Chief.showEditForm();
     });
    $('#chiefform a').click(function(event) {
        event.preventDefault();
        Chief.hideEditForm();
     });
    $('#chiefeditform').submit( function(event) {
        event.preventDefault();
        ProcessForm.handleForm('chiefeditform','savechief');
    });
    $('#expertsearchform').submit( function(event) {
        event.preventDefault();
        Expert.findExpert();
    });

});

(function($) {

    /*
     * Auto-growing textareas; technique ripped from Facebook
     */
    $.fn.autogrow = function(options) {

        this.filter('textarea').each(function() {

            var $this       = $(this),
                minHeight   = $this.height(),
                lineHeight  = $this.css('lineHeight');

            var shadow = $('<div></div>').css({
                position:   'absolute',
                top:        -10000,
                left:       -10000,
                width:      $(this).width(),
                fontSize:   $this.css('fontSize'),
                fontFamily: $this.css('fontFamily'),
                lineHeight: $this.css('lineHeight'),
                resize:     'none'
            }).appendTo(document.body);

            var update = function() {

                var val = this.value.replace(/</g, '&lt;')
                                    .replace(/>/g, '&gt;')
                                    .replace(/&/g, '&amp;')
                                    .replace(/\n/g, '<br/>');

                shadow.html(val);
                $(this).css('height', Math.max(shadow.height() + 20, minHeight));
            }

            $(this).change(update).keyup(update).keydown(update);

            update.apply(this);

        });

        return this;

    }

})(jQuery);