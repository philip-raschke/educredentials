## Setting "trace" true

### Issuing credential by setting trace = true
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


### Response

- Trace information in the terminal provide information about the credential issuance process from Alice to Faber in Aca-py


  Alice      | 2023-08-23 14:23:51,582 aries_cloudagent.utils.tracing INFO  acapy.events {"msg_id": "71d03ff0-8dd7-4345-9499-fd4d361a34ad", "thread_id": "71d03ff0-8dd7-4345-9499-fd4d361a34ad", "traced_type": "did:sov:BzCbsNYhMrjHiqZDTUASHg;spec/issue-credential/2.0/offer-credential", "timestamp": 1692800631.5818691, "str_time": "2023-08-23 14:23:51.581869", "handler": "Alice.Agent.trace", "ellapsed_milli": 0, "outcome": "Dispatcher.handle_message.START"}
Alice      | 2023-08-23 14:23:51,634 aries_cloudagent.utils.tracing INFO  acapy.events {"msg_id": "71d03ff0-8dd7-4345-9499-fd4d361a34ad", "thread_id": "71d03ff0-8dd7-4345-9499-fd4d361a34ad", "traced_type": "did:sov:BzCbsNYhMrjHiqZDTUASHg;spec/issue-credential/2.0/offer-credential", "timestamp": 1692800631.634298, "str_time": "2023-08-23 14:23:51.634298", "handler": "Alice.Agent.trace", "ellapsed_milli": 29, "outcome": "V20CredOfferHandler.handle.END"}
Alice      | 2023-08-23 14:23:51,634 aries_cloudagent.utils.tracing INFO  acapy.events {"msg_id": "71d03ff0-8dd7-4345-9499-fd4d361a34ad", "thread_id": "71d03ff0-8dd7-4345-9499-fd4d361a34ad", "traced_type": "did:sov:BzCbsNYhMrjHiqZDTUASHg;spec/issue-credential/2.0/offer-credential", "timestamp": 1692800631.6346257, "str_time": "2023-08-23 14:23:51.634626", "handler": "Alice.Agent.trace", "ellapsed_milli": 56, "outcome": "Dispatcher.handle_message.END"}
Alice      | Credential: state = offer-received, cred_ex_id bfe71a17-8bb7-417e-a032-dd640c0feb33

#15 After receiving credential offer, send credential request
Alice      | 2023-08-23 14:23:51,637 aries_cloudagent.utils.tracing INFO  acapy.events {"msg_id": "N/A", "thread_id": "71d03ff0-8dd7-4345-9499-fd4d361a34ad", "traced_type": "dict:Exchange", "timestamp": 1692800631.6378086, "str_time": "2023-08-23 14:23:51.637809", "handler": "Alice.Agent.trace", "ellapsed_milli": 0, "outcome": "OutboundTransportManager.DELIVER.START.http://192.168.65.4:8032/webhooks/topic/issue_credential_v2_0/"}
Alice      | 2023-08-23 14:23:51,638 aries_cloudagent.utils.tracing INFO  acapy.events {"msg_id": "N/A", "thread_id": "71d03ff0-8dd7-4345-9499-fd4d361a34ad", "traced_type": "dict:Exchange", "timestamp": 1692800631.6383529, "str_time": "2023-08-23 14:23:51.638353", "handler": "Alice.Agent.trace", "ellapsed_milli": 3, "outcome": "OutboundTransportManager.DELIVER.END.http://192.168.65.4:8032/webhooks/topic/issue_credential_v2_0/"}
Alice      | 2023-08-23 14:23:52,934 aries_cloudagent.utils.tracing INFO  acapy.events {"msg_id": "N/A", "thread_id": "71d03ff0-8dd7-4345-9499-fd4d361a34ad", "traced_type": "dict:Exchange", "timestamp": 1692800632.9348824, "str_time": "2023-08-23 14:23:52.934882", "handler": "Alice.Agent.trace", "ellapsed_milli": 0, "outcome": "OutboundTransportManager.DELIVER.START.http://192.168.65.4:8032/webhooks/topic/issue_credential_v2_0/"}
Alice      | 2023-08-23 14:23:52,935 aries_cloudagent.utils.tracing INFO  acapy.events {"msg_id": "N/A", "thread_id": "71d03ff0-8dd7-4345-9499-fd4d361a34ad", "traced_type": "dict:Exchange", "timestamp": 1692800632.9353375, "str_time": "2023-08-23 14:23:52.935338", "handler": "Alice.Agent.trace", "ellapsed_milli": 0, "outcome": "OutboundTransportManager.DELIVER.END.http://192.168.65.4:8032/webhooks/topic/issue_credential_v2_0/"}
Alice      | Credential: state = request-sent, cred_ex_id bfe71a17-8bb7-417e-a032-dd640c0feb33
Credential request metadata:
  {
    "master_secret_blinding_data": {
      "v_prime": "31346785528995160551628960863339683057070279428952195382700472205792356114248903474354311360844840275799183667274831177045773080324916802175326548006771558202121556839192032018529624241559992405305744035284986862376766619301032909868470900787719207488146576355153351883165293567529174626190836434099560807263209920913543646012916447840450735024923978719947884718492795575341723416768743733301485960099108704902272654884709499113048111993184182763753372213171615674643720557313501854351266970523686920849635004421528189626738642381290799441784684590440049635078845736269135092146671149115616408036880685605490549698667374714488757609550173677",
      "vr_prime": null
    },
    "nonce": "702931334083252876903279",
    "master_secret_name": "alice.agent997525"
  }

Alice      | 2023-08-23 14:23:52,951 aries_cloudagent.utils.tracing INFO  acapy.events {"msg_id": "2f06f0cd-32dc-48f4-8762-d554ce5d548d", "thread_id": "71d03ff0-8dd7-4345-9499-fd4d361a34ad", "traced_type": "did:sov:BzCbsNYhMrjHiqZDTUASHg;spec/issue-credential/2.0/request-credential", "timestamp": 1692800632.95098, "str_time": "2023-08-23 14:23:52.950980", "handler": "Alice.Agent.trace", "ellapsed_milli": 1220, "outcome": "credential_exchange_send_request.END"}
Alice      | 2023-08-23 14:23:52,951 aries_cloudagent.utils.tracing INFO  acapy.events {"msg_id": "2f06f0cd-32dc-48f4-8762-d554ce5d548d", "thread_id": "71d03ff0-8dd7-4345-9499-fd4d361a34ad", "traced_type": "did:sov:BzCbsNYhMrjHiqZDTUASHg;spec/issue-credential/2.0/request-credential", "timestamp": 1692800632.9515457, "str_time": "2023-08-23 14:23:52.951546", "handler": "Alice.Agent.trace", "ellapsed_milli": 0, "outcome": "OutboundTransportManager.ENCODE.START"}
Alice      | 2023-08-23 14:23:52,951 aries_cloudagent.utils.tracing INFO  acapy.events {"msg_id": "2f06f0cd-32dc-48f4-8762-d554ce5d548d", "thread_id": "71d03ff0-8dd7-4345-9499-fd4d361a34ad", "traced_type": "did:sov:BzCbsNYhMrjHiqZDTUASHg;spec/issue-credential/2.0/request-credential", "timestamp": 1692800632.9517448, "str_time": "2023-08-23 14:23:52.951745", "handler": "Alice.Agent.trace", "ellapsed_milli": 0, "outcome": "OutboundTransportManager.ENCODE.END"}
Alice      | 2023-08-23 14:23:52,954 aries_cloudagent.utils.tracing INFO  acapy.events {"msg_id": "2f06f0cd-32dc-48f4-8762-d554ce5d548d", "thread_id": "71d03ff0-8dd7-4345-9499-fd4d361a34ad", "traced_type": "did:sov:BzCbsNYhMrjHiqZDTUASHg;spec/issue-credential/2.0/request-credential", "timestamp": 1692800632.954674, "str_time": "2023-08-23 14:23:52.954674", "handler": "Alice.Agent.trace", "ellapsed_milli": 0, "outcome": "OutboundTransportManager.DELIVER.START.http://192.168.65.4:8020"}
Alice      | 2023-08-23 14:23:52,955 aries_cloudagent.utils.tracing INFO  acapy.events {"msg_id": "2f06f0cd-32dc-48f4-8762-d554ce5d548d", "thread_id": "71d03ff0-8dd7-4345-9499-fd4d361a34ad", "traced_type": "did:sov:BzCbsNYhMrjHiqZDTUASHg;spec/issue-credential/2.0/request-credential", "timestamp": 1692800632.9549448, "str_time": "2023-08-23 14:23:52.954945", "handler": "Alice.Agent.trace", "ellapsed_milli": 0, "outcome": "OutboundTransportManager.DELIVER.END.http://192.168.65.4:8020"}
Alice      | 2023-08-23 14:23:53,363 aries_cloudagent.utils.tracing INFO  acapy.events {"msg_id": "69754f65-ed48-49a4-b836-db5519a387e1", "thread_id": "71d03ff0-8dd7-4345-9499-fd4d361a34ad", "traced_type": "did:sov:BzCbsNYhMrjHiqZDTUASHg;spec/issue-credential/2.0/issue-credential", "timestamp": 1692800633.3632233, "str_time": "2023-08-23 14:23:53.363223", "handler": "Alice.Agent.trace", "ellapsed_milli": 0, "outcome": "Dispatcher.handle_message.START"}
Alice      | 2023-08-23 14:23:53,406 aries_cloudagent.utils.tracing INFO  acapy.events {"msg_id": "69754f65-ed48-49a4-b836-db5519a387e1", "thread_id": "71d03ff0-8dd7-4345-9499-fd4d361a34ad", "traced_type": "did:sov:BzCbsNYhMrjHiqZDTUASHg;spec/issue-credential/2.0/issue-credential", "timestamp": 1692800633.4059346, "str_time": "2023-08-23 14:23:53.405935", "handler": "Alice.Agent.trace", "ellapsed_milli": 38, "outcome": "V20CredIssueHandler.handle.END"}
Alice      | Credential: state = credential-received, cred_ex_id bfe71a17-8bb7-417e-a032-dd640c0feb33
Alice      | 2023-08-23 14:23:53,423 aries_cloudagent.utils.tracing INFO  acapy.events {"msg_id": "N/A", "thread_id": "71d03ff0-8dd7-4345-9499-fd4d361a34ad", "traced_type": "dict:Exchange", "timestamp": 1692800633.4232497, "str_time": "2023-08-23 14:23:53.423250", "handler": "Alice.Agent.trace", "ellapsed_milli": 0, "outcome": "OutboundTransportManager.DELIVER.START.http://192.168.65.4:8032/webhooks/topic/issue_credential_v2_0/"}
Alice      | 2023-08-23 14:23:53,424 aries_cloudagent.utils.tracing INFO  acapy.events {"msg_id": "N/A", "thread_id": "71d03ff0-8dd7-4345-9499-fd4d361a34ad", "traced_type": "dict:Exchange", "timestamp": 1692800633.424537, "str_time": "2023-08-23 14:23:53.424537", "handler": "Alice.Agent.trace", "ellapsed_milli": 1, "outcome": "OutboundTransportManager.DELIVER.END.http://192.168.65.4:8032/webhooks/topic/issue_credential_v2_0/"}
Alice      | Credential: state = done, cred_ex_id bfe71a17-8bb7-417e-a032-dd640c0feb33
Alice      | Stored credential 144c078f-85d1-45a9-8bae-b95f58a913e7 in wallet
