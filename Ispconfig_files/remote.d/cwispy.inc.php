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
class remoting_cwispy extends remoting {
	
	public function mailquota_get_by_userid($session_id, $mailuser_id)
	{
		global $app;
		$app->uses('quota_lib');
		
		if(!$this->checkPerm($session_id, 'mailquota_get_by_user')) {
			$this->server->fault('permission_denied', 'You do not have the permissions to access this function.');
			return false;
		}
		
		$value = $app->quota_lib->get_mailquota_data($client_id, false);
        
        for ($x=0;$x<count($value);$x++) {
            if ($mailuser_id == $value[$x]['mailuser_id']) {
                $quota = $value[$x];
            }
        }
        
        return $quota;
	}
	
}

?>