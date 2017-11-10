# ISPConfig module for WHMCS

* module name: ispcfg3
* Requires ISPConfig 3.1+
* Requires WHMCS 7+

Copyright (C) 2014 - 2017  Shane Chrisp

```
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
```

For discussion and issues with this module, visit our [HowToForge discusion thread](https://www.howtoforge.com/community/threads/new-ispconfig-module-for-whmcs.67824/)

**WARNING 07/Nov/2017 - DO NOT RUN IN PRODUCTION**

Please report issues in our HowtoForge thread above.

This module now requires that you have ISPConfig 3.1 or higher.
It will no longer work with older versions of ISPConfig

This module is currently undergoing changes and is not 
recommended to be run in production in its current status.

*Usage*

- Create a directory called *ispcfg3* on your WHMCS server in the modules/servers directory eg: */var/www/whmcs/modules/server/ispcfg3*
- Download the zip file and extract the contents to the directory you just created, or from the shell change into the directory you just and use ```git clone https://github.com/cwispy/ispcfg3.git .``` to download the repository.
- Edit the file ispcfg3.php and make sure that you turn off the display_errors is set to 0 ```ini_set("display_errors", 0);```


Further setup instrutions can be found in our [GitHub Wiki](https://github.com/cwispy/ispcfg3/wiki)
