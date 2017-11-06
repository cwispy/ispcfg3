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
<link rel="stylesheet" type="text/css" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>

<link rel="stylesheet" type="text/css" href="modules/servers/ispcfg3a/assets/elfinder/css/elfinder.min.css">
<link rel="stylesheet" type="text/css" href="modules/servers/ispcfg3a/assets/elfinder/themes/windows/css/theme.css">

<script src="modules/servers/ispcfg3a/assets/elfinder/js/elfinder.full.js"></script>
<link href="modules/servers/ispcfg3a/assets/ispcfg3.css" rel="stylesheet">
<span class="icon-header icon-filemanager"></span>
<h3>File Manager ({$params.domain})</h3>
<p>Here you can manage the files on your website. You can upload, edit or create new files and folders. You can drag and drop files from your desktop to thr current open folder. To edit a file or folder right click it to get the menu. If you are uploading many files do use an ftp program like <a href="https://filezilla-project.org/download.php" target="_blank">Filezilla</a>.</p>
<hr>
<div id="ihost-elfinder" class="elfinderInit" data-custom-ftp-data="{$variables.elfinder_ftp}"></div>