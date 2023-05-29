# Moodle Badges JSON Plugin

The Moodle Badges JSON Plugin is designed for Moodle version 4.1.2 or higher. It enables students or course participants to view their earned badges in JSON format, which can be useful for transferring them to a Self-Sovereign Identity (SSI) agent.

## Installation

1. Ensure you have Moodle version 4.1.2 or higher installed.
2. Download the plugin folder (badeg_data) from https://github.com/pherbke/educredentials.
3. Extract the plugin folder and copy it into the "local" directory of your Moodle installation.
4. Start Moodle in your browser as an administrator.
5. Follow the prompts to update Moodle.
6. Once the installation is complete, the plugin should be installed and ready to use.

## Usage

1. After installing the plugin, log in to Moodle as a course participant.
2. Navigate to your profile (user/profile.php) and click on one of the badges you have been awarded.
3. Click on "Manage badges" to access the plugin page (/badges/mybadges.php).
4. On the left side of the page, you will find a list of all your badges.
5. Clicking on a badge will display its corresponding JSON format in the field on the right side.
6. Below the JSON field, you will find a copy button.
7. Clicking on this button will copy the JSON badge to your clipboard.
