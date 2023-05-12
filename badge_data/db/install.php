<?php

// Load Moodle core
require_once(dirname(__FILE__) . '/../../../config.php');

function xmldb_local_badge_data_install(){
    rename($CFG->dirroot.'/badges/renderer.php',$CFG->dirroot.'/local/badge_data/original_renderer.php');
    copy($CFG->dirroot.'/local/badge_data/renderer.php',$CFG->dirroot.'/badges/renderer.php');
}
?>