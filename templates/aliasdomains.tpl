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
<h3>Manage Aliasdomains ({$params.domain})</h3>
<p>Aliasdomains are other domain names that you can point to directories within an existing website. 
 The document root you select when creating it, is the name folder to which to upload your files to. 
<br /><br />e.g If you create a aliasdomain www.someotherdomain.com with the document root as 'shop', you will see the shop folder in the file manager.
This is the folder to put the content for your aliasdomain.<p>
<hr>
<h5>Current Aliasdomains ( {$variables.aliasdomains|@count} of {If $variables.client.limit_web_aliasdomain == -1}Unlimited{else}{$variables.client.limit_web_aliasdomain}{/If} )</h5>

<div class="text-right">
    <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalAdd"
    {If $variables.client.locked == "y" || $variables.client.canceled == "y"}
        disabled="disabled"
    {/If}
    >Add Aliasdomain</button>
</div>
{*$variables.domains.0|print_r*}
{assign "server_id" "{$variables.domains.0.server_id}"}
{assign "domain_id" "{$variables.domains.0.domain_id}"}

{if $domain_id != ''}
    {if is_array($variables.aliasdomains) && count($variables.aliasdomains) > 0}
        <table class="table table-condensed table-striped table-hover ihost-smart-table">
            <thead><tr><th>Aliasdomain</th><th>Redirect Path</th><th>&nbsp;</th></tr></thead>
            <tbody>
            {* foreach $variables.aliasdomains.0 as $aliasdomain *}
            {foreach from=$variables.aliasdomains key=aliasdomain item=i}
                <tr>
                    <td>{$i.domain}</td>
                    <td>{$document_root}{$i.redirect_path}</td>
                    <td class="text-right">
                    {If $variables.client.locked == "y" || $variables.client.canceled == "y"}
                        <i class="fa fa-ban"></i>
                    {else}
                        <a href="javascript:;" class="btn btn-xs btn-default" id="btnAction" data-toggle="modal" data-target="#modalEdit" data-target-values="aliasdomain_id={$i.domain_id}&aliasdomain={$i.domain}&server_id={$i.server_id}&domain_id={$i.parent_domain_id}&redirect_type={$i.redirect_type}&directory={$i.redirect_path}&seo_redirect={$i.seo_redirect}&directory_prefix={$document_root}"><i class="fa fa-pencil"></i></a>
                        <a href="javascript:;" class="btn btn-xs btn-default" id="btnAction" data-toggle="modal" data-target="#modalDelete" data-target-values="aliasdomain_id={$i.domain_id}"><i class="fa fa-times"></i></a>
                    {/If}
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    {else}
        <p>No Aliasdomains found</p>
    {/if}
    {else}
        <p>You must create a website first.</p>
{/if}

<div class="modal fade" id="modalAdd" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Add Aliasdomain</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal ajax-form" id="frmAdd">
                    <div id="ajax-params" data-action="{$action_urls.add}" data-method="POST" data-loader="#ajax-loader-add" data-loader-position="outside" data-loader-type="inside-button" data-messages="#ajax-messages" data-callback-on-success="window.location.reload()"></div>
                    <div id="ajax-messages"></div>
                    <input type="hidden" name="server_id" value="{$server_id}">
                    <input type="hidden" name="domain_id" id="domain_id" value="{$domain_id}">
                    <input type="hidden" name="dir_prefix" value="{$dir_prefix}">

                    <div class="form-group">
                        <label for="email" class="col-sm-4 control-label">From Domain</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input type="text" class="form-control" name="aliasdomain" id="directory">
                            </div>
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
                                
                    <div class="form-group">
                        <label for="directory" class="col-sm-4 control-label">Redirect Path</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="directory" id="directory">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="redirect" class="col-sm-4 control-label">SEO Redirect</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <span class="input-group-addon" id="redirect_type"></span>
                                <select class="form-control" name="seoredirect_type" id="directory">
                                    <option value="">No Redirect</option>
                                    <option value="no">domain.tld => www.domain.tld</option>
                                    <option value="www_to_non_www">www.domain.tld => domain.tld</option>
                                    <option value="*_domain_tld_to_domain_tld">*.domain.tld => domain.tld</option>
                                    <option value="*_domain_tld_to_www_domain_tld">*.domain.tld => www.domain.tld</option>
                                    <option value="*_to_domain_tld">* => domain.tld</option>
                                    <option value="*_to_www_domain_tld">* => www.domain.tld</option>
                                </select>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button class="btn btn-success" onclick="$('#frmAdd #domain').val($('#frmAdd #domain_id :selected').html());$('#frmAdd').submit()"><span id="ajax-loader-add"></span> Create Aliasdomain</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Update Aliasdomain</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal ajax-form" id="frmEdit">
                    <div id="ajax-params" data-action="{$action_urls.edit}" data-method="POST" data-loader="#ajax-loader-edit" data-loader-position="outside" data-loader-type="inside-button" data-messages="#ajax-messages" data-callback-on-success="window.location.reload()"></div>
                    <div id="ajax-messages"></div>
                    <input type="hidden" name="server_id" id="server_id">
                    <input type="hidden" name="aliasdomain_id" id="aliasdomain_id">
                    <input type="hidden" name="domain_id" id="domain_id">
                    <input type="hidden" name="parent_domain_id" id="parent_domain_id">

                    <div class="form-group">
                        <label for="email" class="col-sm-4 control-label">Alias Domain</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input type="text" class="form-control" name="aliasdomain" id="aliasdomain">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="redirect" class="col-sm-4 control-label">Redirect Type</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <span class="input-group-addon" id="redirect_type"></span>
                                <select class="form-control" name="redirect_type" id="username">
                                    <option value="" {if $i.redirect_type == ""}selected{/if}>No Redirect</option>
                                    <option value="no" {if $i.redirect_type == "no"}selected{/if}>No Flag</option>
                                    <option value="R" {if $i.redirect_type == "R"}selected{/if}>R</option>
                                    <option value="L" {if $i.redirect_type == "L"}selected{/if}>L</option>
                                    <option value="R,L" {if $i.redirect_type == "R,L"}selected{/if}>R,L</option>
                                    <option value="R=301,L" {if $i.redirect_type == "R=301,L"}selected{/if}>R=301,L</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="directory" class="col-sm-4 control-label">Redirect Path</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <span class="input-group-addon" id="directory_prefix"></span>
                                <input type="text" class="form-control" name="directory" id="directory">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="redirect" class="col-sm-4 control-label">SEO Redirect</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <span class="input-group-addon" id="seo_redirect"></span>
                                <select class="form-control" name="seo_redirect" id="username">
                                    <option value="" {if $i.seo_redirect == ""}selected{/if}>No Redirect</option>
                                    <option value="no" {if $i.seo_redirect == "no"}selected{/if}>domain.tld => www.domain.tld</option>
                                    <option value="www_to_non_www" {if $i.seo_redirect == "www_to_non_www"}selected{/if}>www.domain.tld => domain.tld</option>
                                    <option value="*_domain_tld_to_domain_tld" {if $i.seo_redirect == "*_domain_tld_to_domain_tld"}selected{/if}>*.domain.tld => domain.tld</option>
                                    <option value="*_domain_tld_to_www_domain_tld" {if $i.seo_redirect == "*_domain_tld_to_www_domain_tld"}selected{/if}>*.domain.tld => www.domain.tld</option>
                                    <option value="*_to_domain_tld" {if $i.seo_redirect == "*_to_domain_tld"}selected{/if}>* => domain.tld</option>
                                    <option value="*_to_www_domain_tld" {if $i.seo_redirect == "*_to_www_domain_tld"}selected{/if}>* => www.domain.tld</option>
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
                <h4 class="modal-title">Delete Aliasdomain</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal ajax-form" id="frmDelete">
                    <div id="ajax-params" data-action="{$action_urls.delete}" data-method="POST" data-loader="#ajax-loader-delete" data-loader-position="outside" data-loader-type="inside-button" data-messages="#ajax-messages" data-callback-on-success="window.location.reload()"></div>
                    <div id="ajax-messages"></div>
                    <input name="aliasdomain_id" type="hidden" id="aliasdomain_id">
                </form>
                <p>Are you sure you want to delete this aliasdomain?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button class="btn btn-danger" onclick="$('#frmDelete').submit()"><span id="ajax-loader-delete"></span> Confirm</button>
            </div>
        </div>
    </div>
</div>