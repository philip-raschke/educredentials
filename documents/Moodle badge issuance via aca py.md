## Prerequisites
- Schema and credential definitions must be created.
- Faber agent must be running.

## Process
1. Alice clicks Button 1 in Moodle.
2. -> Displays Invitation Data for connecting to Faber's Agent.
3. Alice connects her own agent to Faber's agent by pasting the Invitation Data into her agent.
4. Alice clicks Button 2.
5. -> Faber retrieves the badge (json) and issues it to Alice automatically.

- Faber belongs to Moodle.

  ## Notes
  - Invitation Data: {"@type": "did:sov:BzCbsNYhMrjHiqZDTUASHg;spec/connections/1.0/invitation", "@id": "9ed31ff5-bfec-4151-b509-ed40223a6c8a", "serviceEndpoint": "http://192.168.65.4:8020", "label": "Faber.Agent", "recipientKeys": ["FTkzF9B8Pd7YbkcB8CKE6VyjKkSdBAocqJKQkx4DKR1i"]}
 -> id and recipientKeys are always different
