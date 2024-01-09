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
    var createInvitationUrl = "<?php echo $CFG->plugin_create_invitation_url; ?>";
    var connectionsUrl = "<?php echo $CFG->plugin_connections_url; ?>";
    var issueCredentialUrl = "<?php echo $CFG->plugin_issue_credential_url; ?>";
    var JSONBadge = <?= json_encode($JSON_badges, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    var schemaId = 'JLXngoc4ahRhFhjZcMzvNs:2:OpenBadge:1.0';
    var credentialDefinitionId = 'VdzxjctRRqAJKEMQKdSCR5:3:CL:227059:default';
</script>
<script src="/moodle4/local/badge_data/js/badgeData.js"></script>


<?php echo $OUTPUT->footer(); ?>