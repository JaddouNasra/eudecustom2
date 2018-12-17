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
 * Moodle custom url for enrolment and custom actions in intensive courses.
 *
 * @package    local_eudecustom
 * @copyright  2017 Planificacion de Entornos Tecnologicos SL
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__) . '/../../config.php');

// Restrict access if the plugin is not active.
if (is_callable('mr_off') && mr_off('eudecustom', '_MR_LOCAL')) {
    die("Plugin not enabled.");
}

require_once(__DIR__ . '/utils.php');
require_once($CFG->dirroot . '/my/lib.php');
require_once($CFG->dirroot . '/tag/lib.php');
require_once($CFG->dirroot . '/user/profile/lib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->libdir . '/pagelib.php');
require_once($CFG->libdir . '/filelib.php');
require_once(__DIR__ . "/classes/models/local_eudecustom_eudeprofile.class.php");

require_login(null, false, null, false, true);

$userid = optional_param('id', 0, PARAM_INT);

global $USER;
global $OUTPUT;
global $CFG;
global $SESSION;

// Set up the page.
$title = get_string('headintensives', 'local_eudecustom');
$pagetitle = $title;
$url = new moodle_url("/local/eudecustom/eudeprofile.php");

$PAGE->set_context(context_system::instance());
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('standard');

$PAGE->requires->jquery();
$PAGE->requires->js_call_amd("local_eudecustom/eude", "profile");
$PAGE->requires->js_call_amd("local_eudecustom/eude", "menu");
$PAGE->requires->css("/local/eudecustom/style/eudecustom_style.css");

// Load the renderer of the page.
$output = $PAGE->get_renderer('local_eudecustom', 'eudeprofile');

if (optional_param('student', 0, PARAM_INT)) {
    $userid = optional_param('student', 0, PARAM_INT);
}

// Owner of the page.
$userid = $userid ? $userid : $USER->id;
// Check if the user is a deleted or invalid user.
if ((!$user = $DB->get_record('user', array('id' => $userid))) || ($user->deleted)) {
    if (!$user) {
        $usererror = get_string('invaliduser', 'error');
    } else {
        $usererror = get_string('userdeleted');
    }
    // Call the functions of the renderar that prints the content.
    echo $output->eude_userproblem_page($usererror);
}

// Update intensive course date.
if (optional_param('letpv_date', 0, PARAM_INT)) {
    if (!confirm_sesskey(sesskey())) {
        print_error('Bad Session Key');
    } else {
        $convnum = optional_param('letpv_date', 0, PARAM_INT);
        $cid = optional_param('course', 0, PARAM_INT);
        update_intensive_dates($convnum, $cid, $userid);
    }
}

// Prepare data required in the renderer.
$categories = get_categories_with_intensive_modules();
$categories = array_flip($categories);
$edit = true;

if (!has_capability('moodle/site:config', context_system::instance())) {
    $categories = get_user_categories($userid);
    $categories = array_flip($categories);
    $edit = false;
}

// Show the table if you edited the date.
if (optional_param('course', 0, PARAM_INT)) {
    $categ = $DB->get_record('course', array('id' => optional_param('course', 0, PARAM_INT)));
    $namecat = $DB->get_record('course_categories', array('id' => $categ->category));
    // Call the functions of the renderar that prints the content.
    echo $output->eude_profile_intensives($categories, $edit, $namecat->name);
} else if (isset($SESSION) && isset($SESSION->module)) {
    $categ = $DB->get_record('course', array('id' => $SESSION->module));
    $namecat = $DB->get_record('course_categories', array('id' => $categ->category));
    // Call the functions of the renderar that prints the content.
    echo $output->eude_profile_intensives($categories, $edit, $namecat->name);
} else {
    // Call the functions of the renderar that prints the content.
    echo $output->eude_profile_intensives($categories, $edit);
}


