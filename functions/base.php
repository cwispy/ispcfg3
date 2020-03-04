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
define('ELFINDER_DIR', dirname(dirname(__FILE__)).'/assets/elfinder/');

function cwispy_handle_view($view, $params) {
    global $server_url;
    global $domain_url;
    $viewFile = dirname(dirname(__FILE__)).'/views/'.$view.'.php';
    $return = array();
    if (file_exists($viewFile)) {
        include($viewFile);
    }
    else {
        $return = array('status' => 'error', 'response' => 'View file not found');
    }
    if (!$return) {
        $return = array('status' => 'error', 'response' => 'Empty response');
    }

    if (isset($_GET['view_action']) && (!isset($return['ajax']) || $return['ajax'] == false)) {
        cwispy_return_ajax_response($return);
    }
    return $return;
}

function cwispy_create_url($params=array()) {
    $request = $_GET;
    if (isset($params['view']) && isset($request['view'])) {
        unset($request['view']);
    }
    else {
        $request['action'] = $_GET['action'];
        $request['id'] = $_GET['id'];
    }
    $request = array_merge($request, $params);
    return 'clientarea.php?'.http_build_query($request);
}

function cwispy_return_ajax_response($response) {
    if (isset($response['response']) && !isset($response['message'])) {
        $response['message'] = $response['response'];
    }
    $out = json_encode($response);
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Content-type: application/json');
    echo $out;
    exit;
}

function cwispy_get_menu_item_class($currentRequest=array(), $params=array()) {
    $int = array_intersect($currentRequest, $params);
    if ($int) {
        return 'active';
    }
}

function cwispy_soap_request($params, $function, $options=array()) {
    $username = $params['username'];
    $password = $params['password'];
    $domain = $params['domain'];

    if ( $params['serversecure'] == 'on' ) {
        $soap_url = 'https://' . $params['serverhostname'].':'.$params['serverport'] . '/remote/index.php';
        $soap_uri = 'https://' . $params['serverhostname'].':'.$params['serverport'] . '/remote/';
    }
    else {
        $soap_url = 'http://' . $params['serverhostname'].':'.$params['serverport'] . '/remote/index.php';
        $soap_uri = 'http://' . $params['serverhostname'].':'.$params['serverport'] . '/remote/';
    }
    
    if (!$username || !$password) {
        return array('status' => 'error', 'response' => 'Username or password not set');
    }

    try {
        $response = NULL;
        $stream_context = stream_context_create(
            array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
            )
        ));
        $soap_options = array(
            'location' => $soap_url, 
            'uri' => $soap_uri, 
            'exceptions' => 1, 
            'trace' => false ,
            'stream_context' => $stream_context
        );
        $client = new SoapClient( null, $soap_options);
        $session_id = $client->login( $params['serverusername'], $params['serverpassword'] );
        $user = $client->client_get_by_username($session_id, $username);
        $client_recordid = $client->client_get_id($session_id, $user['userid']);
        
        if ($function == 'mail_domain_get') {
            $_options = isset($options['id']) ? $options['id'] : array('sys_userid' => $user['userid']);
            $response['domains'] = $client->mail_domain_get($session_id, $_options);
        }
        
        $response['quota'] = array();
        if ($function == 'mail_user_get') {
            $_options = isset($options['id']) ? $options['id'] : array('sys_groupid' => $user['default_group']);
            $response['mailboxes'] = $client->mail_user_get($session_id, $_options);
            $response['quota'] = $client->mailquota_get_by_user($session_id, $user['client_id'] );
        }
        
        if ($function == 'client_get') {
            $response['client'] = $client->client_get($session_id, $user['client_id']);
            $response['ipv4']['all'] = $client->server_ip_get($session_id, 
                    array( 'server_id' => $response['client']['default_webserver'], 
                        'ip_type' => 'IPv4', 
                        'virtualhost' => 'y', 
                        'client_id' => 0 ) );
            $response['ipv4']['client'] = $client->server_ip_get($session_id, 
                    array( 'server_id' => $response['client']['default_webserver'], 
                        'ip_type' => 'IPv4', 
                        'virtualhost' => 'y', 
                        'client_id' => $user['client_id'] ) );
            $response['ipv6']['all'] = $client->server_ip_get($session_id, 
                    array( 'server_id' => $response['client']['default_webserver'], 
                        'ip_type' => 'IPv6', 
                        'virtualhost' => 'y' , 
                        'client_id' => 0 ) );
            $response['ipv6']['client'] = $client->server_ip_get($session_id, 
                    array( 'server_id' => $response['client']['default_webserver'], 
                        'ip_type' => 'IPv6', 
                        'virtualhost' => 'y', 
                        'client_id' => $user['client_id'] ) );
            // Merge the arrays
            $response['ipv4'] = array_merge_recursive($response['ipv4']['all'], $response['ipv4']['client']);
            $response['ipv6'] = array_merge_recursive($response['ipv6']['all'], $response['ipv6']['client']);
        }
        if ($function == 'quota_get_by_user') {
            $response['disk'] = $client->quota_get_by_user( $session_id, $user['client_id'] );
        }
        if ($function == 'trafficquota_get_by_user') {
            $response['traffic'] = $client->trafficquota_get_by_user( $session_id, $user['client_id'] );
        }
        if ($function == 'ftptrafficquota_data') {
            $response['ftptraffic'] = $client->ftptrafficquota_data( $session_id, $user['client_id'] );
        }
        if ($function == 'databasequota_get_by_user') {
            $response['databasedisk'] = $client->databasequota_get_by_user( $session_id, $user['client_id'] );
        }
        if ($function == 'mailquota_get_by_user') {
            $response['maildisk'] = $client->mailquota_get_by_user( $session_id, $user['client_id'] );
        }
	if ($function == 'client_get_by_username') {
            $response = $client->client_get_by_username($session_id, $params['username']);
        }
	if ($function == 'client_template_get_all') {
            $response = $client->client_get_by_username($session_id, $params['username']);
        }
        if ($function == 'mail_user_add') {
            $response = $client->mail_user_add($session_id, $user['client_id'], $options);
        }
        if ($function == 'mail_user_update') {
            $response = $client->mail_user_update($session_id, $user['client_id'], $options['id'], $options);
        }
        if ($function == 'mail_user_delete') {
            $response = $client->mail_user_delete($session_id, $options['id']);
        }

        if ($function == 'mail_forward_get') {
            $_options = isset($options['id']) ? $options['id'] : array('sys_userid' => $user['userid']);
            $response['forwarders'] = $client->mail_forward_get($session_id, $_options);
        }
        if ($function == 'mail_forward_add') {
            $response = $client->mail_forward_add($session_id, $user['client_id'], $options);
        }
        if ($function == 'mail_forward_update') {
            $response = $client->mail_forward_update($session_id, $user['client_id'], $options['id'], $options);
        }
        if ($function == 'mail_forward_delete') {
            $response = $client->mail_forward_delete($session_id, $options['id']);
        }

        
        if ($function == 'sites_ftp_user_get') {
            $_options = isset($options['id']) ? $options['id'] : array('sys_userid' => $user['userid']);
            $response['accounts'] = $client->sites_ftp_user_get($session_id, $_options);
            $response['user'] = $user;
        }
        if ($function == 'sites_ftp_user_add') {
            $response = $client->sites_ftp_user_add($session_id, $user['client_id'], $options);
        }
        if ($function == 'sites_ftp_user_update') {
            $response = $client->sites_ftp_user_update($session_id, $user['client_id'], $options['id'], $options);
        }
        if ($function == 'sites_ftp_user_delete') {
            $response = $client->sites_ftp_user_delete($session_id, $options['id']);
        }

        
        if ($function == 'sites_database_get') {
			$userdbid = $client->client_get_by_username($session_id, $username);
            $client_recordid = $client->client_get_id($session_id, $userdbid['userid']);
            $_options = isset($options['id']) ? $options['id'] : array('sys_userid' => $user['userid']);
            $response['dbs'] = $client->sites_database_get($session_id, $_options);
        }
        if ($function == 'sites_database_add') {
            $response = $client->sites_database_add($session_id, $user['client_id'], $options);
        }
        if ($function == 'sites_database_update') {
            $response = $client->sites_database_update($session_id, $user['client_id'], $options['id'], $options);
        }
        if ($function == 'sites_database_delete') {
            $response = $client->sites_database_delete($session_id, $options['id']);
        }

        
        if ($function == 'sites_database_user_get') {
            $_options = isset($options['id']) ? $options['id'] : array('sys_userid' => $user['userid']);
            $response['db_users'] = $client->sites_database_user_get($session_id, $_options);
        }
        if ($function == 'sites_database_user_add') {
            $response = $client->sites_database_user_add($session_id, $user['client_id'], $options);
        }
        if ($function == 'sites_database_user_update') {
            $response = $client->sites_database_user_update($session_id, $user['client_id'], $options['id'], $options);
        }
        if ($function == 'sites_database_user_delete') {
            $response = $client->sites_database_user_delete($session_id, $options['id']);
        }

        
        if ($function == 'sites_web_domain_get') {
            $_options = isset($options['id']) ? $options['id'] : array('sys_userid' => $user['userid'], 'type' => 'vhost');
            $response['domains'] = $client->sites_web_domain_get($session_id, $_options);
        }
        if ($function == 'sites_web_domain_add') {
            $_options = isset($options['id']) ? $options['id'] : array('sys_userid' => $user['userid'], 'type' => 'vhost');
            $response['websites'] = $client->sites_web_domain_add($session_id, $user['client_id'], $options);
        }
        if ($function == 'sites_web_domain_update') {
            $_options = isset($options['domain_id']) ? $options['domain_id'] : array('sys_userid' => $user['userid'], 'type' => 'vhost');
            $response['websites'] = $client->sites_web_domain_update($session_id, $user['client_id'], $_options, $options);
        }
        if ($function == 'sites_web_domain_delete') {
            $response['websites'] = $client->sites_web_domain_delete($session_id, $options['domain_id']);
        }
        
        
        if ($function == 'sites_web_aliasdomain_get') {
            $_options = isset($options['id']) ? $options['id'] : array('sys_userid' => $user['userid'], 'type' => 'alias');
            $response['aliasdomains'] = $client->sites_web_aliasdomain_get($session_id, $_options);
        }
        if ($function == 'sites_web_aliasdomain_add') {
            $response = $client->sites_web_aliasdomain_add($session_id, $user['client_id'], $options);
            //create_ftp_dir($params, $options, $user);
            //create_dns_a_record($params, $options, $user);
        }
        if ($function == 'sites_web_aliasdomain_update') {
            $response = $client->sites_web_aliasdomain_update($session_id, $user['client_id'], $options['domain_id'], $options);
        }
        if ($function == 'sites_web_aliasdomain_delete') {
            $response = $client->sites_web_aliasdomain_delete($session_id, $options['id']);
        }

        
        if ($function == 'sites_web_subdomain_get') {
            $_options = isset($options['id']) ? $options['id'] : array('sys_userid' => $user['userid'], 'type' => 'subdomain');
            $response['subdomains'] = $client->sites_web_subdomain_get($session_id, $_options);
        }
        if ($function == 'sites_web_subdomain_add') {
            $response = $client->sites_web_subdomain_add($session_id, $user['client_id'], $options);
            //create_ftp_dir($params, $options, $user);
            //create_dns_a_record($params, $options, $user);
        }
        if ($function == 'sites_web_subdomain_update') {
            $response = $client->sites_web_subdomain_update($session_id, $user['client_id'], $options['id'], $options);
        }
        if ($function == 'sites_web_subdomain_delete') {
            $response = $client->sites_web_subdomain_delete($session_id, $options['id']);
        }

        if ($function == 'dns_zone_get') {
            $_options = isset($options['id']) ? $options['id'] : array('sys_userid' => $user['userid']);
            $response['zones'] = $client->dns_zone_get($session_id, $_options);
        }

        
        if ($function == 'dns_a_get') {
            $_options = isset($options['id']) ? $options['id'] : array('sys_userid' => $user['userid']);
            $response['records'] = $client->dns_a_get($session_id, $_options);
        }
        if ($function == 'dns_a_add') {
            $response = $client->dns_a_add($session_id, $user['client_id'], $options);
        }
        if ($function == 'dns_a_delete') {
            $response = $client->dns_a_delete($session_id, $options['id']);
        }

        
        if ($function == 'dns_record_add') {
            $actual_function = $options['ihost_dns_function'];
            $response = $client->{$actual_function}($session_id, $user['client_id'], $options);
        }
        if ($function == 'dns_record_update') {
            $actual_function = $options['ihost_dns_function'];
            $response = $client->{$actual_function}($session_id, $user['client_id'], $options['id'], $options);
        }

        if ($function == 'sites_cron_get') {
            $_options = isset($options['id']) ? $options['id'] : array('sys_userid' => $user['userid']);
            $response['crons'] = $client->sites_cron_get($session_id, $_options);
            $response['servers'] = $client->server_get_serverid_by_ip($session_id, $params['serverip']);
        }
        if ($function == 'sites_cron_add') {
            $response = $client->sites_cron_add($session_id, $user['client_id'], $options);
        }
        if ($function == 'sites_cron_update') {
            $response = $client->sites_cron_update($session_id, $user['client_id'], $options['id'], $options);
        }
        if ($function == 'sites_cron_delete') {
            $response = $client->sites_cron_delete($session_id, $options['id']);
        }

        $client->logout($session_id);
        return array('status' => 'success', 'response' => $response);
    }
    catch (SoapFault $e) {
        $error = $e->getMessage();
        return array('status' => 'error', 'response' => $error);
    }
}

function create_dns_a_record($params, $options, $user) {
    $zones = cwispy_soap_request($params, 'dns_zone_get');
    if (isset($zones['response']['zones']) && $zones['response']['zones']) {
        foreach($zones['response']['zones'] as $_zone) {
            if ($_zone['origin'] == $options['ihost_zone_domain']) {
                $zone = $_zone;
                break;
            }
        }
        if (isset($zone) && $zone) {
            $options = array(
                'server_id' => $zone['server_id'],
                'zone' => $zone['id'],
                'name' => $options['domain'].'.',
                'type' => 'a',
                'data' => $params['serverip'],
                'ttl' => '3600',
                'active' => 'y'
            );
            $create = cwispy_soap_request($params, 'dns_a_add', $options);
        }
    }
}

function create_ftp_dir($params, $options, $user) {

    $dircheck = str_replace('/','',$options['redirect_path']);
    if ($dircheck) {

        $ftp_user = $params['username'].'admin';
        $ftp_pwd = $params['password'];

        change_ftp_password($params, $ftp_user, $ftp_pwd);

        $ftp_host = str_replace(':8080', '', $params['configoption3']);
        $conn_id = ftp_connect($ftp_host);
        ftp_login($conn_id, $ftp_user, $ftp_pwd);
        ftp_mksubdirs($conn_id, '/', $options['redirect_path']);
        ftp_close($conn_id);
    }
}

function change_ftp_password($params, $username, $password) {
    $ftp_r = cwispy_soap_request($params, 'sites_ftp_user_get');
    if (isset($ftp_r['response']['accounts']) && $ftp_r['response']['accounts']) {
        foreach($ftp_r['response']['accounts'] as $account) {
            if ($account['username'] == $username) {
                $ftp = $account;
                break;
            }
        }
        if (isset($ftp) && $ftp) {
            $options = array(
                'server_id' => $ftp['server_id'],
                'username' => $username,
                'password' => $password,
                'quota_size' => $ftp['quota_size'],
                'dir' => $ftp['dir'],
                'uid' => $ftp['uid'],
                'gid' => $ftp['gid'],
                'parent_domain_id' => $ftp['parent_domain_id'],
                'active' => 'y',
                'id' => $ftp['ftp_user_id']
            );
            cwispy_soap_request($params, 'sites_ftp_user_update', $options);
        }
    }
}

function ftp_mksubdirs($ftpcon, $ftpbasedir, $ftpath){
    @ftp_chdir($ftpcon, $ftpbasedir.'/web');
    $parts = explode('/',$ftpath);
    $parts = array_values(array_filter($parts));
    foreach($parts as $i => $part){
        if ($i == 0 && $part == 'web') continue;
        if(!@ftp_chdir($ftpcon, $part)){
            ftp_mkdir($ftpcon, $part);
            ftp_chdir($ftpcon, $part);
        }
    }
}

function return_server( $list ) {
    
    if ( stripos( $list, ',' ) === FALSE ) {
    return $list;

    } else {
        $wtmp = explode(',', $list );
        $rand = rand( 0, ( count( $wtmp ) -1 ) );
        return $wtmp[$rand];
    }
}