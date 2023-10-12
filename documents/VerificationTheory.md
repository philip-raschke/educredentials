## eq_proof ##
- proof of equality or the presence of certain attributes

  Ex: <br>
  "eq_proof": { 
              "revealed_attrs": { 
                "credentialsubject.achievement.name": "102809581340651767048450023577215203800032978732407776553078962252139684308531", 
                "credentialsubject.name": "70834622490325883621582295781239179962139377442979710740336064791767705419608", 
                "issuer.name": "108933861706172243933391778802787198856217656643072243941363789266465579344972" 
              },

## a_prime, e, v, m, m2 ##
- values for ZKP, which can only be generated if the signature of the issuer is known
- these values can be verified with the pub key of issuer located in the corresponding DID Doc
- "a_prime": is a preliminary evidence value that shows that the subject knows a value without revealing the actual value.
- "e": The "challenge" that ensures that the respondent actually knows the data he claims to know.
- "v": a value that shows that the respondent has responded to the "challenge" and knows the original value.
- "m": these are the encoded attributes or data that the subject wants to prove. In the given JSON, these are encrypted values of data such as the name of the issuer, the name of the credential subject, the date of issuance, etc.
- "m2": A cryptographic value derived from the "m" values that helps validate the proof.

## ge_proofs ##
- stands for "Greater than or Equal" proofs
- is where a particular attribute of a credential is shown to be greater than or equal to a particular value
- In provided JSON, this section is empty, which means that such a proof has not been provided

## non_revoc_proof ##
- Evidence that the credential has not been revoked
- "null" here means that either no such evidence has been provided or corresponding credential is not revokable

## aggregated_proof ##
- aggregates the various pieces of evidence into a coherent whole to facilitate review (c_hash and c_list)

## No Holder Signature ##
- instead master_secret which only knows the holder
- holder show the verifier that he knows the master secret (ZKP)

  


  
