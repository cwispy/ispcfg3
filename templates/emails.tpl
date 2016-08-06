{*
 /*  ISPConfig v3.1+ module for WHMCS v6.x or Higher
 *  Copyright (C) 2014 - 2016  Shane Chrisp
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
<link href="modules/servers/ispcfg3/assets/css.css" rel="stylesheet"> <span class="icon-header icon-email"></span>
<h3>Manage Email Accounts ({$params.domain})</h3>
<p>In this area you can manage the email accounts associated with your domain. You can create edit and set the email quota for every email account. 
    You can also see the current usage and adjust the quota to ensure the mailbox is not full and unable to receive new email.</p>
<hr><div class="text-right"><button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalAdd">Add Email</button></div>
{if is_array($variables.mailboxes) && count($variables.mailboxes) > 0}
    <table class="table table-condensed table-striped table-hover ihost-smart-table">
        <thead><tr><th>Email</th><th class="text-right">Used Space</th><th class="text-right">Quota</th><th></th></tr></thead>
        <tbody>
        {foreach $variables.quota as $mailbox}
            <tr>
                 <td>{$mailbox.email}</td>
                <td class="text-right">
                 {{$mailbox.used / 1048576}|number_format:2} MB
				</td>
				<td class="text-right">
				{{$mailbox.quota}|number_format:2} MB
				</td>
                <td class="text-right">
                    <a href="javascript:;" class="btn btn-xs btn-default" id="btnAction" data-toggle="modal" data-target="#modalEdit" 
                       data-target-values="quota={$mailbox.quota}&activeEmail={$mailbox.email}&mail_id={$mailbox.mailuser_id}&email={$mailbox.email}">
                        <i class="fa fa-pencil"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-default" id="btnAction" data-toggle="modal" data-target="#modalDelete" 
                       data-target-values="activeEmail={$mailbox.email}&mail_id={$mailbox.mailuser_id}&email={$mailbox.email}">
                        <i class="fa fa-times"></i></a>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>

{else}
    <p>No emails found</p>
	
{/if}

<div class="modal fade" id="modalAdd" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Add Email Account</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal ajax-form" id="frmAddEmail">
                    <div id="ajax-params" data-action="{$action_urls.add}" data-method="POST" data-loader="#ajax-loader-add" data-loader-position="outside"
                         data-loader-type="inside-button" data-messages="#ajax-messages" data-callback-on-success="window.location.reload()"></div>
                    <div id="ajax-messages"></div>

                    <div class="form-group">
                        <label for="email" class="col-sm-4 control-label">Email</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input type="hidden" class="form-control" name="svrid" value="{$variables.domains.0.server_id}" id="svrid">
                                <input type="text" class="form-control" name="email" id="email" >
                                <span class="input-group-addon">@</span>
                                <select class="form-control" name="domain" readonly="readonly">
                                    
									 <option>{$domain}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="col-sm-4 control-label">Password</label>
                        <div class="col-sm-6">
                            	<input type="password" class="field" name="password" id="inputNewPassword1" placeholder="Password" autocomplete="off">
							<div>{include file="$template/includes/pwstrength.tpl"}</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password2" class="col-sm-4 control-label">Password (Again)</label>
                        <div class="col-sm-6">
                            <input type="password" class="form-control" name="password2" id="password2">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="quota" class="col-sm-4 control-label">Quota</label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <input type="number" class="form-control" name="quota" value="0" id="quota">
                                <span class="input-group-addon">MB</span>
                            </div>
                            <p class="helper-block">enter 0 for unlimited</p>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button class="btn btn-success" onclick="$('#frmAddEmail').submit()"><span id="ajax-loader-add"></span> Create Email Account</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Update Email Account (<span id="activeEmail"></span>)</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal ajax-form" id="frmEditEmail">
                    <div id="ajax-params" data-action="{$action_urls.edit}" data-method="POST" data-loader="#ajax-loader-edit" data-loader-position="outside" 
                         data-loader-type="inside-button" data-messages="#ajax-messages" data-callback-on-success="window.location.reload()"></div>
                    <div id="ajax-messages"></div>
                    <input name="mail_id" type="hidden" id="mail_id">
                    <input name="email" type="hidden" id="email" >

                    <div class="form-group">
                        <label for="password" class="col-sm-4 control-label">Password</label>
                        <div class="col-sm-6">
                           	<input type="password" class="field" name="password" id="inputNewPassword1" placeholder="Password" autocomplete="off">
							<div>{include file="$template/includes/pwstrength.tpl"}</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password2" class="col-sm-4 control-label">Password (Again)</label>
                        <div class="col-sm-6">
                            <input type="password" class="form-control" name="password2" id="password2">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="quota" class="col-sm-4 control-label">Quota</label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <input type="number" class="form-control" name="quota" value="0" id="quota">
                                <span class="input-group-addon">MB</span>
                            </div>
                            <p class="helper-block">enter 0 for unlimited</p>
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