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
<link href="modules/servers/ispcfg3/assets/ispcfg3.css" rel="stylesheet"> <span class="icon-header icon-email"></span>
<h3>Usage ({$params.domain})</h3>
<p>In this area you can see the data and bandwidth usage of your hosting. </p>
<hr><div class="text-right"><button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalAdd">Add Email</button></div>


	{$Total = 0 }  
	{foreach $variables.quota as $mailboxr}
    {$Total = $Total+$mailboxr.used}
    {/foreach}
      
	
	
	
		<table class="table table-condensed table-striped table-hover ihost-smart-table">
	 <thead><tr><th>Site</th><th class="text-right">Space Usage</th><th class="text-right">Email Usage</th><th class="text-right">Traffic usage this month</th><th class="text-right">Traffic Quota</th></tr></thead>
        <tbody>
        {foreach $variables.used.site as $siteusage}
            <tr>
                <td>{$siteusage.domain}</td>
                <td class="text-right">
                {{$siteusage.used /1024}|number_format:2} MB /{$siteusage.hd_quota /1048576} MB
				</td>
				<td class="text-right">
               {{$Total /1024}|number_format:3} MB
				</td>
				<td class="text-right">
				{{$siteusage.traffic.this_month /1048576}|number_format:2} MB / {$siteusage.traffic_quota /1048576} MB
				</td>
				<td class="text-right">
				
				</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
	<br><br>
	
	<br><br>

<!--{$variables|print_r}-->