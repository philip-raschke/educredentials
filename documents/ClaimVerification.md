# Claims to be verified

### Achievement Name/ Badge Name (done)
- Verifier checks if the value in Achievement Name/Badge Name is equal to the entered ones   

### DID of issuer
- Verifier has a register with all issuer DIDs, which are known and trusted
- Verifier checks whether the DID value in issuer.id is equal to one of the DIDs stored in the register of the verifier

### DID of holder
- Verifier checks whether the DID value in credential.Subject.id is equal to the DID that presented the corresponding proof 
