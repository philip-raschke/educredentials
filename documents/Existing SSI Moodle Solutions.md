# Integrating Web3 Features into Moodle 
Urban Vidoviˇc(B), Vid Kerˇsiˇc, and Muhamed Turkanovi´c Faculty of Electrical Engineering and Computer Science, University of Maribor, 2000 Maribor, Slovenia

### Approach: 
- Moodle plugin is a bridge between client, EduCTX blockchain network (https://platform.eductx.org) and the moodle server
-	 MetaMask browser extension needed

<img width="551" alt="Screenshot 2023-04-16 202701" src="https://user-images.githubusercontent.com/103564990/232333947-19d284c0-d473-420c-ba19-618c21832aa5.png">


 
### EduCTX: 
-	Smart contracts store the certificates and users’ identities and present the “back-end” of the system
-	Moodle system
-	consists of an Apache HTTP server, MariaDB database, and the proposed EduCTX plugin
-	Server hosts the Moodle platform and communicates Database

### Teacher’s Workflow
-	teacher connects MetaMask wallet to the plugin and issues the certificate
-	plugin takes student’s public key from EduCTX, encrypts the certificate and hashes the certificate
-	plugin stores encrypted certificate and its hash on the EduCTX blockchain network
-	certificate encryption is carried out entirely on the client-side, that no critical data leaves the teacher’s device
-	certificate can only be decrypted by the student using private key

### Student’s Workflow
-	MetaMask Wallet of student establishes connection with the plugin
-	Student can take certificate from EduCTX 
-	student exports the private key from the MetaMask wallet and fills it into an input field
-	all certificates are decrypted and presented in a table upon clicking on the decrypt button
-	export certificate with EduCTX

### Verifier’s Workflow
-	does not require the usage of plugin since EduCTX aims to enable anyone to verify certificates
-	anyone can visit the platform and verify any EduCTX-based credential without the need of having a wallet set up

### Plugin
-	in PHP and Java Script




# Developing Ethereum Blockchain-Based Document Verification Smart Contract for Moodle Learning Management System
Erinç KARATAŞ 1 1Computer Technologies Department, Elmadag Vocational School, Ankara University, Ankara, Türkiye

-	Approach: application with use of smart contracts in Moodle
-	smart contract in which the certificate information could be stored on the Ethereum blockchain 
-	Ethereum blockchain has to be installed
-	Write Ethereum-based smart contract -> Solidity language 
-	Send smart contract to the blockchain
-	Smart contract consist of two main functions
-> Write name, surname, identity information of the participant, the institution issuing the certificate, document authentication code and hash value of the document 
-> using document authentication code to verify participant's digital certificate 
-	students click on a link to download their certificate
-	students are registered into the Ethereum block chain by running a smart contract




# The Open University’s repository of research publications and other research outputs A case study on the decentralisation of lifelong learning using blockchain technology

-	Approach: Semantic Blockchain

<img width="544" alt="Screenshot 2023-04-16 202726" src="https://user-images.githubusercontent.com/103564990/232334059-e7287d62-a062-403e-85f9-d4759250ef2c.png">

 
-	Solid Personal Data Pod belongs to student (holder) with learning record
-	Student has access to moodle to get learning certificates from moodle
-	IPFS (Interplanetary File System) stores students documents with “token metadata?” and Student Solid URLs 
-	Moodle issues badges and badge issuing rights to the blockchain
-	On Blockchain: signed token metadata URL and badge issuing contracts 
