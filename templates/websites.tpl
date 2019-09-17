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
{*$variables|print_r*}
<span class="icon-header icon-subdomain"></span>
<h3>Manage Websites ({$params.domain})</h3>
<p>This page lets you manage your Websites and their settings.<p>
<hr>
<h5>Current Websites ( {$variables.domains|@count} of {If $variables.client.limit_web_domain == -1}Unlimited{else}{$variables.client.limit_web_domain}{/If} )</h5>

<div class="text-right">
    <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalAdd"
    {If $variables.client.locked == "y" || 
            $variables.client.canceled == "y" || 
            $variables.client.limit_web_domain == count($variables.domains) }
        disabled="disabled"
    {/If}
    >Add Websites</button>
</div>

{assign "web_php_options" ","|explode:$variables.client.web_php_options}
{assign "server_id" "{$variables.client.web_servers}"}

{if is_array($variables.domains) && count($variables.domains) > 0}
    <table class="table table-condensed table-striped table-hover ihost-smart-table">
        <thead><tr><th>Web Domain</th><th>Disk Quota (MB)</th><th>Web Quota (MB)</th><th>Active</th><th>&nbsp;</th></tr></thead>
        <tbody>
        {* foreach $variables.aliasdomains.0 as $aliasdomain *}
        {foreach from=$variables.domains key=domains item=i}
            <tr>
                <td>{$i.domain}</td>
                <td>{if $i.hd_quota == -1}Unlimited{else}{$i.hd_quota}{/If}</td>
                <td>{if $i.traffic_quota == -1}Unlimited{else}{$i.traffic_quota}{/If}</td>
                <td>{if $i.active == "n"}No{else}Yes{/If}</td>
                <td class="text-right">
                    {If $variables.client.locked == "y" || $variables.client.canceled == "y"}
                            <i class="fa fa-ban"></i>
                        {else}
                            {if $i.hd_quota == -1} {assign "hd_quota" "0"} {else} {assign "hd_quota" "{$i.hd_quota}"} {/If}
                            {if $i.traffic_quota == -1} {assign "traffic_quota" "0"} {else} {assign "traffic_quota" "{$i.traffic_quota}"} {/If}
                        <a href="javascript:;" class="btn btn-xs btn-default" id="btnAction" data-toggle="modal" data-target="#modalEdit" data-target-values="domain_id={$i.domain_id}&domain={$i.domain}&ip_address={$i.ip_address}&ipv6_address={$i.ipv6_address}&server_id={$i.server_id}&ip_address={$i.ip_address}&ipv6_address={$i.ipv6_address}&traffic_quota={$traffic_quota}&hd_quota={$hd_quota}&old_traffic_quota={$traffic_quota}&old_hd_quota={$hd_quota}&cgi={$i.cgi}&ssi={$i.ssi}&perl={$i.perl}&ruby={$i.ruby}&python={$i.python}&suexec={$i.suexec}&errordocs={$i.errordocs}&subdomain={$i.subdomain}&php={$i.php}"><i class="fa fa-pencil"></i></a>
                        <a href="javascript:;" class="btn btn-xs btn-default" id="btnAction" data-toggle="modal" data-target="#modalDelete" data-target-values="domain_id={$i.domain_id}"><i class="fa fa-times"></i></a>
                    {/If}
                    {assign "hdtotal" {$hdtotal}+{$hd_quota}}
                    {assign "webtotal" {$webtotal}+{$traffic_quota}}
                </td>
            </tr>
        {/foreach}
            <tr>
                <td>Quota (Used / Assigned)</td>
                <td>{$hdtotal} / {if $variables.client.limit_web_quota == -1}Unlimited{else}{$variables.client.limit_web_quota}{/If}</td>
                <td>{$webtotal} / {if $variables.client.limit_traffic_quota == -1}Unlimited{else}{$variables.client.limit_traffic_quota}{/If}</td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
{else}
    <p>No Websites found</p>
{/if}


<div class="modal fade" id="modalAdd" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Add Website</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal ajax-form" id="frmAdd">
                    <div id="ajax-params" data-action="{$action_urls.add}" data-method="POST" data-loader="#ajax-loader-add" data-loader-position="outside" data-loader-type="inside-button" data-messages="#ajax-messages" data-callback-on-success="window.location.reload()"></div>
                    <div id="ajax-messages"></div>
                    <input type="hidden" name="server_id" value="{$server_id}">
                    <input type="hidden" name="domain_id" id="domain_id" value="{$domain_id}">
                    <input type="hidden" name="dir_prefix" value="{$dir_prefix}">

                    <div class="form-group">
                        <label for="email" class="col-sm-4 control-label">Domain</label>
                        <div class="col-xs-4">
                            <div class="input-group-sm">
                                <input type="text" class="form-control" name="domain" id="directory">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ip_address" class="col-sm-4 control-label">IPv4-Address</label>
                        <div class="col-xs-4">
                            <div class="input-group-sm">
                                <select class="form-control" name="ip_address" id="ip_address">
                                    <option value="*">*</option>
                                    {foreach from=$variables.ipv4 key=serverips item=x}
                                        <option value="{$x.ip_address}">{$x.ip_address}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="ip_address" class="col-sm-4 control-label">IPv6-Address</label>
                        <div class="col-xs-4">
                            <div class="input-group-sm">
                                <select class="form-control" name="ipv6_address" id="ipv6_address">
                                    <option value=""></option>
                                    {foreach from=$variables.ipv6 key=serverips item=x}
                                        <option value="{$x.ip_address}">{$x.ip_address}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    </div>
                                
                    <div class="form-group">
                        <label for="hd_quota" class="col-sm-4 control-label">Disk Quota</label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <input type="number" class="form-control" name="hd_quota" 
                                    {if $variables.client.limit_web_quota == -1} 
                                        min="0" value="0"
                                    {else} 
                                        value="1" min="1" max="{$variables.client.limit_web_quota - $hdtotal}"
                                    {/If}
                                               id="hd_quota">
                                <span class="input-group-addon">MB</span>
                            </div>
                                {if $variables.client.limit_web_quota == -1} 
                                    <p class="helper-block">enter 0 for unlimited</p>
                                {/If}
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="traffic_quota" class="col-sm-4 control-label">Web Quota</label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <input type="number" class="form-control" name="traffic_quota" 
                                    {if $variables.client.limit_traffic_quota == -1}
                                        min ="0" value="0" 
                                    {else} 
                                        value="1" min="1" max="{$variables.client.limit_traffic_quota - $webtotal}"
                                    {/If}
                                       id="traffic_quota">
                                <span class="input-group-addon">MB</span>
                            </div>
                                {if $variables.client.limit_traffic_quota == -1}
                                    <p class="helper-block">enter 0 for unlimited</p>
                                {/If}
                        </div>
                    </div>

                    {If $variables.client.limit_cgi == "y"}
                    <div class="form-group">
                        <label for="email" class="col-sm-4 control-label">CGI</label>
                        <div class="col-sm-6">
                            <div class="checkbox">
                                <input type="checkbox" name="cgi" id="cgi" value="y" />
                            </div>
                        </div>
                    </div>
                    {/If}

                    {If $variables.client.limit_ssi == "y"}
                    <div class="form-group">
                        <label for="email" class="col-sm-4 control-label">SSI</label>
                        <div class="col-sm-6">
                            <div class="checkbox">
                                <input type="checkbox" name="ssi" id="ssi" value="y" />
                            </div>
                        </div>
                    </div>
                    {/If}
                    
                    {If $variables.client.limit_perl == "y"}
                    <div class="form-group">
                        <label for="email" class="col-sm-4 control-label">Perl</label>
                        <div class="col-sm-6">
                            <div class="checkbox">
                                <input type="checkbox" name="perl" id="perl" value="y" />
                            </div>
                        </div>
                    </div>
                    {/If}
                    
                    {If $variables.client.limit_ruby == "y"}
                    <div class="form-group">
                        <label for="email" class="col-sm-4 control-label">Ruby</label>
                        <div class="col-sm-6">
                            <div class="checkbox">
                                <input type="checkbox" name="ruby" id="ruby" value="y" />
                            </div>
                        </div>
                    </div>
                    {/If}
                    
                    {If $variables.client.limit_python == "y"}
                    <div class="form-group">
                        <label for="email" class="col-sm-4 control-label">Python</label>
                        <div class="col-sm-6">
                            <div class="checkbox">
                                <input type="checkbox" name="python" id="python" value="y" />
                            </div>
                        </div>
                    </div>
                    {/If}
                    
                    {If $variables.client.force_suexec == "y"}
                    <div class="form-group">
                        <label for="email" class="col-sm-4 control-label">SuEXEC</label>
                        <div class="col-sm-6">
                            <div class="checkbox">
                                <input type="checkbox" name="suexec" id="suexec" value="y" />
                            </div>
                        </div>
                    </div>
                    {/If}
                    
                    {If $variables.client.limit_hterror == "y"}
                    <div class="form-group">
                        <label for="email" class="col-sm-4 control-label">Own Error-Documents</label>
                        <div class="col-sm-6">
                            <div class="checkbox">
                                <input type="checkbox" name="errordocs" id="errordocs" value="1" />
                            </div>
                        </div>
                    </div>
                    {/If}
                    
                    <div class="form-group">
                        <label for="subdomain" class="col-sm-4 control-label">Auto-Subdomain</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <span class="input-group-addon" id="subdomain"></span>
                                <select class="form-control" name="subdomain" id="directory">
                                    <option value="none">None</option>
                                    <option value="www">www.</option>
                                    {If $variables.client.limit_wildcard == "y"}
                                        <option value="*">*.</option>
                                    {/If}
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="php" class="col-sm-4 control-label">PHP</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <span class="input-group-addon" id="php"></span>
                                <select class="form-control" name="php" id="directory">
                                    <option value="no">Disabled</option>
                                    {If in_array('fast_cgi', $web_php_options)}<option value="fast-cgi">Fast-CGI</option>{/If}
                                    {If in_array('cgi', $web_php_options)}<option value="cgi">CGI</option>{/If}
                                    {If in_array('mod', $web_php_options)}<option value="mod">Mod-PHP</option>{/If}
                                    {If in_array('suphp', $web_php_options)}<option value="suphp">SuPHP</option>{/If}
                                    {If in_array('php-fpm', $web_php_options)}<option value="php-fpm">PHP-FPM</option>{/If}
                                    {If in_array('hhvm', $web_php_options)}<option value="hhvm">HHVM</option>{/If}
                                </select>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button class="btn btn-success" onclick="$('#frmAdd #domain').val($('#frmAdd #domain_id :selected').html());$('#frmAdd').submit()"><span id="ajax-loader-add"></span> Create Website</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Update Web Domain</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal ajax-form" id="frmEdit">
                    <div id="ajax-params" data-action="{$action_urls.edit}" data-method="POST" data-loader="#ajax-loader-edit" data-loader-position="outside" data-loader-type="inside-button" data-messages="#ajax-messages" data-callback-on-success="window.location.reload()"></div>
                    <div id="ajax-messages"></div>
                    <input type="hidden" name="old_hd_quota" id="old_hd_quota">
                    <input type="hidden" name="old_traffic_quota" id="old_traffic_quota">
                    <input type="hidden" name="client_hd_quota" value="{$variables.client.limit_web_quota}">
                    <input type="hidden" name="client_traffic_quota" value="{$variables.client.limit_traffic_quota}">
                    <input type="hidden" name="client_hd_used" value="{$hdtotal}">
                    <input type="hidden" name="client_traffic_used" value="{$webtotal}">
                    <input type="hidden" name="domain_id" id="domain_id">
                    <input type="hidden" name="server_id" id="server_id">
                    
                    <div class="form-group">
                        <label for="domain" class="col-sm-4 control-label">Domain</label>
                        <div class="col-xs-4">
                            <div class="input-group-sm">
                                <input type="text" class="form-control" name="domain" id="domain">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="ip_address" class="col-sm-4 control-label">IPv4-Address</label>
                        <div class="col-xs-4">
                            <div class="input-group-sm">
                                <select class="form-control" name="ip_address" id="ip_address">
                                    <option value="*">*</option>
                                    {foreach from=$variables.ipv4 key=serverips item=x}
                                        <option value="{$x.ip_address}">{$x.ip_address}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="ip_address" class="col-sm-4 control-label">IPv6-Address</label>
                        <div class="col-xs-4">
                            <div class="input-group-sm">
                                <select class="form-control" name="ipv6_address" id="ipv6_address">
                                    <option value=""></option>
                                    {foreach from=$variables.ipv6 key=serverips item=x}
                                        <option value="{$x.ip_address}">{$x.ip_address}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    </div>
                                
                    <div class="form-group">
                        <label for="hd_quota" class="col-sm-4 control-label">Disk Quota</label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <input type="number" class="form-control" name="hd_quota" id="hd_quota"
                                    {if $variables.client.limit_web_quota == -1} 
                                        min="0" value="0"
                                    {else} 
                                        value="1" min="1" max="{$variables.client.limit_web_quota}" 
                                    {/If}
                                    >                                
                            <span class="input-group-addon">MB</span>
                            </div>
                                {if $variables.client.limit_web_quota == -1} 
                                    <p class="helper-block">enter 0 for unlimited</p>
                                {/If}
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="traffic_quota" class="col-sm-4 control-label">Web Quota</label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <input type="number" class="form-control" name="traffic_quota" id="traffic_quota" 
                                    {if $variables.client.limit_traffic_quota == -1}
                                        min ="0"
                                    {else} 
                                        min="1" max="{$variables.client.limit_traffic_quota}" 
                                    {/If}
                                    >
                                    <span class="input-group-addon">MB</span>
                            </div>
                                {if $variables.client.limit_traffic_quota == -1}
                                    <p class="helper-block">enter 0 for unlimited</p>
                                {/If}
                        </div>
                    </div>
                    
                    {If $variables.client.limit_cgi == "y"}
                    <div class="form-group">
                        <label for="cgi" class="col-sm-4 control-label">CGI</label>
                        <div class="col-sm-6">
                            <div class="checkbox">
                                <input type="checkbox" name="cgi" id="cgi" value="y" {if $i.cgi == "y"} checked{/if}/>
                            </div>
                        </div>
                    </div>
                    {/If}
                    
                    {If $variables.client.limit_ssi == "y"}
                    <div class="form-group">
                        <label for="ssi" class="col-sm-4 control-label">SSI</label>
                        <div class="col-sm-6">
                            <div class="checkbox">
                                <input type="checkbox" name="ssi" id="ssi" value="y" {if $i.ssi == "y"} checked{/if}/>
                            </div>
                        </div>
                    </div>
                    {/If}
                    
                    {If $variables.client.limit_perl == "y"}
                    <div class="form-group">
                        <label for="perl" class="col-sm-4 control-label">Perl</label>
                        <div class="col-sm-6">
                            <div class="checkbox">
                                <input type="checkbox" name="perl" id="perl" value="y" {if $i.perl == "y"} checked{/if}/>
                            </div>
                        </div>
                    </div>
                    {/If}
                    
                    {If $variables.client.limit_ruby == "y"}
                    <div class="form-group">
                        <label for="ruby" class="col-sm-4 control-label">Ruby</label>
                        <div class="col-sm-6">
                            <div class="checkbox">
                                <input type="checkbox" name="ruby" id="ruby" value="y" {if $i.ruby == "y"} checked{/if}/>
                            </div>
                        </div>
                    </div>
                    {/If}
                    
                    {If $variables.client.limit_python == "y"}
                    <div class="form-group">
                        <label for="python" class="col-sm-4 control-label">Python</label>
                        <div class="col-sm-6">
                            <div class="checkbox">
                                <input type="checkbox" name="python" id="python" value="y" {if $i.python == "y"} checked{/if}/>
                            </div>
                        </div>
                    </div>
                    {/If}
                    
                    {If $variables.client.force_suexec == "y"}
                    <div class="form-group">
                        <label for="suexec" class="col-sm-4 control-label">SuEXEC</label>
                        <div class="col-sm-6">
                            <div class="checkbox">
                                <input type="checkbox" name="suexec" id="suexec" value="y" {if $i.suexec == "y"} checked{/if}/>
                            </div>
                        </div>
                    </div>
                    {/If}
                    
                    {If $variables.client.limit_hterror == "y"}
                    <div class="form-group">
                        <label for="errordocs" class="col-sm-4 control-label">Own Error-Documents</label>
                        <div class="col-sm-6">
                            <div class="checkbox">
                                <input type="checkbox" name="errordocs" id="errordocs" value="1" {if $i.errordocs == "1"} checked{/if}/>
                            </div>
                        </div>
                    </div>
                    {/If}
                    
                    
                    <div class="form-group">
                        <label for="subdomain" class="col-sm-4 control-label">Auto-Subdomain</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <span class="input-group-addon" id="subdomain"></span>
                                <select class="form-control" name="subdomain" id="directory">
                                    <option value="none"{if $i.subdomain == "none"} selected{/if}>None</option>
                                    <option value="www"{if $i.subdomain == "www"} selected{/if}>www.</option>
                                    {If $variables.client.limit_wildcard == "y"}
                                        <option value="*"{if $i.subdomain == "*"} selected{/if}>*.</option>
                                    {/If}
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="php" class="col-sm-4 control-label">PHP</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <span class="input-group-addon" id="php"></span>
                                <select class="form-control" name="php" id="directory">
                                    <option value="no"{if $i.php == "no"} selected{/if}>Disabled</option>
                                    {If in_array('fast_cgi', $web_php_options)}<option value="fast-cgi"{if $i.php == "fast-cgi"} selected{/if}>Fast-CGI</option>{/If}
                                    {If in_array('cgi', $web_php_options)}<option value="cgi"{if $i.php == "cgi"} selected{/if}>CGI</option>{/If}
                                    {If in_array('mod', $web_php_options)}<option value="mod"{if $i.php == "mod"} selected{/if}>Mod-PHP</option>{/If}
                                    {If in_array('suphp', $web_php_options)}<option value="suphp"{if $i.php == "suphp"} selected{/if}>SuPHP</option>{/If}
                                    {If in_array('php-fpm', $web_php_options)}<option value="php-fpm"{if $i.php == "php-fpm"} selected{/if}>PHP-FPM</option>{/If}
                                    {If in_array('hhvm', $web_php_options)}<option value="hhvm"{if $i.php == "hhvm"} selected{/if}>HHVM</option>{/If}
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
                <h4 class="modal-title">Delete Website</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal ajax-form" id="frmDelete">
                    <div id="ajax-params" data-action="{$action_urls.delete}" data-method="POST" data-loader="#ajax-loader-delete" data-loader-position="outside" data-loader-type="inside-button" data-messages="#ajax-messages" data-callback-on-success="window.location.reload()"></div>
                    <div id="ajax-messages"></div>
                    <input name="domain_id" type="hidden" id="domain_id">
                </form>
                <p>Are you sure you want to delete this website?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button class="btn btn-danger" onclick="$('#frmDelete').submit()"><span id="ajax-loader-delete"></span> Confirm</button>
            </div>
        </div>
    </div>
</div>