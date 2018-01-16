<?php
/**
 * 
 *  ISPConfig v3.1+ module for WHMCS v7.x or Higher
 *  Copyright (C) 2014 - 2017  Shane Chrisp
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
 * 
 * @version 20171115
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

ini_set('error_reporting', E_ALL & ~E_NOTICE);
ini_set("display_errors", 1); // Set this option to Zero on a production machine.
openlog( "ispconfig3", LOG_PID | LOG_PERROR, LOG_LOCAL0 );
include_once(__DIR__.'/functions/base.php');

use WHMCS\Database\Capsule;

function ispcfg3_MetaData() {
    return array(
        'DisplayName' => 'ISPConfig Integration Module',
        'APIVersion' => '1.1',
        'RequiresServer' => true,
        'DefaultNonSSLPort' => '8080',
        'DefaultSSLPort' => '8080',
    );
}


function ispcfg3_ConfigOptions() {
    $configarray = array(
        'ISPConfig Template ID' => array(
                    'Type' => 'text',
                    'Size' => '3',
                    'Description' => '<br/>The ID of the Client Template in ISPConfig'
            ),
        'Website Creation' => array(
                    'Type' => 'yesno',
                    'Description' => '<br/>Tick to create the website automatically' 
            ),
        'ISPConfig Domain Tool' => array(
                    'Type' => 'yesno',
                    'Description' => '<br/>Enable the ISPConfig Domain Tool'
            ),
        'Auto Subdomain' => array(
                    'Type' => 'dropdown',
                    'Options' => 'none,www,*',
                    'Description' => '<br/>Select to create subdomain during setup'
            ),
        'Active' => array(
                    'Type' => 'yesno',
                    'Description' => '<br/>Enable the account once created?'
            ),
        'Create DNS' => array( 
                    'Type' => 'yesno',
                    'Description' => '<br/>Setup DNS records? You must have a DNS '
                                    . 'template configured in ISPConfig'
            ),
        'DNS Template ID' => array(
                    'Type' => 'text',
                    'Size' => '3',
                    'Description' => '<br />DNS Template ID from ISPConfig'
            ),
        'DNS SOA Email Name' => array(
                    'Type' => 'text',
                    'Size' => '40',
                    'Description' => '<br />Username portion of the SOA email. '
                                    . 'eg: hostmaster for hostmaster@domain.tld'
            ),
        'ISPConfig Language' => array(
                    'Type' => 'dropdown',
                    'Options' => 'ar,bg,br,cz,de,el,en,es,fi,fr,hu,hr,id,it,ja,'
                                . 'nl,pl,pt,ro,ru,se,sk,tr',
                    'Description' => '<br/>Choose your default Language',
                    'Default' => 'en'
            ),
        'Create Maildomain' => array( 
                    'Type' => 'yesno',
                    'Description' => '<br/>Tick to create the Email Domain '
                                    . 'automatically during setup'
            ),
        'Create FTP-Account' => array(
                    'Type' => 'yesno',
                    'Description' => '<br/>Create FTP Account during setup'
            ),
        'FTP-Account Suffix' => array(
                    'Type' => 'text',
                    'Size' => '30',
                    'Description' => '<br/>Suffix to append to username for the'
                                    . ' FTP User. eg: ftp for Usernameftp'
            ),
        'Create Database' => array(
                    'Type' => 'yesno',
                    'Description' => '<br/>Create Database during setup'
            ),
        'Create Database Users' => array(
                    'Type' => 'dropdown',
                    'Options' => [
                        '1' => 'Database User',
                        '2' => 'Database User + Read-only db user'
                    ],
                    'Description' => '<br/>Database users to create'
            ),
		'Site Pro api username' => array(
                    'Type' => 'text',
                    'Size' => '30',
                    'Description' => ' <br />Site.pro website builder. '
                    . 'Get one here <a href="http://site.pro/" '
                    . 'title="https://site.pro"><strong>https://site.pro'
                    . '</strong></a> '
            ),
        'Site.Pro api Password' => array(
                    'Type' => 'password',
                    'Size' => '60',
                    'Description' => ' <br />Site.pro api password.'
            )
        );
    return $configarray;
}

function ispcfg3_TestConnection(array $params)
{
    
    if (!extension_loaded( 'soap')) {
        die('The PHP SOAP module is required to run this module.');
    }

    if ( !empty( $params['serverhttpprefix'] ) && !empty( $params['serverhostname'] ) ) {
        
        $soap_uri = $params['serverhttpprefix']. '://' . $params['serverhostname'];
        if ( $params['serverport'] != '80' || $params['serverport'] != '443' ) {
            $soap_uri .= ':'.$params['serverport'];
        }
        $soap_uri .= '/remote/';
            
        $soap_url = $soap_uri.'index.php';

    }
    
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
        //$session_id = $client->login( $soapuser, $soappassword );
        $session_id = $client->login( $params['serverusername'], $params['serverpassword'] );
        $success = true;
        $errorMsg = '';
        
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        $success = false;
        $errorMsg = $e->getMessage();
    }
    return array(
        'success' => $success,
        'error' => $errorMsg,
    );
}


function ispcfg3_CreateAccount(array $params ) {
    
    $productid          = $params['pid'];
    $accountid          = $params['accountid'];
    $domain             = strtolower( $params['domain'] );
    $username           = $params['username'];
    $password           = $params['password'];
    $clientsdetails     = $params['clientsdetails'];
    $templateid         = $params['configoption1'];
    $webcreation        = $params['configoption2'];
    $domaintool         = $params['configoption3'];
    $subdomain          = $params['configoption4'];
    $webactive          = $params['configoption5'];
    $dns                = $params['configoption6'];
    $dnstemplate        = $params['configoption7'];
    $dnssoaname         = $params['configoption8'];
    $defaultlanguage    = $params['configoption9'];
    $addmaildomain      = $params['configoption10'];
    $addftpuser         = $params['configoption11'];
    $ftpsuffix          = $params['configoption12'];
    $dbcreate           = $params['configoption13'];
    $dbusers            = $params['configoption14'];
	$siteprousername    = $params['configoption15'];
	$sitepropass        = $params['configoption16'];
    $soaemail           = $dnssoaname . '@' . $domain;

    $webactive == 'on' ? $webactive = 'y' : $webactive = 'n';

    try {
        $pdo = Capsule::connection()->getPdo();
        $statement = $pdo->prepare("SELECT * FROM tblservers WHERE name = :name");
        $statement->execute( [ ':name' => $params['serverhostname'], ] );
        $allservers =  $statement->fetchAll();
        $nameserver1 = $allservers[0]['nameserver1'];
        $nameserver2 = $allservers[0]['nameserver2'];
    } catch (\Exception $e) {
        echo "error: {$e->getMessage()}";
    }

    logModuleCall('ispconfig','CreateClient',$params['clientsdetails'],$params,'','');
    
    if ( !empty( $params['serverhttpprefix'] ) && !empty( $params['serverhostname'] ) ) {
        
        $soap_uri = $params['serverhttpprefix']. '://' . $params['serverhostname'];
        if ( $params['serverport'] != '80' || $params['serverport'] != '443' ) {
            $soap_uri .= ':'.$params['serverport'];
        }
        $soap_uri .= '/remote/';

        $soap_url = $soap_uri.'index.php';

    }
logModuleCall('ispconfig','URI',$soap_uri,$soap_url,'','');
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
        //$session_id = $client->login( $soapuser, $soappassword );
        $session_id = $client->login( $params['serverusername'], $params['serverpassword'] );
        
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
         
        // Retrieve an array of templates from ISPConfig
        $templates = $client->client_templates_get_all($session_id);
        
        // Loop through the array and find the template matching the one in the
        // product configuration

        $match = 0;
        foreach ($templates as $a) {
            if ($a['template_id'] == $templateid) {
                // Found a matching template.
                $tmpl = $a;
                $match = 1;
            }
        }
        if ( $match == 0 ) {
                // If no template, throw an error and exit
                return "No ISPConfig Limit-Template Match Found";
                end;
                
        } else if ( $match == 1 ) {
            
            if ( $webcreation == 'on' ) {
                if ( !empty( $tmpl['web_servers'] ) ) {
                    $defaultwebserver  = return_server( $tmpl['web_servers'] );
                } else {
                    return "No Default Web Server Found in ISPConfig Template";
                    end;
                }
            }
            if ( $dbcreate == 'on' ) {
                if ( !empty( $tmpl['db_servers'] ) ) {
                    $defaultdbserver   = return_server( $tmpl['db_servers'] );
                } else {
                    return "No Default DB Server Found in ISPConfig Template";
                    end;
                }
            }
            if ( $addmaildomain == 'on' ) {
                if ( !empty( $tmpl['mail_servers'] ) ) {
                    $defaultmailserver = return_server( $tmpl['mail_servers'] );
                } else {
                    return "No Default Mail Server Found in ISPConfig Template";
                    end;
                }
            }
        }
                
        unset($match);
        unset($a);
        unset($templates);
        
        logModuleCall('ispconfig','PreCreateClient',$server,$server,'','');
        
        $ispcparams = array(
            'company_name'                  => $companyname,
            'contact_name'                  => $fullname,
            'customer_no'                   => $accountid,
            'username'                      => $username,
            'password'                      => $password,
            'language'                      => $defaultlanguage,
            'usertheme'                     => 'default',
            'street'                        => $address,
            'zip'                           => $zip,
            'city'                          => $city,
            'state'                         => $state,
            'country'                       => $country,
            'telephone'                     => $phonenumber,
            'mobile'                        => '',
            'fax'                           => '',
            'email'                         => $mail,
            'template_master'               => $templateid,
            'default_mailserver'            => $tmpl['mail_servers'],
            'limit_maildomain'              => $tmpl['limit_maildomain'],
            'limit_mailbox'                 => $tmpl['limit_mailbox'],
            'limit_mailalias'               => $tmpl['limit_mailalias'],
            'limit_mailaliasdomain'         => $tmpl['limit_mailaliasdomain'],
            'limit_mailforward'             => $tmpl['limit_mailforward'],
            'limit_mailcatchall'            => $tmpl['limit_mailcatchall'],
            'limit_mailrouting'             => $tmpl['limit_mailrouting'],
            'limit_mailfilter'              => $tmpl['limit_mailfilter'],
            'limit_fetchmail'               => $tmpl['limit_fetchmail'],
            'limit_mailquota'               => $tmpl['limit_mailquota'],
            'limit_spamfilter_wblist'       => $tmpl['limit_spamfilter_wblist'],
            'limit_spamfilter_user'         => $tmpl['limit_spamfilter_user'],
            'limit_spamfilter_policy'       => $tmpl['limit_spamfilter_policy'],
            'default_xmppserver'            => $tmpl['default_xmppserver'],
            'xmpp_servers'                  => $tmpl['xmpp_servers'],
            'limit_xmpp_domain'             => $tmpl['limit_xmpp_domain'],
            'limit_xmpp_user'               => $tmpl['limit_xmpp_user'],
            'limit_xmpp_muc'                => $tmpl['limit_xmpp_muc'],
            'limit_xmpp_anon'               => $tmpl['limit_xmpp_anon'],
            'limit_xmpp_auth_options'       => $tmpl['limit_xmpp_auth_options'],
            'limit_xmpp_vjud'               => $tmpl['limit_xmpp_vjud'],
            'limit_xmpp_proxy'              => $tmpl['limit_xmpp_proxy'],
            'limit_xmpp_status'             => $tmpl['limit_xmpp_status'],
            'limit_xmpp_pastebin'           => $tmpl['limit_xmpp_pastebin'],
            'limit_xmpp_httparchive'        => $tmpl['limit_xmpp_httparchive'],
            'default_webserver'             => $tmpl['web_servers'],
            'limit_web_ip'                  => $tmpl['limit_web_ip'],
            'limit_web_domain'              => $tmpl['limit_web_domain'],
            'limit_web_quota'               => $tmpl['limit_web_quota'],
            'web_php_options'               => $tmpl['web_php_options'],
            'limit_cgi'                     => $tmpl['limit_cgi'],
            'limit_ssi'                     => $tmpl['limit_ssi'],
            'limit_perl'                    => $tmpl['limit_perl'],
            'limit_ruby'                    => $tmpl['limit_ruby'],
            'limit_python'                  => $tmpl['limit_python'],
            'force_suexec'                  => $tmpl['force_suexec'],
            'limit_hterror'                 => $tmpl['limit_hterror'],
            'limit_wildcard'                => $tmpl['limit_wildcard'],
            'limit_ssl'                     => $tmpl['limit_ssl'],
            'limit_ssl_letsencrypt'         => $tmpl['limit_ssl_letsencrypt'],
            'limit_web_subdomain'           => $tmpl['limit_web_subdomain'],
            'limit_web_aliasdomain'         => $tmpl['limit_web_aliasdomain'],
            'limit_ftp_user'                => $tmpl['limit_ftp_user'],
            'limit_shell_user'              => $tmpl['limit_shell_user'],
            'ssh_chroot'                    => $tmpl['ssh_chroot'],
            'limit_webdav_user'             => $tmpl['limit_webdav_user'],
            'limit_backup'                  => $tmpl['limit_backup'],
            'limit_directive_snippets'      => $tmpl['limit_directive_snippets'],
            'limit_aps'                     => $tmpl['limit_aps'],
            'default_dnsserver'             => $tmpl['dns_servers'],
            'db_servers'                    => $tmpl['db_servers'],
            'limit_dns_zone'                => $tmpl['limit_dns_zone'],
            'default_slave_dnsserver'       => $tmpl['default_slave_dnsserver'],
            'limit_dns_slave_zone'          => $tmpl['limit_dns_slave_zone'],
            'limit_dns_record'              => $tmpl['limit_dns_record'],
            'default_dbserver'              => $tmpl['db_servers'],
            'dns_servers'                   => $tmpl['dns_servers'],
            'limit_database'                => $tmpl['limit_database'],
            'limit_database_user'           => $tmpl['limit_database_user'],
            'limit_database_quota'          => $tmpl['limit_database_quota'],
            'limit_cron'                    => $tmpl['limit_cron'],
            'limit_cron_type'               => $tmpl['limit_cron_type'],
            'limit_cron_frequency'          => $tmpl['limit_cron_frequency'],
            'limit_traffic_quota'           => $tmpl['limit_traffic_quota'],
            'limit_domainmodule'            => $tmpl['limit_domainmodule'],
            'limit_mailmailinglist'         => $tmpl['limit_mailmailinglist'],
            'limit_openvz_vm'               => $tmpl['limit_openvz_vm'],
            'limit_openvz_vm_template_id'   => $tmpl['limit_openvz_vm_template_id'],
            'limit_client'                  => 0, // If this value is > 0, then the client is a reseller
            'parent_client_id'              => 0,
            'locked'                        => '0',
            'added_date'                    => date("Y-m-d"),
            'added_by'                      => $soapuser,
            'created_at'                    => date('Y-m-d')
            );
        
            $reseller_id = 0;

            $client_id = $client->client_add( $session_id, $reseller_id, $ispcparams );

            logModuleCall('ispconfig','PostCreateClient',$client_id,$ispcparams,'','');
        
        if ( $domaintool == 'on' ) {
            
            $ispcparams = array( 'domain' => $domain );
            logModuleCall('ispconfig','CreatePreDomainAdd',$domain_id,$ispcparams,'','');
            $domain_id = $client->domains_domain_add( $session_id, $client_id, $ispcparams );
            logModuleCall('ispconfig','CreatePostDomainAdd',$domain_id,$ispcparams,'','');
            
        }
        
        if ( $dns == 'on' ) {
            
            $zoneip = $client->server_ip_get( $session_id, $defaultwebserver );

            logModuleCall('ispconfig','CreatePreDNSZone',$domain,'DNS Template '.$client_id." ".$dnstemplate." ".$domain." ".$zoneip['ip_address']." ".$nameserver1." ".$nameserver2." ".$soaemail,'','');
            $dns_id = $client->dns_templatezone_add( $session_id, $client_id, $dnstemplate, $domain, $zoneip['ip_address'], $nameserver1, $nameserver2, $soaemail );
            logModuleCall('ispconfig','CreatePostDNSZone',$domain,'DNS Template '.$dnstemplate,'','');
            
        }

        if ( $webcreation == 'on' ) {
            
            logModuleCall('ispconfig','PreCreateWebDomain',$website_id,$defaultwebserver,'','');
            
            $ispcparams = array(
                'server_id'                 => $defaultwebserver,
                'ip_address'                => '*',
                'domain'                    => $domain,
                'type'                      => 'vhost',
                'parent_domain_id'          => '0',
                'vhost_type'                => 'name',
                'hd_quota'                  => $tmpl['limit_web_quota'],
                'traffic_quota'             => $tmpl['limit_traffic_quota'],
                'cgi'                       => $tmpl['limit_cgi'],
                'ssi'                       => $tmpl['limit_ssi'],
                'perl'                      => $tmpl['limit_perl'],
                'ruby'                      => $tmpl['limit_ruby'],
                'python'                    => $tmpl['limit_python'],
                'suexec'                    => $tmpl['force_suexec'],
                'errordocs'                 => $tmpl['limit_hterror'],
                'is_subdomainwww'           => 1,
                'subdomain'                 => $subdomain,
                'redirect_type'             => '',
                'redirect_path'             => '',
                'ssl'                       => $tmpl['limit_ssl'],
                'ssl_letsencrypt'           => $tmpl['limit_ssl_letsencrypt'],
                'ssl_state'                 => '',
                'ssl_locality'              => '',
                'ssl_organisation'          => '',
                'ssl_organisation_unit'     => '',
                'ssl_country'               => '',
                'ssl_domain'                => '',
                'ssl_request'               => '',
                'ssl_key'                   => '',
                'ssl_cert'                  => '',
                'ssl_bundle'                => '',
                'ssl_action'                => '',
                'stats_password'            => $password,
                'stats_type'                => 'webalizer',
                'allow_override'            => 'All',
                'php_open_basedir'          => '/',
                'php_fpm_use_socket'        => 'y',
                'pm'                        => 'dynamic',
                'pm_max_children'           => '10',
                'pm_start_servers'          => '2',
                'pm_min_spare_servers'      => '1',
                'pm_max_spare_servers'      => '5',
                'pm_process_idle_timeout'   => '10',
                'pm_max_requests'           => '0',
                'custom_php_ini'            => '',
                'nginx_directives'          => '',
                'backup_interval'           => '',
                'backup_copies'             => 1,
                'active'                    => $webactive,
                'http_port'                 => '80',
                'https_port'                => '443',
                'traffic_quota_lock'        => 'n',
                'added_date'                => date("Y-m-d"),
                'added_by'                  => $soapuser
                );

            if ( $webwriteprotect == 'on' ) {
                
                $readonly = true;
                
            } else {
                
                $readonly = false;
                
            }

            $website_id = $client->sites_web_domain_add( $session_id, $client_id, $ispcparams, $readonly );

            logModuleCall('ispconfig','PostCreateWebDomain',$website_id,$ispcparams,'','');
            
            
            if ( ( $addftpuser == 'on' ) && ( $webcreation == 'on' ) ) {
                
                $domain_arr = $client->sites_web_domain_get( $session_id, $website_id );
                $ispcparams = array(
                    'server_id'         => $defaultwebserver,
                    'parent_domain_id'  => $website_id,
                    'username'          => $username . $ftpsuffix,
                    'password'          => $password,
                    'quota_size'        => $tmpl['limit_web_domain'],
                    'active'            => 'y',
                    'uid'               => $domain_arr['system_user'],
                    'gid'               => $domain_arr['system_group'],
                    'dir'               => $domain_arr['document_root'],
                    'quota_files'       => -1,
                    'ul_ratio'          => -1,
                    'dl_ratio'          => -1,
                    'ul_bandwidth'      => -1,
                    'dl_bandwidth'      => -1
                    );
                
                $ftp_id = $client->sites_ftp_user_add( $session_id, $client_id, $ispcparams );
                
                logModuleCall('ispconfig','CreateFtpUser',$ftp_id,$ispcparams,'','');
            }

        }

        if ( $addmaildomain == 'on' ) {
            
            $ispcparams = array( 
                    'server_id'     => $defaultmailserver,
                    'domain'        => $domain, 
                    'active'        => 'y' 
                );

            $maildomain_id = $client->mail_domain_add( $session_id, $client_id, $ispcparams );
            logModuleCall('ispconfig','CreateMailDomain',$maildomain_id,$ispcparams,'','');
            
        }
        
        if ( $dbcreate == 'on' ) {
            
            // Create the Customer number based on the ISPConfig settings. 
            // eg: C[CUSTOMER_NO] would become C45
            $temp = $client->client_get($session_id, $client_id);
            $cust = str_replace('[CUSTOMER_NO]',$client_id,$temp['customer_no_template']);
            // Strip the square bracket
            $clientnumber = substr(strstr($cust, "]"), 1);
    
            if ( $dbusers == 1 ) {
                // Create only master db user.
                $dbun = "dbuRW";
                $ispcparams = array(
                    'server_id' => 1,
                    'database_user' => $clientnumber.$dbun,
                    'database_user_prefix' => $clientnumber,
                    'database_password' => $password
                );
                logModuleCall('ispconfig','PreCreateDBRwUser',$clientnumber,$ispcparams,'','');
                $dbuser_id = $client->sites_database_user_add($session_id, $client_id, $ispcparams);
                logModuleCall('ispconfig','PreCreateDBRwUser',$clientnumber,$dbuser_id,'','');
                $rwuser = $dbuser_id;
                $rouser = 0;
                
            } else if ( $dbusers == 2 ) {
                // Create master and read only users.
                $dbun = "dbuRW";
                $ispcparams = array(
                    'server_id' => 1,
                    'database_user' => $clientnumber.$dbun,
                    'database_user_prefix' => $clientnumber,
                    'database_password' => $password
                );
                logModuleCall('ispconfig','PreCreateDBRwUser',$clientnumber,$ispcparams,'','');
                $dbuser_id = $client->sites_database_user_add($session_id, $client_id, $ispcparams);
                logModuleCall('ispconfig','PostCreateDBRwUser',$clientnumber,$dbuser_id,'','');
                $rwuser = $dbuser_id;
                
                $dbun = "dbuRO";
                $chars = "abcdefghjklmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ0123456789@#$%^&*()_-=+;:,.?";
                $ropass = substr( str_shuffle( $chars ), 0, 8 );
                $ispcparams = array(
                    'server_id' => 1,
                    'database_user' => $clientnumber.$dbun,
                    'database_user_prefix' => $clientnumber,
                    'database_password' => $ropass
                );
                logModuleCall('ispconfig','PreCreateDBRwUser',$clientnumber,$ispcparams,'','');
                $dbuser_id = $client->sites_database_user_add($session_id, $client_id, $ispcparams);
                logModuleCall('ispconfig','PostCreateDBRwUser',$clientnumber,$dbuser_id,'','');
                $rouser = $dbuser_id;
            }
            
            
            	$ispcparams = array(
                    'server_id' => $defaultdbserver,
                    'type' => 'mysql',
                    'parent_domain_id' => $website_id,
                    'database_name' => $clientnumber."DB",
                    'database_name_prefix' => $clientnumber,
                    'database_quota' => $tmpl['limit_database_quota'],
                    'database_user_id' => $rwuser,
                    'database_ro_user_id' => $rouser,
                    'database_charset' => '',
                    'remote_access' => 'n',
                    'remote_ips' => '',
                    'active' => 'y'
                );
                
                logModuleCall('ispconfig','PreCreateDB',$clientnumber,$ispcparams,'','');
                $database_id = $client->sites_database_add( $session_id, $client_id, $ispcparams );
                logModuleCall('ispconfig','PostCreateDB',$clientnumber,$database_id,'','');
            
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
    $domaintool         = $params['configoption3'];
    
    if ( !empty( $params['serverhttpprefix'] ) && !empty( $params['serverhostname'] ) ) {
        
        $soap_uri = $params['serverhttpprefix']. '://' . $params['serverhostname'];
        if ( $params['serverport'] != '80' || $params['serverport'] != '443' ) {
            $soap_uri .= ':'.$params['serverport'];
        }
        $soap_uri .= '/remote/';
            
        $soap_url = $soap_uri.'index.php';

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
                                    'stream_context'=> stream_context_create(
                                            array('ssl'=> array(
                                                'verify_peer'=>false,
                                                'verify_peer_name'=>false))
                                            ),
                                        'trace' => false
                                    )
                                );
        
        /* Authenticate with the SOAP Server */
        $session_id = $client->login( $params['serverusername'], $params['serverpassword'] );
              
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
    $templateid         = $params['configoption1'];

    if ( !empty( $params['serverhttpprefix'] ) && !empty( $params['serverhostname'] ) ) {
        
        $soap_uri = $params['serverhttpprefix']. '://' . $params['serverhostname'];
        if ( $params['serverport'] != '80' || $params['serverport'] != '443' ) {
            $soap_uri .= ':'.$params['serverport'];
        }
        $soap_uri .= '/remote/';
            
        $soap_url = $soap_uri.'index.php';

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
                                    'stream_context'=> stream_context_create(
                                            array('ssl'=> array(
                                                'verify_peer'=>false,
                                                'verify_peer_name'=>false))
                                            ),
                                        'trace' => false
                                    )
                                );
        
        /* Authenticate with the SOAP Server */
        $session_id = $client->login( $params['serverusername'], $params['serverpassword'] );
        
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
    $webcreation        = $params['configoption5'];
    $dns                = $params['configoption6'];
    $addmaildomain      = $params['configoption10'];
    $addftpuser         = $params['configoption11'];
    
    if ( !empty( $params['serverhttpprefix'] ) && !empty( $params['serverhostname'] ) ) {
        
        $soap_uri = $params['serverhttpprefix']. '://' . $params['serverhostname'];
        if ( $params['serverport'] != '80' || $params['serverport'] != '443' ) {
            $soap_uri .= ':'.$params['serverport'];
        }
        $soap_uri .= '/remote/';
            
        $soap_url = $soap_uri.'index.php';

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
                                    'stream_context'=> stream_context_create(
                                            array('ssl'=> array(
                                                'verify_peer'=>false,
                                                'verify_peer_name'=>false))
                                            ),
                                        'trace' => false
                                    )
                                );
        
        /* Authenticate with the SOAP Server */
        $session_id = $client->login( $params['serverusername'], $params['serverpassword'] );
        
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
    $webcreation        = $params['configoption2'];
    $dns                = $params['configoption6'];
    $addmaildomain      = $params['configoption10'];
    $addftpuser         = $params['configoption11'];
    
    if ( !empty( $params['serverhttpprefix'] ) && !empty( $params['serverhostname'] ) ) {
        
        $soap_uri = $params['serverhttpprefix']. '://' . $params['serverhostname'];
        if ( $params['serverport'] != '80' || $params['serverport'] != '443' ) {
            $soap_uri .= ':'.$params['serverport'];
        }
        $soap_uri .= '/remote/';
            
        $soap_url = $soap_uri.'index.php';

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
                                    'stream_context'=> stream_context_create(
                                            array('ssl'=> array(
                                                'verify_peer'=>false,
                                                'verify_peer_name'=>false))
                                            ),
                                        'trace' => false
                                    )
                                );
        
        /* Authenticate with the SOAP Server */
        $session_id = $client->login( $params['serverusername'], $params['serverpassword'] );
        
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

    if ( !empty( $params['serverhttpprefix'] ) && !empty( $params['serverhostname'] ) ) {
        
        $soap_uri = $params['serverhttpprefix']. '://' . $params['serverhostname'];
        if ( $params['serverport'] != '80' || $params['serverport'] != '443' ) {
            $soap_uri .= ':'.$params['serverport'];
        }
        $soap_uri .= '/remote/';
            
        $soap_url = $soap_uri.'index.php';

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
                                    'stream_context'=> stream_context_create(
                                            array('ssl'=> array(
                                                'verify_peer'=>false,
                                                'verify_peer_name'=>false))
                                            ),
                                        'trace' => false
                                    )
                                );
        
        /* Authenticate with the SOAP Server */
        $session_id = $client->login( $params['serverusername'], $params['serverpassword'] );
        
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

    if ( !empty( $params['serverhttpprefix'] ) && !empty( $params['serverhostname'] ) ) {
        
        $soapsvrurl = $params['serverhttpprefix']. '://' . $params['serverhostname'];
        if ( $params['serverport'] != '80' || $params['serverport'] != '443' ) {
            $soapsvrurl .= ':'.$params['serverport'];
        }

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

    if ( !empty( $params['serverhttpprefix'] ) && !empty( $params['serverhostname'] ) ) {
        
        $soapsvrurl = $params['serverhttpprefix']. '://' . $params['serverhostname'];
        if ( $params['serverport'] != '80' || $params['serverport'] != '443' ) {
            $soapsvrurl .= ':'.$params['serverport'];
        }

    }
    $domain_url = ($params['serversecure'] == 'on' ? 'https://' : 'http://').$params['domain'];

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
    <form id="frmIspconfigLogin" action="'.$soapsvrurl.'/login/index.php" method="GET" target="_blank">
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

function ispcfg3_UsageUpdate($params) {
    
	$serverid = $params['serverid'];
	$serverhostname = $params['serverhostname'];
	$serverip = $params['serverip'];
	$serverusername = $params['serverusername'];
	$serverpassword = $params['serverpassword'];
	$serveraccesshash = $params['serveraccesshash'];
	$serversecure = $params['serversecure'];

	# Run connection to retrieve usage for all domains/accounts on $serverid
    if ( ( $serverusername = $params['serverusername'] != '') 
            || ( $serverpassword = $params['serverpassword'] != '' ) ) {
    try {
        
        $pdo = Capsule::connection()->getPdo();
        $statement = $pdo->prepare("SELECT * FROM tblservers WHERE name = '".$params['serverhostname']."' AND type = 'ispcfg3'");
        $statement->execute( );
        $svr =  $statement->fetchAll();

        while ( $svr ) {
        $statement = $pdo->prepare("SELECT * FROM tblhosting WHERE server = '".$s['id']."' AND domainstatus = 'Active'");
        $statement->execute();
        $users = $statement->fetchAll();
        $a = print_r($users);
        syslog(LOG_INFO, $a);
        }
        
    } catch (\Exception $e) {
        echo "error: {$e->getMessage()}";
    }
    
    if ( !empty( $params['serverhttpprefix'] ) && !empty( $params['serverhostname'] ) ) {
        
        $soap_uri = $params['serverhttpprefix']. '://' . $params['serverhostname'];
        if ( $params['serverport'] != '80' || $params['serverport'] != '443' ) {
            $soap_uri .= ':'.$params['serverport'];
        }
        $soap_uri .= '/remote/';
            
        $soap_url = $soap_uri.'index.php';

    }
    
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
        //$session_id = $client->login( $soapuser, $soappassword );
        $session_id = $client->login( $params['serverusername'], $params['serverpassword'] );
        
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        $success = false;
        $errorMsg = $e->getMessage();
    }
    
}
	# Now loop through results and update DB

	foreach ($results AS $domain=>$values) {
        
        if ( ( $domain['serverusername'] != '' ) &&
                ( $domain['serverpassword'] != '' ) ) {
            update_query("tblhosting",array(
             "diskused"=>$values['diskusage'],
             "disklimit"=>$values['disklimit'],
             "bwusage"=>$values['bwusage'],
             "bwlimit"=>$values['bwlimit'],
             "lastupdate"=>"now()",
            ),array("server"=>$serverid,"domain"=>$values['domain']));
        }
    }

}
?>