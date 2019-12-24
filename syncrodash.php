<?php

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
#
# syncrodash.php - Wrapper to allow viewing Syncro dashboard in Zabbix using Syncro's REST API
#
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
#
# Date     Notes
# -------- ------------------------------------------------------------------------------------------------------------
# 20191223 Initial version (scott.wells@tsmidwest.com)
#
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #


###
### Definitions
###
$API_KEY   = "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"; # Syncro key to access REST API
$SUBDOMAIN = "xxxxxxxxx";                            # Syncro subdomaing


###
### CSS and opening HTML body tags
###
print "<style>\n";
print "    body {\n";
print "        color: white;\n";
print "        font-family: Verdana;\n";
print "        font-size: small;\n";
print "    }\n";
print "    table, th, td {\n";
print "        border: 1px solid black;\n";
print "        border-collapse: collapse;\n";
print "        border-spacing: 35px;\n";
print "        color: white;\n";
print "        font-family: Verdana;\n";
print "        font-size: small;\n";
print "    }\n";
print "    table {\n";
print "        width: 100%;\n";
print "    }\n";
print "    td, th {\n";
print "        padding: 5px;\n";
print "    }\n";
print "</style>\n\n";
print "<body>\n";


###
### Build URL to retrieve tickets that do not have a resolved status, retrieve JSON results and decode
###
$URI = "https://".$SUBDOMAIN.".syncromsp.com/api/v1/tickets?api_key=".$API_KEY."&status=Not%20Closed";
$Tickets = json_decode(file_get_contents($URI));


###
### Determine if any tickets were returned from REST API, if so create table; otherwise, print message
###
if(count($Tickets->tickets) >= 1) {


        ###
        ### Table Header
        ###
        print "<table>\n";
        print "    <tr>\n";
        print "        <th>Ticket</th>\n";
        print "        <th>Status</th>\n";
        print "        <th>Customer</th>\n";
        print "        <th>Subject</th>\n";
        print "    </tr>\n";


        ###
        ### For each ticket returned, add an entry to the HTML table
        ###
        foreach($Tickets->tickets as $ticket) {
                print "    <tr>\n";
                print "        <td><a target=\"_blank\" rel=\"noopener noreferrer\" href=\"https://".$SUBDOMAIN.".syncromsp.com/tickets/".$ticket->id."\">".$ticket->number."</a></td>\n";
                print "        <td>$ticket->status</td>\n";
                print "        <td>$ticket->customer_business_then_name</td>\n";
                print "        <td>$ticket->subject</td>\n";
                print "    </tr>\n";
        }
        print "</table>\n";
} else {
        print "No unresolved tickets to display.<br>\n";
}


###
### Closing HTML body tag
###
print "</body>\n";

?>
