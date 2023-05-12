<?php
// Load Moodle core
require_once(dirname(__FILE__) . '/../../../config.php');

function xmldb_local_badge_data_uninstall(){
    copy($CFG->dirroot.'/local/badge_data/original_renderer.php',$CFG->dirroot.'/badges/renderer.php');
}

/*

function xmldb_local_yourpluginname_uninstall() {
    global $CFG;

    // Restore original file
    $original_file = file_get_contents($CFG->dirroot . '/badges/renderer.php');
    file_put_contents($CFG->dirroot . 'local/badge_data/renderer.php', $original_file);
}

*/
