<?php
/*
 * 
 *  ISPConfig v3.x module for WHMCS v5.x or Higher
 *  Copyright (C) 2014, 2015  Shane Chrisp
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
 *
 */
class remoting_version extends remoting {
	
	public function ispconfig_version_get() {
		global $app;

        return ISPC_APP_VERSION;
	}
	
}

?>