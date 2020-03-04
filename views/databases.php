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


    if ($_GET['view_action'] == 'db-add') {
        if (!isset($_REQUEST['database_name']) || !$_REQUEST['database_name']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Database name is required'));
        }
        if (!isset($_REQUEST['database_user_id']) || !$_REQUEST['database_user_id']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Database Read / Write User is required'));
        }
        if ( !isset($_REQUEST['parent_domain_id']) || $_REQUEST['parent_domain_id'] == '' ) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Site is required.'));
        }
        if ( $_REQUEST['database_quota'] >= $_REQUEST['old_database_quota'] ) {
            $used = ( $_REQUEST['limit_database_quota'] - $_REQUEST['hdtotalused'] );
            if ( $_REQUEST['database_quota'] > $used ) {
                cwispy_return_ajax_response(array('status' => 'error', 'message' => "Database Quota Error. Maximum available $used MB"));
            }
        }
	
        $create_options = array(
            'server_id' => $_REQUEST['server_id'],
            'parent_domain_id' => $_REQUEST['parent_domain_id'],
            'database_name' => $_REQUEST['prefix'].$_REQUEST['database_name'],
            'database_name_prefix' => $_REQUEST['prefix'],
            'database_user_id' => $_REQUEST['database_user_id'],
            'database_ro_user_id' => $_REQUEST['database_ro_user_id'],
            'database_quota' => $_REQUEST['database_quota'],
            'type' => 'mysql',
            'active' => 'y'
        );

        $create = cwispy_soap_request($params, 'sites_database_add', $create_options);
        if ($create['status'] == 'success') {
            cwispy_return_ajax_response(array('status' => 'success', 'message' => 'Database created successfully'));
        }
        else {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => $create['response']));
        }
    }
    elseif ($_GET['view_action'] == 'db-edit') {
        if (!isset($_REQUEST['database_name']) || !$_REQUEST['database_name']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Database name is required'));
        }
        if (!isset($_REQUEST['database_user_id']) || !$_REQUEST['database_user_id']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Database user is required'));
        }
        if ( $_REQUEST['database_quota'] >= $_REQUEST['old_database_quota'] ) {
        $used = ( $_REQUEST['limit_database_quota'] - $_REQUEST['hdtotalused'] ) + $_REQUEST['old_database_quota'];
            if ( $_REQUEST['database_quota'] > $used ) {
                cwispy_return_ajax_response(array('status' => 'error', 'message' => "Database Quota Error. Maximum available $used MB"));
            }
        }
        
        $update_options = array(
            'database_name' => $_REQUEST['database_name_prefix'].$_REQUEST['database_name'],
            'database_name_prefix' => $_REQUEST['database_name_prefix'],
            'database_user_id' => $_REQUEST['database_user_id'],
            'database_ro_user_id' => $_REQUEST['database_ro_user_id'],
            'database_quota' => $_REQUEST['database_quota'],
            'id' => $_REQUEST['database_id'],
        );

        $update = cwispy_soap_request($params, 'sites_database_update', $update_options);
        if ($update['status'] == 'success') {
            cwispy_return_ajax_response(array('status' => 'success', 'message' => 'Database updated successfully'));
        }
        else {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => $update['response']));
        }
    }
    elseif ($_GET['view_action'] == 'db-delete') {
        $options = array(
            'id' => $_REQUEST['database_id']
        );

        $delete = cwispy_soap_request($params, 'sites_database_delete', $options);
        if ($delete['status'] == 'success') {
            cwispy_return_ajax_response(array('status' => 'success', 'message' => 'Database deleted successfully'));
        }
        else {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => $delete['response']));
        }
    }
    elseif ($_GET['view_action'] == 'db-user-add') {
        if (!isset($_REQUEST['username']) || !$_REQUEST['username']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Username is required'));
        }
        if (!isset($_REQUEST['password']) || !$_REQUEST['password']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Password is required'));
        }
        if ($_REQUEST['password'] != $_REQUEST['password2']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Passwords do not match'));
        }

        $create_options = array(
            'database_user' => $_REQUEST['prefix'].$_REQUEST['username'],
            'database_user_prefix' => $_REQUEST['prefix'],
            'database_password' => $_REQUEST['password'],
        );

        $create = cwispy_soap_request($params, 'sites_database_user_add', $create_options);
        if ($create['status'] == 'success') {
            cwispy_return_ajax_response(array('status' => 'success', 'message' => 'Database user created successfully'));
        }
        else {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => $create['response']));
        }
    }
    elseif ($_GET['view_action'] == 'db-user-edit') {
        if (!isset($_REQUEST['username']) || !$_REQUEST['username']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Username name is required'));
        }
        if (!isset($_REQUEST['password2']) || $_REQUEST['password2'] != $_REQUEST['password']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Passwords do not match'));
        }

        $update_options = array(
            'database_user' => $_REQUEST['prefix'].$_REQUEST['username'],
            'database_user_prefix' => $_REQUEST['prefix'],
            'id' => $_REQUEST['database_user_id'],
        );
        if ($_REQUEST['database_password']) $update_options['database_password'] = $_REQUEST['password'];

        $update = cwispy_soap_request($params, 'sites_database_user_update', $update_options);
        if ($update['status'] == 'success') {
            cwispy_return_ajax_response(array('status' => 'success', 'message' => 'Database user updated successfully'));
        }
        else {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => $update['response']));
        }
    }
    elseif ($_GET['view_action'] == 'db-user-delete') {
        $options = array(
            'id' => $_REQUEST['database_user_id']
        );

        $delete = cwispy_soap_request($params, 'sites_database_user_delete', $options);
        if ($delete['status'] == 'success') {
            cwispy_return_ajax_response(array('status' => 'success', 'message' => 'Database user deleted successfully'));
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
    $dbs      = cwispy_soap_request($params, 'sites_database_get');
    $db_users = cwispy_soap_request($params, 'sites_database_user_get');
    $domains = cwispy_soap_request($params, 'sites_web_domain_get');
	$clientP  = cwispy_soap_request($params, 'client_get_by_username');
    $client   = cwispy_soap_request($params, 'client_get');
    
    // Create the Customer number based on the ISPConfig settings. 
    // eg: C[CUSTOMER_NO] would become C45
    $cust = str_replace('[CUSTOMER_NO]',$client['response']['client']['client_id'],$client['response']['client']['customer_no_template']);
    // Strip the square bracket
    $client['response']['client']['customer_no'] = substr(strstr($cust, "]"), 1);
    
    $return   = array_merge_recursive($dbs, $db_users, $clientP, $client, $domains);

    if (isset($db_users['response']['db_users']) && $db_users['response']['db_users']) {
        foreach($db_users['response']['db_users'] as $db_user) {
            $return['response']['db_users_o'][$db_user['database_user_id']] = $db_user;
        }
    }

    if (is_array($return['status'])) {
        $return['status'] = (in_array('error', $return['status'])) ? 'error' : 'success';
    }

    $return['action_urls']['db']['add'] = cwispy_create_url(array('view' => 'databases', 'view_action' => 'db-add'));
    $return['action_urls']['db']['edit'] = cwispy_create_url(array('view' => 'databases', 'view_action' => 'db-edit'));
    $return['action_urls']['db']['delete'] = cwispy_create_url(array('view' => 'databases', 'view_action' => 'db-delete'));

    $return['action_urls']['db_user']['add'] = cwispy_create_url(array('view' => 'databases', 'view_action' => 'db-user-add'));
    $return['action_urls']['db_user']['edit'] = cwispy_create_url(array('view' => 'databases', 'view_action' => 'db-user-edit'));
    $return['action_urls']['db_user']['delete'] = cwispy_create_url(array('view' => 'databases', 'view_action' => 'db-user-delete'));
}