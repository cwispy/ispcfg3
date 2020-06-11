{*
 /*  ISPConfig v3.1+ module for WHMCS v7.x or Higher
 *  Copyright (C) 2014 - 2018  Shane Chrisp
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
 */
 *}
<link href="modules/servers/ispcfg3/assets/ispcfg3.css" rel="stylesheet"><span class="icon-header icon-email"></span>
<h3>{$LANG.ispcfg3_manage_email_accounts} ({$params.domain})</h3>
<p>{$LANG.ispcfg3_manage_email_accounts_desc}</p>
    {*$variables.quota|print_r*}

<hr>
<h5>{$LANG.ispcfg3_current_mailboxes} ( 
    {$variables.mailboxes|@count} 
    {$LANG.ispcfg3_of} 
    {If $variables.client.limit_mailbox == -1}
        {$LANG.ispcfg3_unlimited}
    {else}
        {$variables.client.limit_mailbox}{/If} )
    </h5>

<div class="text-right">
    <button 
        class="btn btn-sm btn-success" 
        data-toggle="modal" 
        data-target="#modalAdd" 
    {If $variables.client.locked == "y" || 
        $variables.client.canceled == "y" ||
        ( {$variables.mailboxes|@count} >= $variables.client.limit_mailbox &&
        $variables.client.limit_mailbox != -1 )}
        disabled="disabled"
    {/If}
    >{$LANG.ispcfg3_add_email}</button>
</div>

{if is_array($variables.mailboxes) && count($variables.mailboxes) > 0}
    <table class="table table-condensed table-striped table-hover ihost-smart-table">
        <thead><tr><th>{$LANG.ispcfg3_email}</th><th class="text-right">{$LANG.ispcfg3_used_space} (MB)</th><th class="text-right">{$LANG.ispcfg3_quota} (MB)</th><th>&nbsp;</th></tr></thead>
        <tbody>
        {foreach $variables.quota as $quota}

            <tr>
                 <td>{$quota.email}</td>
                <td class="text-right">
                 {{$quota.used / 1048576}|number_format:2:".":""}
				</td>
				<td class="text-right">
				{{$quota.quota / 1048576}|number_format:2:".":""}
				</td>
                <td class="text-right">
                    {If $variables.client.locked == "y" || $variables.client.canceled == "y"}
                            <i class="fa fa-ban"></i>
                        {else}
                    <a href="javascript:;" class="btn btn-xs btn-default" id="btnAction" data-toggle="modal" data-target="#modalEdit" 
                       data-target-values="quota={$quota.quota / 1048576}&old_quota={$quota.quota / 1048576}&totalquota={$variables.client.limit_mailquota}&activeEmail={$quota.email}&mail_id={$quota.mailuser_id}&email={$quota.email}">
                        <i class="fa fa-pencil"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-default" id="btnAction" data-toggle="modal" data-target="#modalDelete" 
                       data-target-values="activeEmail={$quota.email}&mail_id={$quota.mailuser_id}&email={$quota.email}">
                        <i class="fa fa-times"></i></a>
                        {/If}
                </td>
            </tr>
            {assign "emailtotal" {$emailtotal} + {$quota.quota / 1048576} }
        {/foreach}
            <tr>
                <td class="text-left" colspan="2">{$LANG.ispcfg3_quota} ({$LANG.ispcfg3_allocated} / {$LANG.ispcfg3_assigned})</td>
                <td class="text-right">{$emailtotal} / {If $variables.client.limit_mailquota == -1}{$LANG.ispcfg3_unlimited}{else}{$variables.client.limit_mailquota}{/If}</td>
                <td>&nbsp;</td>
            </tr>
        </tbody>
    </table>

{else}
    <p>{$LANG.ispcfg3_no_emails_found}</p>
    <!-- {$variables|print_r} -->
	
{/if}
 
<div class="modal fade" id="modalAdd" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">{$LANG.ispcfg3_add_email_account}</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal ajax-form" id="frmAddEmail">
                    <div id="ajax-params" 
                         data-action="{$action_urls.add}" 
                         data-method="POST" data-loader="#ajax-loader-add" 
                         data-loader-position="outside"
                         data-loader-type="inside-button" 
                         data-messages="#ajax-messages" 
                         data-callback-on-success="window.location.reload()">
                    </div>
                    <div id="ajax-messages"></div>

                    <div class="form-group">
                        <label for="email" class="col-sm-4 control-label">Email</label>
                        <div class="col-sm-6">
                        <div class="input-group">
                                <input type="hidden" class="form-control" name="svrid" value="{$variables.domains.0.server_id}" id="svrid">
                                <input type="hidden" class="form-control" name="totalquota" id="totalquota">
                                <input type="hidden" class="form-control" name="old_quota" id="old_quota">
                                <input type="hidden" class="form-control" name="emailtotal" id="emailtotal" value="{$emailtotal}">
                                <input type="text" class="form-control" name="email" id="email" >
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="domain" class="col-sm-4 control-label">{$LANG.ispcfg3_domain}</label>
                        <div class="col-sm-6">
                        <div class="input-group">
                            <select class="form-control" name="domain" readonly="readonly">
                                <option>{$domain}</option>
                            </select>
                        </div>
                        </div>
                    </div>

                    <div id="newPassword11" class="form-group has-feedback">
                        <label for="inputNewPassword11" class="col-sm-5 control-label">{$LANG.newpassword}</label>
                        <div class="col-sm-6">
                        <div class="input-group">
                            <input type="password" class="form-control" name="password" id="inputNewPassword11" autocomplete="off" />
                            <span class="form-control-feedback glyphicon"></span>
                            <br />

                            <div class="progress" id="passwordStrengthBar1">
                                <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                    <span class="sr-only">New Password Rating: 0%</span>
                                </div>
                            </div>
                        {if file_exists("templates/$template/includes/alert.tpl")}
                            {include file="$template/includes/alert.tpl" type="info" msg="{$LANG.passwordtips}"}
                        {elseif file_exists("templates/six/includes/alert.tpl")}
                            {include file="six/includes/alert.tpl" type="info" msg="{$LANG.passwordtips}"}
                        {/if}

                        <script type="text/javascript">
                        jQuery("#inputNewPassword11").keyup(function() {
                            var $newPassword11 = jQuery("#newPassword11");
                            var pw = jQuery("#inputNewPassword11").val();
                            var pwlength=(pw.length);
                            if(pwlength>5)pwlength=5;
                            else if(pwlength>4)pwlength=4.5;
                            else if(pwlength>2)pwlength=3.5;
                            else if(pwlength>0)pwlength=2.5;
                            var numnumeric=pw.replace(/[0-9]/g,"");
                            var numeric=(pw.length-numnumeric.length);
                            if(numeric>3)numeric=3;
                            var symbols=pw.replace(/\W/g,"");
                            var numsymbols=(pw.length-symbols.length);
                            if(numsymbols>3)numsymbols=3;
                            var numupper=pw.replace(/[A-Z]/g,"");
                            var upper=(pw.length-numupper.length);
                            if(upper>3)upper=3;
                            var pwstrength=((pwlength*10)-20)+(numeric*10)+(numsymbols*15)+(upper*10);
                            if (pwstrength < 0) pwstrength = 0;
                            if (pwstrength > 100) pwstrength = 100;

                            $newPassword11.removeClass('has-error has-warning has-success');
                            jQuery("#inputNewPassword11").next('.form-control-feedback').removeClass('glyphicon-remove glyphicon-warning-sign glyphicon-ok');
                            jQuery("#passwordStrengthBar1 .progress-bar").removeClass("progress-bar-danger progress-bar-warning progress-bar-success").css("width", pwstrength + "%").attr('aria-valuenow', pwstrength);
                            jQuery("#passwordStrengthBar1 .progress-bar .sr-only").html('New Password Rating: ' + pwstrength + '%');
                            if (pwstrength < 30) {
                                $newPassword11.addClass('has-error');
                                jQuery("#inputNewPassword11").next('.form-control-feedback').addClass('glyphicon-remove');
                                jQuery("#passwordStrengthBar1 .progress-bar").addClass("progress-bar-danger");
                            } else if (pwstrength < 75) {
                                $newPassword11.addClass('has-warning');
                                jQuery("#inputNewPassword11").next('.form-control-feedback').addClass('glyphicon-warning-sign');
                                jQuery("#passwordStrengthBar1 .progress-bar").addClass("progress-bar-warning");
                            } else {
                                $newPassword11.addClass('has-success');
                                jQuery("#inputNewPassword11").next('.form-control-feedback').addClass('glyphicon-ok');
                                jQuery("#passwordStrengthBar1 .progress-bar").addClass("progress-bar-success");
                            }
                            validatePassword22();
                        });

                        function validatePassword22() {
                            var password1 = jQuery("#inputNewPassword11").val();
                            var password2 = jQuery("#inputNewPassword22").val();
                            var $newPassword22 = jQuery("#newPassword22");

                            if (password2 && password1 !== password2) {
                                $newPassword22.removeClass('has-success')
                                    .addClass('has-error');
                                jQuery("#inputNewPassword22").next('.form-control-feedback').removeClass('glyphicon-ok').addClass('glyphicon-remove');
                                jQuery("#inputNewPassword22Msg").html('<p class="help-block">{$LANG.pwdoesnotmatch|escape}</p>');
                                {if !isset($noDisable)}jQuery('input[type="submit"]').attr('disabled', 'disabled');{/if}
                            } else {
                                if (password2) {
                                    $newPassword22.removeClass('has-error')
                                        .addClass('has-success');
                                    jQuery("#inputNewPassword22").next('.form-control-feedback').removeClass('glyphicon-remove').addClass('glyphicon-ok');
                                    {if !isset($noDisable)}jQuery('.main-content input[type="submit"]').removeAttr('disabled');{/if}
                                } else {
                                    $newPassword22.removeClass('has-error has-success');
                                    jQuery("#inputNewPassword22").next('.form-control-feedback').removeClass('glyphicon-remove glyphicon-ok');
                                }
                                jQuery("#inputNewPassword22Msg").html('');
                            }
                        }

                        jQuery(document).ready(function(){
                            {if !isset($noDisable)}jQuery('.using-password-strength input[type="submit"]').attr('disabled', 'disabled');{/if}
                            jQuery("#inputNewPassword22").keyup(function() {
                                validatePassword22();
                            });
                        });

                        </script>
                        </div>
                        </div>
                    </div>
                    <div id="newPassword22" class="form-group has-feedback">
                        <label for="inputNewPassword22" class="col-sm-5 control-label">{$LANG.confirmnewpassword}</label>
                        <div class="col-sm-6">
                            <input type="password" class="form-control" name="password2" id="inputNewPassword22" autocomplete="off" />
                            <span class="form-control-feedback glyphicon"></span>
                            <div id="inputNewPassword22Msg"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="quota" class="col-sm-4 control-label">{$LANG.ispcfg3_quota}</label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <input type="number" class="form-control" name="quota" 
                                        {If $variables.client.limit_mailquota == -1} 
                                            value="0"
                                        {else} 
                                            min="1" max="{$variables.client.limit_mailquota - $emailtotal}"
                                        {/If}
                                        id="quota">
                                <span class="input-group-addon">MB</span>
                            </div>
                            {If $variables.client.limit_mailquota == -1} 
                                <p class="helper-block">enter 0 for unlimited</p>
                            {/If}
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal">{$LANG.cancel}</button>
                <button class="btn btn-success" onclick="$('#frmAddEmail').submit()"><span id="ajax-loader-add"></span> {$LANG.ispcfg3_create_email_account}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">{$LANG.ispcfg3_update_email_account} (<span id="activeEmail"></span>)</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal ajax-form" id="frmEditEmail">
                    <div id="ajax-params" data-action="{$action_urls.edit}" data-method="POST" data-loader="#ajax-loader-edit" data-loader-position="outside" 
                         data-loader-type="inside-button" data-messages="#ajax-messages" data-callback-on-success="window.location.reload()"></div>
                    <div id="ajax-messages"></div>
                    <input name="mail_id" type="hidden" id="mail_id">
                    <input name="email" type="hidden" id="email" >
                    <input type="hidden" class="form-control" name="totalquota" id="totalquota">
                    <input type="hidden" class="form-control" name="old_quota" id="old_quota">
                    <input type="hidden" class="form-control" name="emailtotal" id="emailtotal" value="{$emailtotal}">

                    <div id="newPassword33" class="form-group has-feedback">
                        <label for="inputNewPassword33" class="col-sm-5 control-label">{$LANG.newpassword}</label>
                        <div class="col-sm-6">
                            <input type="password" class="form-control" name="password" id="inputNewPassword33" autocomplete="off" />
                            <span class="form-control-feedback glyphicon"></span>
                            
                            <div class="progress" id="passwordStrengthBar2">
                                <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                    <span class="sr-only">New Password Rating: 0%</span>
                                </div>
                            </div>
                            
                        {if file_exists("templates/$template/includes/alert.tpl")}
                            {include file="$template/includes/alert.tpl" type="info" msg="{$LANG.passwordtips}"}
                        {elseif file_exists("templates/six/includes/alert.tpl")}
                            {include file="six/includes/alert.tpl" type="info" msg="{$LANG.passwordtips}"}
                        {/if}

                        <script type="text/javascript">
                        jQuery("#inputNewPassword33").keyup(function() {
                            var $newPassword33 = jQuery("#newPassword33");
                            var pw = jQuery("#inputNewPassword33").val();
                            var pwlength=(pw.length);
                            if(pwlength>5)pwlength=5;
                            else if(pwlength>4)pwlength=4.5;
                            else if(pwlength>2)pwlength=3.5;
                            else if(pwlength>0)pwlength=2.5;
                            var numnumeric=pw.replace(/[0-9]/g,"");
                            var numeric=(pw.length-numnumeric.length);
                            if(numeric>3)numeric=3;
                            var symbols=pw.replace(/\W/g,"");
                            var numsymbols=(pw.length-symbols.length);
                            if(numsymbols>3)numsymbols=3;
                            var numupper=pw.replace(/[A-Z]/g,"");
                            var upper=(pw.length-numupper.length);
                            if(upper>3)upper=3;
                            var pwstrength=((pwlength*10)-20)+(numeric*10)+(numsymbols*15)+(upper*10);
                            if (pwstrength < 0) pwstrength = 0;
                            if (pwstrength > 100) pwstrength = 100;

                            $newPassword33.removeClass('has-error has-warning has-success');
                            jQuery("#inputNewPassword33").next('.form-control-feedback').removeClass('glyphicon-remove glyphicon-warning-sign glyphicon-ok');
                            jQuery("#passwordStrengthBar2 .progress-bar").removeClass("progress-bar-danger progress-bar-warning progress-bar-success").css("width", pwstrength + "%").attr('aria-valuenow', pwstrength);
                            jQuery("#passwordStrengthBar2 .progress-bar .sr-only").html('New Password Rating: ' + pwstrength + '%');
                            if (pwstrength < 30) {
                                $newPassword33.addClass('has-error');
                                jQuery("#inputNewPassword33").next('.form-control-feedback').addClass('glyphicon-remove');
                                jQuery("#passwordStrengthBar2 .progress-bar").addClass("progress-bar-danger");
                            } else if (pwstrength < 75) {
                                $newPassword33.addClass('has-warning');
                                jQuery("#inputNewPassword33").next('.form-control-feedback').addClass('glyphicon-warning-sign');
                                jQuery("#passwordStrengthBar2 .progress-bar").addClass("progress-bar-warning");
                            } else {
                                $newPassword33.addClass('has-success');
                                jQuery("#inputNewPassword33").next('.form-control-feedback').addClass('glyphicon-ok');
                                jQuery("#passwordStrengthBar2 .progress-bar").addClass("progress-bar-success");
                            }
                            validatePassword44();
                        });

                        function validatePassword44() {
                            var password1 = jQuery("#inputNewPassword33").val();
                            var password2 = jQuery("#inputNewPassword44").val();
                            var $newPassword44 = jQuery("#newPassword44");

                            if (password2 && password1 !== password2) {
                                $newPassword44.removeClass('has-success')
                                    .addClass('has-error');
                                jQuery("#inputNewPassword44").next('.form-control-feedback').removeClass('glyphicon-ok').addClass('glyphicon-remove');
                                jQuery("#inputNewPassword44Msg").html('<p class="help-block">{$LANG.pwdoesnotmatch|escape}</p>');
                                {if !isset($noDisable)}jQuery('input[type="submit"]').attr('disabled', 'disabled');{/if}
                            } else {
                                if (password2) {
                                    $newPassword44.removeClass('has-error')
                                        .addClass('has-success');
                                    jQuery("#inputNewPassword44").next('.form-control-feedback').removeClass('glyphicon-remove').addClass('glyphicon-ok');
                                    {if !isset($noDisable)}jQuery('.main-content input[type="submit"]').removeAttr('disabled');{/if}
                                } else {
                                    $newPassword44.removeClass('has-error has-success');
                                    jQuery("#inputNewPassword44").next('.form-control-feedback').removeClass('glyphicon-remove glyphicon-ok');
                                }
                                jQuery("#inputNewPassword44Msg").html('');
                            }
                        }

                        jQuery(document).ready(function(){
                            {if !isset($noDisable)}jQuery('.using-password-strength input[type="submit"]').attr('disabled', 'disabled');{/if}
                            jQuery("#inputNewPassword44").keyup(function() {
                                validatePassword44();
                            });
                        });

                        </script>                        </div>
                    </div>
                    <div id="newPassword44" class="form-group has-feedback">
                        <label for="inputNewPassword44" class="col-sm-5 control-label">{$LANG.confirmnewpassword}</label>
                        <div class="col-sm-6">
                            <input type="password" class="form-control" name="password2" id="inputNewPassword44" autocomplete="off" />
                            <span class="form-control-feedback glyphicon"></span>
                            <div id="inputNewPassword44Msg"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="quota" class="col-sm-4 control-label">{$LANG.ispcfg3_quota}</label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <input type="number" class="form-control" name="quota" 
                                        {If $variables.client.limit_mailquota == -1} 
                                            value="0"
                                        {else} 
                                            min="1" max="{$variables.client.limit_mailquota}" 
                                        {/If}
                                        id="quota">
                                <span class="input-group-addon">MB</span>
                            </div>
                            {If $variables.client.limit_mailquota == -1} 
                                <p class="helper-block">enter 0 for unlimited</p>
                            {/If}
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button class="btn btn-success" onclick="$('#frmEditEmail').submit()"><span id="ajax-loader-edit"></span> Update Email Account</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Delete Email Account (<span id="activeEmail"></span>)</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal ajax-form" id="frmDeleteEmail">
                    <div id="ajax-params" data-action="{$action_urls.delete}" data-method="POST" data-loader="#ajax-loader-delete" data-loader-position="outside" 
                         data-loader-type="inside-button" data-messages="#ajax-messages" data-callback-on-success="window.location.reload()"></div>
                    <div id="ajax-messages"></div>
                    <input name="mail_id" type="hidden" id="mail_id">
                    <input name="email" type="hidden" id="email">
                </form>
                <p>Are you sure you want to delete this email account?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button class="btn btn-danger" onclick="$('#frmDeleteEmail').submit()"><span id="ajax-loader-delete"></span> Confirm</button>
            </div>
        </div>
    </div>
</div>
