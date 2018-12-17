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
 * Moodle custom url for managing starting dates of intensive courses.
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

require_once($CFG->libdir . '/pagelib.php');
require_once(__DIR__ . '/utils.php');
require_once(__DIR__ . "/classes/models/local_eudecustom_eudeintensivemoduledates.class.php");

require_login(null, false, null, false, true);

global $USER;
global $OUTPUT;
global $CFG;


// Set up the page.
$title = get_string('headmatriculationdates', 'local_eudecustom');
$pagetitle = $title;
$url = new moodle_url("/local/eudecustom/eudeintensivemoduledates.php");

$PAGE->set_context(context_system::instance());
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('standard');

$PAGE->requires->jquery();
$PAGE->requires->jquery_plugin('ui');
$PAGE->requires->jquery_plugin('ui-css');
$PAGE->requires->js_call_amd("local_eudecustom/eude", "matriculation");
$PAGE->requires->js_call_amd("local_eudecustom/eude", "menu");
$PAGE->requires->css("/local/eudecustom/style/eudecustom_style.css");

// Load the renderer of the page.
$output = $PAGE->get_renderer('local_eudecustom', 'eudeintensivemoduledates');

// Prepare data required in the renderer.
$sesskey = sesskey();

$data = new local_eudecustom_eudeintensivemoduledates();

// We insert the form data in the db.
if (optional_param('savedates', null, PARAM_TEXT) == 'savedates') {
    if (!confirm_sesskey()) {
        print_error('Bad Session Key');
    } else {
        $coursedata = array();
        foreach ($data->courses as $record) {
            $course = new stdClass();
            $course->courseid = $record->courseid;
            $course->fecha1 = strtotime(optional_param('date1-' . $record->courseid, null, PARAM_TEXT));
            $course->fecha2 = strtotime(optional_param('date2-' . $record->courseid, null, PARAM_TEXT));
            $course->fecha3 = strtotime(optional_param('date3-' . $record->courseid, null, PARAM_TEXT));
            $course->fecha4 = strtotime(optional_param('date4-' . $record->courseid, null, PARAM_TEXT));
            array_push($coursedata, $course);
        }
        save_matriculation_dates($coursedata);
    }
}
// Call the functions of the renderar that prints the content.
// Check if the logged user is the siteadmin.
if (has_capability('moodle/site:config', context_system::instance())) {
    echo $output->eude_intensivemoduledates_page($data, $sesskey);
} else {
    echo $output->eude_nopermission_page();
}


