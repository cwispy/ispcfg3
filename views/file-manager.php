<?php
/*
 *  ISPConfig v3.1+ module for WHMCS v6.x or Higher
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
$elfinder_dir = ELFINDER_DIR;
if (file_exists($elfinder_dir)) {
    $elfinder_options['host'] = $params['domain'];
    //$elfinder_options['host'] = str_replace(':8080', '', $params['configoption3']);
    $elfinder_options['user'] = $params['username'].'admin';
    $elfinder_options['pass'] = $params['password'];
    
    change_ftp_password($params, $elfinder_options['user'], $elfinder_options['pass']);

    $return = array('status' => 'success', 'ajax' => 'false', 'response' => array('elfinder_ftp' => base64_encode(json_encode($elfinder_options))));
}
else {
    $return = array('status' => 'error', 'response' => 'Elfinder not found');
}