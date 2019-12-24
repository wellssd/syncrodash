# syncrodash
Syncro ticket integration with Zabbix dashboard using URL widget

syncrodash.php is a simple PHP script targeted at MSP operators who use both SyncroMSP as a PSA/RMM platform and Zabbix as a 
monitoring platform.  Using syncrodash allows users of both platforms to create a URL widget in Zabbix, arguably the more feature
rich dashboard offering, which will display Syncro tickets that do not have a Resolved status in Zabbix.  This brings users a single
pane view of both platforms great for technicians monitoring systems and service requests as well as kiosk displays.

Since Zabbix already has Apache and PHP as part of the base installation, the script was written with the intention of running on the
Zabbix server.  Update the $API_KEY and $SUBDOMAIN variables to match your Syncro environment and place in /var/www/html on your
Zabbix server using proper permissions (matching the index.html that should already be in that directory).  It can be tested by opening
a browser and requesting http://<zabbix server/syncrodash.php.  After confirmed working, a URL widget can be created on the Zabbix
dashboard to display your Syncro tickets.

Colors are based on the Zabbix dark theme.  If you are using a different them, you might choose to change the font and / or table
border colors by making adjustments to the inline style tags.

For questions or suggestions, please contact software@tsmidwest.com
