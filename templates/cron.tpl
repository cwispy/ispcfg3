{*
 /*  ISPConfig v3.1+ module for WHMCS v6.x or Higher
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
 */
 *}
<link href="modules/servers/ispcfg3a/assets/ispcfg3.css" rel="stylesheet"><span class="icon-header icon-cron"></span><h3>Manage Cron Jobs</h3><p>Cron jobs allow you to automate certain commands or scripts on your site. You can set a command or script to run at a specific time every day, week or month </p><p>If you are unsure of how to create one contact support to set it up for you.</p>
<hr>
<div class="text-right">
    <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalAdd"
    {If $variables.client.locked == "y" || $variables.client.canceled == "y"}
        disabled="disabled"
    {/If}
    >Add Cron Job</button>
</div>
{assign "server_id" "{$variables.servers[0].server_id}"}
{if is_array($variables.crons) && count($variables.crons) > 0}
    <table class="table table-condensed table-striped table-hover ihost-smart-table">
        <thead><tr><th>Command</th><th>Minute</th><th>Hour</th><th>Day</th><th>Month</th><th>Weekday</th><th>&nbsp;</th></tr></thead>
        <tbody>
        {foreach $variables.crons as $cron}
            <tr>
                <td>{$cron.command}</td>
                <td>{$cron.run_min}</td>
                <td>{$cron.run_hour}</td>
                <td>{$cron.run_mday}</td>
                <td>{$cron.run_month}</td>
                <td>{$cron.run_wday}</td>
                <td class="text-right">
                {If $variables.client.locked == "y" || $variables.client.canceled == "y"}
                    <i class="fa fa-ban"></i>
                {else}
                    <a href="javascript:;" class="btn btn-xs btn-default" id="btnAction" data-toggle="modal" data-target="#modalEdit" data-target-values="cron_id={$cron.id}&minute_val={$cron.run_min}&hour_val={$cron.run_hour}&day_val={$cron.run_mday}&month_val={$cron.run_month}&weekday_val={$cron.run_wday}&command={$cron.command}"><i class="fa fa-pencil"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-default" id="btnAction" data-toggle="modal" data-target="#modalDelete" data-target-values="cron_id={$cron.id}"><i class="fa fa-times"></i></a>
                {/If}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
{else}
    <p>No cron jobs found</p>
{/if}

<div class="modal fade" id="modalAdd" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Add Cron Job</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal ajax-form" id="frmAdd">
                    <div id="ajax-params" data-action="{$action_urls.add}" data-method="POST" data-loader="#ajax-loader-add" data-loader-position="outside" data-loader-type="inside-button" data-messages="#ajax-messages" data-callback-on-success="window.location.reload()"></div>
                    <div id="ajax-messages"></div>
                    <input type="hidden" name="server_id" value="{$server_id}">

                    <div class="form-group">
                        <label for="common_settings" class="col-sm-4 control-label">Common Settings</label>
                        <div class="col-sm-6">
                            <select class="form-control" id="common_settings" data-group="add">
                                <option value="">-- Common Settings --</option>
                                <option value="* * * * *">Every minute (* * * * *)</option>
                                <option value="*/5 * * * *">Every 5 minutes (*/5 * * * *)</option>
                                <option value="0,30 * * * *">Twice an hour (0,30 * * * *)</option>
                                <option value="0 * * * *">Once an hour (0 * * * *)</option>
                                <option value="0 0,12 * * *">Twice a day (0 0,12 * * *)</option>
                                <option value="0 0 * * *">Once a day (0 0 * * *)</option>
                                <option value="0 0 * * 0">Once a week (0 0 * * 0)</option>
                                <option value="0 0 1,15 * *">1st and 15th (0 0 1,15 * *)</option>
                                <option value="0 0 1 * *">Once a month (0 0 1 * *)</option>
                                <option value="0 0 1 1 *">Once a year (0 0 1 1 *)</option>  
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="minute_val" class="col-sm-4 control-label">Minute</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" name="minute_val" id="minute_val">
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" name="minute_freq" id="minute_freq">
                                <option value="">-- Common Settings --</option>
                                <option value="*">Every minute (*)</option>
                                <option value="*/2">Every other minute (*/2)</option>
                                <option value="*/5">Every 5 minutes (*/5)</option>
                                <option value="*/10">Every 10 minutes (*/10)</option>
                                <option value="*/15">Every 15 minutes (*/15)</option>
                                <option value="0,30">Every 30 minutes (0,30)</option>
                                <option value="">-- Minutes --</option>
                                <option value="0">:00 top of the hour (0)</option>
                                <option value="1">:01 (1)</option>
                                <option value="2">:02 (2)</option>
                                <option value="3">:03 (3)</option>
                                <option value="4">:04 (4)</option>
                                <option value="5">:05 (5)</option>
                                <option value="6">:06 (6)</option>
                                <option value="7">:07 (7)</option>
                                <option value="8">:08 (8)</option>
                                <option value="9">:09 (9)</option>
                                <option value="10">:10 (10)</option>
                                <option value="11">:11 (11)</option>
                                <option value="12">:12 (12)</option>
                                <option value="13">:13 (13)</option>
                                <option value="14">:14 (14)</option>
                                <option value="15">:15 quarter past (15)</option>
                                <option value="16">:16 (16)</option>
                                <option value="17">:17 (17)</option>
                                <option value="18">:18 (18)</option>
                                <option value="19">:19 (19)</option>
                                <option value="20">:20 (20)</option>
                                <option value="21">:21 (21)</option>
                                <option value="22">:22 (22)</option>
                                <option value="23">:23 (23)</option>
                                <option value="24">:24 (24)</option>
                                <option value="25">:25 (25)</option>
                                <option value="26">:26 (26)</option>
                                <option value="27">:27 (27)</option>
                                <option value="28">:28 (28)</option>
                                <option value="29">:29 (29)</option>
                                <option value="30">:30 half past (30)</option>
                                <option value="31">:31 (31)</option>
                                <option value="32">:32 (32)</option>
                                <option value="33">:33 (33)</option>
                                <option value="34">:34 (34)</option>
                                <option value="35">:35 (35)</option>
                                <option value="36">:36 (36)</option>
                                <option value="37">:37 (37)</option>
                                <option value="38">:38 (38)</option>
                                <option value="39">:39 (39)</option>
                                <option value="40">:40 (40)</option>
                                <option value="41">:41 (41)</option>
                                <option value="42">:42 (42)</option>
                                <option value="43">:43 (43)</option>
                                <option value="44">:44 (44)</option>
                                <option value="45">:45 quarter till (45)</option>
                                <option value="46">:46 (46)</option>
                                <option value="47">:47 (47)</option>
                                <option value="48">:48 (48)</option>
                                <option value="49">:49 (49)</option>
                                <option value="50">:50 (50)</option>
                                <option value="51">:51 (51)</option>
                                <option value="52">:52 (52)</option>
                                <option value="53">:53 (53)</option>
                                <option value="54">:54 (54)</option>
                                <option value="55">:55 (55)</option>
                                <option value="56">:56 (56)</option>
                                <option value="57">:57 (57)</option>
                                <option value="58">:58 (58)</option>
                                <option value="59">:59 (59)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="hour_val" class="col-sm-4 control-label">Hour</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" name="hour_val" id="hour_val">
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" name="hour_freq" id="hour_freq">
                                <option value="">-- Common Settings --</option>
                                <option value="*"> (*)</option>
                                <option value="*/2"> (*/2)</option>
                                <option value="*/3"> (*/3)</option>
                                <option value="*/4"> (*/4)</option>
                                <option value="*/6"> (*/6)</option>
                                <option value="0,12"> (0,12)</option>

                                <option value="">-- Hours --</option>
                                <option value="0">12:00 a.m. midnight (0)</option>
                                <option value="1">1:00 a.m. (1)</option>
                                <option value="2">2:00 a.m. (2)</option>
                                <option value="3">3:00 a.m. (3)</option>
                                <option value="4">4:00 a.m. (4)</option>
                                <option value="5">5:00 a.m. (5)</option>

                                <option value="6">6:00 a.m. (6)</option>
                                <option value="7">7:00 a.m. (7)</option>
                                <option value="8">8:00 a.m. (8)</option>
                                <option value="9">9:00 a.m. (9)</option>
                                <option value="10">10:00 a.m. (10)</option>
                                <option value="11">11:00 a.m. (11)</option>

                                <option value="12">12:00 p.m. noon (12)</option>
                                <option value="13">1:00 p.m. (13)</option>
                                <option value="14">2:00 p.m. (14)</option>
                                <option value="15">3:00 p.m. (15)</option>
                                <option value="16">4:00 p.m. (16)</option>
                                <option value="17">5:00 p.m. (17)</option>

                                <option value="18">6:00 p.m. (18)</option>
                                <option value="19">7:00 p.m. (19)</option>
                                <option value="20">8:00 p.m. (20)</option>
                                <option value="21">9:00 p.m. (21)</option>
                                <option value="22">10:00 p.m. (22)</option>
                                <option value="23">11:00 p.m. (23)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="day_val" class="col-sm-4 control-label">Day</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" name="day_val" id="day_val">
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" name="day_freq" id="day_freq">
                                <option value="">-- Common Settings --</option>
                                <option value="*">Every day (*)</option>
                                <option value="*/2">Every other day (*/2)</option>
                                <option value="1,15">1st and 15th (1,15)</option>
                                <option value="">-- Days --</option>

                                <option value="1">1st (1)</option>
                                <option value="2">2nd (2)</option>
                                <option value="3">3rd (3)</option>
                                <option value="4">4th (4)</option>
                                <option value="5">5th (5)</option>
                                <option value="6">6th (6)</option>

                                <option value="7">7th (7)</option>
                                <option value="8">8th (8)</option>
                                <option value="9">9th (9)</option>
                                <option value="10">10th (10)</option>
                                <option value="11">11th (11)</option>
                                <option value="12">12th (12)</option>

                                <option value="13">13th (13)</option>
                                <option value="14">14th (14)</option>
                                <option value="15">15th (15)</option>
                                <option value="16">16th (16)</option>
                                <option value="17">17th (17)</option>
                                <option value="18">18th (18)</option>

                                <option value="19">19th (19)</option>
                                <option value="20">20th (20)</option>
                                <option value="21">21st (21)</option>
                                <option value="22">22nd (22)</option>
                                <option value="23">23rd (23)</option>
                                <option value="24">24th (24)</option>

                                <option value="25">25th (25)</option>
                                <option value="26">26th (26)</option>
                                <option value="27">27th (27)</option>
                                <option value="28">28th (28)</option>
                                <option value="29">29th (29)</option>
                                <option value="30">30th (30)</option>

                                <option value="31">31st (31)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="month_val" class="col-sm-4 control-label">Month</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" name="month_val" id="month_val">
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" name="month_freq" id="month_freq">
                                <option value="">-- Common Settings --</option>
                                <option value="*">Every month (*)</option>
                                <option value="*/2">Every other month (*/2)</option>
                                <option value="*/4">Every 3 months (*/4)</option>
                                <option value="1,7">Every 6 months (1,7)</option>

                                <option value="">-- Months --</option>
                                <option value="1">January (1)</option>
                                <option value="2">February (2)</option>
                                <option value="3">March (3)</option>
                                <option value="4">April (4)</option>
                                <option value="5">May (5)</option>

                                <option value="6">June (6)</option>
                                <option value="7">July (7)</option>
                                <option value="8">August (8)</option>
                                <option value="9">September (9)</option>
                                <option value="10">October (10)</option>
                                <option value="11">November (11)</option>

                                <option value="12">December (12)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="weekday_val" class="col-sm-4 control-label">Weekday</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" name="weekday_val" id="weekday_val">
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" name="weekday_freq" id="weekday_freq">
                                <option value="">-- Common Settings --</option>
                                <option value="*">Every weekday (*)</option>
                                <option value="1-5">Mon till Fri (1-5)</option>
                                <option value="0,6">Sat and Sun (6,0)</option>
                                <option value="1,3,5">Mon, Wed, Fri (1,3,5)</option>

                                <option value="2,4">Tues, Thurs (2,4)</option>
                                <option value="">-- Weekdays --</option>
                                <option value="0">Sunday (0)</option>
                                <option value="1">Monday (1)</option>
                                <option value="2">Tuesday (2)</option>
                                <option value="3">Wednesday (3)</option>

                                <option value="4">Thursday (4)</option>
                                <option value="5">Friday (5)</option>
                                <option value="6">Saturday (6)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="command" class="col-sm-4 control-label">Command</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="command" id="command">
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button class="btn btn-success" onclick="$('#frmAdd').submit()"><span id="ajax-loader-add"></span> Create Cron Job</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Update Cron Job</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal ajax-form" id="frmEdit">
                    <div id="ajax-params" data-action="{$action_urls.edit}" data-method="POST" data-loader="#ajax-loader-edit" data-loader-position="outside" data-loader-type="inside-button" data-messages="#ajax-messages" data-callback-on-success="window.location.reload()"></div>
                    <div id="ajax-messages"></div>
                    <input type="hidden" name="server_id" value="{$server_id}">
                    <input type="hidden" name="cron_id" id="cron_id">

                    <div class="form-group">
                        <label for="common_settings" class="col-sm-4 control-label">Common Settings</label>
                        <div class="col-sm-6">
                            <select class="form-control" id="common_settings" data-group="add">
                                <option value="">-- Common Settings --</option>
                                <option value="* * * * *">Every minute (* * * * *)</option>
                                <option value="*/5 * * * *">Every 5 minutes (*/5 * * * *)</option>
                                <option value="0,30 * * * *">Twice an hour (0,30 * * * *)</option>
                                <option value="0 * * * *">Once an hour (0 * * * *)</option>
                                <option value="0 0,12 * * *">Twice a day (0 0,12 * * *)</option>
                                <option value="0 0 * * *">Once a day (0 0 * * *)</option>
                                <option value="0 0 * * 0">Once a week (0 0 * * 0)</option>
                                <option value="0 0 1,15 * *">1st and 15th (0 0 1,15 * *)</option>
                                <option value="0 0 1 * *">Once a month (0 0 1 * *)</option>
                                <option value="0 0 1 1 *">Once a year (0 0 1 1 *)</option>  
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="minute_val" class="col-sm-4 control-label">Minute</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" name="minute_val" id="minute_val">
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" name="minute_freq" id="minute_freq">
                                <option value="">-- Common Settings --</option>
                                <option value="*">Every minute (*)</option>
                                <option value="*/2">Every other minute (*/2)</option>
                                <option value="*/5">Every 5 minutes (*/5)</option>
                                <option value="*/10">Every 10 minutes (*/10)</option>
                                <option value="*/15">Every 15 minutes (*/15)</option>
                                <option value="0,30">Every 30 minutes (0,30)</option>
                                <option value="">-- Minutes --</option>
                                <option value="0">:00 top of the hour (0)</option>
                                <option value="1">:01 (1)</option>
                                <option value="2">:02 (2)</option>
                                <option value="3">:03 (3)</option>
                                <option value="4">:04 (4)</option>
                                <option value="5">:05 (5)</option>
                                <option value="6">:06 (6)</option>
                                <option value="7">:07 (7)</option>
                                <option value="8">:08 (8)</option>
                                <option value="9">:09 (9)</option>
                                <option value="10">:10 (10)</option>
                                <option value="11">:11 (11)</option>
                                <option value="12">:12 (12)</option>
                                <option value="13">:13 (13)</option>
                                <option value="14">:14 (14)</option>
                                <option value="15">:15 quarter past (15)</option>
                                <option value="16">:16 (16)</option>
                                <option value="17">:17 (17)</option>
                                <option value="18">:18 (18)</option>
                                <option value="19">:19 (19)</option>
                                <option value="20">:20 (20)</option>
                                <option value="21">:21 (21)</option>
                                <option value="22">:22 (22)</option>
                                <option value="23">:23 (23)</option>
                                <option value="24">:24 (24)</option>
                                <option value="25">:25 (25)</option>
                                <option value="26">:26 (26)</option>
                                <option value="27">:27 (27)</option>
                                <option value="28">:28 (28)</option>
                                <option value="29">:29 (29)</option>
                                <option value="30">:30 half past (30)</option>
                                <option value="31">:31 (31)</option>
                                <option value="32">:32 (32)</option>
                                <option value="33">:33 (33)</option>
                                <option value="34">:34 (34)</option>
                                <option value="35">:35 (35)</option>
                                <option value="36">:36 (36)</option>
                                <option value="37">:37 (37)</option>
                                <option value="38">:38 (38)</option>
                                <option value="39">:39 (39)</option>
                                <option value="40">:40 (40)</option>
                                <option value="41">:41 (41)</option>
                                <option value="42">:42 (42)</option>
                                <option value="43">:43 (43)</option>
                                <option value="44">:44 (44)</option>
                                <option value="45">:45 quarter till (45)</option>
                                <option value="46">:46 (46)</option>
                                <option value="47">:47 (47)</option>
                                <option value="48">:48 (48)</option>
                                <option value="49">:49 (49)</option>
                                <option value="50">:50 (50)</option>
                                <option value="51">:51 (51)</option>
                                <option value="52">:52 (52)</option>
                                <option value="53">:53 (53)</option>
                                <option value="54">:54 (54)</option>
                                <option value="55">:55 (55)</option>
                                <option value="56">:56 (56)</option>
                                <option value="57">:57 (57)</option>
                                <option value="58">:58 (58)</option>
                                <option value="59">:59 (59)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="hour_val" class="col-sm-4 control-label">Hour</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" name="hour_val" id="hour_val">
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" name="hour_freq" id="hour_freq">
                                <option value="">-- Common Settings --</option>
                                <option value="*"> (*)</option>
                                <option value="*/2"> (*/2)</option>
                                <option value="*/3"> (*/3)</option>
                                <option value="*/4"> (*/4)</option>
                                <option value="*/6"> (*/6)</option>
                                <option value="0,12"> (0,12)</option>

                                <option value="">-- Hours --</option>
                                <option value="0">12:00 a.m. midnight (0)</option>
                                <option value="1">1:00 a.m. (1)</option>
                                <option value="2">2:00 a.m. (2)</option>
                                <option value="3">3:00 a.m. (3)</option>
                                <option value="4">4:00 a.m. (4)</option>
                                <option value="5">5:00 a.m. (5)</option>

                                <option value="6">6:00 a.m. (6)</option>
                                <option value="7">7:00 a.m. (7)</option>
                                <option value="8">8:00 a.m. (8)</option>
                                <option value="9">9:00 a.m. (9)</option>
                                <option value="10">10:00 a.m. (10)</option>
                                <option value="11">11:00 a.m. (11)</option>

                                <option value="12">12:00 p.m. noon (12)</option>
                                <option value="13">1:00 p.m. (13)</option>
                                <option value="14">2:00 p.m. (14)</option>
                                <option value="15">3:00 p.m. (15)</option>
                                <option value="16">4:00 p.m. (16)</option>
                                <option value="17">5:00 p.m. (17)</option>

                                <option value="18">6:00 p.m. (18)</option>
                                <option value="19">7:00 p.m. (19)</option>
                                <option value="20">8:00 p.m. (20)</option>
                                <option value="21">9:00 p.m. (21)</option>
                                <option value="22">10:00 p.m. (22)</option>
                                <option value="23">11:00 p.m. (23)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="day_val" class="col-sm-4 control-label">Day</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" name="day_val" id="day_val">
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" name="day_freq" id="day_freq">
                                <option value="">-- Common Settings --</option>
                                <option value="*">Every day (*)</option>
                                <option value="*/2">Every other day (*/2)</option>
                                <option value="1,15">1st and 15th (1,15)</option>
                                <option value="">-- Days --</option>

                                <option value="1">1st (1)</option>
                                <option value="2">2nd (2)</option>
                                <option value="3">3rd (3)</option>
                                <option value="4">4th (4)</option>
                                <option value="5">5th (5)</option>
                                <option value="6">6th (6)</option>

                                <option value="7">7th (7)</option>
                                <option value="8">8th (8)</option>
                                <option value="9">9th (9)</option>
                                <option value="10">10th (10)</option>
                                <option value="11">11th (11)</option>
                                <option value="12">12th (12)</option>

                                <option value="13">13th (13)</option>
                                <option value="14">14th (14)</option>
                                <option value="15">15th (15)</option>
                                <option value="16">16th (16)</option>
                                <option value="17">17th (17)</option>
                                <option value="18">18th (18)</option>

                                <option value="19">19th (19)</option>
                                <option value="20">20th (20)</option>
                                <option value="21">21st (21)</option>
                                <option value="22">22nd (22)</option>
                                <option value="23">23rd (23)</option>
                                <option value="24">24th (24)</option>

                                <option value="25">25th (25)</option>
                                <option value="26">26th (26)</option>
                                <option value="27">27th (27)</option>
                                <option value="28">28th (28)</option>
                                <option value="29">29th (29)</option>
                                <option value="30">30th (30)</option>

                                <option value="31">31st (31)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="month_val" class="col-sm-4 control-label">Month</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" name="month_val" id="month_val">
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" name="month_freq" id="month_freq">
                                <option value="">-- Common Settings --</option>
                                <option value="*">Every month (*)</option>
                                <option value="*/2">Every other month (*/2)</option>
                                <option value="*/4">Every 3 months (*/4)</option>
                                <option value="1,7">Every 6 months (1,7)</option>

                                <option value="">-- Months --</option>
                                <option value="1">January (1)</option>
                                <option value="2">February (2)</option>
                                <option value="3">March (3)</option>
                                <option value="4">April (4)</option>
                                <option value="5">May (5)</option>

                                <option value="6">June (6)</option>
                                <option value="7">July (7)</option>
                                <option value="8">August (8)</option>
                                <option value="9">September (9)</option>
                                <option value="10">October (10)</option>
                                <option value="11">November (11)</option>

                                <option value="12">December (12)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="weekday_val" class="col-sm-4 control-label">Weekday</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" name="weekday_val" id="weekday_val">
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" name="weekday_freq" id="weekday_freq">
                                <option value="">-- Common Settings --</option>
                                <option value="*">Every weekday (*)</option>
                                <option value="1-5">Mon till Fri (1-5)</option>
                                <option value="0,6">Sat and Sun (6,0)</option>
                                <option value="1,3,5">Mon, Wed, Fri (1,3,5)</option>

                                <option value="2,4">Tues, Thurs (2,4)</option>
                                <option value="">-- Weekdays --</option>
                                <option value="0">Sunday (0)</option>
                                <option value="1">Monday (1)</option>
                                <option value="2">Tuesday (2)</option>
                                <option value="3">Wednesday (3)</option>

                                <option value="4">Thursday (4)</option>
                                <option value="5">Friday (5)</option>
                                <option value="6">Saturday (6)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="command" class="col-sm-4 control-label">Command</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="command" id="command">
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button class="btn btn-success" onclick="$('#frmEdit').submit()"><span id="ajax-loader-edit"></span> Update</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Delete Cron Job</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal ajax-form" id="frmDelete">
                    <div id="ajax-params" data-action="{$action_urls.delete}" data-method="POST" data-loader="#ajax-loader-delete" data-loader-position="outside" data-loader-type="inside-button" data-messages="#ajax-messages" data-callback-on-success="window.location.reload()"></div>
                    <div id="ajax-messages"></div>
                    <input name="cron_id" type="hidden" id="cron_id">
                </form>
                <p>Are you sure you want to delete this cron job?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button class="btn btn-danger" onclick="$('#frmDelete').submit()"><span id="ajax-loader-delete"></span> Confirm</button>
            </div>
        </div>
    </div>
</div>