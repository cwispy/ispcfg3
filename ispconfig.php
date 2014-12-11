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

function ispconfig_ConfigOptions() {
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
                    'Description' => 'Syntax: NS1,NS2,Emailname,Templateid '
                                    . 'eg: ns1.domain.tld,ns2.domain.tld,'
                                    . 'webmaster,1'
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

function ispconfig_CreateAccount( $params ) {

    $productid          = $params['pid'];
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
    $soaemail           = $dnssettings[2] . '.' . $domain;
    $dnstemplate        = $dnssettings[3];

    $websettings[0] == 'n' ? $enablecgi = '' : $enablecgi = 'y';
    $websettings[1] == 'n' ? $enablessi = '' : $enablessi = 'y';
    $websettings[2] == 'n' ? $enableruby = '' : $enableruby = 'y';
    $websettings[3] == 'n' ? $enablesuexec = '' : $enablesuexec = 'y';
    $websettings[4] == 'n'  ? $enableerrdocs = '' : $enableerrdocs = '1';
    $websettings[5] == 'n' ? $enablessl = '' : $enablessl = 'y';
    $webactive == 'on' ? $webactive = 'y' : $webactive = 'n';
    
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
        
    } catch (SoapFault $e) {
        
        $error = 'SOAP Error: ' . $e->getMessage();
        $successful = 0;
        
    }

    if ( $successful == 1 ) {
        
        $result = "success";
        
    } else {
        
        $result = "error";
        
    }
    return $result;
}

function ispconfig_TerminateAccount( $params ) {
    
    $soapuser           = $params['configoption1'];
    $soappassword       = $params['configoption2'];
    $username           = $params['username'];
    $soapsvrurl         = $params['configoption3'];
    $domaintool         = $params['configoption10'];
    $domain             = $params['domain'];
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
        }

        $domain_id = $client->client_delete_everything( $session_id, $client_id );

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

function ispconfig_ChangePackage( $params ) {

    $soapuser           = $params['configoption1'];
    $soappassword       = $params['configoption2'];
    $soapsvrurl         = $params['configoption3'];
    $username           = $params['username'];
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

function ispconfig_SuspendAccount( $params ) {

    $soapuser           = $params['configoption1'];
    $soappassword       = $params['configoption2'];
    $soapsvrurl         = $params['configoption3'];
    $username           = $params['username'];
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

        $result_id = $client->client_get_by_username( $session_id, $username );
        $sys_userid = $result_id['client_id'];
        $sys_groupid = $result_id['groups'];
        $resellerid = $client->client_get( $session_id, $sys_userid );
        $parent_client_id = $resellerid['parent_client_id'];
        
        $domain_id = $client->client_get_sites_by_user( $session_id, $sys_userid, $sys_groupid );

        $z = 0;
        foreach ($domain_id as $idx) {
            
            $idx[$z] = $domain_id[$z]['domain_id'];
            $client_record = $client->sites_web_domain_get( $session_id, $idx[$z] );
            $client_record['active'] = 'n';

            $affected_rows = $client->sites_web_domain_update( $session_id, $sys_userid, $idx[$z], $client_record );
        }

        $resellerid['locked'] = 'y';
        $resellerid['password'] = '';
        $client_result = $client->client_update( $session_id, $sys_userid, $parent_client_id, $resellerid );
        
        logModuleCall('ispconfig','Suspend', $sys_userid.' '.$sys_groupid, $resellerid,'','');

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

function ispconfig_UnsuspendAccount( $params ) {

    $soapuser           = $params['configoption1'];
    $soappassword       = $params['configoption2'];
    $soapsvrurl         = $params['configoption3'];
    $username           = $params['username'];
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

        $result_id = $client->client_get_by_username( $session_id, $username );
        $sys_userid = $result_id['client_id'];
        $sys_groupid = $result_id['groups'];
        $resellerid = $client->client_get( $session_id, $sys_userid );
        $parent_client_id = $resellerid['parent_client_id'];

        $domain_id = $client->client_get_sites_by_user( $session_id, $sys_userid, $sys_groupid );
        
        $z = 0;
        foreach ($domain_id as $idx) {
                        
            $idx[$z] = $domain_id[$z]['domain_id'];

            $client_record = $client->sites_web_domain_get( $session_id, $idx[$z] );
            $client_record['active'] = 'y';

            $affected_rows = $client->sites_web_domain_update( $session_id, $sys_userid, $idx[$z], $client_record );
        }
        
        $resellerid['locked'] = 'n';
        $resellerid['password'] = '';
        $client_result = $client->client_update( $session_id, $sys_userid, $parent_client_id, $resellerid );
                            
        logModuleCall('ispconfig','Unsuspend', $sys_userid.' '.$sys_groupid, $client_result,'','');
        
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

function ispconfig_ChangePassword( $params ) {

    $soapuser           = $params['configoption1'];
    $soappassword       = $params['configoption2'];
    $soapsvrurl         = $params['configoption3'];
    $username           = $params['username'];
    $password           = $params['password'];
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

function ispconfig_LoginLink( $params ) {

    $soapsvrurl         = $params['configoption3'];
    $soapsvrssl         = $params['configoption4'];

    if ( $soapsvrssl == 'on' ) {
        
        $soapsvrurl = 'https://' . $soapsvrurl . '';
        
    } else {
        
        $soapsvrurl = 'http://' . $soapsvrurl . '';
        
    }

    echo '<a href="' . $soapsvrurl . '" target="_blank" style="color:#cc0000">Login to Controlpanel</a>';
}

function ispconfig_ClientArea( $params ) {

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
