# DIDComm #
- Creates bidirectional secure communication channel between two entities that know each other’s DIDs and nothing else
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
