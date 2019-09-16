<?php
/**
 * 
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
 * 
 * @version 20190911
 */

namespace ISPCFG;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Cookie\CookieJar;
use WHMCS\Exception;

/**
 * Description of ispcfg3
 *
 * @author crispy
 */
class ispcfg3 {

    protected $username;
    protected $password;
    protected $uri;
    protected $url;
    protected $session_id;
    
    public $client;
    public $jar;
    
    public function __construct( $base_uri, $base_url ) {

        $this->jar = new CookieJar;
        
        $this->uri = $base_uri;
        $this->url = $base_url;
        
        $this->client = new Client([
            'base_uri' => $this->uri,
            'timeout'  => 30,
            'cookies'  => $jar
        ]);
    }

    public function login( array $credentials ) {
        
        $response = $this->client->post( $this->url.'?login', [
            'json' => $credentials
        ]);
        
        logModuleCall(
            'provisioningmodule-login',
            __FUNCTION__,
            $response->getHeaders(),
            $response->getBody(),
            $response->getBody()
        );
        
        $result = self::check_login( $response );
        
        return $result;
    }
    
    public function logout() {
        $response = self::restpost( 'logout' );
        
        return $response;
    }

        public function check_login( $response ) {
        
        $body = json_decode( $response->getBody(), true );
        
        if ( $body['response'] == 'false' ) {
            
            
            logModuleCall(
            'login success',
            __FUNCTION__,
            $this->session_id,
            $this->session_id,
            $this->session_id
        );
            Throw new ErrorException('Login failed');
        } else {
            $this->session_id = $body['response'];
            logModuleCall(
            'login fail',
            __FUNCTION__,
            $this->session_id,
            $this->session_id,
            $this->session_id
        );
            return true;
        }
    }
    
    public function restpost( $action, array $data = null  ) {
        
        if ($data != null) {
            $data['session_id'] = $this->session_id;
                    
            logModuleCall(
            'provisioningmodule-post',
            __FUNCTION__,
            $data,
            $data,
            $data
        );
                    
            $response = $this->client->post( $this->url.'?'.$action, [
                'json' => $data
            ]);
        } else {
            $response = $this->client->post( $this->url.'?'.$action, [
                'json' => [ 'session_id' => $this->session_id ]
            ] );
        }
        

        $data = SELF::create_array( $response->getBody() );
        
        logModuleCall(
            'provisioningmodule-post',
            __FUNCTION__,
            $response,
            $response->getBody(),
            $response->getBody()
        );
        
        return $data;
    }
    
    public function create_array( $data ) {
        $arr = json_decode( $data, true );
        return $arr;
    }
    
    public function check_error( array $data ) {
        if (is_array($data)) {
            if($data['response'] == 'false') {
                 logModuleCall(
                    'check for error',
                __FUNCTION__,
                $data,
                $data,
                $data
            );
                Throw New Exception('Error');
            } else {
                return $data;
            }
        }
    }

    private function get_sessionid() {
        return ['session_id' => $this->session_id ];
    }
   
    public function client_add( $reseller_id, array $params ) {
        $reseller_id = [ 'reseller_id' => $reseller_id ];
        $reseller_id['params'] = $params;
        $result = self::restpost( 'client_add', $reseller_id );
        
        return $result;
    }
    
    public function client_change_password( $client_id, $new_password ) {
        $data  = [ 'client_id' => $client_id, 'new_password' => $new_password ];
        $result = self::restpost( 'client_change_password', $data );
        
        return $result;
    }


    public function client_delete_everything( $client_id ) {
        $data  = [ 'client_id' => $client_id ];
        $result = self::restpost( 'client_delete_everything', $data );
        
        return $result;
    }
    
    public function client_get( $client_id ) {
        $data = [ 'client_id' => $client_id ];
        $result = self::restpost( 'client_get', $data );
        
        return $result;
    }
    
    public function client_get_id( $sys_userid ) {
        $data = [ 'sys_userid' => $sys_userid ];
        $result = self::restpost( 'client_get_id', $data );
        
        return $result;
    }
    
    public function client_get_by_username( $client_id ) {
        $data = ['username' => $client_id ];
        $result = self::restpost( 'client_get_by_username', $data );
        
        return $result;
    }
    
    public function client_get_sites_by_user( $sys_userid, $sys_groupid ) {
        $data = [ 'sys_userid' => $sys_userid, 'sys_groupid' => $sys_groupid ];
        $result = self::restpost( 'client_get_sites_by_user', $data );
        
        return $result;
    }
    
    public function client_templates_get_all() {
        $result = self::restpost( 'client_templates_get_all', $data );
        
        return $result;
    }

    public function client_update( $client_id, $reseller_id, array $params ) {
        $data = [ 'client_id' => $client_id, 'reseller_id' => $reseller_id ];
        $data['params'] = $params;
        $result = self::restpost( 'client_update', $data );
        
        return $result;
    }

    public function dns_a_get( $primary_id ) {
        if ( !is_array( $primary_id ) ) {
            $data = [ 'primary_id' => $primary_id ];
        } else {
            $data = $primary_id;
        }
        $result = self::restpost( 'dns_a_get', $data );

        return $result;
    }

    public function dns_zone_get( $primary_id ) {
        if ( !is_array( $primary_id ) ) {
            $data = [ 'primary_id' => $primary_id ];
        } else {
            $data = $primary_id;
        }
        $result = self::restpost( 'dns_zone_get', $data );

        return $result;
    }


    public function domains_domain_add( $client_id, array $params ) {
        $client_id = [ 'client_id' => $client_id ];
        $client_id['params'] = $params;
        $result = self::restpost( 'domains_domain_add', $client_id );
        
        return $result;
    }
    
    public function domains_domain_delete(  $primary_id ) {
        $data = [ 'primary_id' => $primary_id ];
        $result = self::restpost( 'domains_domain_delete', $data );
        
        return $result;
    }
    
    public function domains_get_all_by_user( $group_id ) {
        $data = ['group_id' => $group_id];
        $result = self::restpost( 'domains_get_all_by_user', $data );
        
        return $result;
    }
    
    public function dns_templatezone_add( $client_id, $template_id, $domain, $ip, $ns1, $ns2, $email) {
        $data = [ 'client_id' => $client_id, 'template_id' => $template_id, 
                    'domain' => $domain, 'ip' => $ip,
                    'ns1' => $ns1, 'ns2' => $ns2, 'email' => $email
                ];
        $result = self::restpost( 'dns_templatezone_add', $data );
        
        return $result;
    }
    
    public function dns_zone_get_by_user( $client_id, $server_id ) {
        $data = [ 'client_id' => $client_id, 'server_id' => $server_id ];
        $result = self::restpost( 'dns_zone_get_by_user', $data );
        
        return $result;
    }
    
    public function dns_zone_set_status( $primary_id, $status ) {
        $data = [ 'primary_id' => $primary_id, 'status' => $status ];
        $result = self::restpost( 'dns_zone_set_status', $data );
        
        return $result;
    }
    
    public function mail_domain_add( $client_id, array $params ) {
        $data = [ 'client_id' => $client_id ];
        $data['params'] = $params;
        $result = self::restpost( 'mail_domain_add', $data );
        
        return $result;
    }
    
    public function mail_domain_get( $primary_id ) {
        if ( !is_array( $primary_id ) ) {
            $data = [ 'primary_id' => $primary_id ];
        } else {
            $data = $primary_id;
        }
        $result = self::restpost( 'mail_domain_get', $data );

        return $result;
    }


    public function mail_domain_get_by_domain( $domain ) {
        $data = [ 'domain' => $domain ];
        $result = self::restpost( 'mail_domain_get_by_domain', $data );
        
        return $result;
    }
    
    public function mail_domain_set_status( $primary_id, $status ) {
        $data = [ 'primary_id' => $primary_id, 'status' => $status ];
        $result = self::restpost( 'mail_domain_set_status', $data );
        
        return $result;
    }

    public function mail_forward_get( $primary_id ) {
        if ( !is_array( $primary_id ) ) {
            $data = [ 'primary_id' => $primary_id ];
        } else {
            $data = $primary_id;
        }
        $result = self::restpost( 'mail_forward_get', $data );

        return $result;
    }

    public function mail_user_add( $client_id, $params = null ) {
        if ( !is_array( $client_id ) ) {
            $data = [ 'client_id' => $client_id ];
            $data['params'] = $params;
        } else {
            $data = $client_id;
        }
        $result = self::restpost( 'mail_user_add', $data );

        return $result;
    }
    
    public function mail_user_delete( $primary_id ) {
        if ( !is_array( $primary_id ) ) {
            $data = [ 'primary_id' => $primary_id ];
            $data['params'] = $params;
        } else {
            $data = $primary_id;
        }
        $result = self::restpost( 'mail_user_delete', $data );

        return $result;
    }
    
    public function mail_user_get( $primary_id ) {
        if ( !is_array( $primary_id ) ) {
            $data = [ 'primary_id' => $primary_id ];
        } else {
            $data = $primary_id;
        }
        $result = self::restpost( 'mail_user_get', $data );

        return $result;
    }

    public function mail_user_update( $client_id, $primary_id, array $params = null ) {
        if ( !is_array( $client_id ) ) {
            $data = [ 'client_id' => $client_id, 'primary_id' => $primary_id ];
            $data['params'] = $params;
        } else {
            $data = $client_id;
        }
        $result = self::restpost( 'mail_user_update', $data );

        return $result;
    }
    
    public function mailquota_get_by_user( $primary_id ) {
        if ( !is_array( $primary_id ) ) {
            $data = [ 'primary_id' => $primary_id ];
        } else {
            $data = $primary_id;
        }
        $result = self::restpost( 'mailquota_get_by_user', $data );

        return $result;
    }
    
    public function sites_database_add( $client_id, array $params ) {
        $data = [ 'client_id' => $client_id ];
        $data['params'] = $params;
        $result = self::restpost( 'sites_database_add', $data );

        return $result;
    }

    public function server_get ( $server_id, $section = '' ) {
        $server_id = ['server' => $server_id, 'section' => $section ];
        $result = self::restpost( 'server_get', $server_id );
        
        return $result;
    }
    
    public function server_get_serverid_by_ip( $ipaddress ) {
        if ( !is_array( $ipaddress ) ) {
            $data = [ 'server_id' => $ipaddress ];
        } else {
            $data = $ipaddress;
        }
        $result = self::restpost( 'server_get_serverid_by_ip', $data );

        return $result;
    }

    public function server_ip_get( $primary_id ) {
        if ( !is_array( $primary_id ) ) {
            $data = [ 'server_id' => $primary_id ];
        } else {
            $data = $primary_id;
        }
        $result = self::restpost( 'server_ip_get', $data );

        return $result;
    }
    
    public function sites_cron_get( $cron_id ) {
        if ( !is_array( $primary_id ) ) {
            $data = [ 'cron_id' => $cron_id ];
        } else {
            $data = $cron_id;
        }
        $result = self::restpost( 'sites_cron_get', $data );

        return $result;
    }

    public function sites_database_get( $primary_id ) {
        if ( !is_array( $primary_id ) ) {
            $data = [ 'primary_id' => $primary_id ];
        } else {
            $data = $primary_id;
        }
        $result = self::restpost( 'sites_database_get', $data );

        return $result;
    }

    public function sites_database_user_get( $primary_id ) {
        if ( !is_array( $primary_id ) ) {
            $data = [ 'primary_id' => $primary_id ];
        } else {
            $data = $primary_id;
        }
        $result = self::restpost( 'sites_database_user_get', $data );

        return $result;
    }

    public function sites_database_user_add( $client_id, array $params ) {
        $data = [ 'client_id' => $client_id ];
        $data['params'] = $params;
        $result = self::restpost( 'sites_database_user_add', $data );

        return $result;
    }

    public function sites_ftp_user_add( $client_id, array $params ) {
        $data = [ 'client_id' => $client_id ];
        $data['params'] = $params;
        $result = self::restpost( 'sites_ftp_user_add', $data );

        return $result;
    }
    
    public function sites_ftp_user_get( $primary_id ) {
        if ( !is_array( $primary_id ) ) {
            $data = ['primary_id' => [ 'username' => $primary_id ] ];
        } else {
            $data = $primary_id;
        }
        $result = self::restpost( 'sites_ftp_user_get', $data );

        return $result;
    }
    
    public function sites_ftp_user_update( $client_id, $primary_id, array $params ) {
        $data = [ 'client_id' => $client_id, 'primary_id' => $primary_id ];
        $data['params'] = $params;
        $result = self::restpost( 'sites_ftp_user_update', $data );

        return $result;
    }
    
    public function sites_web_aliasdomain_get( $primary_id ) {
        if ( !is_array( $primary_id ) ) {
            $data = [ 'primary_id' => $primary_id ];
        } else {
            $data = $primary_id;
        }
        $result = self::restpost( 'sites_web_aliasdomain_get', $data );

        return $result;
    }


    public function sites_web_domain_add( $client_id, array $params, $readonly = false) {
        $data = [ 'client_id' => $client_id, 'readonly' => $readonly ];
        $data['params'] = $params;
        $result = self::restpost( 'sites_web_domain_add', $data );

        return $result;
    }
    
    public function sites_web_domain_delete( $primary_id ) {
        if ( !is_array( $primary_id ) ) {
            $data = [ 'primary_id' => $primary_id ];
        } else {
            $data = $primary_id;
        }
        $result = self::restpost( 'sites_web_domain_delete', $data );

        return $result;
    }
    
    public function sites_web_domain_get( $primary_id ) {
        if ( !is_array( $primary_id ) ) {
            $data = ['primary_id' => ['domain_id' => $website_id['response']]];
        } else {
            $data = $primary_id;
        }
        $result = self::restpost( 'sites_web_domain_get', $data );
        return $result;
    }
    
    public function sites_web_domain_set_status( $primary_id, $status ) {
        $data = [ 'primary_id' => $primary_id, 'status' => $status ];
        $result = self::restpost( 'sites_web_domain_set_status', $data );

        return $result;
    }
    
    public function sites_web_subdomain_get( $primary_id ) {
        if ( !is_array( $primary_id ) ) {
            $data = [ 'primary_id' => $primary_id ];
        } else {
            $data = $primary_id;
        }
        $result = self::restpost( 'sites_web_subdomain_get', $data );

        return $result;
    }
}
