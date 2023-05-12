<?php



require_once(dirname(__FILE__) . '/../../config.php'); // Moodle-Konfigurationsdatei einbinden
#require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/badgeslib.php');
require_once($CFG->libdir . '/filelib.php');

$page        = optional_param('page', 0, PARAM_INT);
$search      = optional_param('search', '', PARAM_CLEAN);
$clearsearch = optional_param('clearsearch', '', PARAM_TEXT);
$download    = optional_param('download', 0, PARAM_INT);
$hash        = optional_param('hash', '', PARAM_ALPHANUM);
$downloadall = optional_param('downloadall', false, PARAM_BOOL);
$hide        = optional_param('hide', 0, PARAM_INT);
$show        = optional_param('show', 0, PARAM_INT);

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

$PAGE->set_url('/local/badge_data/index.php'); // Seiten-URL festlegen
$PAGE->set_title('JSON Badge'); // Seiten-Titel festlegen
$PAGE->set_heading('Badge in JSON'); // Seitenüberschrift festlegen

#var_dump($PAGE);die;

echo $OUTPUT->header(); // Header der Seite anzeigen


#echo $output->render($userbadges);
$JSON_badges = [];


foreach ($badges_detail as $badge_id=>$badge){

    $JSON_badges[$badge_id] = [
        "@context"=> [$badge->issued['badge']['@context']],
        "id"=> [$badge->issued['badge']['id']],
        "type"=> [
            "VerifiableCredential",
            "OpenBadgeCredential"
          ],
        "name"=> $badge->issued['badge']['name'],
        "issuer"=> [
            "id"=> $badge->issued['badge']['issuer']['id'],
            "name"=> $badge->issued['badge']['issuer']['name'],
            "issuanceDate" => date('c',$badge->issued['badge']['issuedOn']),
        ],
        "credentialSubject"=> [
            "id"=> $badge->recipient->id,
            "name"=> $badge->recipient->firstname.($badge->recipient->middlename===""?"": " ".$badge->recipient->middlename)." ".$badge->recipient->lastname,
            "achievement"=> [
                "id"=> [$badge->issued['badge']['id']],
                #"type"=> ['Achievement'],
                #"achievementType"=> "BachelorDegree",
                "name"=> $badge->issued['badge']['name'],
                "description"=> $badge->issued['badge']['description'], 
                "criteria"=> $badge->issued['badge']['criteria'],
                    
            ]
        ]

    ];

}


?>

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <ul class="badges">

<?php
#var_dump(reset($badges));die;
#var_dump(reset($badges_detail));die;
foreach ($badges_detail as $badge_id=>$badge){
?>

                <li onclick = "fillTextarea('<?=$badge_id?>',this); ">
                    <a title="<?=$badge->issued['badge']['name']?>">
                        <img src="<?=$badge->issued['badge']['image']?>" class="badge-image" alt="">
                    </a>
                    <span class="badge-name"><?=$badge->issued['badge']['name']?></span>
                </li>

<?php

}

?>

            </ul>
        </div>
        <div class="col text-center">
            <textarea class="w-100" id= "JSON" readonly style="resize:both !important;height:300px;"></textarea>
            <button id="copy-button" class="btn btn-primary mt-3 p-3" onclick="navigator.clipboard.writeText(document.getElementById('JSON').value);">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-clipboard-fill" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M10 1.5a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-1Zm-5 0A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5v1A1.5 1.5 0 0 1 9.5 4h-3A1.5 1.5 0 0 1 5 2.5v-1Zm-2 0h1v1A2.5 2.5 0 0 0 6.5 5h3A2.5 2.5 0 0 0 12 2.5v-1h1a2 2 0 0 1 2 2V14a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V3.5a2 2 0 0 1 2-2Z"></path>
                </svg>
            </button>
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
</style>
<script>
    document.getElementById('JSON').value = '';
    document.getElementById('copy-button').setAttribute('disabled','');

    var JSONBadge = <?=json_encode($JSON_badges,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)?>;

    function fillTextarea(badgeId,liElem) {
        document.getElementById('JSON').value = JSON.stringify(JSONBadge[badgeId],null,"\t");
        document.getElementById('copy-button').removeAttribute('disabled');
        liElem.parentElement.children.forEach(function(elem){
            elem.classList.remove('selected');
        });
        liElem.classList.add('selected');
    }
</script>

<?php

 
#echo '<div class="alert alert-success">Credentail</div>'; // Inhalt der Seite anzeigen


echo $OUTPUT->footer(); // Footer der Seite anzeigen
