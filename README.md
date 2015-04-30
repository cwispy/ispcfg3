ispcfg3
======

ISPConfig module for WHMCS

Create a directory on your WHMCS server in the modules/servers directory and name the folder ispcfg3

Copy the file ispcfg3.php to your WHMCS modules/servers/ispcfg3 directory.

Further information can be found in the wiki https://github.com/cwispy/ispcfg3/wiki

Be sure to change the below option on a production system.

ini_set("display_errors", 1);
to
ini_set("display_errors", 0);
