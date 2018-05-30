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
    if ($_GET['view_action'] == 'add') {
        if (!isset($_REQUEST['zone']) || !$_REQUEST['zone']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Zone is required'));
        }
        if (!isset($_REQUEST['type']) || !$_REQUEST['type']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Record type is required'));
        }
        if (!isset($_REQUEST['destination']) || !$_REQUEST['destination']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Points to is required'));
        }
        if (!isset($_REQUEST['ttl']) || !$_REQUEST['ttl']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'TTL is required'));
        }

        $options = array(
            'server_id' => $_REQUEST['server_id'],
            'zone' => $_REQUEST['zone'],
            'name' => ($_REQUEST['host'] ? $_REQUEST['host'].'.' : '').$_REQUEST['zone_name'],
            'type' => $_REQUEST['type'],
            'data' => $_REQUEST['destination'],
            'ttl' => $_REQUEST['ttl'],
            'active' => 'y',
            'ihost_dns_function' => 'dns_'.$_REQUEST['type'].'_add',
        );

        $create = cwispy_soap_request($params, 'dns_record_add', $options);
        if ($create['status'] == 'success') {
            cwispy_return_ajax_response(array('status' => 'success', 'message' => 'DNS record created successfully'));
        }
        else {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => $create['response']));
        }
    }
    elseif ($_GET['view_action'] == 'edit') {
        if (!isset($_REQUEST['zone']) || !$_REQUEST['zone']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Zone is required'));
        }
        if (!isset($_REQUEST['type']) || !$_REQUEST['type']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Record type is required'));
        }
        if (!isset($_REQUEST['destination']) || !$_REQUEST['destination']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Points to is required'));
        }
        if (!isset($_REQUEST['ttl']) || !$_REQUEST['ttl']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'TTL is required'));
        }
        $options = array(
            'id' => $_REQUEST['record_id'],
            'server_id' => $_REQUEST['server_id'],
            'zone' => $_REQUEST['zone'],
            'name' => ($_REQUEST['host'] ? $_REQUEST['host'].'.' : '').$_REQUEST['zone_name'],
            'type' => $_REQUEST['type'],
            'data' => $_REQUEST['destination'],
            'ttl' => $_REQUEST['ttl'],
            'active' => 'y',
            'ihost_dns_function' => 'dns_'.$_REQUEST['type'].'_update',
        );

        $update = cwispy_soap_request($params, 'dns_record_update', $options);
        if ($update['status'] == 'success') {
            cwispy_return_ajax_response(array('status' => 'success', 'message' => 'DNS record updated successfully'));
        }
        else {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => $update['response']));
        }
    }
    elseif ($_GET['view_action'] == 'delete') {
        $options = array(
            'id' => $_REQUEST['record_id'],
        );

        $delete = cwispy_soap_request($params, 'dns_a_delete', $options);
        if ($delete['status'] == 'success') {
            cwispy_return_ajax_response(array('status' => 'success', 'message' => 'DNS record deleted successfully'));
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
    $records = cwispy_soap_request($params, 'dns_a_get');
    $client  = cwispy_soap_request($params, 'client_get');
    $zones = cwispy_soap_request($params, 'dns_zone_get');

    $return = array_merge_recursive($records, $zones, $client);

    if ($zones) {
        foreach($zones['response']['zones'] as $zone) {
            $return['response']['zones_processed'][$zone['id']] = $zone;
        }
    }

    if (is_array($return['status'])) {
        $return['status'] = (in_array('error', $return['status'])) ? 'error' : 'success';
    }

    $return['response']['types'] = array('a','aaaa','alias','cname','hinfo','mx','ns','ptr','rp','srv','txt');

    $return['action_urls']['add'] = cwispy_create_url(array('view' => 'dns', 'view_action' => 'add'));
    $return['action_urls']['edit'] = cwispy_create_url(array('view' => 'dns', 'view_action' => 'edit'));
    $return['action_urls']['delete'] = cwispy_create_url(array('view' => 'dns', 'view_action' => 'delete'));
}