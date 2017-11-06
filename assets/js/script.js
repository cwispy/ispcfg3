$(document).ready(function(){
    if (jQuery().dataTable) {
        if ($(".dataTable tr td[colspan]").size() == 0) {
            $(".dataTable").dataTable({paging:false,info:false,aaSorting:[]});
        }
    }

    $('.ihost-smart-table tr #btnAction').each(function(){
        $(this).on('click', function(e){
            var rawValues = $(this).attr('data-target-values');
            var target = $(this).attr('data-target');
            var values = {};
            rawValues.replace(new RegExp("([^?=&]+)(=([^&]*))?", "g"),function($0, $1, $2, $3) {values[$1] = $3;});
            if (rawValues.length > 0) {
                $.each(values, function(key, val){
                    var elementTarget = $(target).find('#'+key);
                    var elementTargetTagName = elementTarget.prop("tagName");
                    var elementTargetType = elementTarget.attr("type");
                    if (elementTargetTagName == 'INPUT' && ['text','password','hidden','number'].indexOf(elementTargetType) > -1) {
                        elementTarget.val(val);
                    }
                    else if (elementTargetTagName == 'SELECT') {
                        elementTarget.val(val);
                    }
                    else {
                        elementTarget.html(val);                            
                    }
                });
            }
        });
    });

    $('.ajaxCpLogin').each(function(){
        var url = $(this).attr('data-url');
        var username = $(this).attr('data-username');
        var password = $(this).attr('data-password');
        var destination = $(this).attr('data-redirect');
        doAjaxCpLogin(url, username, password, destination);
    });

	
	
	
	
	
    $('.elfinderInit').each(function(){
        var ftpData = $(this).attr('data-custom-ftp-data');
        $(this).elfinder({
            url : 'modules/servers/ispcfg3/elfinder.connector.php',
            customData: {ftp: ftpData}
        });
    });

    $('#common_settings').on('change', function(e){
        var common_settings_val = $(this).val();
        var group = $(this).attr('data-group');

        var children_container = (group == 'add') ? '#frmAdd' : '#frmEdit';

        $(children_container).find('#minute_val,#hour_val,#day_val,#month_val,#weekday_val,#minute_freq,#hour_freq,#day_freq,#month_freq,#weekday_freq').val('');

        var arrVals = common_settings_val.split(" ");
        if (arrVals.length == 5) {
            $(children_container).find('#minute_val').val(arrVals[0]);
            $(children_container).find('#hour_val').val(arrVals[1]);
            $(children_container).find('#day_val').val(arrVals[2]);
            $(children_container).find('#month_val').val(arrVals[3]);
            $(children_container).find('#weekday_val').val(arrVals[4]);
        }
    });

    $('#frmAdd,#frmEdit').find('#minute_freq,#hour_freq,#day_freq,#month_freq,#weekday_freq').on('change', function(e){
        var target = $(this).parent().siblings().find('input');
        var val = $(this).val();
        target.val(val);
    });


    $('[data-view="dns"] #modalAdd, [data-view="dns"] #modalEdit').on('shown.bs.modal', function(e){
        $('#frmAdd #zone, #frmEdit #zone').each(function(){
            updateZone($(this));
        });

        $('#frmAdd #zone, #frmEdit #zone').on('change',function(){
            updateZone($(this));
        });
    });
});

var updateZone = function(selector) {
        var selected = selector.find(':selected');
        var selectedVal = selected.length > 0 ? selected.html().slice(0, -1) : '';
        if (selectedVal) {
            var parentForm = selector.parents('form');
            parentForm.find('.zone-placeholder').html(selectedVal);
        }
}

var doAjaxCpLogin = function(url, username, password, destination) {
    $.ajax({ 
        type: "POST", 
        url: url,
        data: "s_mod=login&s_pg=index&username=" + username + "&passwort=" + password, 
        xhrFields: {withCredentials: true},
        success: function(data) {
            window.location.href = destination;
        },
        error: function(xhr,textStatus,thrownError){
            window.location.href = destination;
        }
    });
}
