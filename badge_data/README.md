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
8. 
   <img width="948" alt="6  7" src="https://github.com/pherbke/educredentials/assets/103564990/e5ecce96-920c-4634-b393-73bbff4f3604">
   
9. Start Docker
10. Open a Terminal and type "wsl" to start WSL
11. navigate in the Terminal to the "C:\aries-cloudagent-python\demo" folder
12. Execute the following command in the Terminal to start the Issuer Agent (Make sure that Docker is running):

     ```
    LEDGER_URL=http://dev.greenlight.bcovrin.vonx.io ./run_demo faber
    ```

In Aca-Py the Issuer is called Faber!

<img width="943" alt="8  9  10  11" src="https://github.com/pherbke/educredentials/assets/103564990/42b95768-798d-4392-a1a1-17d48efdfcf5">

12. The Issuer Agent should run now on "http://localhost:8021/". If the Issuer Agent is running and the Terminal displays at the end
```
Waiting for connection...
```
then reload "http://localhost/moodle4/local/badge_data/" in your browser! This will remove the corresponding error message on the webpage.

13. Open the Swagger UI "http://localhost:8021/api/doc" in your browser!
14. Go to the Credential Definition section and click on "Post" at "/credential-definitions"!
15. Then click on "Try it out"!
16. 
<img width="949" alt="12  13  14  15" src="https://github.com/pherbke/educredentials/assets/103564990/66fa3705-fc4a-4f06-86e0-d97ecf0abcb8">

17. Create a new Credential Definition: Enter the following as the credential definition and click on "Execute":

```  
{
  "schema_id": "JLXngoc4ahRhFhjZcMzvNs:2:OpenBadge:1.0",
  "support_revocation": false,
  "tag": "default"
}
```

The Schema was created by an other issuer and is stored in the Blockchain "http://dev.greenlight.bcovrin.vonx.io/browse/domain". After your Execution in the Swagger UI, the credential definition is also stored on the Blockchain "http://dev.greenlight.bcovrin.vonx.io/browse/domain".

<img width="953" alt="16" src="https://github.com/pherbke/educredentials/assets/103564990/5743ef61-b211-49c4-93a6-39c0a11871bc">

17. Copy the generated credential definition id from the previous step (E.g. "XqAYYysM62BHHhvru9VmeQ:3:CL:227059:default").
18. 
<img width="914" alt="17" src="https://github.com/pherbke/educredentials/assets/103564990/81fcb66b-0a9c-47f9-81e7-0d364d97a161">

19. Go to the code of "C:\wamp64\www\moodle4\local\badge_data\index.php" and change the "credentialDefinitionId" in line 271 by pasting what you copied from the previous step. Afterwards save the changes!
20. 
<img width="574" alt="18" src="https://github.com/pherbke/educredentials/assets/103564990/0bb0cba8-00af-4685-ad0a-3aea3e80bf1b">

21. Now open a second Terminal and type "wsl" to start WSL
22. Navigate in the Terminal to the "C:\aries-cloudagent-python\demo" folder
23. Execute the following command to start the Holder Agent:
    ``` 
    LEDGER_URL=http://dev.greenlight.bcovrin.vonx.io ./run_demo alice
    ```
In Aca-Py the Holder is called Alice!

<img width="952" alt="19  20  21" src="https://github.com/pherbke/educredentials/assets/103564990/89d63f4f-7051-4d57-b596-5b97c1e90557">

22. The Holder Agent should run now on "http://localhost:8031/". If the Alice Agent is running and displays in the Terminal at the end
```
#9 Input faber.py invitation details
Invite details:
```
, then reload "http://localhost/moodle4/local/badge_data/" in yout browser! This will save the changes you made and will give you the oportunity to connect to the Alice Agent.

23. Click on the webpage in your browser the "Connect to Holder Wallet" Button. This will display a QR Code representing the invitation data. Additionally it will display the actual invitation data.

<img width="944" alt="22  23" src="https://github.com/pherbke/educredentials/assets/103564990/d69384ca-b037-4f9c-a52a-55e76542e375">

24. Copy the invitation data and go to the terminal of Alice and paste them there!
25. Now you can see in both terminals that Issuer (Faber) and Holder (Alice) are connected. Reload the webpage in your browser!

<img width="959" alt="24  25" src="https://github.com/pherbke/educredentials/assets/103564990/5531a0fc-b178-461d-9bb6-2a61167fca97">

26. Now select a badge icon and click on "Issue Credential to Holder Wallet"!

<img width="954" alt="26" src="https://github.com/pherbke/educredentials/assets/103564990/54f869fe-667b-40e2-91fe-fe713e45f3b2">

27. Now go to the holder/alice terminal! You can see there the badge which is now in the holder wallet! 

<img width="947" alt="27" src="https://github.com/pherbke/educredentials/assets/103564990/6097ddb3-e488-4bf9-9bc9-07bac770b2b0">
