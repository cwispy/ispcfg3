<?php
/*
 *  ISPConfig v3.1+ module for WHMCS v7.x or Higher
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
 *
 */
if (isset($_GET['view_action'])) {
    if ($_GET['view_action'] == 'ispconfig') {
        //$return = array('status' => 'success', 'ajax' => 'false', 'response' => array('server_url' => $domain_url));
		$return = array('status' => 'success', 'ajax' => 'false', 'response' => array('server_url' => $server_url));
    }
	elseif ($_GET['view_action'] == 'stats') {
        $return = array('status' => 'success', 'ajax' => 'false', 'response' => array('server_url' => $domain_url));
		//$return = array('status' => 'success', 'ajax' => 'false', 'response' => array('server_url' => $server_url));
    }
    elseif ($_GET['view_action'] == 'webmail') {
        header('Location: '.$domain_url.'/webmail/');
        exit;
    }
    elseif ($_GET['view_action'] == 'phpmyadmin') {
        header('Location: '.$domain_url.'/phpmyadmin/');
        exit;
    }
}