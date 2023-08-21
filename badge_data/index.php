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
                Get Invitation Data
            </button>
            <button id="issue-credential-button" class="btn btn-secondary mt-3 p-3" onclick="issueCredential();">
                Issue Credential
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
    document.getElementById('JSON').value = '';

    var JSONBadge = <?= json_encode($JSON_badges, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    var schemaId = ''; // Variable zum Speichern der Schema-ID

    function fillTextarea(badgeId, liElem) {
        document.getElementById('JSON').value = JSON.stringify(JSONBadge[badgeId], null, "\t");
        liElem.parentElement.children.forEach(function (elem) {
            elem.classList.remove('selected');
        });
        liElem.classList.add('selected');
    }

    function runCurl() {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "http://192.168.224.1:8021/connections/create-invitation?alias=Alice", true);
        xhr.setRequestHeader("accept", "application/json");
        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    var invitationData = response.invitation;
                    var resultDiv = document.getElementById("curl-result");
                    resultDiv.innerHTML = "<pre>" + JSON.stringify(invitationData, null, 2) + "</pre>";
                    resultDiv.style.display = "block"; // Zeige das Ergebnisfeld an
                } else {
                    alert("Error: Unable to execute cURL command.");
                }
            }
        };
        xhr.send("{}");
    }

    function issueCredential() {
        var curlData = {
            "schema_version": "1.0",
            "schema_name": "OpenBadges",
            "attributes": [
                "@context", "id", "type", "name", "issuer.id", "issuer.name",
                "issuer.issuanceDate", "credentialSubject.id", "credentialSubject.name",
                "credentialSubject.achievement.id", "credentialSubject.achievement.name",
                "credentialSubject.achievement.description", "credentialSubject.achievement.criteria.id",
                "credentialSubject.achievement.criteria.narrative"
            ]
        };

        var xhr1 = new XMLHttpRequest();
        xhr1.open("POST", "http://192.168.224.1:8021/schemas", true);
        xhr1.setRequestHeader("accept", "application/json");
        xhr1.setRequestHeader("Content-Type", "application/json");
        xhr1.onreadystatechange = function () {
            if (xhr1.readyState === XMLHttpRequest.DONE) {
                if (xhr1.status === 200) {
                    var response = JSON.parse(xhr1.responseText);
                    schemaId = response.schema_id;

                    // Führe den zweiten cURL-Befehl aus
                    var credentialData = {
                        "schema_id": schemaId,
                        "support_revocation": false,
                        "tag": "default"
                    };

                    var xhr2 = new XMLHttpRequest();
                    xhr2.open("POST", "http://192.168.224.1:8021/credential-definitions", true);
                    xhr2.setRequestHeader("accept", "application/json");
                    xhr2.setRequestHeader("Content-Type", "application/json");
                    xhr2.onreadystatechange = function () {
                        if (xhr2.readyState === XMLHttpRequest.DONE) {
                            if (xhr2.status === 200) {
                                // Erfolgreich ausgeführt, keine Notwendigkeit, die Antwort anzuzeigen
                            } else {
                                alert("Error: Unable to create credential definition.");
                            }
                        }
                    };
                    xhr2.send(JSON.stringify(credentialData, null, 2));
                } else {
                    alert("Error: Unable to create schema.");
                }
            }
        };
        xhr1.send(JSON.stringify(curlData, null, 2));
    }
</script>

<?php
echo $OUTPUT->footer();
?>
