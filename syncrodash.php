<?php

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
#
# syncrodash.php - Wrapper to allow viewing Syncro dashboard in Zabbix using Syncro's REST API
#
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
#
# Date     Notes
# -------- ------------------------------------------------------------------------------------------------------------
# 20191223 Initial version (software@tsmidwest.com)
# 20191226 Add status bar with colors aligned to Zabbix dark theme (software@tsmidwest.com)
#
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #


###
### Definitions
###
$API_KEY    = "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"; # Syncro key to access REST API
$SUBDOMAIN  = "xxxxxxxxx";                            # Syncro subdomaing
$STATUS_BAR = 1;                                      # Display status bar (0 = no, 1 = yes)
$STATUS_AVL = 48;                                     # Cutoff in hours for tickets to be "Available" (green) status
$STATUS_WRN = 24;                                     # Cutoff in hours for tickets to be "Warn" (yellow) status
$STATUS_HGH = 0;                                      # Cutoff in hours for tickets to be "High" (orange) status


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
print "<body>\n\n";


###
### Build URL to retrieve tickets that do not have a resolved status, retrieve JSON results and decode
###
$URI = "https://".$SUBDOMAIN.".syncromsp.com/api/v1/tickets?api_key=".$API_KEY."&status=Not%20Closed";
$Tickets = json_decode(file_get_contents($URI));


###
### Status Bar - If enabled, use an HTML table to create a status bar
###
if($STATUS_BAR == 1) {
        if(count($Tickets->tickets) >= 1) {
                $tix_avl = 0; # Counter for "Available" tickets
                $tix_wrn = 0; # Counter for "Warn" tickets
                $tix_hgh = 0; # Counter for "High" tickets
                $tix_dsr = 0; # Counter for "Disaster" tickets
                foreach($Tickets->tickets as $ticket) {
                        $duedate = strtotime($ticket->due_date);
                        if($duedate >= (time()+($STATUS_AVL*3600))) {
                                $tix_avl++;
                        } elseif($duedate >= (time()+($STATUS_WRN*3600))) {
                                $tix_wrn++;
                        } elseif($duedate >= (time()+($STATUS_HGH*3600))) {
                                $tix_hgh++;
                        } else {
                                $tix_dsr++;
                        }
                }
                $avltix = ceil((100/($tix_avl+$tix_wrn+$tix_hgh+$tix_dsr))*$tix_avl);
                $wrntix = ceil((100/($tix_avl+$tix_wrn+$tix_hgh+$tix_dsr))*$tix_wrn);
                $hghtix = ceil((100/($tix_avl+$tix_wrn+$tix_hgh+$tix_dsr))*$tix_hgh);
                $dsrtix = ceil((100/($tix_avl+$tix_wrn+$tix_hgh+$tix_dsr))*$tix_dsr);
                print "<center>".($tix_avl+$tix_wrn+$tix_hgh+$tix_dsr)." unresolved ticket(s)</center>\n";
                print "<svg viewbox=\"0 0 ".($tix_avl+$tix_wrn+$tix_hgh+$tix_dsr)." 1\" width=\"100%\" height=\"20\" preserveaspectratio=\"none\">\n";
                print "    <rect x=\"0\" y=\"0\" width=\"".$tix_avl."\" height=\"1\" style=\"fill:rgb(134, 204, 137);\"></rect>\n";
                print "    <rect x=\"".$tix_avl."\" y=\"0\" width=\"".$tix_wrn."\" height=\"1\" style=\"fill:rgb(255, 200, 089);\"></rect>\n";
                print "    <rect x=\"".($tix_avl+$tix_wrn)."\" y=\"0\" width=\"".$tix_hgh."\" height=\"1\" style=\"fill:rgb(233, 118, 089);\"></rect>\n";
                print "    <rect x=\"".($tix_avl+$tix_wrn+$tix_hgh)."\" y=\"0\" width=\"".$tix_dsr."\" height=\"1\" style=\"fill:rgb(228, 089, 089);\"></rect>\n";
                print "</svg>\n\n";
        }
}


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
