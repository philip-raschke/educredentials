<?php
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/badgeslib.php');
require_once($CFG->libdir . '/filelib.php');

$page = optional_param('page', 0, PARAM_INT);
$search = optional_param('search', '', PARAM_CLEAN);
$clearsearch = optional_param('clearsearch', '', PARAM_TEXT);
$download = optional_param('download', 0, PARAM_INT);
$hash = optional_param('hash', '', PARAM_ALPHANUM);
$downloadall = optional_param('downloadall', false, PARAM_BOOL);
$hide = optional_param('hide', 0, PARAM_INT);
$show = optional_param('show', 0, PARAM_INT);

require_login();

if (empty($CFG->enablebadges)) {
    throw new \moodle_exception('badgesdisabled', 'badges');
}

$badges = badges_get_user_badges($USER->id);

$badges_detail = [];
foreach ($badges as $badge_id => $badge) {
    $badges_detail[$badge_id] = new \core_badges\output\issued_badge($badge_id);
}

$output = $PAGE->get_renderer('core', 'badges');

$PAGE->set_url('/local/badge_data/index.php');
$PAGE->set_title('JSON Badge');
$PAGE->set_heading('Badge in JSON');

echo $OUTPUT->header();

$JSON_badges = [];

// Globales Array zum Sammeln von Fehlermeldungen
$errorMessages = [];

function getIssuerIdFromCurl() {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost:8021/wallet/did");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("accept: application/json"));

    $response = curl_exec($ch);

    

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);


    $responseDecoded = json_decode($response, true);

    if (!isset($responseDecoded['results'][0]['did'])) {
        global $errorMessages;
        $errorMessages['issuer_not_connected'] = 'Issuer Server is not connected';
        return 'Unbekannt';
    }

    return $responseDecoded['results'][0]['did'];
}

function getTheirDid() {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost:8021/connections");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("accept: application/json"));

    $response = curl_exec($ch);


    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);


    $responseDecoded = json_decode($response, true);

    // Hier prüfen wir, ob 'their_did' im ersten Element des 'results'-Arrays vorhanden ist
    if (!isset($responseDecoded['results'][0]['their_did'])) {
        global $errorMessages;
        $errorMessages['no_wallet_connected'] = 'No Holder Wallet is connected';
        return 'Unbekannt';
    }

    return $responseDecoded['results'][0]['their_did'];
}



function extractBadgeId($url) {
    $queryParams = [];
    parse_str(parse_url($url, PHP_URL_QUERY), $queryParams);
    // Rückgabe der 'id' aus den Query-Parametern, wenn verfügbar.
    return isset($queryParams['id']) ? $queryParams['id'] : null;
}

function generateUUID() {
    return sprintf(
        'urn:uuid:%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}


#Openbadges without shading
foreach ($badges_detail as $badge_id => $badge) {
    $issuerId = getIssuerIdFromCurl(); 
    $theirDid = getTheirDid(); 

    
    
    $issuanceDatetime = new DateTime($badge->issued['badge']['issuedOn']);
    $issuanceDate = $issuanceDatetime->format('Y-m-d\TH:i:s\Z');
    
    $JSON_badges[$badge_id] = [
        [
            "name" => "@context",
            "value" => $badge->issued['badge']['@context'],
        ],
        [
            "name" => "id",
            "value" => generateUUID(),
        ],                
        [
            "name" => "type",
            "value" => "VerifiableCredential, OpenBadgeCredential",
        ],
        [
            "name" => "name",
            "value" => $badge->issued['badge']['name'],
        ],
        [
            "name" => "issuer.id",
            "value" => "did:key:" . $issuerId, 
        ],
        [
            "name" => "issuer.name",
            "value" => $badge->issued['badge']['issuer']['name'],
        ],
        [
            "name" => "issuer.issuanceDate",
            "value" => $issuanceDate,
        ],
        [
            "name" => "credentialSubject.id",
            "value" => "did:key:" . $theirDid,
        ],
        [
            "name" => "credentialSubject.name",
            "value" => $badge->recipient->firstname . ($badge->recipient->middlename === "" ? "" : " " . $badge->recipient->middlename) . " " . $badge->recipient->lastname,
        ],
        [
            "name" => "credentialSubject.achievement.id",
            "value" => generateUUID(),
        ],        
        [
            "name" => "credentialSubject.achievement.name",
            "value" => $badge->issued['badge']['name'],
        ],
        [
            "name" => "credentialSubject.achievement.description",
            "value" => $badge->issued['badge']['description'],
        ],
        [
            "name" => "credentialSubject.achievement.criteria.narrative",
            "value" => $badge->issued['badge']['criteria']['narrative'],
        ],
    ];
}


#Openbadges Standard with shading:
/* 
foreach ($badges_detail as $badge_id => $badge) {
    $JSON_badges[$badge_id] = [
        "@context" => [$badge->issued['badge']['@context']],
        "id" => [$badge->issued['badge']['id']],
        "type" => [
            "VerifiableCredential",
            "OpenBadgeCredential"
        ],
        "name" => $badge->issued['badge']['name'],
        "issuer" => [
            "id" => $badge->issued['badge']['issuer']['id'],
            "name" => $badge->issued['badge']['issuer']['name'],
            "issuanceDate" => date('c', $badge->issued['badge']['issuedOn']),
        ],
        "credentialSubject" => [
            "id" => $badge->recipient->id,
            "name" => $badge->recipient->firstname . ($badge->recipient->middlename === "" ? "" : " " . $badge->recipient->middlename) . " " . $badge->recipient->lastname,
            "achievement" => [
                "id" => [$badge->issued['badge']['id']],
                "name" => $badge->issued['badge']['name'],
                "description" => $badge->issued['badge']['description'],
                "criteria" => $badge->issued['badge']['criteria'],
            ]
        ]
    ];
}
*/

foreach ($errorMessages as $message) {
    echo $message . '<br>';
}

?>

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <ul class="badges">
                <?php foreach ($badges_detail as $badge_id => $badge) { ?>
                    <li onclick="fillTextarea('<?= $badge_id ?>', this);">
                        <a title="<?= $badge->issued['badge']['name'] ?>">
                            <img src="<?= $badge->issued['badge']['image'] ?>" class="badge-image" alt="">
                        </a>
                        <span class="badge-name"><?= $badge->issued['badge']['name'] ?></span>
                    </li>
                <?php } ?>
            </ul>
        </div>
        <div class="col text-center">
            <textarea class="w-100" id="JSON" readonly style="resize:both !important;height:300px;"></textarea>
            <button id="run-curl-button" class="btn btn-primary mt-3 p-3" onclick="runCurl();">
                Connect to Holder Wallet
            </button>
            <button id="issue-credential-button" class="btn btn-secondary mt-3 p-3" onclick="issueCredential();">
                Issue Credential to Holder Wallet
            </button>
            <div id="curl-result" style="margin-top: 20px;"></div>
        </div>
    </div>
</div>

<style>
    ul.badges li {
        cursor: pointer;
        border: 2px solid transparent;
        transition: border 200ms;
    }

    ul.badges li:hover,
    ul.badges li.selected {
        border: 2px solid #0f6cbf;
    }

    #curl-result {
        text-align: left;
    }
</style>

<script>
    var badgesData = <?php echo json_encode($JSON_badges); ?>;
    document.getElementById('JSON').value = '';

    var JSONBadge = <?= json_encode($JSON_badges, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    var schemaId = 'JLXngoc4ahRhFhjZcMzvNs:2:OpenBadge:1.0'; // Statische Schema-ID
    var credentialDefinitionId = '3mTVLoCjA4QUE1dzxJ3S2A:3:CL:227059:default'; // Statische Credential-Definition-ID, muss vom issuer bei erstellung über swagger angepasst werden!!!!
    var connectionId = ''; // Variable zum Speichern der Connection-ID

    var selectedBadgeId; // Variable zum Speichern der ausgewählten Badge-ID

    function fillTextarea(badgeId, liElem) {
        document.getElementById('JSON').value = JSON.stringify(JSONBadge[badgeId], null, "\t");
        liElem.parentElement.children.forEach(function (elem) {
            elem.classList.remove('selected');
        });
        liElem.classList.add('selected');
        selectedBadgeId = badgeId; // Setzen Sie die ausgewählte Badge-ID
    }

    function runCurl() {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "http://localhost:8021/connections/create-invitation?alias=Alice", true);
        xhr.setRequestHeader("accept", "application/json");
        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    var invitationData = response.invitation;
                    var resultDiv = document.getElementById("curl-result");
                    resultDiv.innerHTML = "<pre>" + JSON.stringify(invitationData, null, 2) + "</pre>";
                    resultDiv.style.display = "block";
                } else {
                    alert("Error: Unable to execute cURL command.");
                }
            }
        };
        xhr.send("{}");
    }

    function issueCredential() {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "http://localhost:8021/connections", true);
        xhr.setRequestHeader("accept", "application/json");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.results && response.results.length > 0) {
                        connectionId = response.results[0].connection_id;
                        
                        var selectedBadgeData = JSONBadge[selectedBadgeId]; // Wir holen das ausgewählte Badge durch die zuvor festgelegte selectedBadgeId
                        if (!selectedBadgeData) {
                            alert("Error: No badge selected.");
                            return;
                        }

                        var xhr2 = new XMLHttpRequest();
                        xhr2.open("POST", "http://localhost:8021/issue-credential-2.0/send", true);
                        xhr2.setRequestHeader("accept", "application/json");
                        xhr2.setRequestHeader("Content-Type", "application/json");
                        xhr2.onreadystatechange = function () {
                            if (xhr2.readyState === XMLHttpRequest.DONE) {
                                if (xhr2.status === 200) {
                                    alert("Badge Issued!");
                                } else {
                                    alert("Error: Unable to issue the badge.");
                                }
                            }
                        };
                        
                        var dataToSend = {
                            "auto_remove": true,
                            "comment": "Ausstellung des OpenBadge für French A1",
                            "connection_id": connectionId,
                            "credential_preview": {
                                "@type": "issue-credential/2.0/credential-preview",
                                "attributes": selectedBadgeData // Wir senden das ausgewählte Badge als Attribut
                            },
                            "filter": {
                                "indy": {
                                    "cred_def_id": credentialDefinitionId,
                                    "schema_id": schemaId
                                }
                            },
                            "trace": false
                        };
                        xhr2.send(JSON.stringify(dataToSend));
                    } else {
                        alert("Error: No connections found.");
                    }
                } else {
                    alert("Error: Unable to get connections.");
                }
            }
        };
        xhr.send();
}



</script>

<?php
echo $OUTPUT->footer();
?>