<?php
// plugin_config.php
defined('MOODLE_INTERNAL') || die(); // Verhindern des direkten Zugriffs

$CFG->plugin_wallet_url = 'http://localhost:8021/wallet/did'; 
$CFG->plugin_connections_url = 'http://localhost:8021/connections'; 
$CFG->plugin_create_invitation_url = 'http://localhost:8021/connections/create-invitation?alias=Alice'; 
$CFG->plugin_issue_credential_url = 'http://localhost:8021/issue-credential-2.0/send'; 


/* ... 
Change URLs for Deployment on Server!
$CFG->plugin_wallet_url = 'https://<ServerIPAddress>:8021/wallet/did'; 
$CFG->plugin_connections_url = 'https://<ServerIPAddress>:8021/connections'; 
$CFG->plugin_create_invitation_url = 'https://<ServerIPAddress>:8021/connections/create-invitation?alias=Alice'; 
$CFG->plugin_issue_credential_url = 'https://<ServerIPAddress>:8021/issue-credential-2.0/send'; 
*/