{*
 /*  ISPConfig v3.1+ module for WHMCS v6.x or Higher
 *  Copyright (C) 2014 - 2016  Shane Chrisp
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
{if $request.view_action eq "ispconfig"}
    <div class="text-center">
        <h3>logging in to the control panel...</h3>
        <i class="fa fa-spinner fa-spin"></i>
    </div>
    <div class="ajaxCpLogin" data-url="{$variables.server_url}/content.php" data-redirect="{$variables.server_url}/index.php" data-username="{$params['username']}" data-password="{$params['password']}"></div>
{/if}
{if $request.view_action eq "stats"}
    <div class="text-center">
        <h3>logging in to the website statistics ...</h3>
        <i class="fa fa-spinner fa-spin"></i>
    </div>
    
	<div class="ajaxCpLogin"  data-redirect="https://admin:{$params['password']}@{$params['domain']}/stats" ></div>
{/if}