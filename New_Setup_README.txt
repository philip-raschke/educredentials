### Prerequisites

1. Download the appropriate Windows version of Moodle 4.1.2 ("https://moodle.en.uptodown.com/windows/post-download/93936265").
2. Download the Aries Cloudagent Python (aca-py) repository ("https://github.com/hyperledger/aries-cloudagent-python").
3. Install Docker ("https://www.docker.com/products/docker-desktop/").
4. Create an ngrok Account ("https://ngrok.com/")
5. Make sure GitBash is installed on your machine.
6. Obtain the files from this repository.


### Initial Setup

Extract the Moodle zip. Inside your new Moodle folder, navigate to Moodle\server\moodle\local and paste the badge_data folder
from this repository.
Start your local moodle instance using the "Start Moodle" executable in the Moodle folder.
Note: Always shut down your Moodle instance using the "Stop Moodle" executable or you risk corrupting your database and will have to re-do this inital setup.
Open "localhost/" in your preferred browser to access your Moodle instance. Follow the installation steps. You will be logged in as the Administrator upon completion.


### Create badges as a teacher

1. Click on "My courses" at the top of the Moodle page to create a new course.
2. Click on "New course" to create a course.
3. Customize the course according to your preferences.
4. Invite participants to join the course. For demo purposes you can also enable self-enrollment using the top-left dropdown menu.
5. On the course page, click on "More" and select "Badges" from the dropdown menu.
6. Click on "Add a new badge" to create a badge.
7. Customize the badge according to your preferences, including criteria and badge picture.
8. Make sure to activate the badge being visible after creating it.
9. Students who meet the set requirements will receive the corresponding badge.


### Setting up the issuing agent

Start Docker
To open an ngrok endpoint on port 8020, execute "ngrok http 8020" in a cmd line.
In the explorer, navigate to the "demo" folder in your aca-py folder.
Shift-rightclick in the folder and click on "Open Git Bash here".
Paste the following command into GitBash and hit enter:

LEDGER_URL=http://dev.greenlight.bcovrin.vonx.io LOG_LEVEL=DEBUG ./run_demo faber --aip 10 --Events

The issuing agent, called faber, is now gonna setup.
Once the setup is complete, the agent will start, using your ngrok address as the endpoint. Make sure this is the case by checking if the beginning of the
invitation_url displayed above the QR code contains your ngrok address.
If all is working correctly, it posts a default schema and credential definition to the ledger upon start, displays an invitation QR code and is waiting
for a connection.


### Posting your own schema to the ledger

For the plugin to work with the agent, they both have to be set to the same schema and credential definition.
This has to be done only once as long as the agent is running. If the agent is shut down, this step has to be repeated.
In a possible future release, it is planned that the user can set their own schema for their own type of badges, which is
why this step is still being done manually during the inital setup.

Once your agent is running, you can access it's Admin API in your browser at:

http://localhost:8021/api/doc

Navigate to the "Schema" section and click on "Post" and "Try it out". To create a new schema, replace the code with the following:

{
  "schema_version": "1.0",
  "schema_name": "OpenBadge",
  "attributes": [
    "@context",
    "id",
    "type",
    "name",
    "issuer.id",
    "issuer.name",
    "issuer.issuanceDate",
    "credentialSubject.id",
    "credentialSubject.name",
    "credentialSubject.achievement.id",
    "credentialSubject.achievement.name",
    "credentialSubject.achievement.description",
    "credentialSubject.achievement.criteria.narrative"
  ]
}

Click "Execute".

Copy the schema id that got generated from the section below. (e.g. "935vGALagzCDG5nNZnDPGj:2:OpenBadge:1.0")

Scroll up to the Credential Definition section and click on "Post" at "/credential-definitions".
Then click on "Try it out".
Delete the text and paste the Schema id you just copied.
Copy and paste the following:

{
  "schema_id": "Copy your schema id here",
  "support_revocation": false,
  "tag": "default"
}

Copy your Schema id into the first line. Make sure you keep the quotation marks (e.g. "schema_id": "935vGALagzCDG5nNZnDPGj:2:OpenBadge:1.0",).
Delete the Schema id you initally pasted into the empty field.
Your input should look like this:

{
  "schema_id": "935vGALagzCDG5nNZnDPGj:2:OpenBadge:1.0",
  "support_revocation": false,
  "tag": "default"
}

Click "Execute".

Your new credential definiton id is now displayed in the section below (e.g. "XqAYYysM62BHHhvru9VmeQ:3:CL:227059:default").

Open the index.php file in the plugin folder you pasted into your Moodle folder at moodle\local\badge_data\ with an editor of your choice.
Search for where the schemaId variable and credentialDefinitionId variable are initialized in the script (search for "Copy", it's marked with a comment, line 552+553)
Copy your newly generated schema id and credential definition id into these variables.
Save your changes.
The issuing agent and the plugin are now set to the same schema and credential definition and the plugin is ready for use.
You can now start your moodle instance and use the plugin, or if your moodle instance has already been running, the changes will be taken over after a few minutes
when the cache refreshes.


### Issuing a badge using the Plugin

Log in to your Moodle instance as a user that has earned a badge in their profile.
Navigate to "Profile" in the top right dropdown menu.
Click on one of the earned badges.
Click on "Manage Badges".
Click on the "Badges as JSON" button under the badges to get to the plugin page.
Follow the steps in the provided guide section.


### Verification
Open the "enroll.html" in the "Verification" folder from this repository.
Select a course you would like to enroll in and have the necessary credential for.

Note: The verification process is currently hardcoded for a credential of the schema shown above by the name "English A1", so select the "English A2" course.

After acknowledging that you want to show a credential you will be led to the index.html page, in which you will find an invitation QR code to scan with your mobile wallet app.

Note: If you went through the earlier steps of issuing a credential to your mobile wallet and your faber agent is still running, your wallet will already be connected to the agent and the index.html page gets skipped. In a real-world setting, the agent doing the verification would be a different one from a different university, in this example case the HU Berlin. That agent would not have a connection with your wallet yet. If you want to go through this process "realistically", shut down your agent by pressing CTRL+C in your GitBash, and starting the agent again with the command shown above. You will now have to connect your wallet to the agent using the QR code shown on the index.html page.

Upon connecting your wallet by scanning the QR code, a proof request is sent to your wallet app. You need to acknowledge this request. (In the BC wallet, click "View" and then "Share".)
A zero-knowledge proof of the credential's existence in your wallet will be created and the result is shown on the page. Upon successful verification, you can continue to the course page.
