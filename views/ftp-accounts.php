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
if (isset($_GET['view_action'])) {
    if ($_GET['view_action'] == 'add') {
        if (!isset($_REQUEST['username']) || !$_REQUEST['username']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Username is required'));
        }
        if (!isset($_REQUEST['password']) || !$_REQUEST['password']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Password is required'));
        }
        if (!isset($_REQUEST['password2']) || $_REQUEST['password2'] != $_REQUEST['password']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Passwords do not match'));
        }
        $options = array(
            'server_id' => $_REQUEST['server_id'],
            'username' => $_REQUEST['username_prefix'].$_REQUEST['username'],
            'password' => $_REQUEST['password'],
            'quota_size' => isset($_REQUEST['quota_size']) ? intval($_REQUEST['quota_size']) : -1,
            'dir' => $_REQUEST['dir_prefix'],
            'uid' => $_REQUEST['uid'],
            'gid' => $_REQUEST['gid'],
            'parent_domain_id' => $_REQUEST['parent_domain_id'],
            'active' => 'y'
        );
        if ($_REQUEST['directory']) $options['dir'] .= '/'.$_REQUEST['directory'];

        $create = cwispy_soap_request($params, 'sites_ftp_user_add', $options);
        if ($create['status'] == 'success') {
            cwispy_return_ajax_response(array('status' => 'success', 'message' => 'FTP account created successfully'));
        }
        else {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => $create['response']));
        }
    }
    elseif ($_GET['view_action'] == 'edit') {
        if (!isset($_REQUEST['username']) || !$_REQUEST['username']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Username is required'));
        }
        if (!isset($_REQUEST['password2']) || $_REQUEST['password2'] != $_REQUEST['password']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Passwords do not match'));
        }
        $options = array(
            'server_id' => $_REQUEST['server_id'],
            'username' => $_REQUEST['username_prefix'].$_REQUEST['username'],
            'quota_size' => isset($_REQUEST['quota_size']) ? intval($_REQUEST['quota_size']) : -1,
            'dir' => $_REQUEST['dir_prefix'],
            'uid' => $_REQUEST['uid'],
            'gid' => $_REQUEST['gid'],
            'parent_domain_id' => $_REQUEST['parent_domain_id'],
            'active' => 'y',
            'id' => $_REQUEST['ftp_user_id']
        );
        if ($_REQUEST['directory']) $options['dir'] .= '/'.$_REQUEST['directory'];
        if ($_REQUEST['password']) $options['password'] = $_REQUEST['password'];

        $update = cwispy_soap_request($params, 'sites_ftp_user_update', $options);
        if ($update['status'] == 'success') {
            cwispy_return_ajax_response(array('status' => 'success', 'message' => 'FTP account updated successfully'));
        }
        else {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => $update['response']));
        }
    }
    elseif ($_GET['view_action'] == 'delete') {
        $options = array(
            'id' => $_REQUEST['ftp_user_id']
        );

        $delete = cwispy_soap_request($params, 'sites_ftp_user_delete', $options);
        if ($delete['status'] == 'success') {
            cwispy_return_ajax_response(array('status' => 'success', 'message' => 'FTP account deleted successfully'));
        }
        else {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => $delete['response']));
        }
    }
    else {
        cwispy_return_ajax_response(array('status' => 'error', 'message' => 'View action missing'));
    }
}
else {
    $return = cwispy_soap_request($params, 'sites_ftp_user_get');

    $return['action_urls']['add'] = cwispy_create_url(array('view' => 'ftp-accounts', 'view_action' => 'add'));
    $return['action_urls']['edit'] = cwispy_create_url(array('view' => 'ftp-accounts', 'view_action' => 'edit'));
    $return['action_urls']['delete'] = cwispy_create_url(array('view' => 'ftp-accounts', 'view_action' => 'delete'));
}