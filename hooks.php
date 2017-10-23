<?php
/*
 *  ISPConfig v3.1+ module for WHMCS v6.x or Higher
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
add_hook('ClientAreaSidebars', 1, function($vars) {
    if (function_exists('cwispy_handle_view')) {
        $currentRequest = $_GET;

        $primarySidebar = Menu::primarySidebar();
        $secondarySidebar = Menu::secondarySidebar();
		//$submodsettings        = explode( ',',$params['configoption11'] );
		//$submodsettings[0]

        $accountMenu = $primarySidebar->addChild(
            'iHost ISPConfig Account Nav',
            array(
                'label' => 'Your Account',
                'order' => 10,
                'icon' => 'fa-cogs',
            )
        );

        $accountMenu->addChild('Overview')
            ->setUri(cwispy_create_url(array('view' => 'overview')))
            ->setLabel('Overview')
            ->setOrder(1)
			->setIcon('fa-street-view')
            ->setClass(cwispy_get_menu_item_class($currentRequest, array('view' => 'overview')));

	
		$accountMenu->addChild('Site Builder')
            ->setUri(cwispy_create_url(array('view' => 'sitebuilder')))
            ->setLabel('Site Builder')
            ->setOrder(1)
			->setIcon('fa-pencil-square-o')
            ->setClass(cwispy_get_menu_item_class($currentRequest, array('view' => 'sitebuilder')));
	
			
        $accountMenu->addChild('File Manager')
            ->setUri(cwispy_create_url(array('view' => 'file-manager')))
            ->setLabel('File Manager')
            ->setOrder(3)
			->setIcon('fa-file-o')
            ->setClass(cwispy_get_menu_item_class($currentRequest, array('view' => 'file-manager')));
	
		
        $accountMenu->addChild('Emails')
            ->setUri(cwispy_create_url(array('view' => 'emails')))
            ->setLabel('Emails')
            ->setOrder(4)
			->setIcon('fa-envelope-o')
            ->setClass(cwispy_get_menu_item_class($currentRequest, array('view' => 'emails')));
		
        
        $accountMenu->addChild('Email Forwarders')
            ->setUri(cwispy_create_url(array('view' => 'email-forwarders')))
            ->setLabel('Email Forwarders')
            ->setOrder(5)
			->setIcon('fa-share')
            ->setClass(cwispy_get_menu_item_class($currentRequest, array('view' => 'email-forwarders')));
		
        
        $accountMenu->addChild('FTP Accounts')
            ->setUri(cwispy_create_url(array('view' => 'ftp-accounts')))
            ->setLabel('FTP Accounts')
            ->setOrder(6)
			->setIcon('fa-exchange')
            ->setClass(cwispy_get_menu_item_class($currentRequest, array('view' => 'ftp-accounts')));
		
        
        $accountMenu->addChild('Databases')
            ->setUri(cwispy_create_url(array('view' => 'databases')))
            ->setLabel('Databases')
            ->setOrder(7)
			->setIcon('fa-database')
            ->setClass(cwispy_get_menu_item_class($currentRequest, array('view' => 'databases')));
			
        
        $accountMenu->addChild('Alias Domains')
            ->setUri(cwispy_create_url(array('view' => 'aliasdomains')))
            ->setLabel('Alias Domains')
            ->setOrder(8)
			->setIcon('fa-plus-circle')
            ->setClass(cwispy_get_menu_item_class($currentRequest, array('view' => 'aliasdomains')));
	
        
        $accountMenu->addChild('Sub Domains')
            ->setUri(cwispy_create_url(array('view' => 'subdomains')))
            ->setLabel('Sub Domains')
            ->setOrder(9)
			->setIcon('fa-sitemap')
            ->setClass(cwispy_get_menu_item_class($currentRequest, array('view' => 'subdomains')));
		
        
        $accountMenu->addChild('Cron Jobs')
            ->setUri(cwispy_create_url(array('view' => 'cron')))
            ->setLabel('Cron Jobs')
            ->setOrder(10)
			->setIcon('fa-calendar')
            ->setClass(cwispy_get_menu_item_class($currentRequest, array('view' => 'cron')));
		
        
        $accountMenu->addChild('DNS Records')
            ->setUri(cwispy_create_url(array('view' => 'dns')))
            ->setLabel('DNS Records')
            ->setOrder(11)
			->setIcon('fa-compress')
            ->setClass(cwispy_get_menu_item_class($currentRequest, array('view' => 'dns')));
			
        
		$accountMenu->addChild('Usage Statistics')
            ->setUri(cwispy_create_url(array('view' => 'usage')))
            ->setLabel('Usage Statistics')
            ->setOrder(12)
			->setIcon('fa-area-chart')
            ->setClass(cwispy_get_menu_item_class($currentRequest, array('view' => 'usage')));
	
	
        $loginMenu = $primarySidebar->addChild(
            'iHost ISPConfig Login Nav',
            array(
                'label' => 'Login To',
                'order' => 11,
                'icon' => 'fa-lock',
            )
        );
		
        $loginMenu->addChild('CP Login')
            ->setUri(cwispy_create_url(array('view' => 'login', 'view_action' => 'ispconfig')))
            ->setLabel('Control Panel')
            ->setOrder(1)
			->setIcon('fa-lock')
            ->setAttribute('target', '_blank');
	
		
		$loginMenu->addChild('View Statistics')
            ->setUri(cwispy_create_url(array('view' => 'login', 'view_action' => 'stats')))
            ->setLabel('Website Statistics')
            ->setOrder(2)
			->setIcon('fa-bar-chart')
            ->setAttribute('target', '_blank');
	
	
        $loginMenu->addChild('Webmail')
            ->setUri(cwispy_create_url(array('view' => 'login', 'view_action' => 'webmail')))
            ->setLabel('Webmail')
            ->setOrder(3)
			->setIcon('fa-envelope')
            ->setAttribute('target', '_blank');
	
        
        $loginMenu->addChild('phpMyAdmin')
            ->setUri(cwispy_create_url(array('view' => 'login', 'view_action' => 'phpmyadmin')))
            ->setLabel('phpMyAdmin')
            ->setOrder(4)
			->setIcon('fa-database')
            ->setAttribute('target', '_blank');
    
	}
});

add_hook('ClientAreaFooterOutput', 1, function($vars) {
    if (function_exists('cwispy_handle_view')) {
        $script = '
        <script type="text/javascript" src="'.$vars['WEB_ROOT'].'/modules/servers/ispcfg3/assets/js/ajax.js"></script>
        <script type="text/javascript" src="'.$vars['WEB_ROOT'].'/modules/servers/ispcfg3/assets/js/script.js"></script>
        ';
        return $script;
    }
});