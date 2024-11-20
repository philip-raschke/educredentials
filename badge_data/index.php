<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/badgeslib.php');
require_once($CFG->libdir . '/filelib.php');

require_login();

if (empty($CFG->enablebadges)) {
    throw new \moodle_exception('badgesdisabled', 'badges');
}

// Fetch user badges
$badges = badges_get_user_badges($USER->id);
$badges_detail = [];
foreach ($badges as $badge_id => $badge) {
    $badges_detail[$badge_id] = new \core_badges\output\issued_badge($badge_id);
}

// Prepare data for JavaScript
$JSON_badges = [];
$errorMessages = [];

// Functions
function getIssuerIdFromCurl() {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost:8021/wallet/did");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("accept: application/json"));

    $response = curl_exec($ch);

    curl_close($ch);

    $responseDecoded = json_decode($response, true);

    if (!isset($responseDecoded['results'][0]['did'])) {
        return 'Unknown';
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

    curl_close($ch);

    $responseDecoded = json_decode($response, true);

    // Only consider active connections
    if (isset($responseDecoded['results'])) {
        foreach ($responseDecoded['results'] as $connection) {
            if ($connection['state'] === 'active') {
                return $connection['their_did'];
            }
        }
    }

    // No active connections found
    return 'Unknown';
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

// Prepare badges data
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

// Set up the page
$PAGE->set_url(new moodle_url('/local/badge_data/index.php'));
$PAGE->set_title('Download Badges');
$PAGE->set_heading('Download Badges');
echo $OUTPUT->header();

echo '<style>
    .page-header-headings {
        color: #C40D20;
        font-size: 42px;
        font-family: "Helvetica", sans-serif;
    }
</style>';

// Status Messages Container
echo '<div id="status-messages">';
echo '    <div id="connection-status"></div>';
echo '</div>';

// Sub Heading
echo '<div class="intro-text">';
echo '<p>Download your language badges as verifiable credentials</p>';
echo '</div>';

// Output any other error messages
foreach ($errorMessages as $message) {
    echo $OUTPUT->notification($message, 'notifyproblem');
}

?>
<!-- Include QR Code library -->
<script src="https://cdn.jsdelivr.net/npm/qrcode-generator/qrcode.min.js"></script>

<div class="container custom-container">
    <!-- Main Content -->
    <div class="main-content">
        <!-- QR Code Column -->
        <div class="qrcode-column">
            <!-- QR Code Header -->
            <div class="qr-header">
                <h3>Invitation QR Code</h3>
            </div>
            <!-- QR Code Frame -->
            <div class="text-center">
                <div id="qr-code-frame">
                    <!-- QR code or placeholder -->
                    <div id="qr-code-placeholder">
                        <p class="text-muted">Press the button below to create an invitation QR code you can scan with your mobile wallet.</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Badges Column -->
        <div class="badges-column">
            <!-- Badges Header -->
            <div class="badges-header">
                <h3>Your Badges</h3>
            </div>
            <!-- Badges List with frame -->
            <div class="badges-frame">
                <div class="list-group" id="badges-list">
                    <?php foreach ($badges_detail as $badge_id => $badge) { ?>
                        <a href="#" class="list-group-item list-group-item-action" onclick="selectBadge('<?php echo $badge_id; ?>', this); return false;">
                            <img src="<?php echo $badge->issued['badge']['image']; ?>" alt="<?php echo $badge->issued['badge']['name']; ?>" class="img-thumbnail">
                            <?php echo $badge->issued['badge']['name']; ?>
                        </a>
                    <?php } ?>
                </div>
            </div>
            <!-- Issue Credential Button -->
            <div class="mt-4 text-center">
                <button id="issue-credential-button" class="btn btn-custom btn-disabled" onclick="issueCredential();" disabled>
                    Issue Credential to Holder Wallet
                </button>
            </div>
        </div>
        <!-- Guide Column -->
        <div class="guide-column">
            <!-- Guide Header -->
            <div class="guide-header">
                <h3>Guide</h3>
            </div>
            <!-- Instructional Text -->
            <div class="instructional-text">
                <ol>
                    <li>Scan the QR code with your mobile wallet app.</li>
                    <li>Once your wallet is connected, select the language badge you would like to be issued to you as a verifiable credential.</li>
                    <li>Press the "Issue Credential to Holder Wallet" button to receive the credential in your mobile wallet app.</li>
                </ol>
            </div>
        </div>
    </div>
        <!-- Service Unavailable Popup Modal -->
    <div id="service-unavailable-modal" class="modal">
        <div class="modal-content">
            <h2>Service Unavailable</h2>
            <p>This service is not available at this time. Contact your IT department.</p>
            <button id="go-back-button" class="btn btn-custom">Go Back</button>
        </div>
    </div>
</div>

<style>
    body {
        font-family: 'Helvetica', sans-serif;
    }

    .custom-container {
        max-width: 1778px;
        padding-left: 0;
    }

    .main-content {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: flex-start;
    }

    .qrcode-column {
        min-width: 300px;
        margin-top: 50px;
        margin-left: 40px;
        margin-right: 40px;
    }

    .badges-column {
        flex: 1;
        min-width: 300px;
        margin-top: 50px;
        margin-left: 40px;
    }

    .guide-column {
        flex: 1;
        min-width: 300px;
        margin-top: 50px;
        margin-right: 40px;
    }

    .qrcode-column {
        order: 2;
    }

    .badges-column {
        order: 3;
    }

    .guide-column {
        order: 1;
    }

    .badges-header {
        background-color: #C40D20;
        color: #fff;
        text-align: left;
        padding: 10px;
        margin-bottom: 0;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px
    }

    .badges-header h3 {
        margin: 0;
        font-size: 1.2rem;
    }

    .badges-frame {
        padding: 0;
    }

    .list-group {
    display: flex;
    flex-direction: column;
    padding-left: 0;
    margin-bottom: 0;
    margin-top: -4px;
    border-top-left-radius: 0px;
    border-top-right-radius: 0px;
    border-bottom-left-radius: 15px;
    border-bottom-right-radius: 15px;
    }

    .list-group-item {
        border: 2px solid #C40D20;
        font-size: 19px;
        color: solid #000;
    }

    .list-group-item.active {
        background-color: #C40D20 !important;
        border-color: #C40D20 !important;
        color: #fff !important;
    }

    .img-thumbnail {
        width: 60px;
        height: 50px;
        margin-right: 10px;
        border: 2px solid #000;
        border-radius: 3.2px;
    }

    .qr-header {
        background-color: #C40D20;
        color: #fff;
        text-align: left;
        padding: 10px;
        margin-bottom: 0;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px
    }

    .qr-header h3 {
        margin: 0;
        font-size: 1.2rem;
    }

    #qr-code-frame {
        width: 410px;
        height: 379px;
        border: 2px solid #C40D20;
        padding: 0;
        box-sizing: border-box;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: -1px auto;
        border-bottom-left-radius: 15px;
        border-bottom-right-radius: 15px
    }

    .guide-header {
        background-color: #C40D20;
        color: #fff;
        text-align: left;
        padding: 10px;
        margin-bottom: 0;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px
    }

    .guide-header h3 {
        margin: 0;
        font-size: 1.2rem;
    }

    .instructional-text {
        padding: 10px;
        border: 2px solid #C40D20;
        border-bottom-left-radius: 15px;
        border-bottom-right-radius: 15px
    }

    .instructional-text ol {
        list-style-type: decimal;
        padding-left: 20px;
    }

    .instructional-text li {
        margin-bottom: 10px;
        font-size: 19px;
        line-height: 1.5;
    }

    .btn-custom {
        background-color: #C40D20;
        border-color: #C40D20;
        color: #fff;
        font-family: 'Helvetica', sans-serif;
        padding: 10px 20px;
        font-size: 1rem;
        margin: 10px 0;
    }

    .btn-custom:hover, .btn-custom:focus {
        background-color: #a20b19;
        border-color: #a20b19;
        color: #fff;
    }

    .btn-disabled {
        background-color: #cccccc !important;
        border-color: #cccccc !important;
        color: #666666 !important;
        cursor: not-allowed !important;
    }

    .intro-text {
        margin-bottom: 20px;
        font-size: 24px;
        color: #000;
    }

    .intro-text p {
        margin: 0;
    }

    #status-messages {
        position: absolute;
        top: 50px;
        right: 86px;
        z-index: 1000;
        display: flex;
        flex-direction: row;
        align-items: center;
    }

    .status-button {
        display: inline-block;
        height: 52px;
        width: 298px;
        padding: 10px 20px;
        font-size: 18px;
        border: 2px solid #C40D20;
        border-radius: 15px;
        color: #C40D20;
        background-color: transparent;
        text-align: center;
        font-family: 'Helvetica', sans-serif;
    }

    .status-button.connected {
        background-color: #C40D20;
        color: #fff;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1001;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        background-color: #fff;
        margin: 15% auto;
        padding: 20px;
        border: 2px solid #C40D20;
        width: 80%;
        max-width: 500px;
        border-radius: 5px;
        text-align: center;
    }

    .modal-content h2 {
        color: #C40D20;
        margin-bottom: 20px;
    }

    .modal-content p {
        font-size: 18px;
        margin-bottom: 30px;
    }

    .modal-content .btn-custom {
        margin-top: 20px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .main-content {
            flex-direction: column;
            align-items: center;
        }

        .qrcode-column,
        .badges-column,
        .guide-column {
            margin: 0 0 20px 0;
        }

        #status-messages {
            position: static;
            text-align: center;
            margin-bottom: 10px;
            flex-direction: column;
            gap: 5px;
        }
    }
</style>

<script>
    var badgesData = <?php echo json_encode($JSON_badges); ?>;
    var selectedBadgeId = null;
    var JSONBadge = <?php echo json_encode($JSON_badges, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    var schemaId = '935vGALagzCDG5nNZnDPGj:2:OpenBadge:1.0'; //Copy your Schema ID here
    var credentialDefinitionId = '935vGALagzCDG5nNZnDPGj:3:CL:2478748:default'; //Copy your Credential Definition ID here
    var connectionId = ''; // Variable to store Connection ID
    var isHolderWalletConnected = false; // Tracks if the holder wallet is connected
    var modalDisplayed = false; //Error message if no issuing agent connected

    function selectBadge(badgeId, elem) {
        selectedBadgeId = badgeId;

        var items = document.querySelectorAll('#badges-list .list-group-item');
        items.forEach(function(item) {
            item.classList.remove('active');
        });

        elem.classList.add('active');

        // Check if the holder wallet is connected
        var issueCredentialButton = document.getElementById('issue-credential-button');
        if (isHolderWalletConnected) {
            // Enable the issue credential button
            issueCredentialButton.disabled = false;
            issueCredentialButton.classList.remove('btn-disabled');
        }
    }

    // Creates new invitation and the QR code
    function runCurl() {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "http://localhost:8021/connections/create-invitation?alias=Alice", true);
        xhr.setRequestHeader("accept", "application/json");
        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    var invitationData = response.invitation_url;

                    // Container for QR code
                    var qrCodeContainer = document.getElementById('qr-code-frame');
                    qrCodeContainer.innerHTML = '';

                    // Generate QR code
                    var qr = qrcode(0, 'L');
                    qr.addData(invitationData);
                    qr.make();

                    // Create QR code image
                    var qrCodeImg = document.createElement('img');
                    qrCodeImg.src = qr.createDataURL(10);

                    // Make sure the QR code image fits within the frame
                    qrCodeImg.style.maxWidth = '100%';
                    qrCodeImg.style.maxHeight = '100%';

                    qrCodeContainer.appendChild(qrCodeImg);

                }
            }
        };
        xhr.send("{}");
    }

    function issueCredential() {
        var issueCredentialButton = document.getElementById('issue-credential-button');
        if (issueCredentialButton.disabled) {
            return;
        }

        if (!selectedBadgeId) {
            alert("Please select a badge first.");
            return;
        }

        var xhr = new XMLHttpRequest();
        xhr.open("GET", "http://localhost:8021/connections", true);
        xhr.setRequestHeader("accept", "application/json");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    var connections = response.results || [];
                    var activeConnection = null;

                    // Find active connection
                    for (var i = 0; i < connections.length; i++) {
                        if (connections[i].state === 'active') {
                            activeConnection = connections[i];
                            break;
                        }
                    }

                    if (activeConnection) {
                        connectionId = activeConnection.connection_id;

                        var selectedBadgeData = JSONBadge[selectedBadgeId];

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
                            "comment": "Issuing OpenBadge credential",
                            "connection_id": connectionId,
                            "credential_preview": {
                                "@type": "issue-credential/2.0/credential-preview",
                                "attributes": selectedBadgeData
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
                        alert("Error: No active connections found.");
                    }
                } else {
                    alert("Error: Unable to get connections.");
                }
            }
        };
        xhr.send();
    }

    function checkConnectionStatus() {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "http://localhost:8021/connections", true);
        xhr.setRequestHeader("accept", "application/json");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                var connectionStatusDiv = document.getElementById('connection-status');
                var issueCredentialButton = document.getElementById('issue-credential-button');
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    var connections = response.results || [];
                    var hasActiveConnection = false;

                    // Check if any connection is in 'active' state
                    for (var i = 0; i < connections.length; i++) {
                        var connection = connections[i];
                        if (connection.state === 'active') {
                            hasActiveConnection = true;
                            break;
                        }
                    }

                    if (hasActiveConnection) {
                        isHolderWalletConnected = true;
                        // Holder wallet is connected
                        connectionStatusDiv.innerHTML = '<div class="status-button connected">Holder Wallet is connected</div>';
                        // Enable the issue credential button if a badge is selected
                        if (selectedBadgeId) {
                            issueCredentialButton.disabled = false;
                            issueCredentialButton.classList.remove('btn-disabled');
                        }
                    } else {
                        isHolderWalletConnected = false;
                        // No active connections found
                        connectionStatusDiv.innerHTML = '<div class="status-button">No Holder Wallet is connected</div>';
                        // Disable the issue credential button
                        issueCredentialButton.disabled = true;
                        issueCredentialButton.classList.add('btn-disabled');
                    }
                } else {
                    isHolderWalletConnected = false;
                    // Error connecting to the agent
                    connectionStatusDiv.innerHTML = '<div class="status-button">No agent found</div>';
                    // Disable the issue credential button
                    issueCredentialButton.disabled = true;
                    issueCredentialButton.classList.add('btn-disabled');
                }
            }
        };
        xhr.send();
    }

    // Check Issuer Agent Connection Status
    function checkIssuerAgentStatus() {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "http://localhost:8021/wallet/did", true);
        xhr.setRequestHeader("accept", "application/json");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                var issuerStatusDiv = document.getElementById('issuer-connection-status');
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.results && response.results.length > 0) {
                        // Issuer agent is connected; do nothing
                    } else {
                        // No issuer agent found
                        showServiceUnavailablePopup();
                        clearIntervals();
                    }
                } else {
                    // Error connecting to the issuer agent
                    showServiceUnavailablePopup();
                    clearIntervals();
                }
            }
        };
        xhr.send();
    }

    function showServiceUnavailablePopup() {
        if (!modalDisplayed) {
            modalDisplayed = true;

            // Show the modal
            var modal = document.getElementById('service-unavailable-modal');
            modal.style.display = 'block';

            // Event listener for the "Go Back" button
            var goBackButton = document.getElementById('go-back-button');
            goBackButton.addEventListener('click', function() {
                window.history.back();
            });
        }
    }

    function clearIntervals() {
        clearInterval(checkConnectionStatusInterval);
        clearInterval(checkIssuerAgentStatusInterval);
    }

    // Check issuer agent connection status
    checkIssuerAgentStatus();
    var checkIssuerAgentStatusInterval = setInterval(checkIssuerAgentStatus, 5000);

    // Check holder wallet connection status
    checkConnectionStatus();
    var checkConnectionStatusInterval = setInterval(checkConnectionStatus, 5000);

    runCurl();

</script>
<?php
echo $OUTPUT->footer();
?>
