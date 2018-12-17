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
 * Moodle custom renderer class for eudecalendar view.
 *
 * @package    local_eudecustom
 * @copyright  2017 Planificacion de Entornos Tecnologicos SL
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_eudecustom\output;

defined('MOODLE_INTERNAL') || die;

use \html_writer;
use renderable;

/**
 * Renderer for eude custom actions plugin.
 *
 * @copyright  2017 Planificacion de Entornos Tecnologicos SL
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class eudecalendar_renderer extends \plugin_renderer_base {

    /**
     * Render the intensive modules matriculation dates custom page for eude.
     * @param calendar $calendar calendar for the display.
     * @param newevent $newevent test capability to add new events.
     * @return string html to output.
     */
    public function eude_calendar_page ($calendar, $newevent = true) {
        global $CFG;
        global $USER;
        global $DB;

        $calendartype = \core_calendar\type_factory::get_calendar_instance();
        $date = $calendartype->timestamp_to_date_array($calendar->time);

        $nextmonth = calendar_add_month($date['mon'], $date['year']);
        $nextmonthtime = $calendartype->convert_to_gregorian($nextmonth[1], $nextmonth[0], 1);
        $nextmonthtime = make_timestamp($nextmonthtime['year'], $nextmonthtime['month'], $nextmonthtime['day'],
                $nextmonthtime['hour'], $nextmonthtime['minute']);

        $response = '';
        $response .= $this->header();

        // Section for the add event button and two calendars.
        $html = '';
        if ($newevent == true) {
            $html .= html_writer::start_div('row');
            $html .= $this->add_event_button($calendar->course->id, 0, 0, 0, $calendar->time);
            $html .= html_writer::end_div();
        }
        $html .= html_writer::start_div('row');
        $html .= html_writer::start_tag('div', array('class' => 'minicalendarblock eudecalendar col-md-6'));
        $html .= $this->calendar_get_mini($calendar->courses, $calendar->groups, $calendar->users, false, false, 'month1',
                $calendar->courseid, $calendar->time);
        $html .= html_writer::end_tag('div');

        $html .= html_writer::start_tag('div', array('class' => 'minicalendarblock eudecalendar col-md-6'));
        $html .= $this->calendar_get_mini($calendar->courses, $calendar->groups, $calendar->users, false, false, 'month2',
                $calendar->courseid, $nextmonthtime);
        $html .= html_writer::end_tag('div');
        $html .= html_writer::end_div();

        // Section for the events key.
        $html .= html_writer::start_div('row');
        $html .= html_writer::start_div('col-md-12 keycontent');
        $html .= generate_event_keys();
        $html .= html_writer::end_div();
        $html .= html_writer::end_div();

        // Section for the modal window to print calendar events.
        $html .= html_writer::start_div('row');
        $html .= html_writer::start_tag('div', array('id' => 'modalwindowforprint', 'class' => 'modalwindowforprint'));
        $html .= html_writer::end_div();
        $html .= html_writer::end_div();

        // Section for the export, subscribe and ical components.
        $html .= html_writer::start_div('row');
        $html .= html_writer::tag('button', get_string('printevents', 'local_eudecustom'),
                        array('id' => 'openmodalwindowforprint', 'class' => 'btn btn-default'));
        $html .= html_writer::link($CFG->wwwroot . '/calendar/export.php', get_string('exportcalendar', 'calendar'),
                        array('course' => $calendar->courseid, 'class' => 'btn btn-default'));
        if (calendar_user_can_add_event($calendar->course)) {
            $html .= html_writer::link($CFG->wwwroot . '/calendar/managesubscriptions.php',
                            get_string('managesubscriptions', 'calendar'),
                            array('course' => $calendar->courseid, 'class' => 'btn btn-default'));
        }
        if (isloggedin()) {
            $authtoken = sha1($USER->id . $DB->get_field('user', 'password', array('id' => $USER->id)) . $CFG->calendar_exportsalt);
            $html .= html_writer::link($CFG->wwwroot . '/calendar/export_execute.php?authtoken=' . $authtoken
                            . '&userid=' . $USER->id . '&preset_what=all&preset_time=recentupcoming', 'iCal',
                            array('title' => get_string('quickdownloadcalendar', 'calendar'),
                        'class' => 'ical-link m-l-1 btn btn-default'));
        }
        $html .= html_writer::end_div();

        $response .= $html;
        $response .= $this->footer();
        return $response;
    }

    /**
     * Creates a button to add a new event
     *
     * @param int $courseid
     * @param int $day
     * @param int $month
     * @param int $year
     * @param int $time the unixtime, used for multiple calendar support. The values $day,
     *     $month and $year are kept for backwards compatibility.
     * @return string
     */
    protected function add_event_button ($courseid, $day = null, $month = null, $year = null, $time = null) {
        // If a day, month and year were passed then convert it to a timestamp. If these were passed
        // then we can assume the day, month and year are passed as Gregorian, as no where in core
        // should we be passing these values rather than the time. This is done for BC.
        if (!empty($day) && !empty($month) && !empty($year)) {
            if (checkdate($month, $day, $year)) {
                $time = make_timestamp($year, $month, $day);
            } else {
                $time = time();
            }
        } else if (empty($time)) {
            $time = time();
        }

        $output = html_writer::start_tag('div', array('class' => 'buttons'));
        $output .= html_writer::start_tag('form', array('action' => CALENDAR_URL . 'event.php', 'method' => 'get'));
        $output .= html_writer::start_tag('div');
        $output .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'action', 'value' => 'new'));
        $output .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'course', 'value' => $courseid));
        $output .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'time', 'value' => $time));
        $attributes = array('type' => 'submit', 'value' => get_string('newevent', 'calendar'), 'class' => 'btn btn-secondary');
        $output .= html_writer::empty_tag('input', $attributes);
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('form');
        $output .= html_writer::end_tag('div');
        return $output;
    }

    /**
     * Generates the HTML for a miniature calendar. This function is similar to the calendar_get_mini() function,
     * but modifiying some of the render.
     *
     * @param array $courses list of course to list events from
     * @param array $groups list of group
     * @param array $users user's info
     * @param int|bool $calmonth calendar month in numeric, default is set to false
     * @param int|bool $calyear calendar month in numeric, default is set to false
     * @param string|bool $placement the place/page the calendar is set to appear - passed on the the controls function
     * @param int|bool $courseid id of the course the calendar is displayed on - passed on the the controls function
     * @param int $time the unixtimestamp representing the date we want to view, this is used instead of $calmonth
     *     and $calyear to support multiple calendars
     * @return string $content return html table for mini calendar
     */
    public function calendar_get_mini ($courses, $groups, $users, $calmonth = false, $calyear = false, $placement = false,
            $courseid = false, $time = 0) {
        global $CFG, $OUTPUT, $PAGE, $DB;

        // Get the calendar type we are using.
        $calendartype = \core_calendar\type_factory::get_calendar_instance();

        $display = new \stdClass;

        // Assume we are not displaying this month for now.
        $display->thismonth = false;

        $content = '';

        // Do this check for backwards compatibility. The core should be passing a timestamp rather than month and year.
        // If a month and year are passed they will be in Gregorian.
        if (!empty($calmonth) && !empty($calyear)) {
            // Ensure it is a valid date, else we will just set it to the current timestamp.
            if (checkdate($calmonth, 1, $calyear)) {
                $time = make_timestamp($calyear, $calmonth, 1);
            } else {
                $time = time();
            }
            $date = usergetdate($time);
            if ($calmonth == $date['mon'] && $calyear == $date['year']) {
                $display->thismonth = true;
            }
            // We can overwrite date now with the date used by the calendar type, if it is not Gregorian, otherwise
            // there is no need as it is already in Gregorian.
            if ($calendartype->get_name() != 'gregorian') {
                $date = $calendartype->timestamp_to_date_array($time);
            }
        } else if (!empty($time)) {
            // Get the specified date in the calendar type being used.
            $date = $calendartype->timestamp_to_date_array($time);
            $thisdate = $calendartype->timestamp_to_date_array(time());
            if ($date['month'] == $thisdate['month'] && $date['year'] == $thisdate['year']) {
                $display->thismonth = true;
                // If we are the current month we want to set the date to the current date, not the start of the month.
                $date = $thisdate;
            }
        } else {
            // Get the current date in the calendar type being used.
            $time = time();
            $date = $calendartype->timestamp_to_date_array($time);
            $display->thismonth = true;
        }

        list($d, $m, $y) = array($date['mday'], $date['mon'], $date['year']); // This is what we want to display.
        // Get Gregorian date for the start of the month.
        $gregoriandate = $calendartype->convert_to_gregorian($date['year'], $date['mon'], 1);

        // Store the gregorian date values to be used later.
        list($gy, $gm, $gd, $gh, $gmin) = array($gregoriandate['year'], $gregoriandate['month'], $gregoriandate['day'],
            $gregoriandate['hour'], $gregoriandate['minute']);

        // Get the max number of days in this month for this calendar type.
        $display->maxdays = calendar_days_in_month($m, $y);
        // Get the starting week day for this month.
        $startwday = dayofweek(1, $m, $y);
        // Get the days in a week.
        $daynames = calendar_get_days();
        // Store the number of days in a week.
        $numberofdaysinweek = $calendartype->get_num_weekdays();

        // Set the min and max weekday.
        $display->minwday = calendar_get_starting_weekday();
        $display->maxwday = $display->minwday + ($numberofdaysinweek - 1);

        // These are used for DB queries, so we want unixtime, so we need to use Gregorian dates.
        $display->tstart = make_timestamp($gy, $gm, $gd, $gh, $gmin, 0);
        $display->tend = $display->tstart + ($display->maxdays * DAYSECS) - 1;

        // Align the starting weekday to fall in our display range
        // This is simple, not foolproof.
        if ($startwday < $display->minwday) {
            $startwday += $numberofdaysinweek;
        }
        $assignnewdate = $display->tend + (86400 * 31);
        // Get the events matching our criteria. Don't forget to offset the timestamps for the user's TZ!.
        $events = calendar_get_events($display->tstart, $assignnewdate, $users, $groups, $courses);
        // Set event course class for course events.
        if (!empty($events)) {
            foreach ($events as $eventid => $event) {
                if (!empty($event->modulename)) {
                    $cm = get_coursemodule_from_instance($event->modulename, $event->instance);
                    if (!\core_availability\info_module::is_user_visible($cm, 0, false)) {
                        unset($events[$eventid]);
                    }
                }
            }
        }

        // This is either a genius idea or an idiot idea: in order to not complicate things, we use this rule: if, after
        // possibly removing SITEID from $courses, there is only one course left, then clicking on a day in the month
        // will also set the $SESSION->cal_courses_shown variable to that one course. Otherwise, we 'd need to add extra
        // arguments to this function.
        $hrefparams = array();
        if (!empty($courses)) {
            $courses = array_diff($courses, array(SITEID));
            if (count($courses) == 1) {
                $hrefparams['course'] = reset($courses);
            }
        }

        // We want to have easy access by day, since the display is on a per-day basis.
        $this->calendar_events_by_day($events, $m, $y, $eventsbyday, $durationbyday, $typesbyday, $courses);

        // Accessibility: added summary and <abbr> elements.
        $summary = get_string('calendarheading', 'calendar', userdate($display->tstart, get_string('strftimemonthyear')));
        // Begin table.
        $content .= '<table class="minicalendar calendartable" summary="' . $summary . '">';
        // Controls section to change between months.
        if (($placement !== false) && ($courseid !== false)) {
            $content .= '<caption>' . $this->calendar_top_controls($placement, array('id' => $courseid, 'time' => $time))
                    . '</caption>';
        }
        $content .= '<tr class="weekdays">'; // Header row: day names
        // Print out the names of the weekdays.
        for ($i = $display->minwday; $i <= $display->maxwday; ++$i) {
            $pos = $i % $numberofdaysinweek;
            $content .= '<th scope="col"><abbr title="' . $daynames[$pos]['fullname'] . '">' .
                    $daynames[$pos]['shortname'] . "</abbr></th>\n";
        }

        $content .= '</tr><tr>'; // End of day names; prepare for day numbers
        // For the table display. $week is the row; $dayweek is the column.
        $dayweek = $startwday;

        // Paddding (the first week may have blank days in the beginning).
        for ($i = $display->minwday; $i < $startwday; ++$i) {
            $content .= '<td class="dayblank"> </td>' . "\n";
        }

        $weekend = CALENDAR_DEFAULT_WEEKEND;
        if (isset($CFG->calendar_weekend)) {
            $weekend = intval($CFG->calendar_weekend);
        }

        // Now display all the calendar.
        $daytime = strtotime('-1 day', $display->tstart);
        for ($day = 1; $day <= $display->maxdays; ++$day, ++$dayweek) {
            $daytime = strtotime('+1 day', $daytime);
            if ($dayweek > $display->maxwday) {
                // We need to change week (table row).
                $content .= '</tr><tr>';
                $dayweek = $display->minwday;
            }
            $content .= html_writer::start_tag('td');

            // Reset vars.
            if ($weekend & (1 << ($dayweek % $numberofdaysinweek))) {
                // Weekend. This is true no matter what the exact range is.
                $class = 'weekend day';
            } else {
                // Normal working day.
                $class = 'day';
            }

            $eventids = array();
            if (!empty($eventsbyday[$day])) {
                $eventids = $eventsbyday[$day];
            }

            if (!empty($durationbyday[$day])) {
                $eventids = array_unique(array_merge($eventids, $durationbyday[$day]));
            }

            $finishclass = false;

            $cell = $day;
            $content .= html_writer::tag('div', $cell, array('class' => 'daydisplay ' . $class));

            if (!empty($eventids)) {
                // There is at least one event on this day.
                $hrefparams['view'] = 'day';

                foreach ($eventids as $eventid) {
                    $dayhref = calendar_get_link_href(new \moodle_url(CALENDAR_URL . 'view.php', $hrefparams), 0, 0, 0, $daytime);
                    $classdiv = $class . ' hasevent';
                    $popupcontent = '';
                    $cellattributes = array();
                    if (!isset($events[$eventid])) {
                        continue;
                    }
                    $event = new \calendar_event($events[$eventid]);
                    $classdiv .= ' ' . $events[$eventid]->class;
                    $popupalt = '';
                    $component = 'moodle';
                    if (!empty($event->modulename)) {
                        $popupicon = 'icon';
                        $popupalt = $event->modulename;
                        $component = $event->modulename;
                    } else if ($event->courseid == SITEID) { // Site event.
                        $popupicon = 'i/siteevent';
                    } else if ($event->courseid != 0 && $event->courseid != SITEID && $event->groupid == 0) { // Course event.
                        $popupicon = 'i/courseevent';
                    } else if ($event->groupid) { // Group event.
                        $popupicon = 'i/groupevent';
                    } else { // Must be a user event.
                        $popupicon = 'i/userevent';
                    }

                    if ($event->timeduration) {
                        $startdate = $calendartype->timestamp_to_date_array($event->timestart);
                        $enddate = $calendartype->timestamp_to_date_array($event->timestart + $event->timeduration - 1);
                        if ($enddate['mon'] == $m && $enddate['year'] == $y && $enddate['mday'] == $day) {
                            $finishclass = true;
                        }
                    } else {
                        $classdiv .= ' oneday';
                    }

                    $dayhref->set_anchor('event_' . $event->id);

                    $popupcontent .= html_writer::start_tag('div');
                    $popupcontent .= $OUTPUT->pix_icon($popupicon, $popupalt, $component);
                    // Show ical source if needed.
                    if (!empty($event->subscription) && $CFG->calendar_showicalsource) {
                        $a = new stdClass();
                        $a->name = format_string($event->name, true);
                        $a->source = $event->subscription->name;
                        $name = get_string('namewithsource', 'calendar', $a);
                    } else {
                        if ($finishclass) {
                            $samedate = $startdate['mon'] == $enddate['mon'] &&
                                    $startdate['year'] == $enddate['year'] &&
                                    $startdate['mday'] == $enddate['mday'];

                            if ($samedate) {
                                $name = format_string($event->name, true);
                            } else {
                                $name = format_string($event->name, true) . ' (' . get_string('eventendtime', 'calendar') . ')';
                            }
                        } else {
                            $name = format_string($event->name, true);
                        }
                    }
                    // Delete [[$indentifier]] to show in the popup (ex. [[COURSE]], etc..).
                    $name = str_replace('[[COURSE]]', '', $name);
                    $name = str_replace('[[MI]]', '', $name);

                    switch ($event->eventtype) {
                        case 'open':
                            if ($event->modulename == 'quiz') {
                                $modulequiz = $DB->get_record('modules', array('name' => 'quiz'));
                                $quizid = $DB->get_record('course_modules',
                                        array('course' => $event->courseid,
                                    'instance' => $event->instance,
                                    'module' => $modulequiz->id));
                                $dayhref = new \moodle_url('/mod/quiz/view.php', array('id' => $quizid->id));
                            }
                            break;
                        case 'due':
                            if ($event->modulename == 'assign') {
                                $moduleassign = $DB->get_record('modules', array('name' => 'assign'));
                                $assignid = $DB->get_record('course_modules',
                                        array('course' => $event->courseid,
                                    'instance' => $event->instance,
                                    'module' => $moduleassign->id));
                                $dayhref = new \moodle_url('/mod/assign/view.php', array('id' => $assignid->id));
                            }
                            break;
                        case 'user':
                            // If contains any of these strings its a module event.
                            if (strpos($event->name, '[[COURSE]]') !== false || strpos($event->name, '[[MI]]') !== false) {
                                $coursename = $event->name;
                                $coursename = str_replace('[[COURSE]]', '', $coursename);
                                $coursename = str_replace('[[MI]]', '', $coursename);
                                $coursename = $DB->get_record('course', array('shortname' => $coursename));
                                if ($coursename) {
                                    $dayhref = new \moodle_url('/course/view.php', array('id' => $coursename->id));
                                } else {
                                    $dayhref = new \moodle_url('/course/view.php');
                                }
                            }
                            break;
                        default:
                            break;
                    }

                    $popupcontent .= html_writer::link($dayhref, $name);
                    $popupcontent .= html_writer::end_tag('div');

                    if ($display->thismonth && $day == $d) {
                        $popupdata = $this->eude_calendar_get_popup(true, $daytime, $popupcontent);
                    } else {
                        $popupdata = $this->eude_calendar_get_popup(false, $daytime, $popupcontent);
                    }
                    $cellattributes = array_merge($cellattributes, $popupdata);

                    // Class and cell content.
                    if (isset($typesbyday[$day][$event->id]['startglobal'])) {
                        $classdiv .= ' calendar_event_global start';
                    } else if (isset($typesbyday[$day][$event->id]['startcourse'])) {
                        $classdiv .= ' calendar_event_course  start';
                    } else if (isset($typesbyday[$day][$event->id]['startgroup'])) {
                        $classdiv .= ' calendar_event_group  start';
                    } else if (isset($typesbyday[$day][$event->id]['startuser'])) {
                        $classdiv .= ' calendar_event_user  start';
                    }
                    if ($finishclass) {
                        $classdiv .= ' duration_finish';
                    }

                    $cell = html_writer::link($dayhref, ' ');

                    $durationclass = false;
                    if (isset($typesbyday[$day][$event->id]['durationglobal'])) {
                        $durationclass = ' duration_global';
                    } else if (isset($typesbyday[$day][$event->id]['durationcourse'])) {
                        $durationclass = ' duration_course';
                    } else if (isset($typesbyday[$day][$event->id]['durationgroup'])) {
                        $durationclass = ' duration_group';
                    } else if (isset($typesbyday[$day][$event->id]['durationuser'])) {
                        $durationclass = ' duration_user';
                    } else if (isset($typesbyday[$day][$event->id]['durationoneday'])) {
                        $durationclass = ' duration_oneday';
                    }
                    if ($durationclass) {
                        $classdiv .= ' duration ' . $durationclass;
                    }

                    if (isset($eventsbyday[$day])) {
                        foreach ($eventsbyday[$day] as $eventid) {

                            if ($event->id != ($events[$event->id])) {
                                continue;
                            }
                            $event = $events[$eventsbyday[$day][$event->id]];
                            if (!empty($event->class)) {
                                $classdiv .= ' ' . $event->class;
                            }
                            break;
                        }
                    }

                    if ($display->thismonth && $day == $d) {
                        // The current cell is for today - add appropriate classes and additional information for styling.
                        $classdiv .= ' today';
                        $today = get_string('today', 'calendar') . ' ' . userdate(time(), get_string('strftimedayshort'));

                        if (!isset($eventsbyday[$day]) && !isset($durationbyday[$day])) {
                            $classdiv .= ' eventnone';
                            $popupdata = $this->eude_calendar_get_popup(true, false);
                            $cellattributes = array_merge($cellattributes, $popupdata);
                            $cell = html_writer::link('#', $day);
                        }
                        $cell = get_accesshide($today . ' ') . $cell;
                    }

                    // Just display it.
                    $cellattributes['class'] = $classdiv;
                    $cellattributes['name'] = $event->id;
                    $content .= html_writer::tag('div', $cell, $cellattributes);
                }
            }
            $content .= html_writer::tag('div', ' ', array('name' => 'normalcourse'));
            $content .= html_writer::tag('div', ' ', array('name' => 'activitysubmission'));
            $content .= html_writer::tag('div', ' ', array('name' => 'testsubmission'));
            $content .= html_writer::tag('div', ' ', array('name' => 'questionnairedate'));
            $content .= html_writer::tag('div', ' ', array('name' => 'intensivecourse'));
            $content .= html_writer::tag('div', ' ', array('name' => 'eudeglobalevent'));
            $content .= html_writer::end_tag('td');
        }

        // Paddding (the last week may have blank days at the end).
        for ($i = $dayweek; $i <= $display->maxwday; ++$i) {
            $content .= '<td class="dayblank"> </td>';
        }
        $content .= '</tr>'; // Last row ends.

        $content .= '</table>'; // Tabular display of days ends.

        static $jsincluded = false;
        if (!$jsincluded) {
            $PAGE->requires->yui_module('moodle-calendar-info', 'Y.M.core_calendar.info.init');
            $jsincluded = true;
        }
        return $content;
    }

    /**
     * Get control options for Calendar, this funcion adds a top control for a minicalendar
     * with different settings depending in the type of minicalendar
     *
     * @param string $type of calendar
     * @param array $data calendar information
     * @return string $content return available control for the calender in html
     */
    public function calendar_top_controls ($type, $data) {
        global $OUTPUT;

        // Get the calendar type we are using.
        $calendartype = \core_calendar\type_factory::get_calendar_instance();

        $content = '';

        // Ensure course id passed if relevant.
        $courseid = '';
        if (!empty($data['id'])) {
            $courseid = '&amp;course=' . $data['id'];
        }

        // If we are passing a month and year then we need to convert this to a timestamp to
        // support multiple calendars. No where in core should these be passed, this logic
        // here is for third party plugins that may use this function.
        if (!empty($data['m']) && !empty($date['y'])) {
            if (!isset($data['d'])) {
                $data['d'] = 1;
            }
            if (!checkdate($data['m'], $data['d'], $data['y'])) {
                $time = time();
            } else {
                $time = make_timestamp($data['y'], $data['m'], $data['d']);
            }
        } else if (!empty($data['time'])) {
            $time = $data['time'];
        } else {
            $time = time();
        }

        // Get the date for the calendar type.
        $date = $calendartype->timestamp_to_date_array($time);

        $prevmonth = calendar_sub_month($date['mon'], $date['year']);
        $prevmonthtime = $calendartype->convert_to_gregorian($prevmonth[1], $prevmonth[0], 1);
        $prevmonthtime = make_timestamp($prevmonthtime['year'], $prevmonthtime['month'], $prevmonthtime['day'],
                $prevmonthtime['hour'], $prevmonthtime['minute']);

        $thismonthtime = make_timestamp($date['year'], $date['mon']);

        $nextmonth = calendar_add_month($date['mon'], $date['year']);
        $nextmonthtime = $calendartype->convert_to_gregorian($nextmonth[1], $nextmonth[0], 1);
        $nextmonthtime = make_timestamp($nextmonthtime['year'], $nextmonthtime['month'], $nextmonthtime['day'],
                $nextmonthtime['hour'], $nextmonthtime['minute']);

        switch ($type) {
            // For type = month1 we only display a switch to the left.
            case 'month1':
                $prevlink = calendar_get_link_previous(userdate($prevmonthtime, get_string('strftimemonthyear')),
                        'eudecalendar.php?view=month' . $courseid . '&amp;', false, false, false, false, $prevmonthtime);
                $content .= html_writer::start_tag('div', array('class' => 'calendar-controls'));
                $content .= $prevlink . '<span class="hide"> | </span>';
                $content .= $OUTPUT->heading(userdate($time, get_string('strftimemonthyear')), 2, 'current');
                $content .= '<span class="hide"> | </span>';
                $content .= '<span class="clearer"><!-- --></span>';
                $content .= html_writer::end_tag('div') . "\n";
                break;
            // For type = month1 we only display a switch to the right.
            case 'month2':
                $nextlink = calendar_get_link_next(userdate($nextmonthtime, get_string('strftimemonthyear')),
                        'eudecalendar.php?view=month' . $courseid . '&amp;', false, false, false, false, $thismonthtime);
                $content .= html_writer::start_tag('div', array('class' => 'calendar-controls'));
                $content .= $nextlink . '<span class="hide"> | </span>';
                $content .= $OUTPUT->heading(userdate($time, get_string('strftimemonthyear')), 2, 'current');
                $content .= '<span class="hide"> | </span>';
                $content .= '<span class="clearer"><!-- --></span>';
                $content .= html_writer::end_tag('div') . "\n";
                break;
        }
        return $content;
    }

    /**
     * Get per-day basis events with added classes for custom events
     *
     * @param array $events list of events
     * @param int $month the number of the month
     * @param int $year the number of the year
     * @param array $eventsbyday event on specific day
     * @param array $durationbyday duration of the event in days
     * @param array $typesbyday event type (eg: global, course, user, or group)
     * @return void
     */
    public function calendar_events_by_day ($events, $month, $year, &$eventsbyday = array(), &$durationbyday = array(),
            &$typesbyday = array()) {
        // Get the calendar type we are using.
        $calendartype = \core_calendar\type_factory::get_calendar_instance();
        if ($events === false) {
            return;
        }
        global $DB;
        foreach ($events as $event) {
            if ($event->modulename == 'assign' && $event->eventtype == 'due') {
                $assign = $DB->get_record('assign', array('id' => $event->instance));
                $event->timestart = $assign->allowsubmissionsfromdate;
                $event->timeduration = $assign->duedate - $assign->allowsubmissionsfromdate;
            }
            $startdate = $calendartype->timestamp_to_date_array($event->timestart);
            // Set end date = start date if no duration.
            if ($event->timeduration) {
                $enddate = $calendartype->timestamp_to_date_array($event->timestart + $event->timeduration - 1);
            } else {
                $enddate = $startdate;
            }

            // Simple arithmetic: $year * 13 + $month is a distinct integer for each distinct ($year, $month) pair.
            if (!($startdate['year'] * 13 + $startdate['mon'] <= $year * 13 + $month) &&
                    ($enddate['year'] * 13 + $enddate['mon'] >= $year * 13 + $month)) {
                // Out of bounds.
                continue;
            }

            $eventdaystart = intval($startdate['mday']);

            if ($startdate['mon'] == $month && $startdate['year'] == $year) {
                // Give the event to its day.
                $eventsbyday[$eventdaystart][$event->id] = $event->id;

                // Mark the day as having such an event.
                if ($event->courseid == SITEID && $event->groupid == 0) {
                    $typesbyday[$eventdaystart][$event->id]['startglobal'] = true;
                    // Set event class for global event.
                    $events[$event->id]->class = 'calendar_event_global eudeevent';
                } else if ($event->courseid != 0 && $event->courseid != SITEID && $event->groupid == 0) {
                    $typesbyday[$eventdaystart][$event->id]['startcourse'] = true;
                    // Set event class for course event.
                    $events[$event->id]->class = 'calendar_event_course';
                    // Set events for assignments end date.
                    if ($events[$event->id]->modulename == 'assign' && $events[$event->id]->eventtype == 'due') {
                        $events[$event->id]->class .= ' activityend';
                    }
                    // Set events for quizs and tests.
                    if ($events[$event->id]->modulename == 'quiz' && $events[$event->id]->eventtype == 'open') {
                        $events[$event->id]->class .= ' testdate';
                    }
                    // Set events for questionaries.
                    if ($events[$event->id]->modulename == 'questionnaire') {
                        $events[$event->id]->class .= ' questionnairedate';
                    }
                } else if ($event->groupid) {
                    $typesbyday[$eventdaystart][$event->id]['startgroup'] = true;
                    // Set event class for group event.
                    $events[$event->id]->class = 'calendar_event_group';
                } else if ($event->userid) {
                    $typesbyday[$eventdaystart][$event->id]['startuser'] = true;
                    // Set event class for user event.
                    $events[$event->id]->class = 'calendar_event_user';
                    // Set events for normal modules.
                    if (strpos($events[$event->id]->name, '[[COURSE]]') !== false) {
                        $events[$event->id]->class .= ' modulebegin';
                    }
                    // Set events for intensive modules.
                    if (strpos($events[$event->id]->name, '[[MI]]') !== false) {
                        $events[$event->id]->class .= ' intensivemodulebegin';
                    }
                }
            }

            if ($event->timeduration == 0) {
                // Proceed with the next.
                continue;
            }

            // The event starts on $month $year or before. So...
            $lowerbound = $startdate['mon'] == $month && $startdate['year'] == $year ? intval($startdate['mday']) : 0;

            // Also, it ends on $month $year or later...
            $upperbound = $enddate['mon'] == $month &&
                    $enddate['year'] == $year ? intval($enddate['mday']) : calendar_days_in_month($month, $year);
            $typesbyday[$lowerbound]['durantiononeday'] = true;
            // Mark all days between $lowerbound and $upperbound (inclusive) as duration.
            for ($i = $lowerbound + 1; $i <= $upperbound; ++$i) {
                $durationbyday[$i][$event->id] = $event->id;
                if ($event->courseid == SITEID && $event->groupid == 0) {
                    $typesbyday[$i][$event->id]['durationglobal'] = true;
                    $events[$event->id]->class = 'calendar_event_global eudeevent';
                } else if ($event->courseid != 0 && $event->courseid != SITEID && $event->groupid == 0) {
                    $typesbyday[$i][$event->id]['durationcourse'] = true;
                    // Set event class for course event.
                    $events[$event->id]->class = 'calendar_event_course';
                    // Set events for assignments end date.
                    if ($events[$event->id]->modulename == 'assign' && $events[$event->id]->eventtype == 'due') {
                        $events[$event->id]->class .= ' activityend';
                    }
                    // Set events for quizs and tests.
                    if ($events[$event->id]->modulename == 'quiz' && $events[$event->id]->eventtype == 'open') {
                        $events[$event->id]->class .= ' testdate';
                    }
                } else if ($event->groupid) {
                    $typesbyday[$i][$event->id]['durationgroup'] = true;
                    $events[$event->id]->class = 'calendar_event_group';
                } else if ($event->userid) {
                    $typesbyday[$i][$event->id]['durationuser'] = true;
                    // Set event class for user event.
                    $events[$event->id]->class = 'calendar_event_user';
                    // Set events for normal modules.
                    if (strpos($events[$event->id]->name, '[[COURSE]]') !== false) {
                        $events[$event->id]->class .= ' modulebegin';
                    }
                    // Set events for intensive modules.
                    if (strpos($events[$event->id]->name, '[[MI]]') !== false) {
                        $events[$event->id]->class .= ' intensivemodulebegin';
                    }
                }
            }
        }
        return;
    }

    /**
     * Render the custom event list page for eudecustom plugin.
     * @param array $events array with the events to create a list.
     * @return string html to output.
     */
    public function eude_eventslist_page ($events) {
        $response = '';
        $response .= $this->header();

        $html = html_writer::start_tag('div', array('id' => 'modalwrapper', 'class' => 'wrapper'));
        $html .= html_writer::start_div('row contentwrapper');
        // The form starts here.
        $html .= html_writer::start_tag('form',
                        array('id' => 'form-print-events',
                    'name' => 'form-print-events',
                    'method' => 'post',
                    'action' => 'eudeeventlist.php'));

        // Section for the events key.
        $html .= html_writer::start_div('col-md-12 eventkeywrapper');
        $html .= html_writer::tag('h3', get_string('eventkeytitle', 'local_eudecustom'));
        $html .= html_writer::start_tag('ul', array('class' => 'eventkey col-md-12'));

        $html .= html_writer::start_div('col-md-4');

        $html .= html_writer::start_tag('li', array('id' => 'eventkeymodulebegin', 'class' => 'eventkey'));
        if (optional_param('modulebegin', null, PARAM_TEXT) || optional_param('modulebegin_modal', null, PARAM_TEXT)) {
            $html .= html_writer::empty_tag('input',
                            array('type' => 'checkbox', 'id' => 'cb-eventkeymodulebegin',
                        'class' => 'cb-eventkey', 'name' => 'modulebegin', 'checked' => 'checked'));
        } else {
            $html .= html_writer::empty_tag('input',
                            array('type' => 'checkbox', 'id' => 'cb-eventkeymodulebegin',
                        'class' => 'cb-eventkey', 'name' => 'modulebegin'));
        }

        $html .= html_writer::start_tag('div',
                        array('id' => 'cd-eventkeymodulebegin',
                    'class' => 'cd-eventkey eventkeymodulebegin'));
        $html .= html_writer::end_tag('div');
        $html .= html_writer::tag('span', get_string('eventkeymodulebegin', 'local_eudecustom'));
        $html .= html_writer::end_tag('li');

        $html .= html_writer::start_tag('li', array('id' => 'eventkeyactivityend', 'class' => 'eventkey'));
        if (optional_param('activityend', null, PARAM_TEXT) || optional_param('activityend_modal', null, PARAM_TEXT)) {
            $html .= html_writer::empty_tag('input',
                            array('type' => 'checkbox', 'id' => 'cb-eventkeyactivityend',
                        'class' => 'cb-eventkey', 'name' => 'activityend', 'checked' => 'checked'));
        } else {
            $html .= html_writer::empty_tag('input',
                            array('type' => 'checkbox', 'id' => 'cb-eventkeyactivityend',
                        'class' => 'cb-eventkey', 'name' => 'activityend'));
        }

        $html .= html_writer::start_tag('div',
                        array('id' => 'cd-eventkeyactivityend',
                    'class' => 'cd-eventkey eventkeyactivityend'));
        $html .= html_writer::end_tag('div');
        $html .= html_writer::tag('span', get_string('eventkeyactivityend', 'local_eudecustom'));
        $html .= html_writer::end_tag('li');

        $html .= html_writer::start_tag('li', array('id' => 'eventkeyquestionnairedate', 'class' => 'eventkey'));
        if (optional_param('questionnairedate', null, PARAM_TEXT) || optional_param('questionnairedate_modal', null, PARAM_TEXT)) {
            $html .= html_writer::empty_tag('input',
                            array('type' => 'checkbox', 'id' => 'cb-eventkeyquestionnairedate',
                        'class' => 'cb-eventkey', 'name' => 'questionnairedate', 'checked' => 'checked'));
        } else {
            $html .= html_writer::empty_tag('input',
                            array('type' => 'checkbox', 'id' => 'cb-eventkeyquestionnairedate',
                        'class' => 'cb-eventkey', 'name' => 'questionnairedate'));
        }

        $html .= html_writer::start_tag('div',
                        array('id' => 'cd-eventkeyquestionnairedate',
                    'class' => 'cd-eventkey eventkeyquestionnairedate'));
        $html .= html_writer::end_tag('div');
        $html .= html_writer::tag('span', get_string('eventkeyquestionnaire', 'local_eudecustom'));
        $html .= html_writer::end_tag('li');

        $html .= html_writer::end_tag('div');
        $html .= html_writer::start_div('col-md-4');

        $html .= html_writer::start_tag('li', array('id' => 'eventkeytestdate', 'class' => 'eventkey'));
        if (optional_param('testdate', null, PARAM_TEXT) || optional_param('testdate_modal', null, PARAM_TEXT)) {
            $html .= html_writer::empty_tag('input',
                            array('type' => 'checkbox', 'id' => 'cb-eventkeytestdate',
                        'class' => 'cb-eventkey', 'name' => 'testdate', 'checked' => 'checked'));
        } else {
            $html .= html_writer::empty_tag('input',
                            array('type' => 'checkbox', 'id' => 'cb-eventkeytestdate',
                        'class' => 'cb-eventkey', 'name' => 'testdate'));
        }

        $html .= html_writer::start_tag('div', array('id' => 'cd-eventkeytestdate', 'class' => 'cd-eventkey eventkeytestdate'));
        $html .= html_writer::end_tag('div');
        $html .= html_writer::tag('span', get_string('eventkeytestdate', 'local_eudecustom'));
        $html .= html_writer::end_tag('li');

        $html .= html_writer::start_tag('li', array('id' => 'eventkeyintensivemodulebegin', 'class' => 'eventkey'));
        if (optional_param('intensivemodulebegin', null, PARAM_TEXT) ||
                optional_param('intensivemodulebegin_modal', null, PARAM_TEXT)) {
            $html .= html_writer::empty_tag('input',
                            array('type' => 'checkbox', 'id' => 'cb-eventkeyintensivemodulebegin',
                        'class' => 'cb-eventkey', 'name' => 'intensivemodulebegin', 'checked' => 'checked'));
        } else {
            $html .= html_writer::empty_tag('input',
                            array('type' => 'checkbox', 'id' => 'cb-eventkeyintensivemodulebegin',
                        'class' => 'cb-eventkey', 'name' => 'intensivemodulebegin'));
        }

        $html .= html_writer::start_tag('div',
                        array('id' => 'cd-eventkeyintensivemodulebegin',
                    'class' => 'cd-eventkey eventkeyintensivemodulebegin'));
        $html .= html_writer::end_tag('div');
        $html .= html_writer::tag('span', get_string('eventkeyintensivemodulebegin', 'local_eudecustom'));
        $html .= html_writer::end_tag('li');

        $html .= html_writer::end_tag('div');
        $html .= html_writer::start_div('col-md-4');

        $html .= html_writer::start_tag('li', array('id' => 'eventkeyeudeevent', 'class' => 'eventkey'));
        if (optional_param('eudeevent', null, PARAM_TEXT) || optional_param('eudeevent_modal', null, PARAM_TEXT)) {
            $html .= html_writer::empty_tag('input',
                            array('type' => 'checkbox', 'id' => 'cb-eventkeyeudeevent',
                        'class' => 'cb-eventkey', 'name' => 'eudeevent', 'checked' => 'checked'));
        } else {
            $html .= html_writer::empty_tag('input',
                            array('type' => 'checkbox', 'id' => 'cb-eventkeyeudeevent',
                        'class' => 'cb-eventkey', 'name' => 'eudeevent'));
        }

        $html .= html_writer::start_tag('div', array('id' => 'cd-eventkeyeudeevent', 'class' => 'cd-eventkey eventkeyeudeevent'));
        $html .= html_writer::end_tag('div');
        $html .= html_writer::tag('span', get_string('eventkeyeudeevent', 'local_eudecustom'));
        $html .= html_writer::end_tag('li');

        $html .= html_writer::end_tag('div');

        $html .= html_writer::end_tag('ul');
        $html .= html_writer::end_tag('div');

        // Section for the datepickers.
        $html .= html_writer::start_div('col-md-3 datepickerwrapper');
        $html .= html_writer::tag('label', get_string('datefrom', 'local_eudecustom'), array('for' => 'categoryname'));
        if (optional_param('startdatemodal', null, PARAM_TEXT)) {
            $html .= html_writer::empty_tag('input',
                            array('type' => 'text', 'id' => 'startdatemodal', 'class' => 'startdatemodal inputdate',
                        'name' => 'startdatemodal', 'placeholder' => 'dd/mm/aaaa',
                        'value' => optional_param('startdatemodal', null, PARAM_TEXT)));
        } else {
            $html .= html_writer::empty_tag('input',
                            array('type' => 'text', 'id' => 'startdatemodal', 'class' => 'startdatemodal inputdate',
                        'name' => 'startdatemodal', 'placeholder' => 'dd/mm/aaaa'));
        }

        $html .= html_writer::end_tag('div');
        $html .= html_writer::start_div('col-md-3 datepickerwrapper');
        $html .= html_writer::tag('label', get_string('dateuntil', 'local_eudecustom'), array('for' => 'categoryname'));
        if (optional_param('enddatemodal', null, PARAM_TEXT)) {
            $html .= html_writer::empty_tag('input',
                            array('type' => 'text', 'id' => 'enddatemodal',
                        'class' => 'enddatemodal inputdate', 'name' => 'enddatemodal', 'placeholder' => 'dd/mm/aaaa',
                        'value' => optional_param('enddatemodal', null, PARAM_TEXT)));
        } else {
            $html .= html_writer::empty_tag('input',
                            array('type' => 'text', 'id' => 'enddatemodal',
                        'class' => 'enddatemodal inputdate', 'name' => 'enddatemodal', 'placeholder' => 'dd/mm/aaaa'));
        }

        $html .= html_writer::end_tag('div');

        // Generate event list button.
        $html .= html_writer::start_div('col-md-6');
        $html .= html_writer::nonempty_tag('button', get_string('updateeventlist', 'local_eudecustom'),
                        array('type' => 'submit',
                    'id' => 'generateeventlist',
                    'name' => 'generateeventlist',
                    'class' => 'btn btn-default',
                    'value' => 'Generate'));
        $html .= html_writer::end_div();

        $html .= html_writer::end_tag('form');

        $html .= html_writer::end_div();
        $html .= html_writer::end_div();

        // Section for the add event button and two calendars.
        $html .= html_writer::start_div('row');
        $html .= html_writer::start_div('col-md-12 eventcontent');
        if (count($events)) {
            // Print button.
            $html .= html_writer::start_div('col-md-2 col-md-offset-10 text-right printbuttonwrapper');
            $html .= html_writer::tag('button', get_string('printevents', 'local_eudecustom'),
                            array('id' => 'printeventbutton', 'class' => 'btn btn-default printeventbutton'));
            $html .= html_writer::end_div();
            $html .= html_writer::start_div('col-md-12 eventtable', array('id' => 'eventtablecontent'));
            $html .= html_writer::start_tag('table', array('class' => 'table table-striped'));
            $html .= html_writer::start_tag('thead');
            $html .= html_writer::start_tag('tr');
            $html .= html_writer::start_tag('th', array('class' => 'idcolumn'));
            $html .= html_writer::tag('span', '#');
            $html .= html_writer::end_tag('th');
            $html .= html_writer::start_tag('th', array('class' => 'datecolumn'));
            $html .= html_writer::tag('span', get_string('eventdate', 'local_eudecustom'));
            $html .= html_writer::end_tag('th');
            $html .= html_writer::start_tag('th', array('class' => 'namecolumn'));
            $html .= html_writer::tag('span', get_string('eventname', 'local_eudecustom'));
            $html .= html_writer::end_tag('th');
            $html .= html_writer::end_tag('tr');
            $html .= html_writer::end_tag('thead');
            $html .= html_writer::start_tag('tbody');
            $i = 1;
            foreach ($events as $event) {
                $html .= html_writer::start_tag('tr', array('class' => $event->class));
                $html .= html_writer::start_tag('th');
                $html .= html_writer::tag('span', $i);
                $html .= html_writer::end_tag('th');
                $html .= html_writer::start_tag('th');
                $html .= html_writer::tag('span', date('d-m-Y', $event->timestart));
                $html .= html_writer::end_tag('th');
                $html .= html_writer::start_tag('th');
                $html .= html_writer::start_tag('div', array('class' => 'cd-eventkey ' . $event->class));
                $html .= html_writer::end_tag('div');
                $html .= html_writer::tag('span', $event->name);
                $html .= html_writer::end_tag('th');
                $html .= html_writer::end_tag('tr');
                $i++;
            }

            $html .= html_writer::end_tag('tbody');
            $html .= html_writer::end_tag('table');
            $html .= html_writer::end_div();
        } else {
            $html .= html_writer::tag('span', get_string('noevents', 'local_eudecustom'));
        }
        $html .= html_writer::end_div();
        $html .= html_writer::end_div();

        $response .= $html;
        $response .= $this->footer();
        return $response;
    }

    /**
     * Gets the calendar popup.
     *
     * It called at multiple points in from calendar_get_mini.
     * Copied and modified from calendar_get_mini.
     *
     * @param bool $today false except when called on the current day.
     * @param mixed $timestart $events[$eventid]->timestart, OR false if there are no events.
     * @param string $popupcontent content for the popup window/layout.
     * @return string eventid for the calendar_tooltip popup window/layout.
     */
    public function eude_calendar_get_popup($today = false, $timestart, $popupcontent = '') {
        $popupcaption = '';
        if ($today) {
            $popupcaption = get_string('today', 'calendar') . ' ';
        }

        if (false === $timestart) {
            $popupcaption .= userdate(time(), get_string('strftimedayshort'));
            $popupcontent = get_string('eventnone', 'calendar');

        } else {
            $popupcaption .= get_string('eventsfor', 'calendar', userdate($timestart, get_string('strftimedayshort')));
        }

        return array(
            'data-core_calendar-title' => $popupcaption,
            'data-core_calendar-popupcontent' => $popupcontent,
        );
    }

}
