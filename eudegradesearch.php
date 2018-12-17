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
 * Moodle custom url that enables searching for an student and redirect to their grades url in a course.
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
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/pagelib.php');
require_once($CFG->dirroot . '/my/lib.php');
require_once($CFG->dirroot . '/tag/lib.php');
require_once($CFG->dirroot . '/user/profile/lib.php');
require_once($CFG->libdir . '/filelib.php');
require_once(__DIR__ . '/utils.php');
require_once(__DIR__ . "/classes/models/local_eudecustom_eudegradesearch.class.php");

require_login(null, false, null, false, true);

global $USER;
global $OUTPUT;
global $CFG;
global $DB;

// Set up the page.
$title = get_string('headgrades', 'local_eudecustom');
$pagetitle = $title;
$url = new moodle_url("/local/eudecustom/eudegradesearch.php");

$PAGE->set_context(context_system::instance());
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('standard');

$PAGE->requires->jquery();
$PAGE->requires->js_call_amd("local_eudecustom/eude", "academic");
$PAGE->requires->js_call_amd("local_eudecustom/eude", "menu");
$PAGE->requires->css("/local/eudecustom/style/eudecustom_style.css");

// Load the renderer of the page.
$output = $PAGE->get_renderer('local_eudecustom', 'eudegradesearch');

// Prepare data required in the renderer.
$sesskey = sesskey();

$cat1 = get_name_categories_by_role($USER->id, 'editingteacher');
$cat2 = get_name_categories_by_role($USER->id, 'teacher');
$cat3 = get_name_categories_by_role($USER->id, 'manager');
$categories = array_unique(array_flip(array_merge($cat1, $cat2, $cat3)));
// Call the functions of the renderar that prints the content.
// Check if the user has the role of editingteacher, teacher or manager in any course.
if (!count($categories)) {
    echo $output->eude_nopermission_url();
} else {
    $data = new local_eudecustom_eudegradesearch($categories);
    echo $output->eude_gradesearch_page($data, $sesskey);
}

