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

require_once($CFG->libdir . '/pagelib.php');
require_once(__DIR__ . '/utils.php');

require_login(null, false, null, false, true);

global $USER;
global $OUTPUT;
global $CFG;
global $DB;

// Set up the page.
$title = get_string('headdashboard', 'local_eudecustom');
$pagetitle = $title;
$url = new moodle_url("/local/eudecustom/eudedashboard.php");

$PAGE->set_context(context_system::instance());
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('standard');


$PAGE->requires->jquery();
$PAGE->requires->jquery_plugin('ui');
$PAGE->requires->jquery_plugin('ui-css');
$PAGE->requires->js_call_amd("local_eudecustom/eude", "dashboard");

$PAGE->requires->css("/local/eudecustom/style/eudecustom_style.css");

$output = $PAGE->get_renderer('local_eudecustom', 'eudedashboard');

$sesskey = sesskey();

$isteacher = check_user_is_teacher($USER->id);

// Call the functions of the renderar that prints the content.
if (!$isteacher) {
    $teacherdata = get_dashboard_teacher_data($USER->id);
    $teacherdata = get_dashboard_teacher_data(3);
    echo $output->eude_dashboard_teacher_page($teacherdata);
} else {
    $studentdata = get_dashboard_student_data($USER->id);
    echo $output->eude_dashboard_student_page($studentdata);
}
