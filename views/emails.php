<?php
/*
 *  ISPConfig v3.1+ module for WHMCS v6.x or Higher
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
if (isset($_GET['view_action'])) {
    if ($_GET['view_action'] == 'add') {
        if (!isset($_REQUEST['email']) || !$_REQUEST['email']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Email is required'));
        }
        if (!isset($_REQUEST['domain']) || !$_REQUEST['domain']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Domain is required'));
        }
        if (!isset($_REQUEST['password']) || !$_REQUEST['password']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Password is required'));
        }
        if (!isset($_REQUEST['password2']) || $_REQUEST['password2'] != $_REQUEST['password']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Passwords do not match'));
        }
        
        
        
        $domain_options = array(
            'email' => $_REQUEST['email'].'@'.$_REQUEST['domain'],
            'login' => $_REQUEST['email'].'@'.$_REQUEST['domain'],
            'password' => $_REQUEST['password'],
            'quota' => intval($_REQUEST['quota']) * 1048576,
            'server_id' => intval($_REQUEST['svrid']),
            'uid' => 5000,
            'gid' => 5000,
            'homedir' => '/var/vmail',
            'maildir' => '/var/vmail/'.$_REQUEST['domain'].'/'.$_REQUEST['email'],
            'maildir_format' => 'maildir',
            'postfix' => 'y',
            'access' => 'y'
        );
        $create = cwispy_soap_request($params, 'mail_user_add', $domain_options);
        if ($create['status'] == 'success') {
            cwispy_return_ajax_response(array('status' => 'success', 'message' => 'Email account created successfully'));
        }
        else {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => $create['response']));
        }
    }
    elseif ($_GET['view_action'] == 'edit') {
        if (!isset($_REQUEST['password2']) || $_REQUEST['password2'] != $_REQUEST['password']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Passwords do not match'));
        }
        
        if ( ($_REQUEST['quota'] > $_REQUEST['old_quota'] ) && ($_REQUEST['totalquota'] != "-1")) {
            $availablequota = ( ( $_REQUEST['totalquota'] - $_REQUEST['emailtotal'] ) + $_REQUEST['old_quota'] );
            if ( $_REQUEST['quota'] > $availablequota ) {
                cwispy_return_ajax_response(array("status" => "error", "message" => "Email Quota Error. Maximum available $availablequota MB"));
            }
        }
        
        $domain_options = array(
            'email' => $_REQUEST['email'],
            'login' => $_REQUEST['email'],
            'quota' => intval($_REQUEST['quota']) * 1048576,
            'id' => $_REQUEST['mail_id']
        );
        if ($_REQUEST['password']) $domain_options['password'] = $_REQUEST['password'];

        $update = cwispy_soap_request($params, 'mail_user_update', $domain_options);
        if ($update['status'] == 'success') {
            cwispy_return_ajax_response(array('status' => 'success', 'message' => 'Email account updated successfully'));
        }
        else {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => $update['response']));
        }
    }
    elseif ($_GET['view_action'] == 'delete') {
        $domain_options = array(
            'id' => $_REQUEST['mail_id']
        );

        $delete = cwispy_soap_request($params, 'mail_user_delete', $domain_options);
        if ($delete['status'] == 'success') {
            cwispy_return_ajax_response(array('status' => 'success', 'message' => 'Email account deleted successfully'));
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
    $client  = cwispy_soap_request($params, 'client_get');
    $domains = cwispy_soap_request($params, 'mail_domain_get');
    $mails = cwispy_soap_request($params, 'mail_user_get');
	$limits = cwispy_soap_request($params, 'client_get_quota');
    $return = array_merge_recursive($domains, $mails, $limits, $client);

    if (is_array($return['status'])) {
        $return['status'] = (in_array('error', $return['status'])) ? 'error' : 'success';
    }

    $return['action_urls']['add'] = cwispy_create_url(array('view' => 'emails', 'view_action' => 'add'));
    $return['action_urls']['edit'] = cwispy_create_url(array('view' => 'emails', 'view_action' => 'edit'));
    $return['action_urls']['delete'] = cwispy_create_url(array('view' => 'emails', 'view_action' => 'delete'));
}