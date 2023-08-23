## Setting "trace" true

{
  "auto_remove": true,
  "comment": "Ausstellung des OpenBadge für French A1",
  "connection_id": "f8bf984c-e301-423e-857f-1808c467a104",
  "credential_preview": {
    "@type": "issue-credential/2.0/credential-preview",
    "attributes": [
      {
        "name": "@context",
        "value": "https://w3id.org/openbadges/v2"
      },
      {
        "name": "id",
        "value": "http://localhost/moodle4/badges/badge_json.php?id=2"
      },
      {
        "name": "type",
        "value": "VerifiableCredential,OpenBadgeCredential"
      },
      {
        "name": "name",
        "value": "French A1"
      },
      {
        "name": "issuer.id",
        "value": "http://localhost/moodle4/badges/issuer_json.php?id=2"
      },
      {
        "name": "issuer.name",
        "value": "Faber"
      },
      {
        "name": "issuer.issuanceDate",
        "value": "2023-08-07T14:23:56+01:00"
      },
      {
        "name": "credentialSubject.id",
        "value": "3"
      },
      {
        "name": "credentialSubject.name",
        "value": "Alice Smith"
      },
      {
        "name": "credentialSubject.achievement.id",
        "value": "http://localhost/moodle4/badges/badge_json.php?id=2"
      },
      {
        "name": "credentialSubject.achievement.name",
        "value": "French A1"
      },
      {
        "name": "credentialSubject.achievement.description",
        "value": "The owner of this badge achieved the French A1 level!"
      },
      {
        "name": "credentialSubject.achievement.criteria.id",
        "value": "http://localhost/moodle4/badges/badgeclass.php?id=2"
      },
      {
        "name": "credentialSubject.achievement.criteria.narrative",
        "value": "Users are awarded this badge when they complete the following requirement:\n* ALL of the following activities are completed:\n\"File - Basics\"\n\"URL - French Link\"\n\n"
      }
    ]
  },
  "filter": {
    "indy": {
      "cred_def_id": "MXYqiXt5VNQGVh7PdCCw1o:3:CL:221631:default",
      "schema_id": "MXYqiXt5VNQGVh7PdCCw1o:2:OpenBadges:1.0"
    }
  },
  "trace": true
}
