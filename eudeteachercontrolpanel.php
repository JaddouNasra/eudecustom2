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
 * Custom Teacher Control Panel
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
require_once(__DIR__ . "/classes/models/local_eudecustom_eudeteachercontrolpanel.class.php");

require_login(null, false, null, false, true);

global $USER;
global $OUTPUT;
global $CFG;
global $DB;

// Set up the page.
$title = get_string('teachercontrolpanel', 'local_eudecustom');
$pagetitle = $title;
$url = new moodle_url("/local/eudecustom/eudeteachercontrolpanel.php");

$PAGE->set_context(context_system::instance());
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('standard');

$PAGE->requires->jquery();
$PAGE->requires->js_call_amd("local_eudecustom/eude", "redirect");
$PAGE->requires->js_call_amd("local_eudecustom/eude", "menu");
$PAGE->requires->css("/local/eudecustom/style/eudecustom_style.css");

// Load the renderer of the page.
$output = $PAGE->get_renderer('local_eudecustom', 'eudeteachercontrolpanel');

// Prepare data required in the renderer.
$cat1 = get_name_categories_by_role($USER->id, 'teacher');
$cat2 = get_name_categories_by_role($USER->id, 'editingteacher');
$cat3 = get_name_categories_by_role($USER->id, 'manager');
$categories = array_flip(array_merge($cat1, $cat2, $cat3));
// Call the functions of the renderar that prints the content.
// Check if the user has the role of editingteacher, teacher or manager in any course.
if (!count($categories)) {
    echo $output->eude_nopermission_page();
} else {
    $courses = get_user_courses($USER->id);
    $data = new local_eudecustom_eudeteachercontrolpanel($categories, $courses);
    $data->courses = $courses;
    echo $output->eude_teachercontrolpanel_page($data);
}
