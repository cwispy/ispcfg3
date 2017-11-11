{*
 /*  ISPConfig v3.1+ module for WHMCS v7.x or Higher
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
 */
 *}
<link href="modules/servers/ispcfg3/assets/ispcfg3.css" rel="stylesheet">
<span class="icon-header icon-database"></span>
<h3>Manage Databases</h3>
<p>MySQL databases are required by many web applications . To use a database, you will need to create one first. </p>
<hr>
<h5>Current Databases ( {$variables.dbs|@count} of {If $variables.client.limit_database == -1}Unlimited{else}{$variables.client.limit_database}{/If} )</h5>
<div class="text-right">
    <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalAddDB"
    {If $variables.client.locked == "y" || $variables.client.canceled == "y"
    || ({$variables.dbs|@count} >= {$variables.client.limit_database} 
        && {$variables.client.limit_database != -1}) }
        disabled="disabled"
    {/If}
    >Add Database</button>
</div>

{*$variables.domains|print_r*}
{assign "userid" "{$variables.client.customer_no_template}"}
{if is_array($variables.dbs) && count($variables.dbs) > 0}
    <table class="table table-condensed table-striped table-hover ihost-smart-table">
        <thead><tr><th>Database</th><th>R/W User</th><th>R/O User</th><th>Quota (MB)</th><th>&nbsp;</th></tr></thead>
        <tbody>
        {foreach $variables.dbs as $db}
            <tr>
                <td>{$db.database_name}</td>
                <td>{$variables.db_users_o[$db.database_user_id].database_user}</td>
                <td>{$variables.db_users_o[$db.database_ro_user_id].database_user}</td>
                <td>{If $db.database_quota == -1}{assign "hd_quota" "0"}Unlimited{else}{assign "hd_quota" "{$db.database_quota}"}{$db.database_quota}{/If}</td>
                <td class="text-right">
                {If $variables.client.locked == "y" || $variables.client.canceled == "y"}
                    <i class="fa fa-ban"></i>
                {else}
                    <a href="javascript:;" class="btn btn-xs btn-default" id="btnAction" data-toggle="modal" data-target="#modalEditDB" data-target-values="database_id={$db.database_id}&parent_domain_id={$db.parent_domain_id}&database_name={$db.database_name|replace:$variables.client.customer_no:''}&database_name_prefix={$db.database_name_prefix}&database_user_id={$db.database_user_id}&database_ro_user_id={$db.database_ro_user_id}&database_quota={If $db.database_quota == -1}0{else}{$db.database_quota}{/If}"><i class="fa fa-pencil"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-default" id="btnAction" data-toggle="modal" data-target="#modalDeleteDB" data-target-values="database_id={$db.database_id}"><i class="fa fa-times"></i></a>
                {/If}
                </td>
            </tr>
            {assign "hdtotal" {$hdtotal}+{$hd_quota}}
        {/foreach}
            <tr>
                <td>Quota (Used / Assigned)</td>
                <td></td>
                <td></td>
                <td>{If $hdtotal == '0'}Unlimited{else}{$hdtotal}{/If} / {if $variables.client.limit_database_quota == -1}Unlimited{else}{$variables.client.limit_database_quota}{/If}</td>
            </tr>
        </tbody>
    </table>
{else}
    <p>No databases found</p>
{/if}

<h5>Current Database Users ( {$variables.db_users|@count} of {If $variables.client.limit_database_user == -1}Unlimited{else}{$variables.client.limit_database_user}{/If} )</h5><p>A Mysql user requires privileges to access a database in order to read from or write to that database. Assign or create a user for each database. You can login to PhpMyadmin using the database user to administer your database.</p>
<div class="text-right">
    <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalAddDBUser"
    {If $variables.client.locked == "y" || $variables.client.canceled == "y"
    || ( {$variables.db_users|@count} >= {$variables.client.limit_database_user}
    && {$variables.client.limit_database_user != -1} ) }
        disabled="disabled"
    {/If}
    >Add Database User</button>
</div>
{if is_array($variables.db_users) && count($variables.db_users) > 0}
    <table class="table table-condensed table-striped table-hover ihost-smart-table">
        <thead><tr><th>User</th><th>&nbsp;</th></tr></thead>
        <tbody>
        {foreach $variables.db_users as $db_user}
            <tr>
                <td>{$db_user.database_user}</td>
                <td class="text-right">
                {If $variables.client.locked == "y" || $variables.client.canceled == "y"}
                    <i class="fa fa-ban"></i>
                {else}
                    <a href="javascript:;" class="btn btn-xs btn-default" id="btnAction" data-toggle="modal" data-target="#modalEditDBUser" data-target-values="database_user_id={$db_user.database_user_id}&username={$db_user.database_user}"><i class="fa fa-pencil"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-default" id="btnAction" data-toggle="modal" data-target="#modalDeleteDBUser" data-target-values="database_user_id={$db_user.database_user_id}"><i class="fa fa-times"></i></a>
                {/If}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
{else}
    <p>No database users found</p>
{/if}

<div class="modal fade" id="modalAddDB" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Add Database</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal ajax-form" id="frmAdd">
                    <div id="ajax-params" data-action="{$action_urls.db.add}" data-method="POST" data-loader="#modalAddDB #ajax-loader-add" data-loader-position="outside" data-loader-type="inside-button" data-messages="#ajax-messages" data-callback-on-success="window.location.reload()"></div>
                    <div id="ajax-messages"></div>

                    <div class="form-group">
                        <label for="database_name" class="col-sm-4 control-label">Database name</label>
                        <div class="col-sm-6"> 
						<div class="input-group">
                            <span class="input-group-addon">{$variables.client.customer_no}</span>
                            <input type="hidden" class="form-control" name="server_id" id="domain" value="{$variables.client.db_servers}">
                            <input type="hidden" class="form-control" name="domain" id="domain" value="{$params.domain}">
                            <input type="hidden" class="form-control" name="prefix" id="prefix" value="{$variables.client.customer_no}">
                            <input type="hidden" class="form-control" name="hdtotalused" id="prefix" value="{$hdtotal}">
                            <input type="hidden" class="form-control" name="limit_database_quota" id="prefix" value="{$variables.client.limit_database_quota}">
                            <input type="text" class="form-control" name="database_name" id="database_name">
							 </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="parent_domain_id" class="col-sm-4 control-label">Site</label>
                        <div class="col-sm-6">
                            <select class="form-control" name="parent_domain_id" id="parent_domain_id">
                                <option value="">- select site -</option>
                                {if is_array($variables.domains) && count($variables.domains) > 0}
                                    {foreach $variables.domains as $domain}
                                        <option value="{$domain.domain_id}">{$domain.domain}</option>
                                    {/foreach}
                                {/if}
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="database_user" class="col-sm-4 control-label">Read / Write User</label>
                        <div class="col-sm-6">
                            <select class="form-control" name="database_user_id" id="database_user_id">
                                <option></option>
                                {if is_array($variables.db_users) && count($variables.db_users) > 0}
                                    {foreach $variables.db_users as $db_user}
                                        <option value="{$db_user.database_user_id}">{$db_user.database_user}</option>
                                    {/foreach}
                                {/if}
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="database_ro_user_id" class="col-sm-4 control-label">Read-only user</label>
                        <div class="col-sm-6">
                            <select class="form-control" name="database_ro_user_id" id="database_ro_user_id">
                                <option value="0"></option>
                                {if is_array($variables.db_users) && count($variables.db_users) > 0}
                                    {foreach $variables.db_users as $db_user}
                                        <option value="{$db_user.database_user_id}">{$db_user.database_user}</option>
                                    {/foreach}
                                {/if}
                            </select>
                        </div>
                    </div>
                            
                    <div class="form-group">
                        <label for="limit_database_quota" class="col-sm-4 control-label">Database Quota</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input type="number" class="form-control" name="database_quota" 
                                    {if $variables.client.limit_database_quota == -1} 
                                        min="0" value="0"
                                    {else} 
                                        value="1" min="1" max="{$variables.client.limit_database_quota}"
                                    {/If}
                                               id="database_quota">
                                <span class="input-group-addon">MB</span>
                            </div>
                                {if $variables.client.limit_database_quota == -1} 
                                    <p class="helper-block">enter 0 for unlimited</p>
                                {/If}
                        </div>
                    </div>
                            
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button class="btn btn-success" onclick="$('#modalAddDB #frmAdd').submit()"><span id="ajax-loader-add"></span> Create Database</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditDB" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Update Database</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal ajax-form" id="frmEdit">
                    <div id="ajax-params" data-action="{$action_urls.db.edit}" data-method="POST" data-loader="#modalEditDB #ajax-loader-edit" data-loader-position="outside" data-loader-type="inside-button" data-messages="#ajax-messages" data-callback-on-success="window.location.reload()"></div>
                    <div id="ajax-messages"></div>
                    <input name="database_id" type="hidden" id="database_id">

                    <div class="form-group">
                        <label for="database_name" class="col-sm-4 control-label">Database name</label>
                        <div class="col-sm-6">
                        <div class="input-group">
                            <span class="input-group-addon">{$db.database_name_prefix}</span>
                            <input type="hidden" class="form-control" name="database_name_prefix" id="prefix" value="{$db.database_name_prefix}">
                            <input type="hidden" class="form-control" name="hdtotalused" id="prefix" value="{$hdtotal}">
                            <input type="hidden" class="form-control" name="limit_database_quota" id="prefix" value="{$variables.client.limit_database_quota}">
                            <input type="hidden" class="form-control" name="old_database_quota" id="database_quota">
                            <input type="text" class="form-control" name="database_name" id="database_name" readonly="readonly">
                        </div>
                        </div>
                    </div>
                    

                    <div class="form-group">
                        <label for="parent_domain_id" class="col-sm-4 control-label">Site</label>
                        <div class="col-sm-6">
                            <select class="form-control" name="parent_domain_id" id="parent_domain_id">
                                <option>- select site</option>
                                {if is_array($variables.domains) && count($variables.domains) > 0}
                                    {foreach $variables.domains as $domain}
                                        <option value="{$domain.domain_id}">{$domain.domain}</option>
                                    {/foreach}
                                {/if}
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="database_user" class="col-sm-4 control-label">Read / Write User</label>
                        <div class="col-sm-6">
                            <select class="form-control" name="database_user_id" id="database_user_id">
                                <option></option>
                                {if is_array($variables.db_users) && count($variables.db_users) > 0}
                                    {foreach $variables.db_users as $db_user}
                                        <option value="{$db_user.database_user_id}">{$db_user.database_user}</option>
                                    {/foreach}
                                {/if}
                            </select>
                        </div>
                    </div>
                            
                    <div class="form-group">
                        <label for="database_ro_user_id" class="col-sm-4 control-label">Read-only user</label>
                        <div class="col-sm-6">
                            <select class="form-control" name="database_ro_user_id" id="database_ro_user_id">
                                <option value="0"></option>
                                {if is_array($variables.db_users) && count($variables.db_users) > 0}
                                    {foreach $variables.db_users as $db_user}
                                        <option value="{$db_user.database_user_id}">{$db_user.database_user}</option>
                                    {/foreach}
                                {/if}
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="limit_database_quota" class="col-sm-4 control-label">Database Quota</label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <input type="number" class="form-control" name="database_quota" 
                                    {if $variables.client.limit_database_quota == -1} 
                                        min="0" value="0"
                                    {else} 
                                        value="1" min="1" max="{$variables.client.limit_database_quota}"
                                    {/If}
                                               id="database_quota">
                                <span class="input-group-addon">MB</span>
                            </div>
                                {if $variables.client.limit_database_quota == -1} 
                                    <p class="helper-block">enter 0 for unlimited</p>
                                {/If}
                        </div>
                    </div>
                            
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button class="btn btn-success" onclick="$('#modalEditDB #frmEdit').submit()"><span id="ajax-loader-edit"></span> Update</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDeleteDB" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Delete Database</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal ajax-form" id="frmDelete">
                    <div id="ajax-params" data-action="{$action_urls.db.delete}" data-method="POST" data-loader="#modalDeleteDB #ajax-loader-delete" data-loader-position="outside" data-loader-type="inside-button" data-messages="#ajax-messages" data-callback-on-success="window.location.reload()"></div>
                    <div id="ajax-messages"></div>
                    <input name="database_id" type="hidden" id="database_id">
                </form>
                <p>Are you sure you want to delete this database?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button class="btn btn-danger" onclick="$('#modalDeleteDB #frmDelete').submit()"><span id="ajax-loader-delete"></span> Confirm</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalAddDBUser" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Add Database User</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal ajax-form" id="frmAdd">
                    <div id="ajax-params" data-action="{$action_urls.db_user.add}" data-method="POST" data-loader="#modalAddDBUser #ajax-loader-add" data-loader-position="outside" data-loader-type="inside-button" data-messages="#ajax-messages" data-callback-on-success="window.location.reload()"></div>
                    <div id="ajax-messages"></div>

                    <div class="form-group">
                     <label for="username" class="col-sm-4 control-label">Username</label>
                      <div class="col-sm-6">
                       <div class="input-group">
                       <span class="input-group-addon">{$variables.client.customer_no}</span>
                        <input type="hidden" class="form-control" name="prefix" id="prefix" value="{$variables.client.customer_no}">
                        <input type="text" class="form-control" name="username" id="username">
                       </div>
                      </div>
                    </div>

                    <div id="newPassword11" class="form-group has-feedback">
                        <label for="inputNewPassword11" class="col-sm-5 control-label">{$LANG.newpassword}</label>
                        <div class="col-sm-6">
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
                    <div id="newPassword22" class="form-group has-feedback">
                        <label for="inputNewPassword22" class="col-sm-5 control-label">{$LANG.confirmnewpassword}</label>
                        <div class="col-sm-6">
                            <input type="password" class="form-control" name="password2" id="inputNewPassword22" autocomplete="off" />
                            <span class="form-control-feedback glyphicon"></span>
                            <div id="inputNewPassword22Msg"></div>
                        </div>
                    </div>
                        
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button class="btn btn-success" onclick="$('#modalAddDBUser #frmAdd').submit()"><span id="ajax-loader-add"></span> Create Database User</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditDBUser" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Update Database User</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal ajax-form" id="frmEdit">
                    <div id="ajax-params" data-action="{$action_urls.db_user.edit}" data-method="POST" data-loader="#modalEditDBUser #ajax-loader-edit" data-loader-position="outside" data-loader-type="inside-button" data-messages="#ajax-messages" data-callback-on-success="window.location.reload()"></div>
                    <div id="ajax-messages"></div>
                    <input name="database_user_id" type="hidden" id="database_user_id">

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

                        </script>                        
                        </div>
                    </div>
                    <div id="newPassword44" class="form-group has-feedback">
                        <label for="inputNewPassword44" class="col-sm-5 control-label">{$LANG.confirmnewpassword}</label>
                        <div class="col-sm-6">
                            <input type="password" class="form-control" name="password2" id="inputNewPassword44" autocomplete="off" />
                            <span class="form-control-feedback glyphicon"></span>
                            <div id="inputNewPassword44Msg"></div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button class="btn btn-success" onclick="$('#modalEditDBUser #frmEdit').submit()"><span id="ajax-loader-edit"></span> Update</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDeleteDBUser" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Delete Database User</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal ajax-form" id="frmDelete">
                    <div id="ajax-params" data-action="{$action_urls.db_user.delete}" data-method="POST" data-loader="#modalDeleteDBUser #ajax-loader-delete" data-loader-position="outside" data-loader-type="inside-button" data-messages="#ajax-messages" data-callback-on-success="window.location.reload()"></div>
                    <div id="ajax-messages"></div>
                    <input name="database_user_id" type="hidden" id="database_user_id">
                </form>
                <p>Are you sure you want to delete this database user?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button class="btn btn-danger" onclick="$('#modalDeleteDBUser #frmDelete').submit()"><span id="ajax-loader-delete"></span> Confirm</button>
            </div>
        </div>
    </div>
</div>