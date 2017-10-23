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
<h2>Oops! Something went wrong.</h2>
<div class="alert alert-danger">
    {if is_array($usefulErrorHelper) && count($usefulErrorHelper) > 0}
        {foreach $usefulErrorHelper as $error}
        <p>{$error}</p>
        {/foreach}
    {else}
    <p>{$usefulErrorHelper}</p>
    {/if}
</div>

<p>Please go back and try again.</p>

<p>If the problem persists, please contact support.</p>