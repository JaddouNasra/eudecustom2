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
 * Display the custom calendar page.
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

require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/calendar/lib.php');
require_once(__DIR__ . '/utils.php');

$courseid = optional_param('course', SITEID, PARAM_INT);
$view = optional_param('view', 'upcoming', PARAM_ALPHA);
$day = optional_param('cal_d', 0, PARAM_INT);
$mon = optional_param('cal_m', 0, PARAM_INT);
$year = optional_param('cal_y', 0, PARAM_INT);
$time = optional_param('time', 0, PARAM_INT);


$url = new moodle_url("/local/eudecustom/eudecalendar.php");

// If a day, month and year were passed then convert it to a timestamp. If these were passed
// then we can assume the day, month and year are passed as Gregorian, as no where in core
// should we be passing these values rather than the time. This is done for BC.
if (!empty($day) && !empty($mon) && !empty($year)) {
    if (checkdate($mon, $day, $year)) {
        $time = make_timestamp($year, $mon, $day);
    }
}

if (empty($time)) {
    $time = time();
}

if ($courseid != SITEID) {
    $url->param('course', $courseid);
}

if ($view !== 'upcoming') {
    $time = usergetmidnight($time);
    $url->param('view', $view);
}

$url->param('time', $time);

$PAGE->set_url($url);

if ($courseid != SITEID && !empty($courseid)) {
    $course = $DB->get_record('course', array('id' => $courseid));
    $courses = array($course->id => $course);
    $issite = false;
    navigation_node::override_active_url(new moodle_url('/course/view.php', array('id' => $course->id)));
} else {
    $course = get_site();
    $courses = calendar_get_default_courses();
    $issite = true;
}

require_login(null, false, null, false, true);

$calendar = new calendar_information(0, 0, 0, $time);
$calendar->set_sources($course, $courses);

// Set up the page.
$title = get_string('headcalendar', 'local_eudecustom');
$pagetitle = $title;
$url = new moodle_url("/local/eudecustom/eudecalendar.php");

$PAGE->set_context(context_system::instance());
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('standard');

$PAGE->requires->jquery();
$PAGE->requires->jquery_plugin('ui');
$PAGE->requires->jquery_plugin('ui-css');
$PAGE->requires->js_call_amd("local_eudecustom/eude", "calendar");
$PAGE->requires->js_call_amd("local_eudecustom/eude", "menu");
$PAGE->requires->css("/local/eudecustom/style/eudecustom_style.css");

// Load the renderer of the page.
$output = $PAGE->get_renderer('local_eudecustom', 'eudecalendar');

// Call the functions of the renderar that prints the content.
if (!has_capability('moodle/calendar:manageownentries', context_system::instance())) {
    echo $output->eude_calendar_page($calendar, false);
} else {
    echo $output->eude_calendar_page($calendar);
}
