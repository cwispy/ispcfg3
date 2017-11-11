<?php
/*
 *  ISPConfig v3.1+ module for WHMCS v7.x or Higher
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
 *
 */
if (isset($_GET['view_action'])) {
    
}
else {

	$disk = cwispy_soap_request($params, 'quota_get_by_user');
    $traffic = cwispy_soap_request($params, 'trafficquota_get_by_user');
    $ftptraffic = cwispy_soap_request($params, 'ftptrafficquota_data');
    $databasedisk = cwispy_soap_request($params, 'databasequota_get_by_user');
    $maildisk = cwispy_soap_request($params, 'mailquota_get_by_user');
    

    $return = array_merge_recursive($disk, $traffic, $ftptraffic, $databasedisk, $maildisk);
	

    if (is_array($return['status'])) {
        $return['status'] = (in_array('error', $return['status'])) ? 'error' : 'success';
    } 
}