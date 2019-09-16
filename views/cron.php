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
if (isset($_GET['view_action'])) {
    if ($_GET['view_action'] == 'add') {
        if (!isset($_REQUEST['minute_val']) || NULL == $_REQUEST['minute_val']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Minute is required'));
        }
        if (!isset($_REQUEST['hour_val']) || NULL == $_REQUEST['hour_val']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Hour is required'));
        }
        if (!isset($_REQUEST['day_val']) || !$_REQUEST['day_val']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Day is required'));
        }
        if (!isset($_REQUEST['month_val']) || !$_REQUEST['month_val']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Month is required'));
        }
        if (!isset($_REQUEST['weekday_val']) || NULL == $_REQUEST['weekday_val']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Weekday is required'));
        }
        if (!isset($_REQUEST['command']) || !$_REQUEST['command']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Command is required'));
        }

        $options = array(
            'server_id' => $_REQUEST['server_id'],
            'command' => $_REQUEST['command'],
            'run_min' => $_REQUEST['minute_val'],
            'run_hour' => $_REQUEST['hour_val'],
            'run_mday' => $_REQUEST['day_val'],
            'run_month' => $_REQUEST['month_val'],
            'run_wday' => $_REQUEST['weekday_val'],
            'active' => 'y'
        );

        $create = cwispy_api_request($params, 'sites_cron_add', $options);
        if ($create['status'] == 'success') {
            cwispy_return_ajax_response(array('status' => 'success', 'message' => 'Cron job created successfully'));
        }
        else {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => $create['response']));
        }
    }
    elseif ($_GET['view_action'] == 'edit') {
        if (!isset($_REQUEST['minute_val']) || NULL == $_REQUEST['minute_val']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Minute is required'));
        }
        if (!isset($_REQUEST['hour_val']) || NULL == $_REQUEST['hour_val']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Hour is required'));
        }
        if (!isset($_REQUEST['day_val']) || !$_REQUEST['day_val']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Day is required'));
        }
        if (!isset($_REQUEST['month_val']) || !$_REQUEST['month_val']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Month is required'));
        }
        if (!isset($_REQUEST['weekday_val']) || NULL == $_REQUEST['weekday_val']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Weekday is required'));
        }
        if (!isset($_REQUEST['command']) || !$_REQUEST['command']) {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => 'Command is required'));
        }

        $options = array(
            'id' => $_REQUEST['cron_id'],
            'server_id' => $_REQUEST['server_id'],
            'command' => $_REQUEST['command'],
            'run_min' => $_REQUEST['minute_val'],
            'run_hour' => $_REQUEST['hour_val'],
            'run_mday' => $_REQUEST['day_val'],
            'run_month' => $_REQUEST['month_val'],
            'run_wday' => $_REQUEST['weekday_val'],
            'active' => 'y'
        );

        $update = cwispy_api_request($params, 'sites_cron_update', $options);
        if ($update['status'] == 'success') {
            cwispy_return_ajax_response(array('status' => 'success', 'message' => 'Cron job updated successfully'));
        }
        else {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => $update['response']));
        }
    }
    elseif ($_GET['view_action'] == 'delete') {
        $options = array(
            'id' => $_REQUEST['cron_id']
        );

        $delete = cwispy_api_request($params, 'sites_cron_delete', $options);
        if ($delete['status'] == 'success') {
            cwispy_return_ajax_response(array('status' => 'success', 'message' => 'Cron job deleted successfully'));
        }
        else {
            cwispy_return_ajax_response(array('status' => 'error', 'message' => $delete['response']));
        }
    }
    else {
        cwispy_return_ajax_response(array('status' => 'error', 'message' => 'View action missing'));
    }
}
else {
    $client = cwispy_api_request($params, 'client_get');
    $crons  = cwispy_api_request($params, 'sites_cron_get');
    $return = array_merge_recursive($crons, $client);
    
    if (is_array($return['status'])) {
        $return['status'] = (in_array('error', $return['status'])) ? 'error' : 'success';
    }

    $return['action_urls']['add'] = cwispy_create_url(array('view' => 'cron', 'view_action' => 'add'));
    $return['action_urls']['edit'] = cwispy_create_url(array('view' => 'cron', 'view_action' => 'edit'));
    $return['action_urls']['delete'] = cwispy_create_url(array('view' => 'cron', 'view_action' => 'delete'));
}