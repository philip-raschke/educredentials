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

#Openbadges without shading
foreach ($badges_detail as $badge_id => $badge) {
    $JSON_badges[$badge_id] = [
        [
            "name" => "@context",
            "value" => $badge->issued['badge']['@context'],
        ],
        [
            "name" => "id",
            "value" => $badge->issued['badge']['id'],
        ],
        [
            "name" => "type",
            "value" => "VerifiableCredential,OpenBadgeCredential",
        ],
        [
            "name" => "name",
            "value" => $badge->issued['badge']['name'],
        ],
        [
            "name" => "issuer.id",
            "value" => $badge->issued['badge']['issuer']['id'],
        ],
        [
            "name" => "issuer.name",
            "value" => $badge->issued['badge']['issuer']['name'],
        ],
        [
            "name" => "issuer.issuanceDate",
            "value" => date('c', $badge->issued['badge']['issuedOn']),
        ],
        [
            "name" => "credentialSubject.id",
            "value" => $badge->recipient->id,
        ],
        [
            "name" => "credentialSubject.name",
            "value" => $badge->recipient->firstname . ($badge->recipient->middlename === "" ? "" : " " . $badge->recipient->middlename) . " " . $badge->recipient->lastname,
        ],
        [
            "name" => "credentialSubject.achievement.id",
            "value" => $badge->issued['badge']['id'],
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
            "name" => "credentialSubject.achievement.criteria.id",
            "value" => $badge->issued['badge']['criteria']['id'],
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
    var badgesData = <?php echo json_encode($JSON_badges); ?>;
    document.getElementById('JSON').value = '';

    var JSONBadge = <?= json_encode($JSON_badges, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    var schemaId = '8d96MpQ4qHJATWfKcqruns:2:OpenBadge:1.0'; // Statische Schema-ID
    var credentialDefinitionId = 'P4ohtfpDvBfZ3zq27j5qcB:3:CL:226299:default'; // Statische Credential-Definition-ID, muss vom issuer bei erstellung über swagger angepasst werden!!!!
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
        xhr.open("GET", "http://192.168.224.1:8021/connections", true);
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
                        xhr2.open("POST", "http://192.168.224.1:8021/issue-credential-2.0/send", true);
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