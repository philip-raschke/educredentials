# DIDComm #
- Creates bidirectional secure communication channel between entities that know each other’s DIDs and nothing else
- Both sides authenticate each other without authentication to a TTP (e.g. with credentials)
- TTPs (Trusted Third Parties) lose visibility into user communications, activities
- For any transport: HTTP, BlueTooth, SMTP, raw sockets, and sneakernet, etc.

## Workflow ##
- Alice = A, Faber = F, Public Key from Alice/Faber = PubKeyA/PubKeyB, Private Key from Alice/Faber = PrivKeyA/PrivKeyB

### Conditions: ### 
- DIDs are known to all
- DID documents are stored in the Ledger containing the Public Keys 

1. Connection Request from F to A signed with PrivKeyF and encrypted with PubKeyA from DID Document
2. A decrypts Connection Request with PrivKeyA and verifies Signature (made with PrivKeyF) using PubKeyF from DID Document
3. A sends Connection Request signed with PrivKeyA and encrypted with PubKeyF from DID Document
4. F decrypts Connection Request with PrivKeyF and verifies Signature (made with PrivKeyA) using PubKeyA from DID Document
5. Connection established!


### Short Description ###
DIDComm is a communication protocol leveraging Decentralized Identifiers (DIDs) to enable secure, peer-to-peer communication between entities. By utilizing paired public and private keys linked to DIDs, it allows parties to authenticate each other without reliance on Trusted Third Parties (TTPs), preserving privacy by obscuring visibility from outside entities. Overall, DIDComm fosters secure and trustworthy digital communication, enhancing privacy and integrity in decentralized networks.
