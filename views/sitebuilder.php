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

global $variables;
if (isset($_GET['view_action'])) {

} else {
    
    $domains = cwispy_soap_request($params, 'sites_web_domain_get');
    $subdomains = cwispy_soap_request($params, 'sites_web_aliasdomain_get');
	$ftpuser = cwispy_soap_request($params, 'sites_ftp_user_get');
    if ( ( empty($params['configoption15']) ) && ( empty($params['configoption16']) ) ) {
        $sitepro['response']['sitepro']['enabled'] = '0';
    } else {
        $sitepro['response']['sitepro']['enabled'] = '1';
    }
    $return = array_merge_recursive($domains, $subdomains, $ftpuser, $sitepro);
	$theftpuser = $return["response"]["accounts"][0]["username"];
    $theftppass = $params['password'];
    $siteprouser = $params['configoption15'];
	$sitepropass = $params['configoption16'];

    if ($domains) {
        foreach($domains['response']['domains'] as $domain) {
            $return['response']['domains_processed'][$domain['domain_id']] = $domain;
        }
    }

    if (is_array($return['status'])) {
        $return['status'] = (in_array('error', $return['status'])) ? 'error' : 'success';
    }
}
$username = $params['username'];
$password = $params['password'];
$domain = $params["domain"];
$serverip = $params["serverip"];
$siteprouser = $params['configoption15'];
$sitepropass = $params['configoption16'];


include __DIR__.'/SiteProApiClient.php';

use Profis\SitePro\SiteProApiClient;

$builderError = null;
if (isset($_GET['editWebsite']) && $_GET['editWebsite']) {
	// get "your_api_username" and "our_api_password" from your license and enter them here
	// use this for premium/free licenses
	//$api = new SiteProApiClient("http://eu.site.pro/api/", $siteprouser, $sitepropass);
	$api = new SiteProApiClient('http://site.pro/api/', 'apikey0', '.r9doye/7FELEjutsxa..9yS/bJDUGew6zD/sJBHOLWV9gu.');
	// use this for enterprise licenses and change 'your-bulder-domain.com' to your builder domain
	//$api = new SiteProApiClient('http://your-bulder-domain.com/api/', 'your_api_username', 'your_api_password');

	try {
		// this call is used to open builder, so you need to set correct parameters to represent users website you want to open
		// this data usually comes from your user/hosting manager system
		$res = $api->remoteCall('requestLogin', array(
			'type' => 'external',				////(required) 'external'
			'domain' => "$domain",				// (required) domain of the user website you want to edit
			'lang' => 'en',						// (optional) 2-letter language code, set language code you whant builder to open in
			'username' => "$theftpuser",		// (required) user websites FTP username
			'password' => "$theftppass",	// (required) user websites FTP password
			//'apiUrl' => "$serverip",				// (required) user websites FTP server IP address
			'apiUrl' => "$domain",
			'uploadDir' => '/web/',		// (required) user websites FTP folder where website files need to be uploaded to
			'hostingPlan' => '',	// (optional) hosting plan that user uses
			'panel' => ''			// (optional) user/hosting management panel name that this script will be run in
		));
		if (!$res || !is_object($res)) {
			// handle errors
			throw new ErrorException('Response format error');
		} else if (isset($res->url) && $res->url) {
			// on success redirect to builder URL
			header('Location: '.$res->url, true);
			exit();
		} else {
			// handle errors
			throw new ErrorException((isset($res->error->message) && $res->error->message) ? $res->error->message : 'Unknown error');
		}
	} catch(ErrorException $ex) {
		// handle errors
		$builderError = 'Request error: '.$ex->getMessage();
    }
}