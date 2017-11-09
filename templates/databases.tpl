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
<p>MySQL databases are required by many web applications . To use a database, you'll need to create it. </p>
<hr>
<h5>Current Databases</h5>
<div class="text-right">
    <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalAddDB"
    {If $variables.client.locked == "y" || $variables.client.canceled == "y"}
        disabled="disabled"
    {/If}
    >Add Database</button>
</div>

{*$variables|print_r*}
{assign "userid" "{$variables.client.customer_no_template}"}
{if is_array($variables.dbs) && count($variables.dbs) > 0}
    <table class="table table-condensed table-striped table-hover ihost-smart-table">
        <thead><tr><th>Database</th><th>User</th><th>&nbsp;</th></tr></thead>
        <tbody>
        {foreach $variables.dbs as $db}
            <tr>
                <td>{$db.database_name}</td>
                <td>{$variables.db_users_o[$db.database_user_id].database_user}</td>
                <td class="text-right">
                {If $variables.client.locked == "y" || $variables.client.canceled == "y"}
                    <i class="fa fa-ban"></i>
                {else}
                    <a href="javascript:;" class="btn btn-xs btn-default" id="btnAction" data-toggle="modal" data-target="#modalEditDB" data-target-values="database_id={$db.database_id}&database_name={$db.database_name}&database_user_id={$db.database_user_id}"><i class="fa fa-pencil"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-default" id="btnAction" data-toggle="modal" data-target="#modalDeleteDB" data-target-values="database_id={$db.database_id}"><i class="fa fa-times"></i></a>
                {/If}
                </td>
            </tr>
        {/foreach}
   <!-- {$variables|print_r} -->
        </tbody>
    </table>
{else}
    <p>No databases found</p>
{/if}

<h5>Current Database Users</h5><p>A Mysql user requires privileges to access a database in order to read from or write to that database. Assign or create a user for each database. You can login to PhpMyadmin using the database user to administer your database.</p>
<div class="text-right">
    <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalAddDBUser"
    {If $variables.client.locked == "y" || $variables.client.canceled == "y"}
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
						<input type="hidden" class="form-control" name="domain" id="domain" value="c{$params.domain}">
                             <input type="hidden" class="form-control" name="prefix" id="prefix" value="{$variables.client.customer_no}">
                                <span class="input-group-addon">{$variables.client.customer_no}</span>
                                <input type="text" class="form-control" name="database_name" id="database_name">
							 </div>
                        </div>
                    </div>

                    <div class="well">
                        <p>Use existing database user</p>
                        <div class="form-group">
                            <label for="database_user" class="col-sm-4 control-label">Database User</label>
                            <div class="col-sm-6">
                                <select class="form-control" name="database_user" id="database_user">
                                    <option></option>
                                    {if is_array($variables.db_users) && count($variables.db_users) > 0}
                                        {foreach $variables.db_users as $db_user}
                                            <option value="{$db_user.database_user_id}">{$db_user.database_user}</option>
                                        {/foreach}
                                    {/if}
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="well">
                        <p>Create new database user</p>
                        <div class="form-group">
                            <label for="username" class="col-sm-4 control-label">Username</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="username" id="username">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password" class="col-sm-4 control-label">Password</label>
                            <div class="col-sm-6">
                                								<input type="password" class="field" name="password" id="inputNewPassword1" placeholder="Password" autocomplete="off">							<div>{include file="$template/includes/pwstrength.tpl"}</div>
                            </div>
                        </div>
						
						
						

                        <div class="form-group">
                            <label for="password2" class="col-sm-4 control-label">Password (Again)</label>
                            <div class="col-sm-6">
                                <input type="password" class="form-control" name="password2" id="password2">
                            </div>
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
                            <input type="text" class="form-control" name="database_name" id="database_name" readonly="readonly">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="database_user" class="col-sm-4 control-label">Database User</label>
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
                        <input type="hidden" class="form-control" name="prefix" id="prefix" value="{$variables.client.customer_no}">   
                        <span class="input-group-addon">{$variables.client.customer_no}</span>
                        <input type="text" class="form-control" name="username" id="username">
                       </div>
                      </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="col-sm-4 control-label">Password</label>
                        <div class="col-sm-6">
                            <input type="password" class="form-control" name="password" id="password">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password2" class="col-sm-4 control-label">Password (Again)</label>
                        <div class="col-sm-6">
                            <input type="password" class="form-control" name="password2" id="password2">
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

                    <div class="form-group">
                        <label for="username" class="col-sm-4 control-label">Username</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="username" id="username" readonly="readonly">
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