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
 * Basic authentication steps definitions.
 *
 * @package    local_eudecustom
 * @category   test
 * @copyright  2017 Planificacion de Entornos Tecnologicos SL
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

use Behat\Behat\Context\Step\Given as Given;
use Behat\Behat\Context\Step\When as When;

/**
 * Log in log out steps definitions.
 *
 * @package    local_eudecustom
 * @category   test
 * @copyright  2017 Planificacion de Entornos Tecnologicos SL
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_local_eudecustom extends behat_base {

    /**
     * Opens Eudecustom Integratedata page.
     *
     * @Given /^I go to eude integration$/
     */
    public function i_go_to_eudeintegration () {
        $this->getSession()->visit($this->locate_path("/local/eudecustom/eudeintegration.php"));
    }

    /**
     * Opens Eudecustom Intensivemoduledates page.
     *
     * @Given /^I go to eude intensive module dates$/
     */
    public function i_go_to_eude_intensive_module_dates () {
        $this->getSession()->visit($this->locate_path("/local/eudecustom/eudeintensivemoduledates.php"));
    }

    /**
     * Opens Eudecustom Gradesearch page.
     *
     * @Given /^I go to search grades$/
     */
    public function i_go_to_eude_grade_search () {
        $this->getSession()->visit($this->locate_path("/local/eudecustom/eudegradesearch.php"));
    }

    /**
     * Opens Moodle intensives page.
     *
     * @Given /^I go to intensives$/
     */
    public function i_go_to_intensives () {
        $this->getSession()->visit($this->locate_path('/local/eudecustom/eudeprofile.php'));
    }

    /**
     * Opens Eudecustom Messages.
     *
     * @Given /^I go to eudemessages$/
     */
    public function i_go_to_eudemessages () {
        $this->getSession()->visit($this->locate_path("/local/eudecustom/eudemessages.php"));
    }

    /**
     * Opens Eudecustom Calendar.
     *
     * @Given /^I go to eudecalendar/
     */
    public function i_go_to_calendar () {
        $this->getSession()->visit($this->locate_path("/local/eudecustom/eudecalendar.php"));
    }

    /**
     * Inserts records into our custom table in order to create the background.
     *
     * @Given /^I set initial dates of intensive modules$/
     */
    public function i_set_initialdates_of_intensivemodules () {
        global $DB;
        $intensivemodule1 = $DB->get_record('course', array('shortname' => 'MI.Course 1'));
        $intensivemodule2 = $DB->get_record('course', array('shortname' => 'MI.Course 2'));
        $record1 = new stdClass();
        $record1->courseid = $intensivemodule1->id;
        $record1->fecha1 = time();
        $record1->fecha2 = time();
        $record1->fecha3 = time();
        $record1->fecha4 = time();
        $record2 = new stdClass();
        $record2->courseid = $intensivemodule2->id;
        $record2->fecha1 = time();
        $record2->fecha2 = time();
        $record2->fecha3 = time();
        $record2->fecha4 = time();
        $DB->insert_record('local_eudecustom_call_date', $record1, false);
        $DB->insert_record('local_eudecustom_call_date', $record2, false);
    }

    /**
     * Click on module beginning.
     *
     * @Given /^I click module beginning/
     */
    public function i_process_course_enrolments_and_generate_holiday () {
        global $CFG;
        global $DB;

        $generatecourseevents = $CFG->local_eudest_genenrolcalendar;
        if (!$generatecourseevents) {
            return 0;
        }

        $sql = "SELECT * FROM {eudest_enrols} WHERE pend_event = :event";
        $records = $DB->get_records_sql($sql, array('event' => 1));

        foreach ($records as $record) {
            $evname = "[[COURSE]]$record->shortname";
            if ($record->intensive) {
                $evname = "[[MI]]$record->shortname";
            }
            $evdescription = $evname;
            $evtimestart = $record->startdate;
            $evduration = $record->enddate - $record->startdate;
            $evuserid = $record->userid;
            $this->eude_add_event_to_calendar($evname, $evdescription, $evtimestart, $evduration, $evuserid);
            $record->pend_event = 0;
            $DB->update_record('eudest_enrols', $record);
        }

        $generateholidaysevents = $CFG->local_eudest_genholidaycalendar;
        if (!$generateholidaysevents) {
            return 0;
        }

        $noticeholidays = $CFG->local_eudest_holydaynotice;

        $sql = "SELECT * FROM {eudest_masters} WHERE pend_holidays = :holidays";
        $masters = $DB->get_records_sql($sql, array('holidays' => 1));

        $nodeholidays = [];
        foreach ($masters as $master) {

            $sqlenrols = "SELECT * FROM {eudest_enrols} WHERE masterid = :masterid ORDER BY startdate asc";
            $enrols = $DB->get_records_sql($sqlenrols, array("masterid" => $master->id));

            $gapdate = 0;
            foreach ($enrols as $enrol) {

                if (strrpos($enrol->shortname, ".M00")) {
                    continue;
                }

                $enrol->startdate = strtotime(date("Y-m-d", $enrol->startdate));
                $enrol->enddate = strtotime(date("Y-m-d", $enrol->enddate));
                if ($enrol->enddate < $gapdate) {
                    continue;
                }
                if ($gapdate == 0 || ($enrol->startdate == $gapdate)) {
                    $gapdate = strtotime('+1 day', $enrol->enddate);
                    continue;
                }

                $evname = "[[HOLIDAYS]]";
                $evdescription = $evname;
                $evtimestart = $gapdate;
                $evduration = strtotime('-1 minutes', $enrol->startdate) - $gapdate;
                $evuserid = $enrol->userid;
                $this->eude_add_event_to_calendar($evname, $evdescription, $evtimestart, $evduration, $evuserid);

                if ($noticeholidays) {
                    $exists = false;
                    $noticedate = strtotime('-3 days', $gapdate);
                    foreach ($nodeholidays as $nodeholiday) {
                        if ($nodeholiday->noticedate == $noticedate && $nodeholiday->categoryid == $enrol->categoryid) {
                            if (!in_array($enrol->userid, $nodeholiday->users)) {
                                array_push($nodeholiday->users, $enrol->userid);
                            }
                            $exists = true;
                            break;
                        }
                    }
                    if (!$exists) {
                        $nodeholiday = new stdClass();
                        $nodeholiday->noticedate = $noticedate;
                        $nodeholiday->categoryid = $enrol->categoryid;
                        $nodeholiday->users = [];
                        array_push($nodeholiday->users, $enrol->userid);
                        array_push($nodeholidays, $nodeholiday);
                    }
                }

                $gapdate = strtotime('+1 day', $enrol->enddate);
            }

            $master->pend_holidays = 0;
            $DB->update_record('eudest_masters', $master);
        }

        if ($noticeholidays) {
            foreach ($nodeholidays as $nodeholiday) {
                $to = implode(",", $nodeholiday->users);
                $this->eude_add_message_to_stack($nodeholiday->categoryid, $to, "", $this->msgtypeHolidays,
                        $nodeholiday->noticedate);
            }
        }
    }

    /**
     * Add local_eudecustom_call_dates data.
     *
     * @Given /^I add matriculation dates$/
     */
    public function add_dates() {
        global $DB;

        // Use an actual time variable and force to relative matricualtion dates.
        $today = time();
        $course1 = $DB->get_record('course', array('shortname' => 'MI.C1'));
        $record = new stdClass();
        $record->courseid = $course1->id;
        $record->fecha1 = $today - 604800;
        $record->fecha2 = $today + 604800;
        $record->fecha3 = $today + 1280000;
        $record->fecha4 = $today + 3800000;
        $DB->insert_record('local_eudecustom_call_date', $record, false);

        $course2 = $DB->get_record('course', array('shortname' => 'MI.C2'));
        $record2 = new stdClass();
        $record2->courseid = $course2->id;
        $record2->fecha1 = $today;
        $record2->fecha2 = $today + 704800;
        $record2->fecha3 = $today + 1704800;
        $record2->fecha4 = $today + 2804800;
        $DB->insert_record('local_eudecustom_call_date', $record2, false);
    }

    /**
     *
     * This function add enrols
     *
     * @When /^I add intensive enrols$/
     */
    public function intensive_enrols() {

        global $DB;
        $coursedata = $DB->get_record('course', array('shortname' => 'MI.C1'));
        $enroldata = $DB->get_record('enrol', array('courseid' => $coursedata->id, 'enrol' => 'manual'));
        $userdata = $DB->get_record('user', array('email' => 'student3@example.com'));
        $enrolmentdata = $DB->get_record('user_enrolments', array('enrolid' => $enroldata->id, 'userid' => $userdata->id));

        $record = new stdClass();
        $record->user_email = "student1@example.com";
        $record->course_category = $coursedata->category;
        $record->num_intensive = 1;
        $DB->insert_record('local_eudecustom_user', $record, false);

        // Use an actual time variable and force to relative matricualtion dates.
        $today = time();

        $record2 = new stdClass();
        $record2->user_email = "student1@example.com";
        $record2->course_shortname = $coursedata->shortname;
        $record2->category_id = $coursedata->category;
        $record2->matriculation_date = $today - 604800;
        $record2->conv_number = 1;
        $DB->insert_record('local_eudecustom_mat_int', $record2, false);

        $record3 = new stdClass();
        $record3->user_email = "student3@example.com";
        $record3->course_category = $coursedata->category;
        $record3->num_intensive = 2;
        $DB->insert_record('local_eudecustom_user', $record3, false);

        $record4 = new stdClass();
        $record4->user_email = "student3@example.com";
        $record4->course_shortname = $coursedata->shortname;
        $record4->category_id = $coursedata->category;
        $record4->matriculation_date = $today - 604800;
        $record4->conv_number = 1;
        $DB->insert_record('local_eudecustom_mat_int', $record4, false);

        $record5 = new stdClass();
        $record5->user_email = "student3@example.com";
        $record5->course_shortname = $coursedata->shortname;
        $record5->category_id = $coursedata->category;
        $record5->matriculation_date = $today + 3800000;
        $record5->conv_number = 4;
        $DB->insert_record('local_eudecustom_mat_int', $record5, false);

        // Require a timestart on user_enrolment table to test editing data.

        $enroldata = new StdClass();
        $enroldata = $DB->get_record('user_enrolments', array('id' => $enrolmentdata->id));
        $enroldata->timestart = $today + 3800000;

        $DB->update_record('user_enrolments', $enroldata, false);
    }

    /**
     * Click on the element with the provided xpath query
     *
     * @When /^I click on the element with xpath "([^"]*)"$/
     * @param string $xpath xpath of the element
     */
    public function i_click_on_the_element_with_xpath($xpath) {
        $session = $this->getSession();
        // Get the mink session.
        $element = $session->getPage()->find(
                'xpath', $session->getSelectorsHandler()->selectorToXpath('xpath', $xpath)
        );
        // Runs the actual query and returns the element.
        // Errors must not pass silently.
        if (null === $element) {
            throw new \InvalidArgumentException(sprintf('Could not evaluate XPath: "%s"', $xpath));
        }

        $element->click();
    }

    /**
     * This function add events to calendar
     *
     * @When /^I add events to calendar/
     * @param string $name
     * @param string $description
     * @param integer $timestart
     * @param integer $duration
     * @param integer $userid
     *
     */
    private function eude_add_event_to_calendar($name, $description, $timestart, $duration, $userid) {
        $event = new stdClass();
        $event->name = $name;
        $event->modulename = "";
        $event->description = $description;
        $event->groupid = 0;
        $event->timestart = $timestart;
        $event->visible = 1;
        $event->timeduration = $duration;
        $event->userid = $userid;

        calendar_event::create($event);
    }

    /**
     * Opens Eude teacher control panel.
     *
     * @Given /^I go to eudeteachercontrolpanel$/
     */
    public function i_go_to_eudeteachercontrolpanel() {
        $this->getSession()->visit($this->locate_path("/local/eudecustom/eudeteachercontrolpanel.php"));
    }

    /**
     * This function looks for the shortname in a specific xpath depending the timestart and timeend
     *
     * @Given I should visualize :text with :timestart and :timeend
     * @param string $text a course shortname
     * @param int $timestart the start of enrolment
     * @param int $timeend the end of the enrolment
     */
    public function i_should_visualize_with_and($text, $timestart, $timeend) {
        $time = time();
        switch ($time) {
            case $time > $timeend:
                $element = "//div[@class='row eude_panel_bg']/div/div[1]/div[2]";
                break;
            case $time < $timestart:
                $element = "//div[@class='row eude_panel_bg']/div/div[2]/div[2]";
                break;
            case $time > $timestart && $time < $timeend:
                $element = "//div[@class='row eude_panel_bg']/div/div[2]/div[1]";
        }
        $selectortype = "xpath_element";
        // Getting the container where the text should be found.
        $container = $this->get_selected_node($selectortype, $element);

        // Looking for all the matching nodes without any other descendant matching the
        // same xpath (we are using contains(., ....).
        $xpathliteral = behat_context_helper::escape($text);
        $xpath = "/descendant-or-self::*[contains(., $xpathliteral)]" .
                "[count(descendant::*[contains(., $xpathliteral)]) = 0]";

        // Wait until it finds the text inside the container, otherwise custom exception.
        try {
            $nodes = $this->find_all('xpath', $xpath, false, $container);
        } catch (ElementNotFoundException $e) {
            throw new ExpectationException('"' . $text . '" text was not found in the "' . $element . '" element',
            $this->getSession());
        }

        // If we are not running javascript we have enough with the
        // element existing as we can't check if it is visible.
        if (!$this->running_javascript()) {
            return;
        }

        // We also check the element visibility when running JS tests. Using microsleep as this
        // is a repeated step and global performance is important.
        $this->spin(
                function($context, $args) {

                    foreach ($args['nodes'] as $node) {
                        if ($node->isVisible()) {
                            return true;
                        }
                    }

                    throw new ExpectationException('"'
                        . $args['text']
                        . '" text was found in the "'
                        . $args['element']
                        . '" element but was not visible',
                    $context->getSession());
                }, array('nodes' => $nodes,
                    'text' => $text,
                    'element' => $element),
                        false, false, true
        );
    }

    /**
     * This function add events
     *
     * @When /^I add events/
     */
    public function add_events() {
        global $DB;
        $coursedata = $DB->get_record('course', array('shortname' => 'M01'));
        $userdata = $DB->get_record('user', array('username' => 'user1'));
        $admindata = $DB->get_record('user', array('username' => 'admin'));

        $event = new stdClass();

        $event->name = "[[COURSE]]$coursedata->shortname";
        $event->description = $coursedata->fullname;
        $event->format = 1;
        $event->courseid = 0;
        $event->groupid = 0;
        $event->userid = $userdata->id;
        $event->instance = 0;
        $event->eventtype = 'user';
        $event->timestart = 1483255184;
        $event->timeduration = 1491031184 - 1483255184;
        $event->visible = 1;
        $event->sequence = 1;
        calendar_event::create($event);

        $coursedata = $DB->get_record('course', array('shortname' => 'MI.M02'));
        $event = new stdClass();

        $event->name = "[[MI]]$coursedata->shortname";
        $event->description = $coursedata->fullname;
        $event->format = 1;
        $event->courseid = 0;
        $event->groupid = 0;
        $event->userid = $userdata->id;
        $event->instance = 0;
        $event->eventtype = 'user';
        $event->timestart = 1512717833;
        $event->timeduration = 1513927433 - 1512717833;
        $event->visible = 1;
        $event->sequence = 1;
        calendar_event::create($event);

        $event = new stdClass();

        $event->name = 'Event site 1';
        $event->description = 'Event site test';
        $event->format = 1;
        $event->courseid = 1;
        $event->groupid = 0;
        $event->userid = $admindata->id;
        $event->instance = 0;
        $event->eventtype = 'site';
        $event->timestart = 1483258962;
        $event->timeduration = 864000;
        $event->visible = 1;
        $event->sequence = 1;
        calendar_event::create($event);
    }

    /**
     * Opens Eude dashboard page.
     *
     * @Given /^I go to eudedashboard$/
     */
    public function i_go_to_eudedashboard() {
        $this->getSession()->visit($this->locate_path("/local/eudecustom/eudedashboard.php"));
    }
}
