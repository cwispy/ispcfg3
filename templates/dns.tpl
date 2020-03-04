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
<link href="modules/servers/ispcfg3/assets/ispcfg3.css" rel="stylesheet">
<span class="icon-header icon-dns"></span>
<div data-view="dns">
    <h3>Manage DNS Records ({$params.domain})</h3>
	<p>DNS holds records such as the address of the server that handles e-mail, the web server, mail server among others. The default DNS settings are already configured for you. </p>
    <hr>
    <h5>Current DNS Records ( {$variables.records|@count} of {If $variables.client.limit_dns_record == -1}Unlimited{else}{$variables.client.limit_dns_record}{/If} )</h5>

    <div class="text-right">
        <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalAdd"
        {If $variables.client.locked == "y" || $variables.client.canceled == "y"}
        disabled="disabled"
        {/If}        
        >Add DNS Record</button>
    </div>
    {assign "server_id" "{$variables.zones[0].server_id}"}
    {if is_array($variables.records) && count($variables.records) > 0}
        <table class="table table-condensed table-striped table-hover ihost-smart-table" style="font-size: 1.3rem;" >
            <thead><tr><th>Host</th><th>Type</th><th >Points to</th><th>TTL</th><th>&nbsp;</th></tr></thead>
            <tbody>
            {foreach $variables.records as $record}
                {assign "zone_name" "{$variables.zones_processed[$record.zone].origin}"}
                {assign "zone_name_dot" ".{$zone_name}"}
                {assign "host_striped" "{$record.name|replace:$zone_name_dot:''}"}
                {assign "host_short" "{($record.name == $zone_name) ? '' : $host_striped}"}
                <tr>
                    <td>{$record.name}</td>
                    <td>{$record.type}</td>
                    <td style="-ms-word-break: break-all;word-break: break-all;word-break: break-word; " >{$record.data|escape:'html'}</td>
                    <td>{$record.ttl}</td>
                    <td class="text-right">
                    {If $variables.client.locked == "y" || $variables.client.canceled == "y"}
                        <i class="fa fa-ban"></i>
                    {else}
                        <a href="javascript:;" class="btn btn-xs btn-default" id="btnAction" data-toggle="modal" data-target="#modalEdit" data-target-values="record_id={$record.id}&zone={$record.zone}&type={$record.type|lower}&host={$host_short}&destination={$record.data|escape:'html'}&ttl={$record.ttl}"><i class="fa fa-pencil"></i></a>
                        <a href="javascript:;" class="btn btn-xs btn-default" id="btnAction" data-toggle="modal" data-target="#modalDelete" data-target-values="record_id={$record.id}"><i class="fa fa-times"></i></a>
                    {/If}
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    {else}
        <p>No DNS records found</p>
    {/if}

    <div class="modal fade" id="modalAdd" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Add DNS Record</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal ajax-form" id="frmAdd">
                        <div id="ajax-params" data-action="{$action_urls.add}" data-method="POST" data-loader="#ajax-loader-add" data-loader-position="outside" data-loader-type="inside-button" data-messages="#ajax-messages" data-callback-on-success="window.location.reload()"></div>
                        <div id="ajax-messages"></div>
                        <input type="hidden" name="server_id" value="{$server_id}">
                        <input type="hidden" name="zone_name" value="" id="zone_name">

                        <div class="form-group">
                            <label for="zone" class="col-sm-4 control-label">DNS Zone</label>
                            <div class="col-sm-6">
                                <select class="form-control" name="zone" id="zone">
                                    {if is_array($variables.zones) && count($variables.zones) > 0}
                                        {foreach $variables.zones as $zone}
                                        <option value="{$zone.id}">{$zone.origin}</option>
                                        {/foreach}
                                    {/if}
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="type" class="col-sm-4 control-label">Type</label>
                            <div class="col-sm-6">
                                <select class="form-control" name="type" id="type">
                                    {if is_array($variables.types) && count($variables.types) > 0}
                                        {foreach $variables.types as $type}
                                        <option value="{$type}">{$type|upper}</option>
                                        {/foreach}
                                    {/if}
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="host" class="col-sm-4 control-label">Host</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="host" id="host">
                                    <span class="input-group-addon">.<span class="zone-placeholder"></span></span>
                                </div>
                                <p class="help-block">Leave blank for <span class="zone-placeholder"></span></p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="destination" class="col-sm-4 control-label">Points to</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="destination" id="destination">
                                <!-- <p class="help-block">This could be either an IP or hostname, depending on the record type</p> -->
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="ttl" class="col-sm-4 control-label">TTL</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="ttl" id="ttl" value="86400">
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-success" onclick="$('#frmAdd #zone_name').val($('#frmAdd #zone :selected').html());$('#frmAdd').submit()"><span id="ajax-loader-add"></span> Create DNS Record</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Update DNS Record</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal ajax-form" id="frmEdit">
                        <div id="ajax-params" data-action="{$action_urls.edit}" data-method="POST" data-loader="#ajax-loader-edit" data-loader-position="outside" data-loader-type="inside-button" data-messages="#ajax-messages" data-callback-on-success="window.location.reload()"></div>
                        <div id="ajax-messages"></div>
                        <input type="hidden" name="server_id" value="{$server_id}">
                        <input type="hidden" name="record_id" id="record_id">
                        <input type="hidden" name="zone_name" value="" id="zone_name">

                        <div class="form-group">
                            <label for="zone" class="col-sm-4 control-label">DNS Zone</label>
                            <div class="col-sm-6">
                                <select class="form-control" name="zone" id="zone">
                                    {if is_array($variables.zones) && count($variables.zones) > 0}
                                        {foreach $variables.zones as $zone}
                                        <option value="{$zone.id}">{$zone.origin}</option>
                                        {/foreach}
                                    {/if}
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="type" class="col-sm-4 control-label">Type</label>
                            <div class="col-sm-6">
                                <select class="form-control" name="type" id="type">
                                    {if is_array($variables.types) && count($variables.types) > 0}
                                        {foreach $variables.types as $type}
                                        <option value="{$type}">{$type|upper}</option>
                                        {/foreach}
                                    {/if}
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="host" class="col-sm-4 control-label">Host</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="host" id="host">
                                    <span class="input-group-addon">.<span class="zone-placeholder"></span></span>
                                </div>
                                <p class="help-block">Leave blank for <span class="zone-placeholder"></span></p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="destination" class="col-sm-4 control-label">Points to</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="destination" id="destination">
                                <!-- <p class="help-block">This could be either an IP or hostname, depending on the record type</p> -->
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="ttl" class="col-sm-4 control-label">TTL</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="ttl" id="ttl" value="86400">
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-success" onclick="$('#frmEdit #zone_name').val($('#frmEdit #zone :selected').html());$('#frmEdit').submit()"><span id="ajax-loader-edit"></span> Update</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Delete DNS Record</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal ajax-form" id="frmDelete">
                        <div id="ajax-params" data-action="{$action_urls.delete}" data-method="POST" data-loader="#ajax-loader-delete" data-loader-position="outside" data-loader-type="inside-button" data-messages="#ajax-messages" data-callback-on-success="window.location.reload()"></div>
                        <div id="ajax-messages"></div>
                        <input name="record_id" type="hidden" id="record_id">
                    </form>
                    <p>Are you sure you want to delete this DNS record?</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-danger" onclick="$('#frmDelete').submit()"><span id="ajax-loader-delete"></span> Confirm</button>
                </div>
            </div>
        </div>
    </div>
</div>