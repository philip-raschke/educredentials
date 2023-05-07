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



#$badge = new \core_badges\output\issued_badge(key($badges));


$output = $PAGE->get_renderer('core', 'badges');

$PAGE->set_url('/local/badge_data/index.php'); // Seiten-URL festlegen
$PAGE->set_title('JSON Badge'); // Seiten-Titel festlegen
$PAGE->set_heading('Badge in JSON'); // Seitenüberschrift festlegen



echo $OUTPUT->header(); // Header der Seite anzeigen


#echo $output->render($userbadges);
$JSON_badges = [];


foreach ($badges as $badge_id=>$badge){

    

    $badge_obj = new \core_badges\output\issued_badge($badge_id);

    $JSON_badges[$badge_id] = [
        "@context"=> [$badge_obj->issued['badge']['@context']],
        "id"=> [$badge_obj->issued['badge']['id']],


    ];

}


foreach ($badges as $badge_id=>$badge){

    

    ?>
    <script>
        var  = 
    </script>
    <div onclick = "document.getElementById('JSON').value = '<?= json_encode($JSON_badges[$badge_id], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)?>'; ">
    <?= $badge->name?>
    </div>

    <?php

}

?>

<textarea id= "JSON" readonly>
    Test
</textarea>


<?php

 
#echo '<div class="alert alert-success">Credentail</div>'; // Inhalt der Seite anzeigen


echo $OUTPUT->footer(); // Footer der Seite anzeigen
