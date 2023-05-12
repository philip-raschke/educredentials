<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Extend the my badges page.
 *
 * @param string $content The page content.
 * @param object $page The page object.
 * @return string The modified page content.
 */
function local_mypluginname_extend_mybadges($content, $page) {
    global $CFG;

    // Check if we're on the my badges page.
    if ($page->url->compare(new moodle_url('/badges/mybadges.php'), URL_MATCH_BASE)) {
        // Add a link to the page header.
        #$link = html_writer::link($CFG->wwwroot . 'local/badge_data/', 'JSON Badges');
        #$content = str_replace('<div id="page-header">', '<div id="page-header">' . $link, $content);
        $content = preg_replace('~(name="downloadall".*?</form>\s*</div></div></div>)~ui','$1<a class="btn btn-secondary" href="'.$CFG->wwwroot.'/local/badge_data/">JSON badges</a>',$content);
    }

    return $content;
}

// Register the page extension hook.
$hookname = '\\core\\output\\renderers\\core_renderer::standard_end_of_body_html';
$pluginname = 'local_badge_data';
$functionname = 'local_badge_data_extend_mybadges';
$priority = 999;

$handlers = array(array(
    'hook' => $hookname,
    'handlerfunction' => $functionname,
    'priority' => $priority
));

$eventmanager = \core\event\manager::instance();
$eventmanager->register_event_handlers($handlers);
