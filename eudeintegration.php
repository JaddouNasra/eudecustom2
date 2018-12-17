<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Moodle integration of previous company data page.
 *
 * @package    local_eudecustom
 * @copyright  2017 Planificacion de Entornos Tecnologicos SL
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../config.php');

// Restrict access if the plugin is not active.
if (is_callable('mr_off') && mr_off('eudecustom', '_MR_LOCAL')) {
    die("Plugin not enabled.");
}

require_once($CFG->dirroot . '/message/lib.php');
require_once($CFG->libdir . '/messagelib.php');
require_once($CFG->libdir . '/pagelib.php');
require_once(__DIR__ . '/utils.php');

require_login(null, false, null, false, true);

global $USER;
global $OUTPUT;
global $CFG;
global $DB;
// Set up the page.
$title = get_string('headintegration', 'local_eudecustom');
$pagetitle = $title;
$url = new moodle_url("/local/eudecustom/eudeintegration.php");

$PAGE->set_context(context_system::instance());
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('standard');

$PAGE->requires->jquery();
$PAGE->requires->css("/local/eudecustom/style/eudecustom_style.css");

$output = $PAGE->get_renderer('local_eudecustom', 'eudeintegration');

$sesskey = sesskey();

if (optional_param('processtext', null, PARAM_TEXT) == 'processtext') {
    if (!confirm_sesskey()) {
        print_error('Bad Session Key');
    } else {
        $data = optional_param('integrationtext', null, PARAM_TEXT);
        if (integrate_previous_data ($data)) {
            redirect($CFG->wwwroot . '/local/eudecustom/eudeintegration.php',
                get_string('savecompleted', 'local_eudecustom'), null, \core\output\notification::NOTIFY_SUCCESS);
        } else {
            redirect($CFG->wwwroot . '/local/eudecustom/eudeintegration.php',
                get_string('savefailed', 'local_eudecustom'), null, \core\output\notification::NOTIFY_ERROR);
        }
    }
}

// Call the functions of the renderar that prints the content.
// Check if the logged user is the siteadmin.
if (has_capability('moodle/site:config', context_system::instance())) {
    echo $output->eude_integration_page($sesskey);
} else {
    echo $output->eude_nopermission_page();
}
