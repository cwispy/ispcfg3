<?php
/*
 * 
 *  ISPConfig v3.x module for WHMCS v5.x
 *  Copyright (C) 2014  Shane Chrisp
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
ini_set("display_errors", 1);
openlog( "ispconfig3", LOG_PID | LOG_PERROR, LOG_LOCAL0 );

function ispcfg3_ConfigOptions() {
    $configarray = array(
        'ISPConfig Remote Username' => array(
                    'Type' => 'text',
                    'Size' => '16',
                    'Description' => 'Remote Username configured in ISPConfig.'
            ),
        'ISPConfig Remote Password' => array(
                    'Type' => 'password',
                    'Size' => '16',
                    'Description' => 'Remote Password configured in ISPConfig.'
            ),
        'ISPConfig URL' => array(
                    'Type' => 'text',
                    'Size' => '32',
                    'Description' => 'E.g. ispconfig.example.tld:8080'
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
                    'Description' => 'The ISPConfig theme to use, typically '
                                    . 'this will be \'default\''
            ),
        'Global Client PHP Options' => array(
                    'Type' => 'text',
                    'Size' => '32',
                    'Description' => 'E.g. no,fast-cgi,cgi,mod,suphp,php-fpm'
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
        'Website Readonly' => array(
                    'Type' => 'yesno',
                    'Description' => 'Enabled to prevent client from changing'
                                    . ' website settings' 
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
                    'Description' => 'Syntax: CGI,SSI,Ruby,SuEXEC,ErrorDocuments'
                                    . ',SSL E.g.: y,y,y,n,y,n'
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
                    'Size' => '20',
                    'Description' => 'Syntax: NS1,NS2,Emailname,Templateid,Zone IP Address'
                                    . 'eg: ns1.domain.tld,ns2.domain.tld,'
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
    $webwriteprotect    = $params['configoption11'];
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
    
    $nameserver1        = $dnssettings[0];
    $nameserver2        = $dnssettings[1];
    $soaemail           = $dnssettings[2] . '.' . $domain.'.';
    $dnstemplate        = $dnssettings[3];
    $zoneip             = $dnssettings[4];

    $websettings[0] == 'n' ? $enablecgi = '' : $enablecgi = 'y';
    $websettings[1] == 'n' ? $enablessi = '' : $enablessi = 'y';
    $websettings[2] == 'n' ? $enableruby = '' : $enableruby = 'y';
    $websettings[3] == 'n' ? $enablesuexec = '' : $enablesuexec = 'y';
    $websettings[4] == 'n'  ? $enableerrdocs = '' : $enableerrdocs = '1';
    $websettings[5] == 'n' ? $enablessl = '' : $enablessl = 'y';
    $webactive == 'on' ? $webactive = 'y' : $webactive = 'n';

    logModuleCall('ispconfig','CreateClient',$params['clientsdetails'],$params,'','');
    
    if ( $soapsvrssl == 'on' ) {
        
        $soap_url = 'https://' . $soapsvrurl . '/remote/index.php';
        $soap_uri = 'https://' . $soapsvrurl . '/remote/';
        
    } else {
        
        $soap_url = 'http://' . $soapsvrurl . '/remote/index.php';
        $soap_uri = 'http://' . $soapsvrurl . '/remote/';
        
    }

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
            
            $rnd = rand(0, ( count( $server['web_server'] ) - 1 ) );
            $defaultwebserver = $server['web_server'][$rnd]['server_id'];
            
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


        if ( $webcreation == 'on' ) {
            
            $ispcparams = array(
                    'server_id' => $defaultwebserver, 
                    'ip_address' => '*',
                    'pm_process_idle_timeout' => '10',
                    'pm_max_requests' => '0',
                    'type' => 'vhost',
                    'vhost_type' => 'name',
                    'domain' => $domain,
                    'hd_quota' => $webquota,
                    'traffic_quota' => $webtraffic,
                    'cgi' => $enablecgi,
                    'ssi' => $enablessi,
                    'ruby' => $enableruby,
                    'suexec' => $enablesuexec,
                    'errordocs' => $enableerrdocs,
                    'subdomain' => $subdomain,
                    'ssl' => $enablessl,
                    'php' => $phpmode,
                    'active' => $webactive,
                    'allow_override' => 'All',
                    'php_open_basedir' => '/'
                );

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
                        'quota_size' => 0 - 1,
                        'active' => 'y',
                        'uid' => $domain_arr['system_user'],
                        'gid' => $domain_arr['system_group'],
                        'dir' => $domain_arr['document_root'],
                        'quota_files' => 0 - 1,
                        'ul_ratio' => 0 - 1,
                        'dl_ratio' => 0 - 1,
                        'ul_bandwidth' => 0 - 1,
                        'dl_bandwidth' => 0 - 1
                    );
                
                $ftp_id = $client->sites_ftp_user_add( $session_id, $client_id, $ispcparams );
                
                logModuleCall('ispconfig','CreateFtpUser',$ftp_id,$ispcparams,'','');
            }
        }


        if ( $dns == 'on' ) {
            
            $ispcparams = array (
                'server_id'     => $defaultdnsserver,
                'origin'        => $domain,
                'ns'            => $nameserver1,
                'mbox'          => $soaemail,
                'serial'        => date('Ymd').'00',
                'refresh'       => '3600',
                'retry'         => '300',
                'expire'        => '604800',
                'minimum'       => '86400',
                'ttl'           => '3600',
                'active'        => 'Y',
                'xfer'          => '',
                'also_notify'   => '',
                'update_acl'    => ''
            );

            //$dns_id = $client->dns_zone_add( $session_id, $client_id, $ispcparams );
            $dns_id = $client->dns_templatezone_add( $session_id, $client_id, $dnstemplate, $domain, $zoneip, $nameserver1, $nameserver2, $soaemail );
            logModuleCall('ispconfig','CreateDNSZone',$dns_id,$dns_id,'','');
        }

        if ( $addmaildomain == 'on' ) {
            
            $ispcparams = array( 
                    'server_id' => $defaultmailserver, 
                    'domain'    => $domain, 
                    'active'    => 'y' 
                );

            $maildomain_id = $client->mail_domain_add( $session_id, $client_id, $ispcparams );
            logModuleCall('ispconfig','CreateMailDomain',$maildomain_id,$ispcparams,'','');
            
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

    return $result;
}

function ispcfg3_TerminateAccount( $params ) {

    $username           = $params['username'];
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

    return $result;
}

function ispcfg3_ChangePackage( $params ) {

    $username           = $params['username'];
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

    return $result;
}

function ispcfg3_SuspendAccount( $params ) {

    $username           = $params['username'];
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
                $i++;
                $j++;
                logModuleCall('ispconfig','Suspend Web Domain',$clientsites[$i]['domain_id'], $domainres,'','');
                
            }
            
        }
        
        if ( $addftpuser == 'on' ) {
            
           $username = $username . 'admin';
           $ftpclient = $client->sites_ftp_user_get( $session_id, array( 'username' => $username ) );
           
           $ftpclient[0]['active'] = 'n';
           $ftpid = $client->sites_ftp_user_update( $session_id, $sys_userid, $ftpclient[0]['ftp_user_id'], $ftpclient[0] );
           
           logModuleCall('ispconfig','Suspend Ftp User',$ftpclient[0]['ftp_user_id'], $ftpclient,'','');
           
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

    return $result;
}

function ispcfg3_UnsuspendAccount( $params ) {

    $username           = $params['username'];
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
                $i++;
                $j++;
                logModuleCall('ispconfig','UnSuspend Web Domain',$clientsites[$i]['domain_id'], $domainres,'','');
                
            }
            
        }
        
        if ( $addftpuser == 'on' ) {
            
           $username = $username . 'admin';
           $ftpclient = $client->sites_ftp_user_get( $session_id, array( 'username' => $username ) );
           
           $ftpclient[0]['active'] = 'y';
           $ftpid = $client->sites_ftp_user_update( $session_id, $sys_userid, $ftpclient[0]['ftp_user_id'], $ftpclient[0] );
           
           logModuleCall('ispconfig','UnSuspend Ftp User',$ftpclient[0]['ftp_user_id'], $ftpclient,'','');
           
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

    echo '<a href="' . $soapsvrurl . '" target="_blank" style="color:#cc0000">Login to Controlpanel</a>';
}

function ispcfg3_ClientArea( $params ) {

    $soapsvrurl         = $params['configoption3'];
    $soapsvrssl         = $params['configoption4'];

    if ( $soapsvrssl == 'on' ) {
        
        $soapsvrurl = 'https://' . $soapsvrurl . '';
        
    } else {
        
        $soapsvrurl = 'http://' . $soapsvrurl . '';
        
    }

    $code = '<a href=' . $soapsvrurl . ' target="_blank"><b>CONTROLPANEL LOGIN</b></a>';
    return $code;
}
?>
