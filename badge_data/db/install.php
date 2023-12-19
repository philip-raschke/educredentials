<?php

// Load Moodle core
// 1. Load the Moodle core. This ensures all Moodle functionalities and configurations are available.
require_once(dirname(__FILE__) . '/../../../config.php');

function xmldb_local_badge_data_install(){
    // 2. Declare the global configuration variable $CFG to access Moodle configurations.
    global $CFG;

    // 3. Define the base path for the Moodle directory to enable relative path specification.
    // This makes the code independent of the specific server or Moodle installation.
    $moodleBasePath = $CFG->dirroot;

    // 4. Set the paths for the required file operations relative to the Moodle base path.
    // a. Path to the original 'renderer.php' in the 'badges' directory of Moodle instance.
    $originalRendererPath = $moodleBasePath . '/badges/renderer.php';
    // b. Path for backing up the original 'renderer.php' in the plugin directory.
    $backupRendererPath = $moodleBasePath . '/local/badge_data/original_renderer.php';
    // c. Path to the new 'renderer.php' in the plugin directory.
    $newRendererPath = $moodleBasePath . '/local/badge_data/renderer.php';

    
    // 5. Check the existence of the required files.
    // If any of the files do not exist, log an error and exit the function.
    if (!file_exists($originalRendererPath) || !file_exists($newRendererPath)) {
        error_log('One of the required files does not exist.');
        return false;
    }

    // 6. Perform the file operations with error checking.
    // a. Rename the original 'renderer.php' for backup purposes.
    if (!rename($originalRendererPath, $backupRendererPath)) {
        error_log('Error renaming the renderer file');
        return false;
    }

    // 7. Return 'true' if all operations were successful.
    if (!copy($newRendererPath, $originalRendererPath)) {
        error_log('Error copying the new renderer file');
        return false;
    }

    return true;
}
?>
