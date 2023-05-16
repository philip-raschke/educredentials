<?php

// Load Moodle core
require_once(dirname(__FILE__) . '/../../../config.php');

function xmldb_local_badge_data_install(){
    rename('C:\wamp64\www\moodle4\badges\renderer.php', 'C:\wamp64\www\moodle4\local\badge_data\original_renderer.php');
    copy('C:\wamp64\www\moodle4\local\badge_data\renderer.php','C:\wamp64\www\moodle4\badges\renderer.php');
}
?>