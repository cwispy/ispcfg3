{*
 /*  ISPConfig v3.1+ module for WHMCS v6.x or Higher
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
<span class="icon-header icon-ftp"></span>
<h3>Manage FTP Accounts ({$params.domain})</h3>
<p>FTP accounts allow you to access your website's files through a protocol called FTP. You will need a third-party FTP program  like <a href="https://filezilla-project.org/download.php" target="_blank">Filezilla</a> to access your files. You can connect to the server via FTP by using  previously created account details.</p>
<hr>
<div class="text-right">
    <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalAdd"
        {If $variables.client.locked == "y" || $variables.client.canceled == "y"}
        disabled="disabled"
    {/If}
    >Add FTP Account</button>
</div>
{if is_array($variables.accounts) && count($variables.accounts) > 0}
    {assign "server_id" "{$variables.accounts[0].server_id}"}
    {assign "dir_prefix" "{$variables.accounts[0].dir}"}
    {assign "username_prefix" "{$variables.user.username}"}
    <table class="table table-condensed table-striped table-hover ihost-smart-table">
        <thead><tr><th>Username</th><th>Directory</th><th>&nbsp;</th></tr></thead>
        <tbody>
        {foreach $variables.accounts as $account}
		{if $account.first}
            {continue}
        {/if}
            <tr>
                <td>{$account.username}</td>
                <td>{$account.dir}</td>
                <td class="text-right">
                {If $variables.client.locked == "y" || $variables.client.canceled == "y"}
                    <i class="fa fa-ban"></i>
                {else}
                    <a href="javascript:;" class="btn btn-xs btn-default" id="btnAction" data-toggle="modal" data-target="#modalEdit" data-target-values="ftp_user_id={$account.ftp_user_id}&quota_size={$account.quota_size}&username={$account.username|replace:$username_prefix:''}&directory={$account.dir|replace:$dir_prefix:''|ltrim:'/'}"><i class="fa fa-pencil"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-default" id="btnAction" data-toggle="modal" data-target="#modalDelete" data-target-values="ftp_user_id={$account.ftp_user_id}&username={$account.username}"><i class="fa fa-times"></i></a>
                {/If}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
{else}
    <p>No FTP accounts found</p>
{/if}

<div class="modal fade" id="modalAdd" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Add FTP Account</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal ajax-form" id="frmAdd">
                    <div id="ajax-params" data-action="{$action_urls.add}" data-method="POST" data-loader="#ajax-loader-add" data-loader-position="outside" data-loader-type="inside-button" data-messages="#ajax-messages" data-callback-on-success="window.location.reload()"></div>
                    <div id="ajax-messages"></div>
                    <input type="hidden" name="server_id" value="{$server_id}">
                    <input type="hidden" name="parent_domain_id" value="{$variables.accounts[0].parent_domain_id}">
                    <input type="hidden" name="uid" value="{$variables.accounts[0].uid}">
                    <input type="hidden" name="gid" value="{$variables.accounts[0].gid}">
                    <input type="hidden" name="dir_prefix" value="{$dir_prefix}">
                    <input type="hidden" name="username_prefix" value="{$username_prefix}">

                    <div class="form-group">
                        <label for="email" class="col-sm-4 control-label">Username</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <span class="input-group-addon">{$username_prefix}</span>
                                <input type="text" class="form-control" name="username" id="username">
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
                        <label for="directory" class="col-sm-3 control-label">Directory</label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <span class="input-group-addon">{$dir_prefix}/</span>
                                <input type="text" class="form-control" name="directory" id="directory">
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button class="btn btn-success" onclick="$('#frmAdd').submit()"><span id="ajax-loader-add"></span> Create FTP Account</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Update FTP Account</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal ajax-form" id="frmEdit">
                    <div id="ajax-params" data-action="{$action_urls.edit}" data-method="POST" data-loader="#ajax-loader-edit" data-loader-position="outside" data-loader-type="inside-button" data-messages="#ajax-messages" data-callback-on-success="window.location.reload()"></div>
                    <div id="ajax-messages"></div>
                    <input type="hidden" name="server_id" value="{$server_id}">
                    <input type="hidden" name="ftp_user_id" id="ftp_user_id">
                    <input type="hidden" name="parent_domain_id" value="{$variables.accounts[0].parent_domain_id}">
                    <input type="hidden" name="uid" value="{$variables.accounts[0].uid}">
                    <input type="hidden" name="gid" value="{$variables.accounts[0].gid}">
                    <input type="hidden" name="dir_prefix" value="{$dir_prefix}">
                    <input type="hidden" name="username_prefix" value="{$username_prefix}">

                    <div class="form-group">
                        <label for="email" class="col-sm-4 control-label">Username</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <span class="input-group-addon">{$username_prefix}</span>
                                <input type="text" class="form-control" name="username" id="username" readonly="readonly">
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
                        <label for="directory" class="col-sm-3 control-label">Directory</label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <span class="input-group-addon">{$dir_prefix}/</span>
                                <input type="text" class="form-control" name="directory" id="directory">
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button class="btn btn-success" onclick="$('#frmEdit').submit()"><span id="ajax-loader-edit"></span> Update</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Delete FTP Account</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal ajax-form" id="frmDelete">
                    <div id="ajax-params" data-action="{$action_urls.delete}" data-method="POST" data-loader="#ajax-loader-delete" data-loader-position="outside" data-loader-type="inside-button" data-messages="#ajax-messages" data-callback-on-success="window.location.reload()"></div>
                    <div id="ajax-messages"></div>
                    <input name="ftp_user_id" type="hidden" id="ftp_user_id">
                </form>
                <p>Are you sure you want to delete this FTP account?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button class="btn btn-danger" onclick="$('#frmDelete').submit()"><span id="ajax-loader-delete"></span> Confirm</button>
            </div>
        </div>
    </div>
</div>