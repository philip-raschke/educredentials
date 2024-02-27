# Moodle Plugin for Verifiable Credentials

The Moodle Plugin for Verifiable Credentials is designed for Moodle version 4.1.2. It enables students or course participants to issue their earned badges in JSON format to their own Holder Wallet.

## Installation of WSL

As we are using Windows, we need to install WSL from here: "https://learn.microsoft.com/de-de/windows/wsl/install"!

## Installation of the Plugin

1. Make sure you have Moodle version 4.1.2 installed (refer to the Moodle installation guide in https://github.com/pherbke/educredentials/blob/main/README.md).
2. Clone the repository to obtain the plugin from https://github.com/pherbke/educredentials.
3. Copy the plugin folder ("educredentials\badge_data") of this Github repository into the "C:\wamp64\www\moodle4\local" directory of your local Moodle instance.
4. Start Moodle in your browser as an administrator by typing "http://localhost/moodle4/".
5. Follow the prompts to update Moodle.
6. Once the installation is complete, the plugin should be installed and ready to use.

## Installation of Aries Cloud Agent Python

Clone the repository to your C drive by running the following command in your terminal:
```bash
git clone https://github.com/hyperledger/aries-cloudagent-python
```

## Installation of Docker

Install Docker for the Aries Cloud Agent Python from here: https://docs.docker.com/engine/install/


## Usage of the Plugin

1. After installing the plugin, log in to Moodle as a course participant.
2. Go to your profile (`http://localhost/moodle4/user/profile.php`).
   ![Profile](https://github.com/pherbke/educredentials/assets/103564990/87530c07-caf3-4e8d-a8ac-3e7cc3fef475)
3. Click on one of the badges you have been awarded.
   ![Badge](https://github.com/pherbke/educredentials/assets/103564990/66147928-1bf5-453f-bd60-d60273d350ae)
4. Click on "Manage badges" to access the page where your badges are listed (`http://localhost/moodle4/badges/mybadges.php`).
   ![Manage Badges](https://github.com/pherbke/educredentials/assets/103564990/4a274a7d-5dd2-44f8-8b59-b564edcdd9a1)
5. Click on the "Badges as JSON" button.
   ![JSON Button](https://github.com/pherbke/educredentials/assets/103564990/4f305ca6-3099-4037-839b-3ca78d0c06ff)
6. On the left side of the page, you will find a list of all your badges.
7. Clicking on a badge will display its corresponding JSON format in the field on the right side. Additionally it shows the generated QR Code of the badge. As we can see "Error: Failed to connect to Issuer AgentError: No Holder Wallet is connectedError: Failed to connect to Issuer AgentError: No Holder Wallet is connected" is displayed. 
8. Start Docker
9. Open a Terminal
10. type "wsl" to start WSL
11. navigate to the demo folder
12. Execute the command "LEDGER_URL=http://dev.greenlight.bcovrin.vonx.io ./run_demo faber" to start the Issuer Agent.
13. If the Faber Agent is running, then reload "http://localhost/moodle4/local/badge_data/"! This will remove the corresponding error message.
14. Open the Swagger UI "http://localhost:8021/api/doc" in your browser!
15. Go to the Credential Definition section and click on "Post" at "/credential-definitions"!
16. Then click on "Try it out"!
17. Enter the following as the credential definition and click on "Execute":

```  
{
  "schema_id": "JLXngoc4ahRhFhjZcMzvNs:2:OpenBadge:1.0",
  "support_revocation": false,
  "tag": "default"
}
```
18. Copy the generated credential definition id from the previous step.
19. Go to the code of "C:\wamp64\www\moodle4\local\badge_data\index.php" and change the "credentialDefinitionId" in line 271 by pasting what you copied from the previous step. Afterwards save the changes!
20. Now open a second Terminal
21. type "wsl" to start WSL
22. navigate to the demo folder
23. Execute the command "LEDGER_URL=http://dev.greenlight.bcovrin.vonx.io ./run_demo alice" to start the Holder Agent
24. If the Alice Agent is running, then reload "http://localhost/moodle4/local/badge_data/"! This will save the changes you made in the index.php and will give you the oportunity to connect to the Alice Agent.
25. Click on the "Connect to Holder Wallet" Button. This will display a QR Code representing the invitation data. Additionally it will display the actual invitation data.
26. Copy the invitation data and go to the holder/alice terminal and paste them there!
27. Now you can see in both terminals that Issuer (Faber) and Holder (Alice) are connected. Reload the page in your browser!
28. Now select a badge icon and click on "Issue Credential to Holder Wallet"!
29. Now go to the holder/alice terminal! You can see there the badge which is now in the holder wallet! 
