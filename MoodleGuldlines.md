# Moodle Guidlines

### TODO for Plugin

- change hardcoded paths in install.php (to: "/badges/renderer.php",...)
- localhost needs to be modified: use HTTPS protocol for url

  &rarr; Global variable ($CFG), to store URLs and use them

  &rarr; define global variable in config.php and load the urls there 

- Better error handling for function for functions: getIssuerIdFromCurl() and getTheirDid()

  &rarr; Check whether cURL requests are successful and handle HTTP error codes appropriately

- String APIs

  &rarr; lang/en Ordner erstellen. Aus
  '''
  $errorMessages['issuer_not_connected'] = 'Issuer Agent is not running';" wird 

  '''
