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
        if (!isset($_REQUEST['email']) || !$_REQUEST['email']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Email is required'));
        }
        if (!isset($_REQUEST['domain']) || !$_REQUEST['domain']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Domain is required'));
        }
        if (!isset($_REQUEST['destination']) || !$_REQUEST['destination']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Destination is required'));
        }

        $domain_options = array(
            'source' => $_REQUEST['email'].'@'.$_REQUEST['domain'],
            'destination' => $_REQUEST['destination'].'@'.$_REQUEST['domain'],
            'server_id' => $_REQUEST['svrid'],
            'type' => 'forward',
            'active' => 'y'
        );
        $create = cwispy_soap_request($params, 'mail_forward_add', $domain_options);
        if ($create['status'] == 'success') {
            cwispy_return_ajax_response(array('status' => 'success', 'message' => 'Email forwarder created successfully'));
        }
        else {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => $create['response']));
        }
    }
    elseif ($_GET['view_action'] == 'edit') {
        if (!isset($_REQUEST['destination']) || !$_REQUEST['destination']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Destination is required'));
        }
        $domain_options = array(
            'source' => $_REQUEST['source'],
            'destination' => $_REQUEST['destination'],
            'id' => $_REQUEST['forwarder_id'],
        );

        $update = cwispy_soap_request($params, 'mail_forward_update', $domain_options);
        if ($update['status'] == 'success') {
            cwispy_return_ajax_response(array('status' => 'success', 'message' => 'Email forwarder updated successfully'));
        }
        else {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => $update['response']));
        }
    }
    elseif ($_GET['view_action'] == 'delete') {
        $domain_options = array(
            'id' => $_REQUEST['forwarder_id']
        );

        $delete = cwispy_soap_request($params, 'mail_forward_delete', $domain_options);
        if ($delete['status'] == 'success') {
            cwispy_return_ajax_response(array('status' => 'success', 'message' => 'Email forwarder deleted successfully'));
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
    $domains = cwispy_soap_request($params, 'mail_domain_get');
    $forwarders = cwispy_soap_request($params, 'mail_forward_get');

    $return = array_merge_recursive($domains, $forwarders);

    if (is_array($return['status'])) {
        $return['status'] = (in_array('error', $return['status'])) ? 'error' : 'success';
    }

    $return['action_urls']['add'] = cwispy_create_url(array('view' => 'email-forwarders', 'view_action' => 'add'));
    $return['action_urls']['edit'] = cwispy_create_url(array('view' => 'email-forwarders', 'view_action' => 'edit'));
    $return['action_urls']['delete'] = cwispy_create_url(array('view' => 'email-forwarders', 'view_action' => 'delete'));
}