<?php

require_once(dirname(__FILE__) . '/../../config.php'); // Moodle-Konfigurationsdatei einbinden

$PAGE->set_url('/local/plugin/index.php'); // Seiten-URL festlegen
$PAGE->set_title('JSON Badge'); // Seiten-Titel festlegen
$PAGE->set_heading('Badge in JSON'); // Seitenüberschrift festlegen

echo $OUTPUT->header(); // Header der Seite anzeigen
echo '<div class="alert alert-success">Credentail</div>'; // Inhalt der Seite anzeigen
echo $OUTPUT->footer(); // Footer der Seite anzeigen
