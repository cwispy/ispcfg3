ispcfg3
======
Requires ISPConfig 3.1+
WHMCS 6+

WARNING 07/Nov/2017

This module now requires that you have ISPConfig 3.1 or higher
It will no longer work with older versions of ISPConfig

This module is currently undergoing changes and is not 
recommended to be run in production in its current status.



ISPConfig module for WHMCS

Create a directory on your WHMCS server in the modules/servers directory and name the folder ispcfg3

Copy the contents to your WHMCS modules/servers/ispcfg3 directory.

Further information can be found in the wiki https://github.com/cwispy/ispcfg3/wiki

Be sure to change the option below, which is located near the top of the 
ispcfg3.php file, on a production systems.

ini_set("display_errors", 1);
to
ini_set("display_errors", 0);
