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
 * Moodle request php for gradesearch ajax petitions.
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

require_once(__DIR__ . '/utils.php');
require_once($CFG->dirroot . '/calendar/lib.php');
require_once($CFG->libdir . '/enrollib.php');

require_login(null, false, null, false, true);

global $DB;
global $USER;

$courseid = optional_param('course', SITEID, PARAM_INT);
$time = optional_param('time', 0, PARAM_INT);

if ($courseid != SITEID && !empty($courseid)) {
    $course = $DB->get_record('course', array('id' => $courseid));
    $courses = array($course->id => $course);
} else {
    $course = get_site();
    $courses = calendar_get_default_courses();
}

$calendar = new calendar_information(0, 0, 0, $time);
$calendar->prepare_for_view($course, $courses);

$eventstoshow = array();
$startdate = 0;
$enddate = 0;


if (optional_param('generateeventlist', 0, PARAM_TEXT)) {
    if (optional_param('startdatemodal', 0, PARAM_TEXT)) {
        $startdate = strtotime(preg_replace(
                "/(\d+)\D+(\d+)\D+(\d+)/", "$1-$2-$3",
                optional_param('startdatemodal', 0, PARAM_TEXT)));
    }

    if (optional_param('enddatemodal', 0, PARAM_TEXT)) {
        $enddate = strtotime(preg_replace("/(\d+)\D+(\d+)\D+(\d+)/", "$1-$2-$3", optional_param('enddatemodal', 0, PARAM_TEXT)));
    }

    $events = calendar_get_events($startdate, $enddate, $USER->id, false, $calendar->courses);

    foreach ($events as $event) {

        switch ($event->courseid) {
            case 0:
                if (strpos($event->name, '[[MI]]') === 0 && $event->eventtype == 'user' &&
                        (optional_param('intensivemodulebegin', 0, PARAM_TEXT) ||
                        optional_param('intensivemodulebegin_modal', 0, PARAM_TEXT))) {
                    $event->class = 'intensivemodule';
                    $event->name = get_string('eventkeyintensivemodulebegin', 'local_eudecustom') . ' ' . str_replace('[[MI]]',
                            '', $event->name);
                    array_push($eventstoshow, $event);
                }
                if (strpos($event->name, '[[COURSE]]') === 0 && $event->eventtype == 'user' &&
                        (optional_param('modulebegin', 0, PARAM_TEXT) || optional_param('modulebegin_modal', 0, PARAM_TEXT))) {
                    $event->class = 'normalmodule';
                    $event->name = get_string('eventkeymodulebegin', 'local_eudecustom') . ' ' . str_replace('[[COURSE]]',
                            '', $event->name);
                    array_push($eventstoshow, $event);
                }
                break;
            case 1:
                if (optional_param('eudeevent', 0, PARAM_TEXT) || optional_param('eudeevent_modal', 0, PARAM_TEXT)) {
                    $event->class = 'globalevent';
                    array_push($eventstoshow, $event);
                }
                break;
            default:
                $course = $DB->get_record('course', array('id' => $event->courseid));
                if ($event->modulename == 'assign' && $event->eventtype == 'due' && (optional_param('activityend', 0, PARAM_TEXT) ||
                        optional_param('activityend_modal', 0, PARAM_TEXT))) {
                    $event->class = 'assignmentevent';
                    $event->name = $event->name . ' ' . $course->fullname;
                    array_push($eventstoshow, $event);
                }
                if ($event->modulename == 'questionnaire' && (optional_param('questionnairedate', 0, PARAM_TEXT) ||
                        optional_param('questionnairedate_modal', 0, PARAM_TEXT))) {
                    $event->name = $event->name . ' ' . $course->fullname;
                    $event->class = 'questionnaireevent';
                    array_push($eventstoshow, $event);
                }
                if ($event->modulename == 'quiz' &&
                        (optional_param('testdate', 0, PARAM_TEXT) || optional_param('testdate_modal', 0, PARAM_TEXT))) {
                    $event->name = $event->name . ' ' . $course->fullname;
                    $event->class = 'quizevent';
                    array_push($eventstoshow, $event);
                }
                break;
        }
    }

}

// Set up the page.
$title = get_string('eventhead1', 'local_eudecustom');
if (optional_param('startdatemodal', null, PARAM_TEXT) && optional_param('enddatemodal', null, PARAM_TEXT)) {
    $title .= ' ' . optional_param('startdatemodal', null, PARAM_TEXT) . ' - ' . optional_param('enddatemodal', null, PARAM_TEXT);
}
$url = new moodle_url("/local/eudecustom/eudeeventlist.php");

$PAGE->set_context(context_system::instance());
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('standard');

$PAGE->requires->jquery();
$PAGE->requires->jquery_plugin('ui');
$PAGE->requires->jquery_plugin('ui-css');
$PAGE->requires->js_call_amd("local_eudecustom/eude", "calendar");
$PAGE->requires->js_call_amd("local_eudecustom/eude", "eventlist");

// Load the renderer of the page.
$output = $PAGE->get_renderer('local_eudecustom', 'eudecalendar');

// Call the functions of the renderar that prints the content.
echo $output->eude_eventslist_page($eventstoshow);

