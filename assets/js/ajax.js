/*
 * 
 *  ISPConfig v3.1+ module for WHMCS v7.x or Higher
 *  Copyright (C) 2014 - 2017  Shane Chrisp
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
function doAjaxPost(type,url,data,oResponse,oMessage,oLoading,extraData) {
	var loaderType = "";
	if (extraData != "" || extraData != "undefined") {
		loaderType = extraData['loader-type'];
		responseType = extraData['response-type'];
	}

	hideMessage(oMessage);
	showLoading(oLoading,loaderType);

	$.ajax({
		cache: false,
		timeout: 60000,
		type: type,
		url: url,
		data: data,
		dataType: "json",
		success: function(resp){
			hideLoading(oLoading,loaderType);
			if (oResponse != undefined && resp.data != undefined) {
				if (responseType == "prepend") {
					oResponse.prepend(resp.data);
				}
				else if (responseType == "append") {
					oResponse.append(resp.data);
				}
				else {
					oResponse.html(resp.data);
				}
			}
			if (resp.status == "error") {
				if (resp.message != undefined) {
					showMessage("error",resp.message,oMessage);
				}

				var callbackOnError = extraData['callback-on-error'];
				if (typeof(callbackOnError) !== undefined && callbackOnError != "") {
					eval(callbackOnError);
				}

			}
			else if (resp.status == "success") {
				if (resp.message != undefined && resp.message != "") {
					showMessage("success",resp.message,oMessage);
				}

				var callback = extraData['callback'];
				if (typeof(callback) !== undefined && callback != "") {
					eval(callback);
				}

				var callbackOnSuccess = extraData['callback-on-success'];
				if (typeof(callbackOnSuccess) !== undefined && callbackOnSuccess != "") {
					eval(callbackOnSuccess);
				}

			}
			if (resp.response_callback != undefined) {
				eval(resp.response_callback);
			}
			if (resp.goto != undefined) {
				document.location = resp.goto;
			}
		},
		error: function(xhr,textStatus,thrownError){
			if (textStatus == "timeout") {
				var instanceID = "ajaxID"+Math.floor((Math.random()*100)+1);
				showMessage("error","Request has taken too long. This is usually caused by slow internet connection. <a class=\"btn btn-default btn-xs\" href=\"javascript:;\" id=\""+instanceID+"\">Try again</a>",oMessage);
				$(oMessage).find('#'+instanceID).click(function(){
					doAjaxPost(type,url,data,oResponse,oMessage,oLoading,extraData);
				});
			}
			else {
				alert(xhr.responseText);
				alert(thrownError);
				showMessage("error","There was an error processing your request. Please try again.",oMessage);
			}
			hideLoading(oLoading,loaderType);
		}
	});
}

function showLoading(oElement,loaderType) {
	oElement = (oElement != undefined && oElement != "" && oElement != null) ? oElement : $("body");
	if (loaderType == "inside-button") oElement.parent().attr("disabled",true);
	oElement.html('<span id="loading"><i class="fa fa-spinner fa-spin"></i></span>').show();
}

function hideLoading(oElement,loaderType) {
	if (oElement != undefined && oElement != "" && oElement != null) {
		oElement.find('#loading').html("").hide();
		if (loaderType == "inside-button") oElement.parent().attr("disabled",false);
	}
}

function showMessage(type,message,oElement) {
	oElement = (oElement != undefined && oElement != "" && oElement != null) ? oElement : $("#global-messages");
	if (type == "error") type = "danger";
	if (oElement.length > 0) {
		oElement.hide();
		oElement.html('<div class="alert alert-' + type + '"><a class="close" data-dismiss="alert">&times;</a>' + message + '</div>');
		oElement.fadeIn('slow');
		$("html,body").animate({ scrollTop : oElement.offset().top-100 }, 1000);
	}
}

function hideMessage(oElement) {
	oElement = (oElement != undefined && oElement != "" && oElement != null) ? oElement : $("#global-messages");
	oElement.children().fadeOut('slow');
}

function doFormPost(frmName) {
	oElement = (oElement != undefined && oElement != "") ? oElement : ".error";
	$(oElement)
	.html(message)
	.slideDown('fast')
}

function ajaxify(mainContainer) {
	var oParams = mainContainer.find('#ajax-params');

	if (oParams.size() == 0) {
		return false;
	}

	var pMessages = oParams.attr('data-messages-position');
	var pResponse = oParams.attr('data-response-position');
	var pLoader = oParams.attr('data-loader-position');

	if (typeof(pMessages) === "undefined" || pMessages == "") pMessages = "inside";
	if (typeof(pResponse) === "undefined" || pResponse == "") pResponse = "inside";
	if (typeof(pLoader) === "undefined" || pLoader == "") pLoader = "inside";

	var jMessages = oParams.attr('data-messages');
	var jResponse = oParams.attr('data-response');
	var jLoader = oParams.attr('data-loader');
	if (typeof(jMessages) !== "undefined") {
		jMessages = (pMessages == "inside") ? mainContainer.find(jMessages) : $(jMessages);
	}
	if (typeof(jResponse) !== "undefined") {
		jResponse = (pResponse == "inside") ? mainContainer.find(jResponse) : $(jResponse);
	}
	if (typeof(jLoader) !== "undefined") {
		jLoader = (pLoader == "inside") ? mainContainer.find(jLoader) : $(jLoader);
	}

	var extraData = new Array();
	tLoader = oParams.attr('data-loader-type');
	tResponse = oParams.attr('data-response-type');

	if (typeof(tLoader) === "undefined" || tLoader == "") tLoader = "inside-button";
	if (typeof(tResponse) === "undefined" || tResponse == "") tResponse = "replace";

	extraData['loader-type'] = tLoader;
	extraData['response-type'] = tResponse;
	extraData['callback'] = oParams.attr('data-callback');
	extraData['callback-on-success'] = oParams.attr('data-callback-on-success');
	extraData['callback-on-error'] = oParams.attr('data-callback-on-error');

	var formAction = oParams.attr('data-action');
	var formData = oParams.attr('data-querystring');
	var formMethod = oParams.attr('data-method');

	if (typeof(formMethod) === "undefined") formMethod = "GET";
	formMethod.toUpperCase();

	var jConfirm = oParams.attr('data-confirm');
	if (typeof(jConfirm) !== "undefined") {
		if (jConfirm == "false") {
			return false;
		}
		else {
			strConfirm = (jConfirm != "true") ? jConfirm : "Do you really want to continue?";
			var rConfirm = confirm(strConfirm);
			if (!rConfirm) {
				return false;
			}
		}
	}

	doAjaxPost(formMethod,formAction,formData,jResponse,jMessages,jLoader,extraData);

	return false;
}

function ajaxifyForm(thisForm) {
	oParams = thisForm.find('#ajax-params');
	oParams.attr('data-querystring',thisForm.serialize());
	ajaxify(thisForm);
}

$(document).ready(function(){
	$('.ajaxifyMyForm').each(function(){
		var mainContainer = $(this);
		$(this).find('form').eq(0).submit(function(e){
			e.preventDefault();
			oParams = mainContainer.find('#ajax-params');
			oParams.attr('data-querystring',$(this).serialize());
			ajaxify(mainContainer);
		});
	});

	$('.ajaxifyMeNow').each(function(){
		var mainContainer = $(this);
		ajaxify(mainContainer);
	});

	$('.ajax-form').each(function(){
		$(this).submit(function(){
			ajaxifyForm($(this));
			return false;
		});
	});

	$('.ajaxifyButton').each(function(){
		var mainContainer = $(this);
		$(this).click(function(e){
			e.preventDefault();
			ajaxify(mainContainer);
		});
	});
});
