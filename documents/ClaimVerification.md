# Claims to be verified

## Credential details:

```json
{
  "referent": "aece411f-ad2e-4052-9e3f-395be07babf4",
  "attrs": {
    "credentialSubject.name": "Lisa Obermann",
    "@context": "https://w3id.org/openbadges/v2",
    "issuer.id": "did:key:KTL4kGZqrA2iX9Gk2irzEt",
    "credentialSubject.achievement.name": "French A1",
    "credentialSubject.achievement.criteria.narrative": "Users are awarded this badge when they complete the following requirement:\n* ALL of the following activities are completed:\n\"File - Basics\"\n\"URL - French Link\"\n\n",
    "issuer.issuanceDate": "2023-10-19T09:27:33Z",
    "credentialSubject.achievement.description": "The owner of this badge achieved the French A1 level!",
    "issuer.name": "moodle4",
    "credentialSubject.achievement.id": "urn:uuid:aded1d22-cefb-422c-8ee8-f87fd6de89b6",
    "name": "French A1",
    "credentialSubject.id": "did:key:2zx1JEXesMmXvtuDu7jNdy",
    "id": "urn:uuid:994ee8a2-0495-4f68-801f-3bd3c146354f",
    "type": "VerifiableCredential, OpenBadgeCredential"
  },
  "schema_id": "JLXngoc4ahRhFhjZcMzvNs:2:OpenBadge:1.0",
  "cred_def_id": "KTL4kGZqrA2iX9Gk2irzEt:3:CL:227059:default",
  "rev_reg_id": null,
  "cred_rev_id": null
}
```

### Achievement Name/ Badge Name (done)
- Verifier checks if the value in Achievement Name/Badge Name is equal to the entered ones   

### DID of issuer
- Verifier has a register with all issuer DIDs, which are known and trusted
- Verifier checks whether the DID value in issuer.id is equal to one of the DIDs stored in the register of the verifier

### DID of holder
- Verifier checks whether the DID value in credentialSubject.id is equal to the DID that presented the corresponding proof 
