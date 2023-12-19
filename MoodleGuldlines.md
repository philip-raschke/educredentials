# Moodle Guidlines

### TODO for Plugin

- change hardcoded paths in install.php (to: "/badges/renderer.php",...) -> DONE

- localhost needs to be modified: use HTTPS protocol for url

  &rarr; Global variable ($CFG), to store URLs and use them

  &rarr; define global variable in config.php and load the urls there 

- Better error handling for function for functions: getIssuerIdFromCurl() and getTheirDid()

  &rarr; Check whether cURL requests are successful and handle HTTP error codes appropriately

- String APIs for internationality (Example in the following)

  &rarr; lang/en Ordner erstellen. Aus

  &rarr; $errorMessages['issuer_not_connected'] = 'Issuer Agent is not running';" wird zu "$string['issuer_not_connected'] = 'Issuer Agent is not running'; $errorMessages['issuer_not_connected'] = get_string('issuer_not_connected', 'local_badge_data');"

- Coding Styles of moodle

  &rarr; functions and variables in lower camel case ("$jsonBadges = [];" instead of "$JSON_badges = [];")

  &rarr; Correct Indentation (4 spaces)

  &rarr; PHPDoc style for comments (in function is different to outside function) and write good documentation in the code

  &rarr; less global variables (when used, then with clear documentation)

  &rarr; string concartenation (spaces around the dot)

- Input Validation

  &rarr; Example: Correct cleaning of "optional_param", validate curl responses, validate badge id in  from extractBadgeID() function, no error masseges that include secret data, maybe "require_login()" to only give access to authorized people

- HTML code in Mustage template files instead of inserting directly into the PHP file
  
- Outsource Javascirpt code to AMD modules/files
  
- Outsource CSS code to files
  
   

  
