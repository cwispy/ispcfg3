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
<link href="modules/servers/ispcfg3/assets/ispcfg3.css" rel="stylesheet"> <span class="icon-header icon-email"></span>
<h3>Usage ({$params.domain})</h3>
<p>In this area you can see the data and bandwidth usage of your hosting. </p>
<hr>
<div class="text-right"><button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalAdd">Add Email</button></div>

    <h5>Website Harddisk Quota</h5>
	<table class="table table-condensed table-striped table-hover ihost-smart-table">
        <thead>
            <tr>
                <th class="text-left">Domain/Website</th>
                <th class="text-right">Used space</th>
                <th class="text-right">Soft Limit</th>
                <th class="text-right">Hard Limit</th>
            </tr>
        </thead>
        <tbody>
        {foreach $variables.disk as $siteusage}
            <tr>
                <td class="text-left">{$siteusage.domain}</td>
                <td class="text-right">
                {{$siteusage.used /1024}|number_format:2} MB
				</td>
				<td class="text-right">
               {{$siteusage.soft /1024}|number_format:2} MB
				</td>
				<td class="text-right">
				{{$siteusage.hard /1024}|number_format:2} MB
				</td>
				<td class="text-right">
				
				</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
<br/>

    <h5>Mailbox Quota</h5>
	<table class="table table-condensed table-striped table-hover ihost-smart-table">
        <thead>
            <tr>
                <th class="text-left">Email Address</th>
                <th class="text-left">Name</th>
                <th class="text-right">Used Space</th>
                <th class="text-right">Quota</th>
            </tr>
        </thead>
        <tbody>
        {foreach $variables.maildisk as $mailusage}
            <tr>
                <td class="text-left">{$mailusage.email}</td>
                <td class="text-left">
                {$mailusage.name}
				</td>
				<td class="text-right">
                {{$mailusage.used / 1048576}|number_format:2} MB
				</td>
				<td class="text-right">
				{{$mailusage.quota_raw / 1048576}|number_format:2} MB
				</td>
				<td class="text-right">
				
				</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
	<br/>
    
    <h5>Database Quota</h5>
	<table class="table table-condensed table-striped table-hover ihost-smart-table">
        <thead>
            <tr>
                <th class="text-left">Database Name</th>
                <th class="text-right">Used Space</th>
                <th class="text-right">Quota</th>
            </tr>
        </thead>
        <tbody>
        {foreach $variables.databasedisk as $databasedisk}
            <tr>
                <td class="text-left">{$databasedisk.database_name}</td>
				<td class="text-right">
                {{$databasedisk.used / 1048576}|number_format:2} MB
				</td>
				<td class="text-right">
				{If $databasedisk.quota_raw == ''||$databasedisk.quota_raw == '-1'}Unlimited{else}{$databasedisk.quota_raw} MB{/If}
				</td>
				<td class="text-right">
				
				</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
	<br/>