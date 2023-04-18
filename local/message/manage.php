<?php
/**
 * @package     local_message
 * @author      Kristian
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */



 
echo'{"@context": [  <br />';
echo'&nbsp;&nbsp;"https://www.moodle/badges/badge.php",<br />';
echo'&nbsp;&nbsp;"https://w3c-ccg.github.io/vc-ed-models/context.json"<br />';
echo'&nbsp;&nbsp;],<br />';


echo'<br />';
echo'<br />';     


echo'"id": "urn:uuid:a63a60be-f4af-491c-87fc-2c8fd3007a58",<br />'; #credentials identifier!
echo'"type": [<br />'; 
echo' &nbsp;&nbsp;"VerifiableCredential",<br />';
echo' &nbsp;&nbsp; "OpenBadgeCredential"<br />';
echo' &nbsp;&nbsp; ],<br />';


echo'<br />';
echo'<br />';


echo' &nbsp;&nbsp; "name": "Teacher",<br />'; #which open badge instance!  
echo' &nbsp;&nbsp;  "issuer": { <br />';
echo' &nbsp;&nbsp;&nbsp;&nbsp; "type": ["Profile"],<br />'; 
echo' &nbsp;&nbsp;&nbsp;&nbsp; "id": "did:key:z6MktiSzqF9kqwdU8VkdBKx56EYzXfpgnNPUAGznpicNiWfn",<br />'; #DID of issuer!
echo' &nbsp;&nbsp;&nbsp;&nbsp;  "name": "Moodle4" <br />'; #name of issuer!
echo' &nbsp;&nbsp; &nbsp;&nbsp;},<br />';


echo'<br />';
echo'<br />';     


echo'&nbsp;"issuanceDate": "2023-01-16T09:52:00Z",<br />'; #Issuance Date!
echo'&nbsp;"credentialSubject": { <br />'; 
echo'&nbsp;"type": ["AchievementSubject"],<br />';
echo'&nbsp;"id": "did:key:123",<br />'; #holder's DID!
echo'&nbsp; "achievement": {<br />';
echo'&nbsp;&nbsp;    "id": "urn:uuid:bd6d9316-f7ae-4073-a1e5-2f7f5bd22922",<br />'; #achievement's identifier!
echo'&nbsp;&nbsp;    "type": ["Achievement"],<br />';
echo'&nbsp;&nbsp;    "achievementType": "Certificate of Successful Completion",<br />';
echo'&nbsp;&nbsp;   "name": "English A1 Course",<br />'; #name of achievement!
echo'&nbsp;&nbsp;   "description": "The holder of this badge has passed the English A1 course from teacher."{<br />'; #description!
echo'&nbsp;&nbsp;  "criteria": {<br />';
echo'&nbsp;&nbsp;      "type": "Criteria",<br />';
echo'&nbsp;&nbsp;      "narrative": "ALL of the following activities are completed: <br />';

echo'  &nbsp;&nbsp;     File - lecture,<br />';
echo'  &nbsp;&nbsp;     Quiz - Basic Quiz,<br />';
echo'  &nbsp;&nbsp;     File . lecture, <br />';
echo'  &nbsp;&nbsp;     Chat - Chat, <br />';
echo'  &nbsp;&nbsp;     URL -Englisch crash course <br />';
echo'  &nbsp;&nbsp;     Feedback - Feedback <br />';  #Criteria!
echo'"<br />';
echo'&nbsp;&nbsp;&nbsp;}<br />';
echo' &nbsp;&nbsp;}<br />';
echo'&nbsp;}<br />';
echo'}; <br />';











