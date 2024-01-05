<?php
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/badgeslib.php');
require_once($CFG->libdir . '/filelib.php');
require_once(__DIR__ . '/plugin_config.php');


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
    global $CFG;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $CFG->plugin_wallet_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("accept: application/json"));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false || $httpCode != 200) {
        echo get_string('issuer_connection_error', 'local_badge_data');
        return false; 
    }

    $responseDecoded = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo get_string('issuer_json_error', 'local_badge_data');
        return false;
    }

    if (!isset($responseDecoded['results'][0]['did'])) {
        echo get_string('issuer_did_error', 'local_badge_data');
        return false;
    }

    return $responseDecoded['results'][0]['did'];
}


function getTheirDid() {
    global $CFG;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $CFG->plugin_connections_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("accept: application/json"));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false || $httpCode != 200) {
        echo get_string('holder_connection_error', 'local_badge_data');
        return false;
    }

    $responseDecoded = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo get_string('holder_json_error', 'local_badge_data');
        return false;
    }

    if (!isset($responseDecoded['results'][0]['their_did'])) {
        echo get_string('holder_connection_error', 'local_badge_data');
        return false;
    }

    return $responseDecoded['results'][0]['their_did'];
}







function extractBadgeId($url) {
    // Prüfen, ob die URL gültig ist
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        echo get_string('url_invalid', 'local_badge_data');
        return null;
    }

    // Parsen der Query-Parameter
    $queryParams = [];
    parse_str(parse_url($url, PHP_URL_QUERY), $queryParams);

    // Überprüfen, ob der 'id'-Parameter existiert
    if (!isset($queryParams['id']) || empty($queryParams['id'])) {
        echo get_string('badge_id_missing', 'local_badge_data');
        return null;
    }

    return $queryParams['id'];
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



foreach ($badges_detail as $badge_id => $badge) {
    // Standardwerte für issuerId und theirDid
    $issuerId = 'Unknown';
    $theirDid = 'Unknown';

    // Funktionen aufrufen und Ergebnisse überprüfen
    $fetchedIssuerId = getIssuerIdFromCurl();
    if ($fetchedIssuerId !== false) {
        $issuerId = $fetchedIssuerId;
    }

    $fetchedTheirDid = getTheirDid();
    if ($fetchedTheirDid !== false) {
        $theirDid = $fetchedTheirDid;
    }

    // Datum überprüfen und formatieren
    try {
        $issuanceDatetime = new DateTime($badge->issued['badge']['issuedOn']);
        $issuanceDate = $issuanceDatetime->format('Y-m-d\TH:i:s\Z');
    } catch (Exception $e) {
        echo get_string('date_error', 'local_badge_data', $badge_id) . ' - ' . $e->getMessage() . '<br>';
        $issuanceDate = 'Unknown';
    }

    // Erstellen des Badge-Datenarrays
    $JSON_badges[$badge_id] = [
        ["name" => "@context", "value" => $badge->issued['badge']['@context']],
        ["name" => "id", "value" => generateUUID()],
        ["name" => "type", "value" => "VerifiableCredential, OpenBadgeCredential"],
        ["name" => "name", "value" => $badge->issued['badge']['name']],
        ["name" => "issuer.id", "value" => "did:key:" . $issuerId],
        ["name" => "issuer.name", "value" => $badge->issued['badge']['issuer']['name']],
        ["name" => "issuer.issuanceDate", "value" => $issuanceDate],
        ["name" => "credentialSubject.id", "value" => "did:key:" . $theirDid],
        ["name" => "credentialSubject.name", "value" => $badge->recipient->firstname . ($badge->recipient->middlename === "" ? "" : " " . $badge->recipient->middlename) . " " . $badge->recipient->lastname],
        ["name" => "credentialSubject.achievement.id", "value" => generateUUID()],
        ["name" => "credentialSubject.achievement.name", "value" => $badge->issued['badge']['name']],
        ["name" => "credentialSubject.achievement.description", "value" => $badge->issued['badge']['description']],
        ["name" => "credentialSubject.achievement.criteria.narrative", "value" => $badge->issued['badge']['criteria']['narrative']]
    ];
}





foreach ($errorMessages as $message) {
    echo $message . '<br>';
}

?>
<script src="https://cdn.jsdelivr.net/npm/qrcode-generator/qrcode.min.js"></script>
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
            <div id="textarea-qr-code"></div> <!-- Leerer Container für den ersten QR-Code -->
            <div id="qr-code"></div> <!-- Leerer Container für den zweiten QR-Code -->
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
        text-align: center;
    }

    .qr-code-description {
        margin-top: 20px;
        text-align: center;
    }

    .description-text {
        font-weight: bold;
        display: block; /* sorgt dafür, dass der Text über dem QR-Code steht */
        margin-bottom: 10px;
    }

    .qr-code-box {
        border: 1px solid #ccc;
        padding: 10px;
        background-color: #f9f9f9;
        display: inline-block; /* passt die Größe des Containers an den Inhalt an */
    }
    
</style>

<script>
    // Validierung der aus PHP stammenden Daten
    var badgesData = <?php echo json_encode($JSON_badges); ?>;
    if (!badgesData) {
        console.error('Error: Badge data does not exist or is invalid.');
    }

    var JSONBadge = <?= json_encode($JSON_badges, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

    var schemaId = 'JLXngoc4ahRhFhjZcMzvNs:2:OpenBadge:1.0'; // Statische Schema-ID
    var credentialDefinitionId = '8h84ZyQbkXi6c6HgZS4wxz:3:CL:227059:default'; // Statische Credential-Definition-ID
    var connectionId = ''; // Variable zum Speichern der Connection-ID
    var selectedBadgeId; // Variable zum Speichern der ausgewählten Badge-ID

    // Funktion zum Erzeugen eines QR-Codes aus dem Textarea-Inhalt
    function generateTextareaQRCode() {
        var textarea = document.getElementById('JSON');
        if (!textarea) {
            console.error('Error: Textarea element was not found in the DOM.');
            return; // Frühes Beenden der Funktion, wenn das Element nicht gefunden wird
        }

        var textareaContent = textarea.value;
        if (textareaContent) {
            var qrCodeContainer = document.getElementById('textarea-qr-code');
            if (!qrCodeContainer) {
                console.error('Error: QR code container was not found in the DOM.');
                return; // Frühes Beenden der Funktion, wenn das Element nicht gefunden wird
            }

            qrCodeContainer.innerHTML = ''; // Alten Inhalt leeren

            // Erstellen der Überschrift und des Rahmens
            var label = document.createElement('span');
            label.className = 'description-text';
            label.textContent = 'Badge Data';
            qrCodeContainer.appendChild(label);

            var qrCodeBox = document.createElement('div');
            qrCodeBox.className = 'qr-code-box';
            qrCodeContainer.appendChild(qrCodeBox);

            // QR-Code generieren
            try {
                var qr = qrcode(0, 'L');
                qr.addData(textareaContent);
                qr.make();
                qrCodeBox.innerHTML = qr.createImgTag(4);
            } catch (error) {
                console.error('Error when creating the QR code: ', error);
            }
        } else {
            alert('Textarea is empty.');
        }
    }

    function fillTextarea(badgeId, liElem) {
    if (!badgeId || !liElem) {
        console.error('Error: Invalid parameters passed to fillTextarea.');
        return;
    }

    var textarea = document.getElementById('JSON');
    if (!textarea) {
        console.error('Error: Textarea element not found.');
        return;
    }

    var badgeData = JSONBadge[badgeId];
    if (!badgeData) {
        console.error('Error: Badge data not found for badge ID:', badgeId);
        return;
    }

    textarea.value = JSON.stringify(badgeData, null, "\t");
    generateTextareaQRCode(); // QR-Code aus dem Inhalt der Textarea generieren

    liElem.parentElement.children.forEach(function (elem) {
        elem.classList.remove('selected');
    });

    liElem.classList.add('selected');
    selectedBadgeId = badgeId; // Setzen Sie die ausgewählte Badge-ID
    }



    function runCurl() {
        var createInvitationUrl = "<?php echo $CFG->plugin_create_invitation_url; ?>";
        if (!createInvitationUrl) {
            alert('Error: Invitation URL is not set.');
            return;
        }

        var xhr = new XMLHttpRequest();
        xhr.open("POST", createInvitationUrl, true);
        xhr.setRequestHeader("accept", "application/json");
        xhr.setRequestHeader("Content-Type", "application/json");

        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.invitation) {
                        var qrCodeContainer = document.getElementById('qr-code');
                        qrCodeContainer.innerHTML = '';

                        var label = document.createElement('span');
                        label.className = 'description-text';
                        label.textContent = 'Invitation Data';
                        qrCodeContainer.appendChild(label);

                        var qrCodeBox = document.createElement('div');
                        qrCodeBox.className = 'qr-code-box';
                        qrCodeContainer.appendChild(qrCodeBox);

                        var qr = qrcode(0, 'L');
                        qr.addData(JSON.stringify(response.invitation));
                        qr.make();
                        qrCodeBox.innerHTML = qr.createImgTag(6);

                        var invitationDataDisplay = document.createElement('pre');
                        invitationDataDisplay.style.overflow = 'auto';
                        invitationDataDisplay.style.maxHeight = '150px'; 
                        invitationDataDisplay.textContent = JSON.stringify(response.invitation, null, 2);
                        qrCodeContainer.appendChild(invitationDataDisplay);
                    } else {
                        alert('Error: Invitation data is missing in the response.');
                    }
                } else {
                    alert('Error: Unable to execute cURL command with status ' + xhr.status);
                }
            }
        };

        xhr.onerror = function () {
            alert('Network error occurred during the HTTP request.');
        };

        xhr.send("{}");
    }







    function issueCredential() {
    var connectionsUrl = "<?php echo $CFG->plugin_connections_url; ?>";
    if (!connectionsUrl) {
        console.error('Error: Connections URL is not set.');
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open("GET", connectionsUrl, true);
    xhr.setRequestHeader("accept", "application/json");

    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.results && response.results.length > 0) {
                        connectionId = response.results[0].connection_id;
                        
                        var selectedBadgeData = JSONBadge[selectedBadgeId];
                        if (!selectedBadgeData) {
                            alert("Error: No badge selected.");
                            return;
                        }

                        sendCredential(connectionId, selectedBadgeData);
                    } else {
                        alert("Error: No connections found.");
                    }
                } catch (e) {
                    console.error('Error parsing JSON response:', e);
                }
            } else {
                alert('Error: Unable to get connections with status ' + xhr.status);
            }
        }
    };

    xhr.onerror = function () {
        console.error('Network error occurred during the HTTP request.');
    };

    xhr.send();
    }


    function sendCredential(connectionId, badgeData) {
    var issueCredentialUrl = "<?php echo $CFG->plugin_issue_credential_url; ?>";
    if (!issueCredentialUrl) {
        console.error('Error: Issue credential URL is not set.');
        return;
    }

    var xhr2 = new XMLHttpRequest();
    xhr2.open("POST", issueCredentialUrl, true);
    xhr2.setRequestHeader("accept", "application/json");
    xhr2.setRequestHeader("Content-Type", "application/json");

    xhr2.onreadystatechange = function () {
        if (xhr2.readyState === XMLHttpRequest.DONE) {
            if (xhr2.status === 200) {
                alert("Badge Issued!");
            } else {
                console.error('Error: Unable to issue the badge with status:', xhr2.status);
            }
        }
    };

    xhr2.onerror = function () {
        console.error('Network error occurred during the HTTP request.');
    };

    var dataToSend = {
        "auto_remove": true,
        "comment": "Ausstellung des OpenBadge für French A1",
        "connection_id": connectionId,
        "credential_preview": {
            "@type": "issue-credential/2.0/credential-preview",
            "attributes": badgeData
        },
        "filter": {
            "indy": {
                "cred_def_id": credentialDefinitionId,
                "schema_id": schemaId
            }
        },
        "trace": false
    };

    try {
        xhr2.send(JSON.stringify(dataToSend));
    } catch (error) {
        console.error('Error sending the credential:', error);
    }
}


</script>

<?php
echo $OUTPUT->footer();
?>