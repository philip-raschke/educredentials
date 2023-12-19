<?php

// Load Moodle core
require_once(dirname(__FILE__) . '/../../../config.php');

function xmldb_local_badge_data_install(){
    global $CFG;

    // Define the base path for the Moodle directory
    $moodleBasePath = $CFG->dirroot;

    // Set the paths relative to the Moodle base path
    $originalRendererPath = $moodleBasePath . '/badges/renderer.php';
    $backupRendererPath = $moodleBasePath . '/local/badge_data/original_renderer.php';
    $newRendererPath = $moodleBasePath . '/local/badge_data/renderer.php';

    // Check if the files exist
    if (!file_exists($originalRendererPath) || !file_exists($newRendererPath)) {
        error_log('One of the required files does not exist.');
        return false;
    }

    // Perform the file operations and check for errors
    if (!rename($originalRendererPath, $backupRendererPath)) {
        error_log('Error renaming the renderer file');
        return false;
    }

    if (!copy($newRendererPath, $originalRendererPath)) {
        error_log('Error copying the new renderer file');
        return false;
    }

    return true;
}
?>
