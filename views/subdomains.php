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
    if ($_GET['view_action'] == 'add') {
        if (!isset($_REQUEST['subdomain']) || !$_REQUEST['subdomain']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Subdomain is required'));
        }
        if (!isset($_REQUEST['domain']) || !$_REQUEST['domain']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'You must select a parent domain'));
        }

        $document_root = '';
        if (isset($_REQUEST['directory']) && $_REQUEST['directory']) {
            $document_root .= '/'.$_REQUEST['directory'].'/';
            $document_root = str_replace("//","/",$document_root);
        }

        $options = array(
            'server_id' => $_REQUEST['server_id'],
            'domain' => $_REQUEST['subdomain'].'.'.$_REQUEST['domain'],
            'parent_domain_id' => $_REQUEST['domain_id'],
            'type' => 'subdomain',
            'redirect_type' => $_REQUEST['redirect_type'],
            'redirect_path' => $document_root,
            'active' => 'y',
            'ihost_zone_domain' => $_REQUEST['domain'].'.',
        );

        $create = cwispy_soap_request($params, 'sites_web_subdomain_add', $options);
        if ($create['status'] == 'success') {
            cwispy_return_ajax_response(array('status' => 'success', 'message' => 'Subdomain created successfully'));
        }
        else {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => $create['response']));
        }
    }
    elseif ($_GET['view_action'] == 'edit') {
        if (!isset($_REQUEST['subdomain']) || !$_REQUEST['subdomain']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Subdomain is required'));
        }
        if (!isset($_REQUEST['domain']) || !$_REQUEST['domain']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'You must select a parent domain'));
        }

        $document_root = '';
        if (isset($_REQUEST['directory']) && $_REQUEST['directory']) {
            $document_root = '/'.$_REQUEST['directory'].'/';
            $document_root = str_replace("//","/",$document_root);
        }

        $options = array(
            'id' => $_REQUEST['subdomain_id'],
            'server_id' => $_REQUEST['server_id'],
            'domain' => $_REQUEST['subdomain'].'.'.$_REQUEST['domain'],
            'parent_domain_id' => $_REQUEST['domain_id'],
            'type' => 'subdomain',
            'redirect_type' => $_REQUEST['redirect_type'],
            'redirect_path' => $document_root,
            'active' => 'y'
        );

        $update = cwispy_soap_request($params, 'sites_web_subdomain_update', $options);
        if ($update['status'] == 'success') {
            cwispy_return_ajax_response(array('status' => 'success', 'message' => 'Subdomain updated successfully'));
        }
        else {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => $update['response']));
        }
    }
    elseif ($_GET['view_action'] == 'delete') {
        $options = array(
            'id' => $_REQUEST['subdomain_id']
        );

        $delete = cwispy_soap_request($params, 'sites_web_subdomain_delete', $options);
        if ($delete['status'] == 'success') {
            cwispy_return_ajax_response(array('status' => 'success', 'message' => 'Subdomain deleted successfully'));
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
    $domains = cwispy_soap_request($params, 'sites_web_domain_get');
    $client  = cwispy_soap_request($params, 'client_get');
    $subdomains = cwispy_soap_request($params, 'sites_web_subdomain_get');

    $return = array_merge_recursive($domains, $subdomains, $client);

    if ($domains) {
        foreach($domains['response']['domains'] as $domain) {
            $return['response']['domains_processed'][$domain['domain_id']] = $domain;
        }
    }

    if (is_array($return['status'])) {
        $return['status'] = (in_array('error', $return['status'])) ? 'error' : 'success';
    }

    $return['action_urls']['add'] = cwispy_create_url(array('view' => 'subdomains', 'view_action' => 'add'));
    $return['action_urls']['edit'] = cwispy_create_url(array('view' => 'subdomains', 'view_action' => 'edit'));
    $return['action_urls']['delete'] = cwispy_create_url(array('view' => 'subdomains', 'view_action' => 'delete'));
}