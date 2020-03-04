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
        if (!isset($_REQUEST['domain']) || !$_REQUEST['domain']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Domain is required'));
        }

        $cgi = isset($_REQUEST['cgi']) ? $_REQUEST['cgi'] : 'n';
        $ssi = isset($_REQUEST['ssi']) ? $_REQUEST['ssi'] : 'n';
        $suexec = isset($_REQUEST['suexec']) ? $_REQUEST['suexec'] : 'n';
        $errordocs = isset($_REQUEST['errordocs']) ? $_REQUEST['errordocs'] : '0';
        $ruby = isset($_REQUEST['ruby']) ? $_REQUEST['ruby'] : 'n';
        $python = isset($_REQUEST['python']) ? $_REQUEST['python'] : 'n';
        $perl = isset($_REQUEST['perl']) ? $_REQUEST['perl'] : 'n';

        $options = array(
            'server_id' => $_REQUEST['server_id'],
            'ip_address' => $_REQUEST['ip_address'],
            'ipv6_address' => $_REQUEST['ipv6_address'],
            'domain' => $_REQUEST['domain'],
            'parent_domain_id' => 0,
            'type' => 'vhost',
            'hd_quota' => $_REQUEST['hd_quota'],
            'traffic_quota' => $_REQUEST['traffic_quota'],
            'cgi' => $cgi,
            'ssi' => $ssi,
            'suexec' => $suexec,
            'errordocs' => $errordocs,
            'is_subdomainwww' => 1,
            'subdomain' => $_REQUEST['subdomain'],
            'php' => $_REQUEST['php'],
            'ruby' => $ruby,
            'python' => $python,
            'perl' => $perl,
            'redirect_type' => '',
            'redirect_path' => '',
            'http_port' => 80,
            'https_port' => 443,
            'pm_max_requests' => 0,
            'pm_process_idle_timeout' => 10,
            'allow_override' => 'All',
            'active' => 'y',
            'traffic_quota_lock' => 'n'
        );
        $create = cwispy_api_request( $params, 'sites_web_domain_add', $options );
        if ($create['response']['code'] == 'ok') {
            cwispy_return_ajax_response(array('status' => 'success', 'message' => 'Website created successfully'));
        }
        else {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => $create['response']['message']));
        }
    }
    elseif ($_GET['view_action'] == 'edit') {
        if (!isset($_REQUEST['domain']) || !$_REQUEST['domain']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Website is required'));
        }
        if ( ( ( $_REQUEST['old_hd_quota'] < $_REQUEST['hd_quota'] ) && ( $_REQUEST['client_hd_quota'] != "-1" ) ) || 
             ( ( $_REQUEST['old_traffic_quota'] < $_REQUEST['traffic_quota'] ) && ( $_REQUEST['old_traffic_quota'] != "-1" ) ) ) {
            //$website = cwispy_api_request($params, 'sites_web_domain_get');
            //print_r($_REQUEST);
            
            $availablehdquota = ( ($_REQUEST['client_hd_quota'] - $_REQUEST['client_hd_used']) + $_REQUEST['old_hd_quota'] );
            //print_r($_REQUEST['hd_quota']." ".$availablehdquota);
            if ( ( $_REQUEST['hd_quota']) > $availablehdquota ) { 
                cwispy_return_ajax_response(array("status" => "error", "message" => "Disk Quota Error. Maximum available $availablehdquota MB"));
            }
            
            $availablewebquota = ( ($_REQUEST['client_traffic_quota'] - $_REQUEST['client_traffic_used']) + $_REQUEST['old_traffic_quota'] );
            if ( ( $_REQUEST['traffic_quota']) > $availablewebquota ) { 
                cwispy_return_ajax_response(array("status" => "error", "message" => "Web Quota Error. Maximum available $availablewebquota MB"));
            }
        }

        
        $cgi = isset($_REQUEST['cgi']) ? $_REQUEST['cgi'] : 'n';
        $ssi = isset($_REQUEST['ssi']) ? $_REQUEST['ssi'] : 'n';
        $suexec = isset($_REQUEST['suexec']) ? $_REQUEST['suexec'] : 'n';
        $errordocs = isset($_REQUEST['errordocs']) ? $_REQUEST['errordocs'] : '0';
        $ruby = isset($_REQUEST['ruby']) ? $_REQUEST['ruby'] : 'n';
        $python = isset($_REQUEST['python']) ? $_REQUEST['python'] : 'n';
        $perl = isset($_REQUEST['perl']) ? $_REQUEST['perl'] : 'n';

        $options = array(
            'domain_id' => $_REQUEST['domain_id'],
            'server_id' => $_REQUEST['server_id'],
            'ip_address' => $_REQUEST['ip_address'],
            'ipv6_address' => $_REQUEST['ipv6_address'],
            'domain' => $_REQUEST['domain'],
            'parent_domain_id' => 0,
            'type' => 'vhost',
            'hd_quota' => $_REQUEST['hd_quota'],
            'traffic_quota' => $_REQUEST['traffic_quota'],
            'cgi' => $cgi,
            'ssi' => $ssi,
            'suexec' => $suexec,
            'errordocs' => $errordocs,
            'is_subdomainwww' => 1,
            'subdomain' => $_REQUEST['subdomain'],
            'php' => $_REQUEST['php'],
            'ruby' => $ruby,
            'python' => $python,
            'perl' => $perl,
            'redirect_type' => '',
            'redirect_path' => '',
            'http_port' => 80,
            'https_port' => 443,
            'pm_max_requests' => 0,
            'pm_process_idle_timeout' => 10,
            'allow_override' => 'All',
            'active' => 'y',
            'traffic_quota_lock' => 'n'
        );

        $update = cwispy_api_request($params, 'sites_web_domain_update', $options);
        if ($update['response']['code'] == 'ok') {
            cwispy_return_ajax_response(array('status' => 'success', 'message' => 'Website updated successfully'));
        }
        else {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => $update['response']['message']));
        }
    }
    elseif ($_GET['view_action'] == 'delete') {
        $options = array(
            'domain_id' => $_REQUEST['domain_id']
        );

        $delete = cwispy_api_request($params, 'sites_web_domain_delete', $options);
        if ($delete['response']['code'] == 'ok') {
            cwispy_return_ajax_response(array('status' => 'success', 'message' => 'Website deleted successfully'));
        }
        else {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => $delete['response']['message']));
        }
    }
    else {
        cwispy_return_ajax_response(array('status' => 'error', 'message' => 'View action missing'));
    }
}
else {

    $websites = cwispy_api_request($params, 'sites_web_domain_get');
    logModuleCall('ispconfig','webapiget',$websites, $websites ,'','');

    $client  = cwispy_api_request($params, 'client_get');
    logModuleCall('ispconfig','clientapiget',$client, $client ,'','');

    $return   = array_merge_recursive($client, $websites);
    
    if ($websites) {
        foreach($websites['response']['websites'] as $websites) {
            $return['response']['websites_processed'][$websites['domain_id']] = $websites;
        }
    }
    
    if (is_array($return['status'])) {
        $return['status'] = (in_array('error', $return['status'])) ? 'error' : 'success';
    }
    
    $return['action_urls']['add'] = cwispy_create_url(array('view' => 'websites', 'view_action' => 'add'));
    $return['action_urls']['edit'] = cwispy_create_url(array('view' => 'websites', 'view_action' => 'edit'));
    $return['action_urls']['delete'] = cwispy_create_url(array('view' => 'websites', 'view_action' => 'delete'));
}