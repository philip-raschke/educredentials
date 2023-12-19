<?php

// Load Moodle core
// 1. Load the Moodle core. This is essential to access all Moodle functionalities and configurations.
require_once(dirname(__FILE__) . '/../../../config.php');

function xmldb_local_badge_data_uninstall() {
    // 2. Declare the global configuration variable $CFG to access global Moodle configurations.
    global $CFG;

    // 3. Define the base path for the Moodle directory to enable relative path specification.
    //    This approach ensures that the code is server-independent and more portable.
    $moodleBasePath = $CFG->dirroot;

    // 4. Set the paths for the required file operations relative to the Moodle base path.
    //    a. Path to the backup of the original 'renderer.php' in the plugin directory.
    //       This file was saved during the plugin installation as a backup.
    $backupRendererPath = $moodleBasePath . '/local/badge_data/original_renderer.php';
    //    b. Path to the 'renderer.php' in the Moodle 'badges' directory.
    //       This is where the original renderer file will be restored from the backup.
    $originalRendererPath = $moodleBasePath . '/badges/renderer.php';

    // 5. Perform the file operation with error checking.
    //    a. Copy the original 'renderer.php' back from the backup location.
    //       This step is crucial to restore the original state of the 'renderer.php' file upon uninstallation of the plugin.
    //       Logging errors helps in diagnosing issues during the uninstallation process.
    if (!copy($backupRendererPath, $originalRendererPath)) {
        error_log('Error restoring the original renderer file');
        return false;
    }

    // 6. Return true to indicate a successful restoration and completion of the uninstallation process.
    return true;
}
?>
