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
<span class="icon-header icon-sitebuilder"></span>
<h3>Create or Edit your website - {$params.domain}</h3>
<p>SiteBuilder is a web publishing tool. It enables you to put information on your own websites quickly and easily. You can maintain your website through a web browser using SiteBuilder's editing tools. You don't need any specialist software or web programming skills.</p>

<hr>


		<div class="container3">
			<h1>My Website</h1>
			
			<table class="tbl-websites">
				<thead>
					<tr>
						<th>Website</th>
						<th>Functions</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>{$params.domain}</td>
						<td><a target=blank href="{$smarty.server.REQUEST_URI}&editWebsite=1">Create/Edit Website</a></td>
					
					</tr>
				</tbody>
			</table>
		</div>