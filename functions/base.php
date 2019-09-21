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
require_once(__DIR__.'/../classes/ispcfg3_class.php');
use ISPCFG3\ispcfg3;

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

function cwispy_return_ajax_response($result) {
    if (isset($result['response']) && !isset($result['message'])) {
        $result['message'] = $result['response'];
    }
    $out = json_encode($result);
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

function cwispy_api_request($params, $function, $options=array()) {
    $username = $params['username'];
    $password = $params['password'];
    $domain = $params['domain'];

    if ( !empty( $params['serverhttpprefix'] ) && !empty( $params['serverhostname'] ) ) {
        
        $rest_uri = $params['serverhttpprefix']. '://' . $params['serverhostname'];
        if ( $params['serverport'] != '80' || $params['serverport'] != '443' ) {
            $rest_uri .= ':'.$params['serverport'];
        }

        $rest_url = $rest_uri.'/remote/json.php';
    }
    
    if (!$username || !$password) {
        return array('status' => 'error', 'response' => 'Username or password not set');
    }

    try {
        $result = NULL;
        $client = new ispcfg3( $rest_uri, $rest_url );

        $creds = array(
            'username' => $params['serverusername'],
            'password' => $params['serverpassword'],
        );

        $result = $client->login( $creds );
        $user = $client->client_get_by_username( $username );
        $clientall = $client->client_get( $user['response']['client_id'] );
        logModuleCall('ispconfig','client_get_by_username',$user, $clientall ,'','');
        $client_recordid = $client->client_get_id( $user['response']['userid'] );
        
        if ($function == 'mail_domain_get') {
            $_options = isset($options['id']) ? $options['id'] : [ 'primary_id' => [ 'sys_userid' => $user['response']['userid'] ] ];
            $domains = $client->mail_domain_get( $_options );
            logModuleCall('ispconfig','maildomainget',$domains, $domains ,'','');
            $result = makearray( $domains['response'], 'domains');
        }
        
        if ($function == 'mail_domain_get_by_domain') {
            $_options = isset($options['id']) ? $options['id'] : [ 'domain' => $params['domain'] ];
            $domains = $client->mail_domain_get_by_domain( $_options );
            logModuleCall('ispconfig','maildomainget',$domains, $domains ,'','');
            $result = makearray( $domains['response'], 'domains');
        }
        
        $result['quota'] = array();
        if ($function == 'mail_user_get') {
            $_options = isset($options['id']) ? $options['id'] : [ 'primary_id' => [ 'sys_groupid' => $user['response']['default_group'] ] ];
            $mailboxes = $client->mail_user_get( $_options );
            $mbx = makearray( $mailboxes['response'], 'mailboxes' );
            $quota = $client->mailquota_get_by_user( $user['response']['client_id'] );
            $qta = makearray( $quota['response'], 'quota' );
            $result = array_merge_recursive( $mbx, $qta );
        }
        
        if ($function == 'client_get') {
            $clnt = $client->client_get( $user['response']['client_id'] );
            $clntresult = makearray( $clnt['response'], 'client' );
            logModuleCall('ispconfig','clientget',$myresult, $clnt ,'','');
            $ipv4a = $client->server_ip_get( ['primary_id' =>
                        ['server_id' => $clnt['response']['default_webserver'],
                        'ip_type' => 'IPv4',
                        'virtualhost' => 'y',
                        'client_id' => 0 ] ] );
            logModuleCall('ispconfig','ip4all',$ipv4a, $ipv4a['response'] ,'','');
            $ipv4c = $client->server_ip_get( ['primary_id' =>
                        [ 'server_id' => $clnt['response']['default_webserver'],
                        'ip_type' => 'IPv4',
                        'virtualhost' => 'y',
                        'client_id' => $user['response']['client_id'] ] ] );
            logModuleCall('ispconfig','ip4client',$ipv4c, $ipv4c['response'] ,'','');
            $ipv6a = $client->server_ip_get( ['primary_id' =>
                        [ 'server_id' => $clnt['response']['default_webserver'],
                        'ip_type' => 'IPv6',
                        'virtualhost' => 'y' ,
                        'client_id' => 0 ] ] );
            logModuleCall('ispconfig','ip6all',$ipv6a, $ipv6a ,'','');
            $ipv6c = $client->server_ip_get( ['primary_id' =>
                        [ 'server_id' => $clnt['response']['default_webserver'],
                        'ip_type' => 'IPv6',
                        'virtualhost' => 'y',
                        'client_id' => $user['response']['client_id'] ] ] );
            logModuleCall('ispconfig','ip6client',$ipv6c, $ipv6c ,'','');
            // Merge the arrays
            $ipv4 = array_merge_recursive($ipv4a['response'], $ipv4c['response']);
            $ip4result = makearray($ipv4, 'ipv4');
            logModuleCall('ispconfig','ip4array',$myresult, $ipv4 ,'','');
            $ipv6 = array_merge_recursive($ipv6a['response'], $ipv6c['response']);
            $ip6result = makearray($ipv6, 'ipv6');
            logModuleCall('ispconfig','ip6array',$myresult, $ipv6 ,'','');
            
            $result = array_merge_recursive( $clntresult, $ip4result, $ip6result );
            logModuleCall('ispconfig','endclientget',$result, $result ,'','');

        }
        if ($function == 'quota_get_by_user') {
            $result['disk'] = $client->quota_get_by_user( $user['response']['client_id'] );
        }
        if ($function == 'trafficquota_get_by_user') {
            $result['traffic'] = $client->trafficquota_get_by_user( $user['response']['client_id'] );
        }
        if ($function == 'ftptrafficquota_data') {
            $result['ftptraffic'] = $client->ftptrafficquota_data( $user['response']['client_id'] );
        }
        if ($function == 'databasequota_get_by_user') {
            $result['databasedisk'] = $client->databasequota_get_by_user( $user['response']['client_id'] );
        }
        if ($function == 'mailquota_get_by_user') {
            $result['maildisk'] = $client->mailquota_get_by_user( $user['response']['client_id'] );
        }
	if ($function == 'client_get_by_username') {
            $res = $client->client_get_by_username( $params['username'] );
            $result = $res['response'];
        }
        if ($function == 'client_template_get_all') {
            $result = $client->client_get_by_username( $params['username'] );
        }
        if ($function == 'mail_user_add') {
            $result = $client->mail_user_add( $user['response']['client_id'], $options );
        }
        if ($function == 'mail_user_update') {
            $result = $client->mail_user_update( $user['response']['client_id'], $options['id'], $options );
        }
        if ($function == 'mail_user_delete') {
            $result = $client->mail_user_delete( $options['id'] );
        }

        if ($function == 'mail_forward_get') {
            $_options = isset($options['id']) ? $options['id'] : [ 'primary_id' => [ 'sys_userid' => $user['response']['userid'] ] ];
            $forwarders = $client->mail_forward_get( $_options );
            $result = makearray( $forwarders['response'], 'forwarders' );
        }
        if ($function == 'mail_forward_add') {
            $result = $client->mail_forward_add( $user['response']['client_id'], $options );
        }
        if ($function == 'mail_forward_update') {
            $result = $client->mail_forward_update( $user['response']['client_id'], $options['id'], $options );
        }
        if ($function == 'mail_forward_delete') {
            $result = $client->mail_forward_delete( $options['id'] );
        }

        
        if ($function == 'sites_ftp_user_get') {
            $_options = isset($options['id']) ? $options['id'] : $user['response']['username']."%";
            $accounts = $client->sites_ftp_user_get( $_options );
            $result = makearray($accounts['response'], 'accounts');
            $result['user'] = $user['response'];
        }
        if ($function == 'sites_ftp_user_add') {
            $result = $client->sites_ftp_user_add( $user['response']['client_id'], $options );
        }
        if ($function == 'sites_ftp_user_update') {
            $result = $client->sites_ftp_user_update( $user['response']['client_id'], $options['id'], $options );
        }
        if ($function == 'sites_ftp_user_delete') {
            $result = $client->sites_ftp_user_delete( $options['id'] );
        }

        
        if ($function == 'sites_database_get') {
			$userdbid = $client->client_get_by_username( $username );
            $client_recordid = $client->client_get_id( $user['response']['userid'] );
            $_options = isset($options['id']) ? $options['id'] : [ 'primary_id' => [ 'sys_userid' => $user['response']['userid'] ] ];
            $dbs = $client->sites_database_get( $_options );
            $result = makearray( $dbs['response'], 'dbs' );
        }
        if ($function == 'sites_database_add') {
            $result = $client->sites_database_add( $user['response']['client_id'], $options );
        }
        if ($function == 'sites_database_update') {
            $result = $client->sites_database_update( $user['response']['client_id'], $options['id'], $options );
        }
        if ($function == 'sites_database_delete') {
            $result = $client->sites_database_delete( $options['id'] );
        }

        
        if ($function == 'sites_database_user_get') {
            $_options = isset($options['id']) ? $options['id'] : [ 'primary_id' => [ 'sys_userid' => $user['response']['userid'] ] ];
            $db_users = $client->sites_database_user_get( $_options );
            $result = makearray( $db_users['response'], 'db_users'); 
        }
        if ($function == 'sites_database_user_add') {
            $result = $client->sites_database_user_add( $user['response']['client_id'], $options );
        }
        if ($function == 'sites_database_user_update') {
            $result = $client->sites_database_user_update( $user['response']['client_id'], $options['id'], $options );
        }
        if ($function == 'sites_database_user_delete') {
            $result = $client->sites_database_user_delete( $options['id'] );
        }

        
        if ($function == 'sites_web_domain_get') {
            $_options = isset($options['id']) ? $options['id'] : ['primary_id' => [ 'sys_userid' => $user['response']['userid'], 'type' => 'vhost' ] ];
            $web = $client->sites_web_domain_get( $_options );
            $result = makearray( $web['response'], 'domains');
        }
        if ($function == 'sites_web_domain_add') {
            $_options = isset($options['id']) ? $options['id'] : ['primary_id' => [ 'sys_userid' => $user['response']['userid'], 'type' => 'vhost' ] ];
            $result['websites'] = $client->sites_web_domain_add( $user['response']['client_id'], $options );
        }
        if ($function == 'sites_web_domain_update') {
            $_options = isset($options['domain_id']) ? $options['domain_id'] : ['primary_id' => [ 'sys_userid' => $user['response']['userid'], 'type' => 'vhost' ] ];
            $result['websites'] = $client->sites_web_domain_update( $user['response']['client_id'], $_options, $options );
        }
        if ($function == 'sites_web_domain_delete') {
            $result['websites'] = $client->sites_web_domain_delete( $options['domain_id'] );
        }
        
        
        if ($function == 'sites_web_aliasdomain_get') {
            $_options = isset($options['id']) ? $options['id'] : [ 'primary_id' => [ 'sys_userid' => $user['response']['userid'], 'type' => 'alias' ] ];
            $aliasdomains = $client->sites_web_aliasdomain_get( $_options );
            $result = makearray( $aliasdomains['response'], 'aliasdomains' );
        }
        if ($function == 'sites_web_aliasdomain_add') {
            $result = $client->sites_web_aliasdomain_add( $user['response']['client_id'], $options );
            //create_ftp_dir($params, $options, $user);
            //create_dns_a_record($params, $options, $user);
        }
        if ($function == 'sites_web_aliasdomain_update') {
            $result = $client->sites_web_aliasdomain_update( $user['response']['client_id'], $options['domain_id'], $options );
        }
        if ($function == 'sites_web_aliasdomain_delete') {
            $result = $client->sites_web_aliasdomain_delete( $options['id'] );
        }

        
        if ($function == 'sites_web_subdomain_get') {
            $_options = isset($options['id']) ? $options['id'] : [ 'primary_id' => [ 'sys_userid' => $user['response']['userid'], 'type' => 'subdomain' ] ];
            $subdomains = $client->sites_web_subdomain_get( $_options );
            $result = makearray( $subdomains['response'], 'subdomains' );
        }
        if ($function == 'sites_web_subdomain_add') {
            $result = $client->sites_web_subdomain_add( $user['response']['client_id'], $options );
            //create_ftp_dir($params, $options, $user);
            //create_dns_a_record($params, $options, $user);
        }
        if ($function == 'sites_web_subdomain_update') {
            $result = $client->sites_web_subdomain_update( $user['response']['client_id'], $options['id'], $options );
        }
        if ($function == 'sites_web_subdomain_delete') {
            $result = $client->sites_web_subdomain_delete( $options['id'] );
        }

        if ($function == 'dns_zone_get') {
            $_options = isset($options['id']) ? $options['id'] : [ 'primary_id' => [ 'sys_userid' => $user['response']['userid'] ] ];
            $zones = $client->dns_zone_get($session_id, $_options);
            $result = makearray( $zones['response'], 'zones' );
        }

        if ($function == 'dns_a_get') {
            $_options = isset($options['id']) ? $options['id'] : [ 'primary_id' => [ 'sys_userid' => $user['response']['userid'] ] ];
            $records = $client->dns_a_get( $_options );
            $result = makearray( $records['response'], 'records' );
        }
        if ($function == 'dns_a_add') {
            $result = $client->dns_a_add( $user['response']['client_id'], $options );
        }
        if ($function == 'dns_a_delete') {
            $result = $client->dns_a_delete( $options['id'] );
        }

        if ($function == 'dns_record_add') {
            $actual_function = $options['ihost_dns_function'];
            $result = $client->{$actual_function}( $user['response']['client_id'], $options );
        }
        if ($function == 'dns_record_update') {
            $actual_function = $options['ihost_dns_function'];
            $result = $client->{$actual_function}( $user['response']['client_id'], $options['id'], $options );
        }

        if ($function == 'sites_cron_get') {
            $_options = isset($options['id']) ? $options['id'] : [ 'sys_userid' => $user['response']['userid'] ];
            $res = $client->sites_cron_get( $_options );
            $crons = makearray( $res['response'], 'crons' );
            $res = $client->server_get( $clientall['response']['web_servers'] );
            $servers = makearray( $res['response'], 'servers' );
            $result = array_merge_recursive( $crons, $servers );
        }
        if ($function == 'sites_cron_add') {
            $result = $client->sites_cron_add( $user['response']['client_id'], $options );
        }
        if ($function == 'sites_cron_update') {
            $result = $client->sites_cron_update( $user['response']['client_id'], $options['id'], $options );
        }
        if ($function == 'sites_cron_delete') {
            $result = $client->sites_cron_delete( $options['id'] );
        }

        $client->logout($session_id);
        return array('status' => 'success', 'response' => $result);
    }
    catch (SoapFault $e) {
        $error = $e->getMessage();
        return array('status' => 'error', 'response' => $error);
    }
}

function create_dns_a_record($params, $options, $user) {
    $zones = cwispy_api_request($params, 'dns_zone_get');
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
            $create = cwispy_api_request($params, 'dns_a_add', $options);
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
    $ftp_r = cwispy_api_request($params, 'sites_ftp_user_get');
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
            cwispy_api_request($params, 'sites_ftp_user_update', $options);
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

function makearray( array $inarray, $outkey = null ) {
    $a =0;
    if ($outkey) {
        foreach ($inarray as $key => $value) {
            $arr[$outkey][$key] = $value;
        }
    } else {
        foreach ($inarray as $key => $value) {
            $arr[$key] = $value;
        }
    }
    return $arr;
}