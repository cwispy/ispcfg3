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
<span class="icon-header icon-subdomain"></span>
<h3>Manage Subdomains ({$params.domain})</h3>
<p>Subdomains are extensions of your domain name that you can forward to URLs or point to IP addresses and directories within your hosting account. It can be a second website, with its own unique content. The document root you select when creating it, is the name folder to which to upload your files to. <br /><br />e.g If you create a subdomain shop.mydomain.com with the document root as 'shop', you will see the shop folder in the file manager. This is the folder to put the content for your subdomain.<p>
<hr>
<h5>Current Subdomains ( {$variables.subdomains|@count} of {If $variables.client.limit_web_subdomain == -1}Unlimited{else}{$variables.client.limit_web_subdomain}{/If} )</h5>

<div class="text-right">
    <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalAdd"
    {If $variables.client.locked == "y" || $variables.client.canceled == "y"}
        disabled="disabled"
    {/If}
    >Add Subdomain</button>
</div>
{assign "server_id" "{$variables.domains[0].server_id}"}
{assign "dir_prefix" "{$variables.domains[0].document_root}/web"}

{if is_array($variables.subdomains) && count($variables.subdomains) > 0}
    <table class="table table-condensed table-striped table-hover ihost-smart-table">
        <thead><tr><th>Subdomain</th><th>Document Root</th><th>Redirect Type</th><th>&nbsp;</th></tr></thead>
        <tbody>
        {foreach $variables.subdomains as $subdomain}
            {assign "document_root" "{$variables.domains_processed[$subdomain.parent_domain_id].document_root}/web"}
            {assign "parent_domain" "{$variables.domains_processed[$subdomain.parent_domain_id].domain}"}
            {assign "subdomain_suffix" ".{$parent_domain}"}
            {assign "subdomain_name" "{$subdomain.domain|replace:$subdomain_suffix:''}"}
            {assign "redirect_type" "{$subdomain.redirect_type}"}
            <tr>
                <td>{$subdomain.domain}</td>
                <td>{$document_root}{$subdomain.redirect_path}</td>
                <td>{$subdomain.redirect_type}</td>
                <td class="text-right">
                {If $variables.client.locked == "y" || $variables.client.canceled == "y"}
                    <i class="fa fa-ban"></i>
                {else}
                    <a href="javascript:;" class="btn btn-xs btn-default" id="btnAction" data-toggle="modal" data-target="#modalEdit" data-target-values="subdomain_id={$subdomain.domain_id}&subdomain={$subdomain_name}&domain_id={$subdomain.parent_domain_id}&domain={$parent_domain}&subdomain_suffix={$subdomain_suffix}&directory={$subdomain.redirect_path}&directory_prefix={$document_root}"><i class="fa fa-pencil"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-default" id="btnAction" data-toggle="modal" data-target="#modalDelete" data-target-values="subdomain_id={$subdomain.domain_id}"><i class="fa fa-times"></i></a>
                {/If}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
{else}
    <p>No subdomains found</p>
{/if}

<div class="modal fade" id="modalAdd" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Add Subdomain</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal ajax-form" id="frmAdd">
                    <div id="ajax-params" data-action="{$action_urls.add}" data-method="POST" data-loader="#ajax-loader-add" data-loader-position="outside" data-loader-type="inside-button" data-messages="#ajax-messages" data-callback-on-success="window.location.reload()"></div>
                    <div id="ajax-messages"></div>
                    <input type="hidden" name="server_id" value="{$server_id}">
                    <input type="hidden" name="domain" id="domain">
                    <input type="hidden" name="dir_prefix" value="{$dir_prefix}">

                    <div class="form-group">
                        <label for="email" class="col-sm-4 control-label">Subdomain</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input type="text" class="form-control" name="subdomain" id="username">
                                <span class="input-group-addon">.</span>
                                <select class="form-control" name="domain_id" id="domain_id">
                                    {if is_array($variables.domains) && count($variables.domains) > 0}
                                        {foreach $variables.domains as $domain}
                                        <option value="{$domain.domain_id}">{$domain.domain}</option>
                                        {/foreach}
                                    {/if}
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="directory" class="col-sm-4 control-label">Document Root</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="directory" id="directory">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="redirect" class="col-sm-4 control-label">Redirect Type</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <span class="input-group-addon" id="redirect_type"></span>
                                <select class="form-control" name="redirect_type" id="directory">
                                    <option value="">No Redirect</option>
                                    <option value="no">No Flag</option>
                                    <option value="R">R</option>
                                    <option value="L">L</option>
                                    <option value="R,L">R,L</option>
                                    <option value="R=301,L">R=301,L</option>
                                </select>
                            </div>
                        </div>
                    </div>
                                
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button class="btn btn-success" onclick="$('#frmAdd #domain').val($('#frmAdd #domain_id :selected').html());$('#frmAdd').submit()"><span id="ajax-loader-add"></span> Create Subdomain</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Update Subdomain</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal ajax-form" id="frmEdit">
                    <div id="ajax-params" data-action="{$action_urls.edit}" data-method="POST" data-loader="#ajax-loader-edit" data-loader-position="outside" data-loader-type="inside-button" data-messages="#ajax-messages" data-callback-on-success="window.location.reload()"></div>
                    <div id="ajax-messages"></div>
                    <input type="hidden" name="server_id" value="{$server_id}">
                    <input type="hidden" name="subdomain_id" id="subdomain_id">
                    <input type="hidden" name="domain_id" id="domain_id">
                    <input type="hidden" name="domain" id="domain">

                    <div class="form-group">
                        <label for="email" class="col-sm-4 control-label">Subdomain</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input type="text" class="form-control" name="subdomain" id="subdomain">
                                <span class="input-group-addon" id="subdomain_suffix"></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="directory" class="col-sm-4 control-label">Document Root</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <span class="input-group-addon" id="directory_prefix"></span>
                                <input type="text" class="form-control" name="directory" id="directory">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="redirect" class="col-sm-4 control-label">Redirect Type</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <span class="input-group-addon" id="redirect_type"></span>
                                <select class="form-control" name="redirect_type" id="redirect_type">
                                    <option value=""{if $redirect_type == ""} selected{/if} >No Redirect</option>
                                    <option value="no"{if $redirect_type == "no"} selected{/if}>No Flag</option>
                                    <option value="R"{if $redirect_type == "R"} selected{/if} >R</option>
                                    <option value="L"{if $redirect_type == "L"} selected{/if}>L</option>
                                    <option value="R,L"{if $redirect_type == "R,L"} selected{/if}>R,L</option>
                                    <option value="R=301,L"{if $redirect_type == "R=301,L"} selected{/if}>R=301,L</option>
                                </select>
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
                <h4 class="modal-title">Delete Subdomain</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal ajax-form" id="frmDelete">
                    <div id="ajax-params" data-action="{$action_urls.delete}" data-method="POST" data-loader="#ajax-loader-delete" data-loader-position="outside" data-loader-type="inside-button" data-messages="#ajax-messages" data-callback-on-success="window.location.reload()"></div>
                    <div id="ajax-messages"></div>
                    <input name="subdomain_id" type="hidden" id="subdomain_id">
                </form>
                <p>Are you sure you want to delete this subdomain?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button class="btn btn-danger" onclick="$('#frmDelete').submit()"><span id="ajax-loader-delete"></span> Confirm</button>
            </div>
        </div>
    </div>
</div>