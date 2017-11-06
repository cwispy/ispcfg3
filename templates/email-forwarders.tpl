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
<link href="modules/servers/ispcfg3a/assets/ispcfg3.css" rel="stylesheet"><span class="icon-header icon-email-forward"></span><h3>Manage Email Forwarders</h3><p>Forwarders allow you to send a copy of all mail from one email address to another. For example, if you have two different email accounts, info@mydomain.com and david@mydomain.com, you could forward info@mydomain.com to david@mydomain.com so that you do not need to check both accounts.</p>
<hr>
<div class="text-right">
    <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalAdd"
        {If $variables.client.locked == "y" || $variables.client.canceled == "y"}
        disabled="disabled"
    {/If}        
    >Add Forwarder</button>
</div>

{if is_array($variables.forwarders) && count($variables.forwarders) > 0}
    <table class="table table-condensed table-striped table-hover ihost-smart-table">
        <thead><tr><th>Source</th><th>Destination</th><th>&nbsp;</th></tr></thead>
        <tbody>
        {foreach $variables.forwarders as $forwarder}
            <tr>
                <td>{$forwarder.source}</td>
                <td>{$forwarder.destination}</td>
                <td class="text-right">
                {If $variables.client.locked == "y" || $variables.client.canceled == "y"}
                    <i class="fa fa-ban"></i>
                {else}
                    <a href="javascript:;" class="btn btn-xs btn-default" id="btnAction" data-toggle="modal" data-target="#modalEdit" data-target-values="forwarder_id={$forwarder.forwarding_id}&source={$forwarder.source}&destination={$forwarder.destination}"><i class="fa fa-pencil"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-default" id="btnAction" data-toggle="modal" data-target="#modalDelete" data-target-values="forwarder_id={$forwarder.forwarding_id}"><i class="fa fa-times"></i></a>
                {/If}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
{else}
    <p>No email forwarders found</p>
    <!-- {$variables|print_r} -->
{/if}

<div class="modal fade" id="modalAdd" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Add Email Forwarder</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal ajax-form" id="frmAdd">
                    <div id="ajax-params" data-action="{$action_urls.add}" data-method="POST" data-loader="#ajax-loader-add" data-loader-position="outside" data-loader-type="inside-button" data-messages="#ajax-messages" data-callback-on-success="window.location.reload()"></div>
                    <div id="ajax-messages"></div>

                    <div class="form-group">
                        <label for="email" class="col-sm-4 control-label">Email</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input type="hidden" class="form-control" name="svrid" value="{$variables.domains.0.server_id}" id="svrid">
                                <input type="text" class="form-control" name="email" id="email">
                                <span class="input-group-addon">@</span>
                                <select class="form-control" name="domain">
                                    {if is_array($variables.domains) && count($variables.domains) > 0}
                                        {foreach $variables.domains as $domain}
                                        <option>{$domain.domain}</option>
                                        {/foreach}
                                    {/if}}
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="destination" class="col-sm-4 control-label">Destination</label>
                        <div class="col-sm-6">
                            <textarea rows="10" cols="40" name="destination" id="destination" type="text" class="form-control"></textarea>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button class="btn btn-success" onclick="$('#frmAdd').submit()"><span id="ajax-loader-add"></span> Create Email Forwarder</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Update Email Forwarder</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal ajax-form" id="frmEdit">
                    <div id="ajax-params" data-action="{$action_urls.edit}" data-method="POST" data-loader="#ajax-loader-edit" data-loader-position="outside" data-loader-type="inside-button" data-messages="#ajax-messages" data-callback-on-success="window.location.reload()"></div>
                    <div id="ajax-messages"></div>
                    <input name="forwarder_id" type="hidden" id="forwarder_id">

                    <div class="form-group">
                        <label for="source" class="col-sm-4 control-label">Source</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="source" id="source" readonly="readonly">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="destination" class="col-sm-4 control-label">Destination</label>
                        <div class="col-sm-6">
                           <textarea rows="10" cols="40" name="destination" id="destination" type="text" class="form-control"></textarea>
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
                <h4 class="modal-title">Delete Email Forwarder</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal ajax-form" id="frmDelete">
                    <div id="ajax-params" data-action="{$action_urls.delete}" data-method="POST" data-loader="#ajax-loader-delete" data-loader-position="outside" data-loader-type="inside-button" data-messages="#ajax-messages" data-callback-on-success="window.location.reload()"></div>
                    <div id="ajax-messages"></div>
                    <input name="forwarder_id" type="hidden" id="forwarder_id">
                </form>
                <p>Are you sure you want to delete this email forwarder?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button class="btn btn-danger" onclick="$('#frmDelete').submit()"><span id="ajax-loader-delete"></span> Confirm</button>
            </div>
        </div>
    </div>
</div>