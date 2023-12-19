<?php

// Load Moodle core
// 1. Load the Moodle core. This is essential to access all Moodle functionalities and configurations.
require_once(dirname(__FILE__) . '/../../../config.php');

function xmldb_local_badge_data_install(){
    // 3. Define the base path for the Moodle directory to enable relative path specification.
    //    This approach ensures that the code is server-independent and more portable.
    global $CFG;

    // 4. Set the paths for the required file operations relative to the Moodle base path.
    //    a. Path to the original 'renderer.php' in the 'badges' directory. This file is essential for badge rendering.
    $moodleBasePath = $CFG->dirroot;

    // 4. Set the paths for the required file operations relative to the Moodle base path.
    //    a. Path to the original 'renderer.php' in the 'badges' directory of Moodle instance.
    $originalRendererPath = $moodleBasePath . '/badges/renderer.php';
    //    b. Path for backing up the original 'renderer.php' in the plugin directory, for safekeeping.
    $backupRendererPath = $moodleBasePath . '/local/badge_data/original_renderer.php';
    //    c. Path to the new 'renderer.php' in the plugin directory. This is the file that will replace the original.
    $newRendererPath = $moodleBasePath . '/local/badge_data/renderer.php';

    
    // 5. Check the existence of the required files.
    //    If any file is missing, log the error for debugging purposes and abort the installation.
    if (!file_exists($originalRendererPath) || !file_exists($newRendererPath)) {
        error_log('One of the required files does not exist.');
        return false;
    }

    // 6. Perform the file operations with error checking.
    //    a. Rename the original 'renderer.php' from the 'moodle4/badges' directory to keep it as a backup as 'original_renderer.php' in the plugin directory.
    //       Logging errors helps in diagnosing issues during the plugin installation process.
    if (!rename($originalRendererPath, $backupRendererPath)) {
        error_log('Error renaming the renderer file');
        return false;
    }

    //    b. Copy the new 'renderer.php' from the plugin directory to the location of the original file in the 'moodle4/badges' directory.
    //       This operation is critical to replace the renderer with the customized version.
    if (!copy($newRendererPath, $originalRendererPath)) {
        error_log('Error copying the new renderer file');
        return false;
    }

    // 7. Return 'true' if all file operations were successful, indicating a successful installation.
    return true;
}
?>
