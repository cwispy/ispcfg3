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
if(file_exists(dirname(dirname(dirname(dirname(__FILE__)))) .'/init.php')){
    require(dirname(dirname(dirname(dirname(__FILE__)))) .'/init.php');
}

include('functions/base.php');

error_reporting(0); // Set E_ALL for debuging

global $CONFIG;

$whmcsurl = ($CONFIG['SystemSSLURL']) ?  $CONFIG['SystemSSLURL'] : $CONFIG['SystemURL'];

include_once ELFINDER_DIR.'php'.DIRECTORY_SEPARATOR.'elFinderConnector.class.php';
include_once ELFINDER_DIR.'php'.DIRECTORY_SEPARATOR.'elFinder.class.php';
include_once ELFINDER_DIR.'php'.DIRECTORY_SEPARATOR.'elFinderVolumeDriver.class.php';
include_once ELFINDER_DIR.'php'.DIRECTORY_SEPARATOR.'elFinderVolumeFTP.class.php';
include_once ELFINDER_DIR.'php'.DIRECTORY_SEPARATOR.'elFinderVolumeLocalFileSystem.class.php';

$ftpDataRaw = @$_REQUEST['ftp'];
if ($ftpDataRaw) {
    $ftpData = json_decode(base64_decode($ftpDataRaw),true);
}

if (isset($ftpData) && $ftpData) {
	//$ip = gethostbyname($ftpData['host']);
    $opts = array(
        // 'debug' => true,
             'roots' => array(
            array(
                'driver' => 'FTP',
				//'host'   => '',
				// 'user'   => '',
               // 'pass'   => '',
				'host'   => $ftpData['host'],
                'user'   => $ftpData['user'],
                'pass'   => $ftpData['pass'],
				'uploadMaxSize' => '2M',
				'alias'      => 'Home',
				'imgLib'       => 'auto',
				'mimeDetect' => 'internal',
			    'fileMode'     => 0644,         // new files mode
				'dirMode'      => 0755,
				'uploadAllow'   => array('all'),// Mimetype `image` and `text/plain` allowed to upload
				'uploadOrder'   => array('deny', 'allow'),      // allowed Mimetype `image` and `text/plain` only
				// 'uploadAllow' => array('image'), # allow any images
				'dragUploadAllow' => 'auto',
				'mode'          => 'passive',
				'tmbPath'       => '/tmp2/',
				'tmbURL'       => '/tmp2/',
				'tmpPath'       => '/tmp/',
				'tmbCrop'    => false,
				'owner'         => true,
                'path'   => '/web/'
            )
        )
    );

    // run elFinder
    $connector = new elFinderConnector(new elFinder($opts));
    $connector->run();
}