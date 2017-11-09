ispcfg3
======
/*
 * 
 *  ISPConfig v3.1+ module for WHMCS v7.x or Higher
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
 *
 */

Requires ISPConfig 3.1+
WHMCS 7+

WARNING 07/Nov/2017

This module now requires that you have ISPConfig 3.1 or higher
It will no longer work with older versions of ISPConfig

This module is currently undergoing changes and is not 
recommended to be run in production in its current status.



ISPConfig module for WHMCS

Create a directory on your WHMCS server in the modules/servers directory and name the folder ispcfg3

Copy the contents to your WHMCS modules/servers/ispcfg3 directory.

Further information can be found in the wiki https://github.com/cwispy/ispcfg3/wiki

Be sure to change the option below, which is located near the top of the 
ispcfg3.php file, on a production systems.

ini_set("display_errors", 1);
to
ini_set("display_errors", 0);
