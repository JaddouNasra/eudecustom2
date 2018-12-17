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
 * Moodle custom renderer class for eudeprofile view.
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
class eudedashboard_renderer extends \plugin_renderer_base {

    /**
     * Render custom for eude new dashboard.
     *
     * @param array $data all the student data related to this view.
     * @return string html to output.
     */
    public function eude_dashboard_student_page($data) {
        $response = '';
        $response .= $this->header();

        $html = html_writer::start_div('row');
        $html .= html_writer::start_div('col-md-10 offset-md-1');

        $html .= html_writer::start_tag('ul',
                                        array('class' => 'nav nav-tabs nav-tabs-responsive',
                                              'id' => 'eudedashboardmyTab', 'role' => 'tablist'));
        $catnum = 0;
        foreach ($data as $key => $value) {
            $active = "";
            $ariaselected = "false";
            if ($catnum == 0) {
                $active = "active";
                $ariaselected = "true";
            }
            if ($catnum == 1) {
                $active = "next";
            }
            $html .= $this->eude_dashboard_nav_category_tab($key, $value, $active, $ariaselected);
            $catnum += 1;
        }
        $html .= html_writer::end_tag('ul');

        $html .= html_writer::start_tag('div', array('class' => 'tab-content', 'id' => 'eudedashboardmyTabContent'));
        $catnum = 0;
        foreach ($data as $key => $value) {
            $active = "";
            if ($catnum == 0) {
                $active = "show active";
            }
            $html .= $this->eude_dashboard_nav_category_content($key, $value, $active);
            $catnum += 1;
        }
        $html .= html_writer::end_tag('div');

        $html .= html_writer::end_div();
        $html .= html_writer::end_div();

        $response .= $html;
        $response .= $this->footer();
        return $response;
    }

    /**
     * Render custom for eude new dashboard.
     *
     * @param array $data all the teacher data related to this view.
     * @return string html to output.
     */
    public function eude_dashboard_teacher_page($data) {
        $response = '';
        $response .= $this->header();

        $html = html_writer::start_div('row');
        $html .= html_writer::start_div('filterbuttonswrapper col-md-12');

        $html .= html_writer::start_tag('button', array('class' => "btn btn-default dashboardbtn dashboardbtnteachertotal",
                                                        'id' => "dashboardbtnteachertotal"));
        $html .= "<span class='edb-number edb-total'>" . count($data->courses) . "</span>";
        $html .= "<span class='edb-text'>" . get_string('dashboardfiltertotal', 'local_eudecustom') . "</span>";
        $html .= "<i class='icon edbicon fa fa-bullseye'></i>";
        $html .= html_writer::end_tag('button');

        $html .= html_writer::start_tag('button', array('class' => "btn btn-default dashboardbtn dashboardbtnteacherincourse",
                                                        'id' => "dashboardbtnteacherincourse"));
        $html .= "<span class='edb-number edb-teacherincourse'>" . $data->totalactivestudents  . "</span>";
        $html .= "<span class='edb-text'>" . get_string('dashboardfilterteacherincourse', 'local_eudecustom') . "</span>";
        $html .= "<i class='icon edbicon fa fa-info-circle'></i>";
        $html .= html_writer::end_tag('button');

        $html .= html_writer::start_tag('button',
                                        array('class' => "btn btn-default dashboardbtn dashboardbtnteacherpendingactivities",
                                              'id' => "dashboardbtnteacherpendingactivities"));
        $html .= "<span class='edb-number edb-teacherpendingactivities'>" . $data->totalpendingactivities  . "</span>";
        $html .= "<span class='edb-text'>" . get_string('dashboardbtnteacherpendingactivities', 'local_eudecustom') . "</span>";
        $html .= "<i class='icon edbicon fa fa-arrow-down'></i>";
        $html .= html_writer::end_tag('button');

        $html .= html_writer::start_tag('button',
                                        array('class' => "btn btn-default dashboardbtn dashboardbtnteacherpendingmessages",
                                              'id' => "dashboardbtnteacherpendingmessages"));
        $html .= "<span class='edb-number edb-teacherpendingmessages'>" . $data->totalpendingmessages  . "</span>";
        $html .= "<span class='edb-text'>" . get_string('dashboardbtnteacherpendingmessages', 'local_eudecustom') . "</span>";
        $html .= "<i class='icon edbicon fa fa-check-circle'></i>";
        $html .= html_writer::end_tag('button');

        $html .= html_writer::end_div();

        $html .= html_writer::start_div('dashboardcoursecardswrapper col-md-12 row');

        foreach ($data->courses as $key => $value) {
            $html .= $this->eude_dashboard_teacher_course_card($key, $value);
        }

        $html .= html_writer::end_div();

        $html .= html_writer::end_div();

        $response .= $html;
        $response .= $this->footer();
        return $response;
    }

    /**
     * Render nav tabs for course categories
     *
     * @param string $categoryid category id
     * @param stdClass $categoryinfo object with info from the courses in the category
     * @param string $active for bootstrap tab
     * @param string $ariaselected for bootstrap tab
     * @return string html to output.
     */
    public function eude_dashboard_nav_category_tab($categoryid, $categoryinfo, $active = "", $ariaselected = "false") {
        $response = "";
        $html = html_writer::start_tag('li', array('class' => "nav-item col-md-3 $active"));
        $html .= html_writer::start_tag('a', array('class' => "nav-link $active",
                                                   'id' => "nav-category$categoryid-tab",
                                                   'data-toggle' => 'tab',
                                                   'href' => "#nav-category$categoryid",
                                                   'role' => 'tab',
                                                   'aria-controls' => "nav-category$categoryid",
                                                   'aria-selected' => $ariaselected));
        $html .= "<span class='eudedashboardcategoryname'>" . $categoryinfo->name . "</span>";
        if ($categoryinfo->averagecoursecompletion >= 0  && $categoryinfo->nextconvocatory == "") {
            $html .= "<span class='eudedashboardprogressinfo'>" . $categoryinfo->averagecoursecompletion
                     . get_string('dashboardcategorycourseprogresstext', 'local_eudecustom') . "</span>";
            $html .= "<div class='progress eudedashboardprogresswrapper'>"
                     . "<div class='progress-bar eudedashboardprogressbar' role='progressbar' aria-valuenow='"
                     . $categoryinfo->averagecoursecompletion . "' aria-valuemin='0' aria-valuemax='100' style='width:"
                     . $categoryinfo->averagecoursecompletion . "%'><span class='sr-only'>70% Complete</span></div></div>";
        }
        if ($categoryinfo->nextconvocatory != "") {
            $html .= "<span class='eudedashboardcategoryconvocatory'>"
                     . get_string('eudedashboardcategoryconvocatory', 'local_eudecustom')
                     . " " . $categoryinfo->nextconvocatory . "</span>";
        }
        $html .= html_writer::end_tag('a');
        $html .= html_writer::end_tag('li');

        $response = $html;

        return $response;
    }

    /**
     * Render nav tab content for course categories
     *
     * @param string $categoryid category id
     * @param stdClass $categoryinfo object with info from the courses in the category
     * @param string $active for bootstrap tab
     * @return string html to output.
     */
    public function eude_dashboard_nav_category_content($categoryid, $categoryinfo, $active = "") {
        $response = "";

        $html = html_writer::start_tag('div', array('class' => "tab-pane fade $active",
                                                  'id' => "nav-category$categoryid",
                                                  'aria-labelledby' => "nav-category$categoryid-tab",
                                                  'role' => 'tabpanel'));
        $html .= html_writer::start_div('row');

        $html .= html_writer::start_div('filterbuttonswrapper col-md-12');
        $html .= html_writer::start_tag('button',
                                        array('class' => "btn btn-default dashboardbtn dashboardbtntotal col-md-2 eudeactive",
                                              'id' => "dashboardbtntotal-$categoryid"));
        $html .= "<span class='edb-number edb-total'>" . count($categoryinfo->courses) . "</span>";
        $html .= "<span class='edb-text'>" . get_string('dashboardfiltertotal', 'local_eudecustom') . "</span>";
        $html .= "<i class='icon edbicon fa fa-bullseye'></i>";
        $html .= html_writer::end_tag('button');
        $html .= html_writer::start_tag('button', array('class' => "btn btn-default dashboardbtn dashboardbtnincourse col-md-2",
                                                        'id' => "dashboardbtnincourse-$categoryid"));
        $html .= "<span class='edb-number edb-incourse'>" . $categoryinfo->totalincourse . "</span>";
        $html .= "<span class='edb-text'>" . get_string('dashboardfilterincourse', 'local_eudecustom') . "</span>";
        $html .= "<i class='icon edbicon fa fa-info-circle'></i>";
        $html .= html_writer::end_tag('button');
        $html .= html_writer::start_tag('button', array('class' => "btn btn-default dashboardbtn dashboardbtnfailed col-md-2",
                                                        'id' => "dashboardbtnfailed-$categoryid"));
        $html .= "<span class='edb-number edb-failed'>" . $categoryinfo->totalfailed . "</span>";
        $html .= "<span class='edb-text'>" . get_string('dashboardfilterfailed', 'local_eudecustom') . "</span>";
        $html .= "<i class='icon edbicon fa fa-arrow-down'></i>";
        $html .= html_writer::end_tag('button');
        $html .= html_writer::start_tag('button', array('class' => "btn btn-default dashboardbtn dashboardbtnpassed col-md-2",
                                                        'id' => "dashboardbtnpassed-$categoryid"));
        $html .= "<span class='edb-number edb-passed'>" . $categoryinfo->totalpassed  . "</span>";
        $html .= "<span class='edb-text'>" . get_string('dashboardfilterpassed', 'local_eudecustom') . "</span>";
        $html .= "<i class='icon edbicon fa fa-check-circle'></i>";
        $html .= html_writer::end_tag('button');
        $html .= html_writer::start_tag('button', array('class' => "btn btn-default dashboardbtn dashboardbtnconvalidated col-md-2",
                                                        'id' => "dashboardbtnconvalidated-$categoryid"));
        $html .= "<span class='edb-number edb-convalidated'>" . $categoryinfo->totalconvalidated . "</span>";
        $html .= "<span class='edb-text'>" . get_string('dashboardfilterconvalidated', 'local_eudecustom') . "</span>";
        $html .= "<i class='icon edbicon fa fa-exchange'></i>";
        $html .= html_writer::end_tag('button');
        $html .= html_writer::start_tag('button', array('class' => "btn btn-default dashboardbtn dashboardbtnpending col-md-2",
                                                        'id' => "dashboardbtnpending-$categoryid"));
        $html .= "<span class='edb-number edb-total'>" . $categoryinfo->totalpending  . "</span>";
        $html .= "<span class='edb-text'>" . get_string('dashboardfilterpending', 'local_eudecustom') . "</span>";
        $html .= "<i class='icon edbicon fa fa-hourglass-half'></i>";
        $html .= html_writer::end_tag('button');
        $html .= html_writer::end_tag('div');

        $html .= html_writer::start_div('dashboardcoursecardswrapper col-md-12 row');

        foreach ($categoryinfo->courses as $key => $value) {
            $html .= $this->eude_dashboard_nav_category_course_card($key, $value);
        }

        $html .= html_writer::end_tag('div');

        $html .= html_writer::end_tag('div');

        $html .= html_writer::end_tag('div');

        $response = $html;

        return $response;
    }

    /**
     * Render dashboard custom course box
     *
     * @param string $courseid course id
     * @param stdClass $coursedata object with info from the course
     * @return string html to output.
     */
    public function eude_dashboard_nav_category_course_card($courseid, $coursedata) {
        global $CFG;

        $response = "";

        $html = html_writer::start_tag('div', array('class' => "dashboardcoursebox col-md-3 $coursedata->filterclasses",
                                                    'id' => "dashboardcoursebox-$courseid"));

        $html .= html_writer::start_tag('div', array('class' => "dashboardcourseimagewrapper"));
        $html .= html_writer::start_tag('img', array('class' => "dashboardcourseimage",
                                                             'src' => $coursedata->courseimagepath));
        $html .= html_writer::end_tag('img');
        $html .= html_writer::end_tag('div');

        $html .= html_writer::start_tag('div', array('class' => "dashboardcourseinfowrapper"));

        $html .= html_writer::tag('span', $coursedata->coursename, array('class' => "dashboardcoursename"));

        $html .= html_writer::tag('span', $coursedata->coursecatname, array('class' => "dashboardcoursecategoryname"));

        $html .= html_writer::start_tag('div', array('class' => "dashboardcoursecompletionbar"));

        if (is_numeric($coursedata->completionstatus) && $coursedata->completionstatus >= 0) {
            $html .= "<span class='eudedashboardprogressinfo'>"
                     . $coursedata->completionstatus . get_string('dashboardcourseprogresstext', 'local_eudecustom') . "</span>";
            $html .= "<div class='progress eudedashboardprogresswrapper'>"
                     . "<div class='progress-bar eudedashboardprogressbar' role='progressbar' aria-valuenow='"
                     . $coursedata->completionstatus . "' aria-valuemin='0' aria-valuemax='100' style='width:"
                     . $coursedata->completionstatus . "%'><span class='sr-only'>70% Complete</span></div></div>";
        } else {
            $html .= "<span class='eudedashboardprogressinfo'>"
                    . get_string('dashboardcourseprogressnottracked', 'local_eudecustom') . "</span>";
        }

        $html .= html_writer::end_tag('div');

        $html .= html_writer::start_tag('div', array('class' => "dashboardcoursefooter"));
        if (strpos($coursedata->filterclasses, "pending") !== false) {
            $html .= html_writer::tag('span', get_string('eudedashboardupcomingcourse', 'local_eudecustom'),
                                      array('class' => "dashboardcourseupcomingmessage"));
        } else {
            $html .= html_writer::tag('span', $coursedata->coursefinalgrade, array('class' => "dashboardcoursefinalgrade"));

            $html .= html_writer::start_tag('a', array('class' => "dashboardcourselink dashboardcourseimage",
                                               'href' => $CFG->wwwroot . "/course/view.php?id=$coursedata->courseid"));
                $html .= "<i class='icon edbicon fa fa-arrow-right'></i>";
            $html .= html_writer::end_tag('a');
        }

        $html .= html_writer::end_tag('div');

        $html .= html_writer::end_tag('div');

        $html .= html_writer::end_tag('div');

        $response = $html;

        return $response;
    }

    /**
     * Render dashboard custom course box for teacher views
     *
     * @param string $courseid course id
     * @param stdClass $coursedata object with info from the course
     * @return string html to output.
     */
    public function eude_dashboard_teacher_course_card($courseid, $coursedata) {
        global $CFG;

        $response = "";

        $html = html_writer::start_tag('div',
                                array('class' => "dashboardcoursebox col-md-3 dashboardcourse "
                                      . "$coursedata->activestudents $coursedata->pendingactivities $coursedata->pendingmessages",
                                      'id' => "dashboardcoursebox-$courseid"));

        $html .= html_writer::start_tag('div', array('class' => "dashboardcourseimagewrapper"));
        $html .= html_writer::start_tag('img', array('class' => "dashboardcourseimage",
                                                             'src' => $coursedata->courseimagepath));
        $html .= html_writer::end_tag('img');
        $html .= html_writer::end_tag('div');

        $html .= html_writer::start_tag('div', array('class' => "dashboardcourseinfowrapper"));

        $html .= html_writer::tag('span', $coursedata->coursename, array('class' => "dashboardcoursename"));

        $html .= "<br>";

        $html .= html_writer::tag('span', $coursedata->coursecatname, array('class' => "dashboardcoursecategoryname"));

        $html .= html_writer::start_tag('div', array('class' => "dashboardcoursefooter"));

        $html .= html_writer::start_tag('a', array('class' => "dashboardcourselink dashboardcourseimage",
                                                           'href' => $CFG->wwwroot . "/course/view.php?id=$coursedata->courseid"));
        $html .= "<i class='icon edbicon fa fa-arrow-right'></i>";
        $html .= html_writer::end_tag('a');

        $html .= html_writer::end_tag('div');

        $html .= html_writer::end_tag('div');

        $html .= html_writer::end_tag('div');

        $response = $html;

        return $response;
    }
}