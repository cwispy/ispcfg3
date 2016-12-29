<?php
/*
 * 
 *  ISPConfig v3.x module for WHMCS v5.x or Higher
 *  Copyright (C) 2014, 2015  Shane Chrisp
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

ini_set('error_reporting', E_ALL & ~E_NOTICE);
ini_set("display_errors", 0); // Set this option to Zero on a production machine.
openlog( "ispconfig3", LOG_PID | LOG_PERROR, LOG_LOCAL0 );
include_once(__DIR__.'/functions/base.php');

function ispcfg3_ConfigOptions() {
    $configarray = array(
        'ISPConfig Remote Username' => array(
                    'Type' => 'text',
                    'Size' => '16',
                    'Description' => '<br />Remote Username configured in ISPConfig.'
            ),
        'ISPConfig Remote Password' => array(
                    'Type' => 'password',
                    'Size' => '16',
                    'Description' => '<br />Remote Password configured in ISPConfig.'
            ),
        'ISPConfig URL' => array(
                    'Type' => 'text',
                    'Size' => '50',
                    'Description' => '<br />E.g. ispconfig.example.tld:8080'
            ),
        'ISPConfig SSL' => array(
                    'Type' => 'yesno',
                    'Description' => 'Tick if you enabled SSL on your ISPConfig'
                                    . ' Controlpanel Web Interface.'
            ),
        'ISPConfig Template ID' => array(
                    'Type' => 'text',
                    'Size' => '3',
                    'Description' => 'The ID of the Client Template in ISPConfig'
            ),
        'ISPConfig Usertheme' => array( 
                    'Type' => 'text',
                    'Size' => '20',
                    'Description' => '<br />The ISPConfig theme to use, typically '
                                    . 'this will be \'default\''
            ),
        'Global Client PHP Options' => array(
                    'Type' => 'text',
                    'Size' => '32',
                    'Description' => '<br />E.g. no,fast-cgi,cgi,mod,suphp,php-fpm'
            ),
        'Global Client Chroot Options' => array(
                    'Type' => 'dropdown',
                    'Options' => 'no,jailkit'
            ),
        'Website Creation' => array(
                    'Type' => 'yesno',
                    'Description' => 'Tick to create the website automatically' 
            ),
        'ISPConfig Domain Tool' => array(
                    'Type' => 'yesno',
                    'Description' => ''
            ),
        'ISPConfig Version' => array(
                    'Type' => 'dropdown',
					'Options' => '3.0,3.1',
					'Default' => '3.1',
                    'Description' => '<br />Select your Ispconfig Version'
             ),
        'Website Quota' => array(
                    'Type' => 'text',
                    'Size' => '6',
                    'Description' => 'MB'
            ),
        'Traffic Quota' => array(
                    'Type' => 'text',
                    'Size' => '6',
                    'Description' => 'MB'
            ),
        'Website Settings' => array(
                    'Type' => 'text',
                    'Size' => '20',
                    'Description' => '<br />Syntax: CGI,SSI,Ruby,SuEXEC,ErrorDocuments'
                                    . ',SSL,Letsencrypt <br />E.g.: y,y,y,n,y,y,y'
            ),
        'Auto Subdomain' => array(
                    'Type' => 'dropdown',
                    'Options' => 'none,www,*',
                    'Description' => 'Select to create subdomain during setup'
            ),
        'PHP Mode' => array(
                    'Type' => 'dropdown',
                    'Options' => 'no,fast-cgi,cgi,mod,suphp,php-fpm'
            ),
        'Active' => array(
                    'Type' => 'yesno',
                    'Description' => 'Enable the account once created?'
            ),
        'Create DNS' => array( 
                    'Type' => 'yesno',
                    'Description' => 'Setup DNS records? You must have a DNS '
                                    . 'template configured in ISPConfig'
            ),
        'DNS Settings' => array(
                    'Type' => 'text',
                    'Size' => '60',
                    'Description' => '<br />Syntax:ns1,ns2,Emailname,Templateid,Zone IP Address'
                                    . '<br />eg: ns1.domain.tld,ns2.domain.tld,'
                                    . 'webmaster,1,123.123.123.123'
            ),
        'ISPConfig Language' => array(
                    'Type' => 'dropdown',
                    'Options' => 'ar,bg,br,cz,de,el,en,es,fi,fr,hu,hr,id,it,ja,'
                                . 'nl,pl,pt,ro,ru,se,sk,tr',
                    'Default' => 'en'
            ),
        'Create Maildomain' => array( 
                    'Type' => 'yesno',
                    'Description' => 'Tick to create the Email Domain '
                                    . 'automatically during setup'
            ),
        'Create FTP-Account' => array(
                    'Type' => 'yesno',
                    'Description' => 'Tick to create the FTP Accounts '
                                    . 'automatically during setup'
            ),
		'Site Pro api username' => array(
                    'Type' => 'text',
                    'Size' => '16',
                    'Description' => ' <br />Site.pro website builder. Get one here <a href="http://site.pro/" title="https://site.pro"><strong>https://site.pro</strong></a> '
            ),
        'Site.Pro api Password' => array(
                    'Type' => 'password',
                    'Size' => '50',
                    'Description' => ' <br />Site.pro api password. '
            )
        );
    return $configarray;
}

function ispcfg3_CreateAccount( $params ) {

    $productid          = $params['pid'];
    $accountid          = $params['accountid'];
    $domain             = strtolower( $params['domain'] );
    $username           = $params['username'];
    $password           = $params['password'];
    $clientsdetails     = $params['clientsdetails'];
    $soapuser           = $params['configoption1'];
    $soappassword       = $params['configoption2'];
    $soapsvrurl         = $params['configoption3'];
    $soapsvrssl         = $params['configoption4'];
    $templateid         = $params['configoption5'];
    $designtheme        = $params['configoption6'];
    $globalphp          = $params['configoption7'];
    $chrootenable       = $params['configoption8'];
    $webcreation        = $params['configoption9'];
    $domaintool         = $params['configoption10'];
	$ispconfigver 		= $params['configoption11'];
	//$submodsettings        = explode( ',',$params['configoption11'] );
    //$webwriteprotect    = $params['configoption11'];
    $webquota           = $params['configoption12'];
    $webtraffic         = $params['configoption13'];
    $websettings        = explode( ',',$params['configoption14'] );
    $subdomain          = $params['configoption15'];
    $phpmode            = $params['configoption16'];
    $webactive          = $params['configoption17'];
    $dns                = $params['configoption18'];
    $dnssettings        = explode( ',', $params['configoption19'] );
    $defaultlanguage    = $params['configoption20'];
    $addmaildomain      = $params['configoption21'];
    $addftpuser         = $params['configoption22'];
	
	$siteprousername    = $params['configoption23'];
	$sitepropass        = $params['configoption24'];
	
	//$statpackage        = 'webalizer';
	
    
    $nameserver1        = $dnssettings[0];
    $nameserver2        = $dnssettings[1];
    $soaemail           = $dnssettings[2] . '.' . $domain;
    $dnstemplate        = $dnssettings[3];
    $zoneip             = $dnssettings[4];

    $websettings[0] == 'n'  ? $enablecgi = '' : $enablecgi = 'y';
    $websettings[1] == 'n'  ? $enablessi = '' : $enablessi = 'y';
    $websettings[2] == 'n'  ? $enableperl = '' : $enableperl = 'y';
    $websettings[3] == 'n'  ? $enableruby = '' : $enableruby = 'y';
    $websettings[4] == 'n'  ? $enablepython = '' : $enablepython = 'y';
    $websettings[5] == 'n'  ? $enablesuexec = '' : $enablesuexec = 'y';
    $websettings[6] == 'n'  ? $enableerrdocs = '' : $enableerrdocs = '1';
    $websettings[7] == 'n'  ? $wildcardsubdom = '' : $wildcardsubdom = '1';
    $websettings[8] == 'n'  ? $enablessl = '' : $enablessl = 'y';
	$websettings[9] == 'n'  ? $enablessletsencrypt = '' : $enablessletsencrypt = 'y';
    $webactive      == 'on' ? $webactive = 'y' : $webactive = 'n';

    logModuleCall('ispconfig','CreateClient',$params['clientsdetails'],$params,'','');
    
    if ( $soapsvrssl == 'on' ) {
        
        $soap_url = 'https://' . $soapsvrurl . '/remote/index.php';
        $soap_uri = 'https://' . $soapsvrurl . '/remote/';
        
    } else {
        
        $soap_url = 'http://' . $soapsvrurl . '/remote/index.php';
        $soap_uri = 'http://' . $soapsvrurl . '/remote/';
        
    }

    /* 
     * Make sure that a username and password have been set
     * or exit with error.
    */
    
    if (
            ((isset($username)) &&
            ($username != '')) &&
            ((isset($password)) &&
            ($password != ''))
            ) 
        {
    
    try {
        /* Connect to SOAP Server */
        $client = new SoapClient( null, 
                            array( 'location' => $soap_url,
                                    'uri' => $soap_uri,
                                    'exceptions' => 1,
                                    'stream_context'=> stream_context_create(
                                            array('ssl'=> array(
                                                'verify_peer'=>false,
                                                'verify_peer_name'=>false))
                                            ),
                                        'trace' => false
                                    )
                                );
        
        /* Authenticate with the SOAP Server */
        $session_id = $client->login( $soapuser, $soappassword );      

        $fullname = htmlspecialchars_decode( $clientsdetails['firstname'] );
        $fullname .= ' ' . htmlspecialchars_decode( $clientsdetails['lastname'] );
        
        $companyname = htmlspecialchars_decode( $clientsdetails['companyname'] );
        $address = $clientsdetails['address1'];
        if (!empty($clientsdetails['address2'])) {
            $address .= ',' . $clientsdetails['address2'];
        }
        $zip = $clientsdetails['postcode'];
        $city = $clientsdetails['city'];
        $state = $clientsdetails['state'];
        $mail = $clientsdetails['email'];
        $country = $clientsdetails['country'];
        $phonenumber = $clientsdetails['phonenumberformatted'];
        $customerno = $clientsdetails['userid'];
        
        /* Get the serverid's from WHMCS */
        $sql = 'SELECT serverid FROM tblservergroupsrel WHERE groupid  = '
                . '( SELECT servergroup FROM tblproducts '
                . 'WHERE id = "' . $productid . '")';
        $res = mysql_query( $sql );
        $servernames = array();

        /* Loop through the serverid's and retrieve the hostnames of the
         * servers from WHMCS
         */
        $i = 0;
        while ($groupservers = mysql_fetch_array( $res )) {
            $sql = 'SELECT hostname FROM tblservers '
                    . 'WHERE id  = "' . $groupservers['serverid'] . '"';
            $db_result = mysql_query( $sql );
            $servernames2 = mysql_fetch_array( $db_result );
            $servernames[$i] = $servernames2['hostname'];
            $i++;
        }
        
        $a = 0;
        $b = 0;
        $c = 0;
        $d = 0;
        $e = 0;
        $i = 0;
        $j = 1;
        $server = array();

        while ($j <= count( $servernames )) {
            /* Retreive the serverid from ispconfig */
            $result = $client->server_get_serverid_by_name( $session_id, $servernames[$i] );

            /* Retrieve the services for the server from ispconfig */
            $servicesresult = $client->server_get_functions( $session_id, $result[0]['server_id'] );
            
            /* Loop through the results to find the services on each server */

            
            if ($servicesresult[0]['mail_server'] == 1 ) {
                $server['mail_server'][$a]['server_id'] = $result[0]['server_id'];
                $server['mail_server'][$a]['hostname'] = $servernames[$i];
                $a++;
            }
            if ($servicesresult[0]['web_server'] == 1 ) {
                $server['web_server'][$b]['server_id'] = $result[0]['server_id'];
                $server['web_server'][$b]['hostname'] = $servernames[$i];
                $b++;
            }
            if ($servicesresult[0]['dns_server'] == 1 ) {
                $server['dns_server'][$c]['server_id'] = $result[0]['server_id'];
                $server['dns_server'][$c]['hostname'] = $servernames[$i];
                $c++;
            }
            if ($servicesresult[0]['file_server'] == 1 ) {
                $server['file_server'][$d]['server_id'] = $result[0]['server_id'];
                $server['file_server'][$d]['hostname'] = $servernames[$i];
                $d++;
            }
            if ($servicesresult[0]['db_server'] == 1 ) {
                $server['db_server'][$e]['server_id'] = $result[0]['server_id'];
                $server['db_server'][$e]['hostname'] = $servernames[$i];
                $e++;
            }
            ++$i;
            ++$j;
        }
        
        unset($a);
        unset($b);
        unset($c);
        unset($d);
        unset($e);
        
        logModuleCall('ispconfig','CreateClient',$servicesresult,$server,'','');

        if (count( $server['mail_server'] ) == 1 ) {
            
            $defaultmailserver = $server['mail_server'][0]['server_id'];
            
        } else {
            
            $rnd = rand(0, ( count( $server['mail_server'] ) - 1 ) );
            $defaultmailserver = $server['mail_server'][$rnd]['server_id'];
            
        }
        
        if (count( $server['web_server'] ) == 1 ) {
            
            $defaultwebserver = $server['web_server'][0]['server_id'];
            
        } else {
            
            $a = 1;
            $b = 0;
                    
            while ( $a <= count($server['web_server']) ) {
            
                $result = array();
                $result = $client->sites_web_domain_get( $session_id, 
                    array( 'server_id' => $server['web_server'][$b]['server_id'],
                            'type' => 'vhost') );
                
                if (!isset($webservercnt)) { 
                    $webservercnt = count( array_keys($result) ); 
                    
                }
                
                if ( $webservercnt > count( array_keys($result) ) OR 
                    ( !isset($defaultwebserver) ) ) {
                        
                    $webservercnt = count( array_keys($result) );
                    $defaultwebserver = $server['web_server'][$b]['server_id'];
                
             }
             $a++;
             $b++;
            
            }
            
            unset($a);
            unset($b);
            
        }
        
        if (count( $server['db_server'] ) == 1 ) {
            
            $defaultdbserver = $server['db_server'][0]['server_id'];
            
        } else {
            
            $rnd = rand(0, ( count( $server['db_server'] ) - 1 ) );
            $defaultdbserver = $server['db_server'][$rnd]['server_id'];
            
        }
        
        if (count( $server['dns_server'] ) == 1 ) {
            
            $defaultdnsserver = $server['dns_server'][0]['server_id'];
            
        } else {
            
            $rnd = rand(0, ( count( $server['dns_server'] ) - 1 ) );
            $defaultdnsserver = $server['dns_server'][$rnd]['server_id'];
            
        }
        
        if (count( $server['file_server'] ) == 1 ) {
            
            $defaultfileserver = $server['file_server'][0]['server_id'];
            
        } else {
            
            $rnd = rand(0, ( count( $server['file_server'] ) - 1 ) );
            $defaultfileserver = $server['file_server'][$rnd]['server_id'];
            
        }
        
        logModuleCall('ispconfig','CreateClient',$server,$server,'','');
            
            $ispcparams = array(
                    'company_name' => $companyname,
                    'contact_name' => $fullname,
                    'customer_no' => $accountid,
                    'username' => $username,
                    'password' => $password,
                    'language' => $defaultlanguage,
                    'usertheme' => $designtheme,
                    'street' => $address,
                    'zip' => $zip,
                    'city' => $city,
                    'state' => $state,
                    'country' => $country,
                    'telephone' => $phonenumber,
                    'mobile' => '',
                    'fax' => '',
                    'email' => $mail,
                    'template_master' => $templateid,
                    'web_php_options' => $globalphp,
                    'ssh_chroot' => $chrootenable,
                    'default_mailserver' => $defaultmailserver,
                    'default_webserver' => $defaultwebserver,
                    'default_dbserver' => $defaultdbserver,
                    'default_dnsserver' => $defaultdnsserver,
                    'locked' => '0',
                    'created_at' => date('Y-m-d')
                    );
        
            $reseller_id = 0;

            $client_id = $client->client_add( $session_id, $reseller_id, $ispcparams );

            logModuleCall('ispconfig','CreateClient',$client_id,$ispcparams,'','');
        
        if ( $domaintool == 'on' ) {
            
            $ispcparams = array( 'domain' => $domain );
            $domain_id = $client->domains_domain_add( $session_id, $client_id, $ispcparams );
            
            logModuleCall('ispconfig','CreateDomainAdd',$domain_id,$ispcparams,'','');
            
        }
        
        
        if ( $dns == 'on' ) {

            $dns_id = $client->dns_templatezone_add( $session_id, $client_id, $dnstemplate, $domain, $zoneip, $nameserver1, $nameserver2, $soaemail );
            logModuleCall('ispconfig','CreateDNSZone',$domain,'DNS Template '.$dnstemplate,'','');
            
        }


        if ( $webcreation == 'on' ) {
            
            $ispcparams = array(
                    'server_id' => $defaultwebserver, 
                    'ip_address' => '*',
                    'domain' => $domain,
                    'type' => 'vhost',
                    'parent_domain_id' => '0',
                    'vhost_type' => 'name',
                    'hd_quota' => $webquota,
                    'traffic_quota' => $webtraffic,
                    'cgi' => $enablecgi,
                    'ssi' => $enablessi,
                    'perl' => $enableperl,
                    'ruby' => $enableruby,
                    'python' => $enablepython,
                    'suexec' => $enablesuexec,
                    'errordocs' => $enableerrdocs,
                    'is_subdomainwww' => 1,
                    'subdomain' => $subdomain,
                    'php' => $phpmode,
                    'ruby' => $enableruby,
                    'redirect_type' => '',
                    'redirect_path' => '',
                    'ssl' => $enablessl,
					'ssl_letsencrypt' =>$enablessletsencrypt,
                    'ssl_state' => '',
                    'ssl_locality' => '',
                    'ssl_organisation' => '',
                    'ssl_organisation_unit' => '',
                    'ssl_country' => '',
                    'ssl_domain' => '',
                    'ssl_request' => '',
                    'ssl_key' => '',
                    'ssl_cert' => '',
                    'ssl_bundle' => '',
                    'ssl_action' => '',
                    'stats_password' => $password,
                    'stats_type' => 'webalizer',
                    'allow_override' => 'All',
                    'php_open_basedir' => '/',
                    'php_fpm_use_socket' => 'y',
                    'pm' => 'dynamic',
                    'pm_max_children' => '10',
                    'pm_start_servers' => '2',
                    'pm_min_spare_servers' => '1',
                    'pm_max_spare_servers' => '5',
                    'pm_process_idle_timeout' => '10',
                    'pm_max_requests' => '0',
                    'custom_php_ini' => '',
		    'nginx_directives' => '',
                    'backup_interval' => '',
                    'backup_copies' => 1,
                    'active' => $webactive,
                    'traffic_quota_lock' => 'n',
                    'added_date' => date("Y-m-d"),
                    'added_by' => $soapuser
                );
            
                $ispconfigver = $client->ispconfig_version_get();
                
				if (preg_match("/3.1/", $ispconfigver)) {
					$ispcparams['http_port'] = '80';
					$ispcparams['https_port'] = '443';
					};

            if ( $webwriteprotect == 'on' ) {
                
                $readonly = true;
                
            } else {
                
                $readonly = false;
                
            }

            $website_id = $client->sites_web_domain_add( $session_id, $client_id, $ispcparams, $readonly );

            logModuleCall('ispconfig','CreateWebDomain',$website_id,$ispcparams,'','');
            
            
            if ( $addftpuser == 'on' ) {
                
                $domain_arr = $client->sites_web_domain_get( $session_id, $website_id );
                $ispcparams = array(
                        'server_id' => $defaultwebserver,
                        'parent_domain_id' => $website_id,
                        'username' => $username . 'admin',
                        'password' => $password,
                        'quota_size' => $webquota,
                        'active' => 'y',
                        'uid' => $domain_arr['system_user'],
                        'gid' => $domain_arr['system_group'],
                        'dir' => $domain_arr['document_root'],
                        'quota_files' => -1,
                        'ul_ratio' => -1,
                        'dl_ratio' => -1,
                        'ul_bandwidth' => -1,
                        'dl_bandwidth' => -1
                    );
                
                $ftp_id = $client->sites_ftp_user_add( $session_id, $client_id, $ispcparams );
                
                logModuleCall('ispconfig','CreateFtpUser',$ftp_id,$ispcparams,'','');
            }
            
            // Add A Record and CNAME Records for website to dns.
            if ( $dns == 'on' ) {
            
                $zone_id = $client->dns_zone_get_by_user($session_id, $client_id, $defaultdnsserver);
                $dns_svr = $client->dns_zone_get($session_id, $zone_id[0]['id']);
                $a_svr = $client->server_get_all($session_id);
                
                // Loop through the array till we find the mail server name
                while ($arec == '') {
                    $poparr = array_pop($a_svr);
                    if ( $poparr['server_id'] == $defaultwebserver )
                            $arec = $poparr['server_name'];
                }
                
                $sql = 'SELECT ipaddress FROM tblservers '
                    . 'WHERE hostname  = "' . $arec . '"';
                $db_result = mysql_query( $sql );
                $a_ip = mysql_fetch_array( $db_result );
                logModuleCall('ispconfig','CreateDNSA',$zone_mx,$a_ip,'','');
                
                $params = array(
                    'server_id' => $dns_svr['server_id'],
                    'zone' => $zone_id[0]['id'],
                    'name' => $domain.'.',
                    'type' => 'A',
                    'data' => $a_ip['ipaddress'],
                    'aux' => '0',
                    'ttl' => '3600',
                    'active' => 'y',
                    'stamp' => date('Y-m-d H:i:s'),
                    'serial' => '',
                );
                
                $zone_mx = $client->dns_a_add($session_id, $client_id, $params);
                logModuleCall('ispconfig','CreateDNSA',$zone_mx,$params,'','');
                
                // Add cname record
                $params = array(
                    'server_id' => $dns_svr['server_id'],
                    'zone' => $zone_id[0]['id'],
                    'name' => 'www',
                    'type' => 'CNAME',
                    'data' => $domain.'.',
                    'aux' => '0',
                    'ttl' => '3600',
                    'active' => 'y',
                    'stamp' => date('Y-m-d H:i:s'),
                    'serial' => '',
                );
                
                $zone_mx = $client->dns_cname_add($session_id, $client_id, $params);
                logModuleCall('ispconfig','CreateDNSCNAME',$zone_mx,$params,'','');
                
            }
            
        }

        if ( $addmaildomain == 'on' ) {
            
            $ispcparams = array( 
                    'server_id' => $defaultmailserver, 
                    'domain'    => $domain, 
                    'active'    => 'y' 
                );

            $maildomain_id = $client->mail_domain_add( $session_id, $client_id, $ispcparams );
            logModuleCall('ispconfig','CreateMailDomain',$maildomain_id,$ispcparams,'','');
            
            // Add MX Record to dns.
            if ( $dns == 'on' ) {
            
                $zone_id = $client->dns_zone_get_by_user($session_id, $client_id, $defaultdnsserver);
                $dns_svr = $client->dns_zone_get($session_id, $zone_id[0]['id']);
                $mx_svr = $client->server_get_all($session_id);
                
                // Loop through the array till we find the mail server name
                while ($mx == '') {
                    $poparr = array_pop($mx_svr);
                    if ( $poparr['server_id'] == $defaultmailserver )
                            $mx = $poparr['server_name'];
                }
                $params = array(
                    'server_id' => $dns_svr['server_id'],
                    'zone' => $zone_id[0]['id'],
                    'name' => $domain.'.',
                    'type' => 'mx',
                    'data' => $mx.'.',
                    'aux' => '0',
                    'ttl' => '3600',
                    'active' => 'y',
                    'stamp' => date('Y-m-d H:i:s'),
                    'serial' => '',
                );
                
                $zone_mx = $client->dns_mx_add($session_id, $client_id, $params);
                logModuleCall('ispconfig','CreateDNSMX',$zone_mx,$params,'','');
            }
            
        }

        if ( $client->logout( $session_id ) ) {
            
        }
        
        $successful = 1;
        
    } catch (SoapFault $e) {
        
        $error = 'SOAP Error: ' . $e->getMessage();
        $successful = 0;
        logModuleCall('ispconfig','Create Failed',$e->getMessage(), $params,'','');

        
    }

    if ( $successful == 1 ) {
        
        $result = "success";
        
    } else {
        
        $result = $error;
        
    }
    
    } else {
        /*
         * No username or password set.
         */
        $result = 'Username or Password is Blank or Not Set';
    }
            
    return $result;
}

function ispcfg3_TerminateAccount( $params ) {

    $username           = $params['username'];
    $password           = $params['password'];
    $clientsdetails     = $params['clientsdetails'];
    $domain             = $params['domain'];
    $soapuser           = $params['configoption1'];
    $soappassword       = $params['configoption2'];
    $soapsvrurl         = $params['configoption3'];
    $soapsvrssl         = $params['configoption4'];
    $domaintool         = $params['configoption10'];
    
    if ( $soapsvrssl == 'on' ) {
        
        $soap_url = 'https://' . $soapsvrurl . '/remote/index.php';
        $soap_uri = 'https://' . $soapsvrurl . '/remote/';
        
    } else {
        
        $soap_url = 'http://' . $soapsvrurl . '/remote/index.php';
        $soap_uri = 'http://' . $soapsvrurl . '/remote/';
        
    }

    if (
            ((isset($username)) &&
            ($username != '')) &&
            ((isset($password)) &&
            ($password != ''))
            ) 
        {
    
    try {
        /* Connect to SOAP Server */
        $client = new SoapClient( null, 
                                array( 'location' => $soap_url, 
                                        'uri' => $soap_uri, 
                                        'exceptions' => 1, 
                                        'trace' => false 
                                    )
                                );
        
        /* Authenticate with the SOAP Server */
        $session_id = $client->login( $soapuser, $soappassword );
              
        $domain_id = $client->client_get_by_username( $session_id, $username );

        $group_id = $domain_id['default_group'];
        $client_id = $domain_id['client_id'];
        
        if ( $domaintool == 'on' ) {

            $result = $client->domains_get_all_by_user( $session_id, $group_id );
            logModuleCall('ispconfig','Terminate Get Domains','Get Domains',$result,'','');
            if (!empty($result)) {
                $key = '0';
                foreach ( $result as $key => $value ) {
                
                    if ( $result[$key]['domain'] = $domain ) {
                    
                        $primary_id = $result[$key]['domain_id'];
                        continue;
                    
                    }
                }
            
                $result = $client->domains_domain_delete( $session_id, $primary_id );
                logModuleCall('ispconfig','Terminate Domain',$primary_id, $result,'','');
            }
        }

        $client_res = $client->client_delete_everything( $session_id, $client_id );
        logModuleCall('ispconfig','Terminate Client',$client_id, $client_res,'','');
        
        if ( $client->logout( $session_id ) ) {
            
        }

        $successful = '1';
        
    } catch (SoapFault $e) {
        
        $error = 'SOAP Error: ' . $e->getMessage();
        $successful = 0;
        
    }

    if ( $successful == 1 ) {
        
        $result = "success";
        
    } else {
        
        $result = $error;
        
    }

    } else {
        /*
         * No username or password set.
         */
        $result = 'Username or Password is Blank or Not Set';
    }
    
    return $result;
}

function ispcfg3_ChangePackage( $params ) {

    $username           = $params['username'];
    $password           = $params['password'];
    $clientsdetails     = $params['clientsdetails'];
    $soapuser           = $params['configoption1'];
    $soappassword       = $params['configoption2'];
    $soapsvrurl         = $params['configoption3'];
    $soapsvrssl         = $params['configoption4'];
    $templateid         = $params['configoption5'];

    if ( $soapsvrssl == 'on' ) {
        
        $soap_url = 'https://' . $soapsvrurl . '/remote/index.php';
        $soap_uri = 'https://' . $soapsvrurl . '/remote/';
        
    } else {
        
        $soap_url = 'http://' . $soapsvrurl . '/remote/index.php';
        $soap_uri = 'http://' . $soapsvrurl . '/remote/';
        
    }
    
    if (
            ((isset($username)) &&
            ($username != '')) &&
            ((isset($password)) &&
            ($password != ''))
            ) 
        {
    
    try {
        /* Connect to SOAP Server */
        $client = new SoapClient( null, 
                                array( 'location' => $soap_url, 
                                        'uri' => $soap_uri, 
                                        'exceptions' => 1, 
                                        'trace' => false 
                                    )
                                );
        
        /* Authenticate with the SOAP Server */
        $session_id = $client->login( $soapuser, $soappassword );
        
        $domain_id = $client->client_get_by_username( $session_id, $username );

        $client_id = $domain_id['client_id'];

        $client_record = $client->client_get( $session_id, $client_id );
        $client_record['template_master'] = $templateid;
        $reseller_id = $client->client_get( $session_id, $client_id );
        $parent_client_id = $resellerid['parent_client_id'];

        $affected_rows = $client->client_update( $session_id, $client_id, $parent_client_id, $client_record );

        if ($client->logout( $session_id )) {
        }

        $successful = '1';
    
    } catch (SoapFault $e) {
        
        $error = 'SOAP Error: ' . $e->getMessage();
        $successful = '0';
        
    }

    if ($successful == 1) {

        $result = 'success';

    } else {

        $result = 'Error: ' . $error;

    }
    
    } else {
        /*
         * No username or password set.
         */
        $result = 'Username or Password is Blank or Not Set';
    }
    
    return $result;
}

function ispcfg3_SuspendAccount( $params ) {

    $username           = $params['username'];
    $password           = $params['password'];
    $domain             = strtolower( $params['domain'] );
    $clientsdetails     = $params['clientsdetails'];
    $soapuser           = $params['configoption1'];
    $soappassword       = $params['configoption2'];
    $soapsvrurl         = $params['configoption3'];
    $soapsvrssl         = $params['configoption4'];
    $webcreation        = $params['configoption9'];
    $dns                = $params['configoption18'];
    $addmaildomain      = $params['configoption21'];
    $addftpuser         = $params['configoption22'];
    
    if ( $soapsvrssl == 'on' ) {
        
        $soap_url = 'https://' . $soapsvrurl . '/remote/index.php';
        $soap_uri = 'https://' . $soapsvrurl . '/remote/';
        
    } else {
        
        $soap_url = 'http://' . $soapsvrurl . '/remote/index.php';
        $soap_uri = 'http://' . $soapsvrurl . '/remote/';
        
    }
    
    if (
            ((isset($username)) &&
            ($username != '')) &&
            ((isset($password)) &&
            ($password != ''))
            ) 
        {
    
    try {
        /* Connect to SOAP Server */
        $client = new SoapClient( null,
                                array( 'location' => $soap_url,
                                        'uri' => $soap_uri,
                                        'exceptions' => 1,
                                        'trace' => false
                                    )
                                );
        
        /* Authenticate with the SOAP Server */
        $session_id = $client->login( $soapuser, $soappassword );
        
        $result_id = $client->client_get_by_username( $session_id, $username );
        
        $sys_userid = $result_id['client_id'];
        $sys_groupid = $result_id['groups'];
        $client_detail = $client->client_get( $session_id, $sys_userid );
        $parent_client_id = $client_detail['parent_client_id'];
        
        $domain_id = $client->dns_zone_get_by_user( $session_id, $sys_userid,  $client_detail['default_dnsserver'] );
        
        if ( $webcreation == 'on' ) {
            
            $clientsites = $client->client_get_sites_by_user( $session_id, $sys_userid, $sys_groupid );
            
            $i = 0;
            $j = 1;
            while ($j <= count($clientsites) ) {

                $domainres = $client->sites_web_domain_set_status( $session_id, $clientsites[$i]['domain_id'],  'inactive' );
                logModuleCall('ispconfig','Suspend Web Domain',$clientsites[$i]['domain_id'], $clientsites[$i],'','');
                $i++;
                $j++;
                
            }
            
        }
        
        if ( $addftpuser == 'on' ) {
           
            $ftpclient = $client->sites_ftp_user_get( $session_id, array( 'username' => $username.'%' ) );
           
            $i = 0;
            $j = 1;
            while ($j <= count($ftpclient) ) {
            
                $ftpclient[$i]['active'] = 'n';
                $ftpclient[$i]['password'] = '';
                $ftpid = $client->sites_ftp_user_update( $session_id, $sys_userid, $ftpclient[$i]['ftp_user_id'], $ftpclient[$i] );
                logModuleCall('ispconfig','Suspend Ftp User',$ftpclient[$i]['ftp_user_id'], $ftpclient[$i],'','');
                $i++;
                $j++;

            
            }
        }
        
        if ( $addmaildomain == 'on' ) {
            
            $emaildomain = $client->mail_domain_get_by_domain( $session_id, $domain );            
            $mailid = $client->mail_domain_set_status($session_id, $emaildomain[0]['domain_id'], 'inactive');
            logModuleCall('ispconfig','Suspend Email Domain',$emaildomain[0]['domain_id'], $mailid,'','');
            
        }
        
        if ( $dns == 'on' ) {           
        
            $i = 0;
            $j = 1;
            while ($j <= count($domain_id) ) {

                $affected_rows = $client->dns_zone_set_status( $session_id, $domain_id[$i]['id'], 'inactive' );
                $i++;
                $j++;
                logModuleCall('ispconfig','Suspend Domain',$domain_id[$i]['id'], $affected_rows,'','');
                
            }
            
        }

        $client_detail['locked'] = 'y';
        $client_detail['password'] = '';
        $client_result = $client->client_update( $session_id, $sys_userid, $parent_client_id, $client_detail );
        
        logModuleCall('ispconfig','Suspend Client', $sys_userid.' '.$sys_groupid, $client_result,'','');
                
        if ($client->logout( $session_id )) {
        }

        $successful = '1';
    
    } catch (SoapFault $e) {
        
        $error = 'SOAP Error: ' . $e->getMessage();
        $successful = '0';
        
    }

    if ($successful == 1) {

        $result = 'success';

    } else {

        $result = 'Error: ' . $error;

    }
    
    } else {
        /*
         * No username or password set.
         */
        $result = 'Username or Password is Blank or Not Set';
    }
    
    return $result;
}

function ispcfg3_UnsuspendAccount( $params ) {

    $username           = $params['username'];
    $password           = $params['password'];
    $domain             = strtolower( $params['domain'] );
    $clientsdetails     = $params['clientsdetails'];
    $soapuser           = $params['configoption1'];
    $soappassword       = $params['configoption2'];
    $soapsvrurl         = $params['configoption3'];
    $soapsvrssl         = $params['configoption4'];
    $webcreation        = $params['configoption9'];
    $dns                = $params['configoption18'];
    $addmaildomain      = $params['configoption21'];
    $addftpuser         = $params['configoption22'];
    
    if ( $soapsvrssl == 'on' ) {
        
        $soap_url = 'https://' . $soapsvrurl . '/remote/index.php';
        $soap_uri = 'https://' . $soapsvrurl . '/remote/';
        
    } else {
        
        $soap_url = 'http://' . $soapsvrurl . '/remote/index.php';
        $soap_uri = 'http://' . $soapsvrurl . '/remote/';
        
    }
    
    if (
            ((isset($username)) &&
            ($username != '')) &&
            ((isset($password)) &&
            ($password != ''))
            ) 
        {
    
    try {
        /* Connect to SOAP Server */
        $client = new SoapClient( null,
                                array( 'location' => $soap_url,
                                        'uri' => $soap_uri,
                                        'exceptions' => 1,
                                        'trace' => false
                                    )
                                );
        
        /* Authenticate with the SOAP Server */
        $session_id = $client->login( $soapuser, $soappassword );
        
        $result_id = $client->client_get_by_username( $session_id, $username );
        
        $sys_userid = $result_id['client_id'];
        $sys_groupid = $result_id['groups'];
        $client_detail = $client->client_get( $session_id, $sys_userid );
        $parent_client_id = $client_detail['parent_client_id'];
        
        $domain_id = $client->dns_zone_get_by_user( $session_id, $sys_userid,  $client_detail['default_dnsserver'] );
        
        if ( $webcreation == 'on' ) {
            
            $clientsites = $client->client_get_sites_by_user( $session_id, $sys_userid, $sys_groupid );
            
            $i = 0;
            $j = 1;
            while ($j <= count($clientsites) ) {

                $domainres = $client->sites_web_domain_set_status( $session_id, $clientsites[$i]['domain_id'],  'active' );
                logModuleCall('ispconfig','UnSuspend Web Domain',$clientsites[$i]['domain_id'], $domainres,'','');
                $i++;
                $j++;
                
            }
            
        }
        
        if ( $addftpuser == 'on' ) {
            
            $ftpclient = $client->sites_ftp_user_get( $session_id, array( 'username' => $username.'%' ) );
           
            $i = 0;
            $j = 1;
            while ($j <= count($ftpclient) ) {
            
                $ftpclient[$i]['active'] = 'y';
                $ftpclient[$i]['password'] = '';
                $ftpid = $client->sites_ftp_user_update( $session_id, $sys_userid, $ftpclient[$i]['ftp_user_id'], $ftpclient[$i] );
                logModuleCall('ispconfig','UnSuspend Ftp User',$ftpclient[$i]['ftp_user_id'], $ftpclient[$i],'','');
                $i++;
                $j++;

            
            }
        }
        
        if ( $addmaildomain == 'on' ) {
            
            $emaildomain = $client->mail_domain_get_by_domain( $session_id, $domain );            
            $mailid = $client->mail_domain_set_status($session_id, $emaildomain[0]['domain_id'], 'active');
            logModuleCall('ispconfig','UnSuspend Email Domain',$emaildomain[0]['domain_id'], $mailid,'','');
            
        }
        
        if ( $dns == 'on' ) {           
        
            $i = 0;
            $j = 1;
            while ($j <= count($domain_id) ) {

                $affected_rows = $client->dns_zone_set_status( $session_id, $domain_id[$i]['id'], 'active' );
                $i++;
                $j++;
                logModuleCall('ispconfig','UnSuspend Domain',$domain_id[$i]['id'], $affected_rows,'','');
            }
            
        }

        $client_detail['locked'] = 'n';
        $client_detail['password'] = '';
        $client_result = $client->client_update( $session_id, $sys_userid, $parent_client_id, $client_detail );
        
        logModuleCall('ispconfig','UnSuspend Client', $sys_userid.' '.$sys_groupid, $client_result,'','');
        
        if ($client->logout( $session_id )) {
        }

        $successful = '1';
    
    } catch (SoapFault $e) {
        
        $error = 'SOAP Error: ' . $e->getMessage();
        $successful = '0';
        
    }

    if ($successful == 1) {

        $result = 'success';

    } else {

        $result = 'Error: ' . $error;

    }
    
    } else {
        /*
         * No username or password set.
         */
        $result = 'Username or Password is Blank or Not Set';
    }
    
    return $result;
}

function ispcfg3_ChangePassword( $params ) {

    $username           = $params['username'];
    $password           = $params['password'];
    $clientsdetails     = $params['clientsdetails'];
    $soapuser           = $params['configoption1'];
    $soappassword       = $params['configoption2'];
    $soapsvrurl         = $params['configoption3'];
    $soapsvrssl         = $params['configoption4'];

    if ( $soapsvrssl == 'on' ) {
        
        $soap_url = 'https://' . $soapsvrurl . '/remote/index.php';
        $soap_uri = 'https://' . $soapsvrurl . '/remote/';
        
    } else {
        
        $soap_url = 'http://' . $soapsvrurl . '/remote/index.php';
        $soap_uri = 'http://' . $soapsvrurl . '/remote/';
        
    }
    
    if (
            ((isset($username)) &&
            ($username != '')) &&
            ((isset($password)) &&
            ($password != ''))
            ) 
        {
    
    try {
        /* Connect to SOAP Server */
        $client = new SoapClient( null, 
                                array( 'location' => $soap_url, 
                                        'uri' => $soap_uri, 
                                        'exceptions' => 1, 
                                        'trace' => false 
                                    )
                                );
        
        /* Authenticate with the SOAP Server */
        $session_id = $client->login( $soapuser, $soappassword );
        
        $domain_id = $client->client_get_by_username( $session_id, $username );

        $client_id = $domain_id['client_id'];

        $returnresult = $client->client_change_password( $session_id, $client_id, $password );

        logModuleCall('ispconfig','ChangePassword', $clientsdetails, $returnresult,'','');
        
        if ($client->logout( $session_id )) {

        }

        if ($returnresult == 1 ) {
            
            $successful = '1';
            
        } else {
            
            $successful = '0';
            $result = "Password change failed";
            
        }
        
        
    } catch (SoapFault $e) {
        
        $error = 'SOAP Error: ' . $e->getMessage();
        $successful = '0';
        
    }
    
    if ($successful == 1) {
        
        $result = 'success';
        
    } else {
        
        $result = 'Error: ' . $error;
        
    }
    
    } else {
        /*
         * No username or password set.
         */
        $result = 'Username or Password is Blank or Not Set';
    }
    
    return $result;
}

function ispcfg3_LoginLink( $params ) {

    $soapsvrurl         = $params['configoption3'];
    $soapsvrssl         = $params['configoption4'];

    if ( $soapsvrssl == 'on' ) {
        
        $soapsvrurl = 'https://' . $soapsvrurl . '';
        
    } else {
        
        $soapsvrurl = 'http://' . $soapsvrurl . '';
        
    }

    return '
    <button type="button" class="btn btn-xs btn-success" onclick="$(\'#frmIspconfigLogin\').submit()">Login to Controlpanel</button>
    <script type="text/javascript">
    var ispconfigForm = "<form id=\"frmIspconfigLogin\" action=\"'.$soapsvrurl.'/index.php\" method=\"GET\" target=\"_blank\"></form>";
    $(document).ready(function(){
        $("body").append(ispconfigForm);
        $("#frmIspconfigLogin").submit(function(){
            $.ajax({ 
                type: "POST", 
                url: "'.$soapsvrurl.'/login/index.php",
                data: "s_mod=login&s_pg=index&username='.$params['username'].'&password='.$params['password'].'", 
                xhrFields: {withCredentials: true} 
            });
        });
    });
    </script>';
}

function ispcfg3_ClientArea( $params ) {
    global $server_url;
    global $domain_url;
    $soapsvrurl = ($params['configoption4'] == 'on') ? 'https://' : 'http://';
    $soapsvrurl .= $params['configoption3'];
    $domain_url = ($params['configoption4'] == 'on' ? 'https://' : 'http://').$params['domain'];

    $requestedView = isset($_REQUEST['view']) ? $_REQUEST['view'] : '';
    if ($requestedView && $requestedView != 'overview') {
        $viewResponse = cwispy_handle_view($requestedView, $params);
        $templateFile = 'templates/'.$requestedView.'.tpl';
        if (isset($viewResponse['status']) && $viewResponse['status'] == 'success') {
            if (file_exists(__DIR__.'/'.$templateFile)) {
                return array(
                    'tabOverviewReplacementTemplate' => $templateFile,
                    'templateVariables' => array(
                        'variables' => $viewResponse['response'],
                        'params' => $params,
                        'action_urls' => @$viewResponse['action_urls'],
                        'request' => $_REQUEST,
                    ),
                );
            }
        }
        else {
            return array(
                'tabOverviewReplacementTemplate' => 'error.tpl',
                'templateVariables' => array(
                    'usefulErrorHelper' => $viewResponse['response'],
                ),
            );
        }
    }
    else {

    $code = '
    <form id="frmIspconfigLogin" action="'.$soapsvrurl.'/index.php" method="GET" target="_blank">
    <button type="submit" class="btn btn-xs btn-success">CONTROLPANEL LOGIN</button>
    </form>

    <script type="text/javascript">
    $("#frmIspconfigLogin").submit(function(){
        $.ajax({ 
            type: "POST", 
            url: "'.$soapsvrurl.'/login/index.php",
            data: "s_mod=login&s_pg=index&username='.$params['username'].'&password='.$params['password'].'", 
            xhrFields: {withCredentials: true} 
        });
    });
    </script>';

        return $code;
    }
}
?>
