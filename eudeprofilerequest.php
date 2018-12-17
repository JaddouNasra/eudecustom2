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
 * Moodle academic management teacher page.
 *
 * @package    local_eudecustom
 * @copyright  2017 Planificacion de Entornos Tecnologicos SL
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../config.php');

// Restrict access if the plugin is not active.
if (is_callable('mr_off') && mr_off('eudecustom', '_MR_LOCAL')) {
    die("Plugin not enabled.");
}

require_once(__DIR__ . '/utils.php');
require_once($CFG->dirroot . '/my/lib.php');
require_once($CFG->dirroot . '/tag/lib.php');
require_once($CFG->dirroot . '/user/profile/lib.php');
require_once($CFG->libdir . '/filelib.php');
require_once(__DIR__ . "/classes/models/local_eudecustom_eudeprofile.class.php");

require_login(null, false, null, false, true);

global $DB;
global $CFG;
global $USER;

$PAGE->requires->jquery();
$PAGE->requires->js_call_amd("local_eudecustom/eude", "profile");
$PAGE->requires->js_call_amd("local_eudecustom/eude", "menu");

// Request of the category.
if (optional_param('profilecat', 0, PARAM_INT)) {
    $category = optional_param('profilecat', 0, PARAM_INT);
    $response['student'] = '';
    $response['table'] = '';
    $data = $DB->get_records('course', array('category' => $category));
    $studentvalrole = get_shortname_courses_by_category($USER->id, 'studentval', $category);
    // If the request includes the id of a student we return data for the tables.
    if (optional_param('profilestudent', 0, PARAM_INT)) {
        $studentid = optional_param('profilestudent', 0, PARAM_INT);
        $table = new \html_table();
        $table->width = '100%';
        $table->head = array(get_string('module', 'local_eudecustom'), get_string('actions', 'local_eudecustom'),
            get_string('attemps', 'local_eudecustom'),
            get_string('provisionalgrades', 'local_eudecustom'), get_string('finalgrades', 'local_eudecustom'));
        $table->align = array('left', 'center', 'center', 'center', 'center');
        $table->size = array('45%', '15%', '10%', '15%', '15%');
        foreach ($data as $course) {
            if (substr($course->shortname, 0, 3) !== 'MI.') {
                $row = get_intensivecourse_data($course, $studentid);
                if ($row) {
                    $actiondata = html_writer::tag('span', $row->actions, array('class' => 'eudeprofilespan'));
                    $tr = new \html_table_row();
                    $tr->attributes['class'] = "letpv_cat" . $category . " letpv_mod" . $course->id;
                    $cell = new \html_table_cell($row->name);
                    $cell->attributes['title'] = $course->fullname;
                    $tr->cells[] = $cell;
                    if (($USER->id == $studentid && !$studentvalrole) || is_siteadmin($USER->id)) {
                        $ok = false;
                        $newdata = configureprofiledata($studentid, $course->id);
                        foreach ($newdata as $newd) {
                            if ($newd->name == $row->name) {
                                $ok = true;
                                $cell = get_intensive_action($newd, $studentid);
                                break;
                            }
                        }
                        if ($ok == false) {
                            $cell = new \html_table_cell('');
                        }
                    } else {
                        $cell = new \html_table_cell($actiondata);
                    }
                    $tr->cells[] = $cell;
                    $html = html_writer::tag('span', $row->attempts, array('class' => 'attempts'));
                    if ($row->attempts > 0 &&
                            $row->info != get_string('nogrades', 'local_eudecustom')) {
                        $html .= html_writer::empty_tag('i',
                                        array('id' => 'info', 'class' => 'fa fa-info-circle',
                                    'title' => $row->info,
                                    'aria-hidden' => 'true'));
                    }
                    $cell = new \html_table_cell($html);
                    $tr->cells[] = $cell;
                    $cell = new \html_table_cell($row->provgrades);
                    $tr->cells[] = $cell;
                    $cell = new \html_table_cell($row->finalgrades);
                    $tr->cells[] = $cell;
                    $table->data[] = $tr;
                }
            }
        }
        $categorygrades = get_grade_category($category, $studentid);
        $tr = new \html_table_row();
            $tr->attributes['class'] = "letpv_cat" . $category . " letpv_mod" . $course->id . " total";
            $cell = new \html_table_cell(get_string('totalgrade', 'local_eudecustom'));
            $tr->cells[] = $cell;
            $cell = new \html_table_cell('');
            $tr->cells[] = $cell;
            $cell = new \html_table_cell('');
            $tr->cells[] = $cell;
        if ($categorygrades != -1) {
            $cell = new \html_table_cell($categorygrades);
        } else {
            $cell = new \html_table_cell('-');
        }
        $tr->cells[] = $cell;
        $cell = new \html_table_cell('');
        $tr->cells[] = $cell;
        $table->data[] = $tr;
        $html = html_writer::table($table);
        $response = $html;
        // If the request is only for the category we return the select to choose a student.
    } else {
        $testeditingteacherrole = get_shortname_courses_by_category($USER->id, 'editingteacher', $category);
        $testteacherrole = get_shortname_courses_by_category($USER->id, 'teacher', $category);
        $testmanagerrole = get_shortname_courses_by_category($USER->id, 'manager', $category);
        $students = array();
        if (has_capability('moodle/site:config',
                context_system::instance()) || $testeditingteacherrole || $testteacherrole || $testmanagerrole) {
            foreach ($data as $course) {
                $students += get_course_students($course->id, 'student');
            }
            // Sort the array for the lastname of the students.
            sort_array_of_array($students, 'lastname');
            if (count($students)) {
                $html = html_writer::tag('label', get_string('choosestudent', 'local_eudecustom'), array());
                $html .= html_writer::start_tag('select',
                    array('id' => 'menucategoryname', 'name' => 'categoryname',
                          'class' => 'select custom-select menucategoryname'));
                $html .= html_writer::tag('option', '-- Alumno --', array('value' => ''));

                foreach ($students as $student) {
                    $html .= html_writer::tag('option', $student->lastname . ', ' . $student->firstname,
                        array('value' => $student->id));
                }

                $html .= html_writer::end_tag('select');

                $response['student'] .= $html;
            }
        } else {
            $data = get_user_all_courses($USER->id);
            $table = new \html_table();
            $table->width = '100%';
            $table->head = array(get_string('module', 'local_eudecustom'), get_string('actions', 'local_eudecustom'),
                get_string('attemps', 'local_eudecustom'),
                get_string('provisionalgrades', 'local_eudecustom'), get_string('finalgrades', 'local_eudecustom'));
            $table->align = array('left', 'center', 'center', 'center', 'center');
            $table->size = array('45%', '15%', '10%', '15%', '15%');
            foreach ($data as $course) {
                if ($course->category == $category) {
                    $row = get_intensivecourse_data($course, $USER->id);
                    if ($row) {
                        $actiondata = html_writer::tag('span', $row->actions, array('class' => 'eudeprofilespan'));
                        $tr = new \html_table_row();
                        $tr->attributes['class'] = "letpv_cat" . $category . " letpv_mod" . $course->id;
                        $cell = new \html_table_cell($row->name);
                        $cell->attributes['title'] = $course->fullname;
                        $tr->cells[] = $cell;
                        $ok = false;
                        $newdata = configureprofiledata($USER->id, false);
                        foreach ($newdata as $newd) {
                            if ($newd->name == $row->name) {
                                $ok = true;
                                $cell = get_intensive_action($newd);
                                break;
                            }
                        }
                        if ($ok == false) {
                            $cell = new \html_table_cell($actiondata);
                        }
                        $tr->cells[] = $cell;
                        $html = html_writer::tag('span', $row->attempts, array('class' => 'attempts'));
                        if ($row->attempts > 0 &&
                            $row->info != get_string('nogrades', 'local_eudecustom')) {
                            $html .= html_writer::empty_tag('i',
                                            array('id' => 'info', 'class' => 'fa fa-info-circle',
                                        'title' => $newd->info,
                                        'aria-hidden' => 'true'));
                        }
                        $cell = new \html_table_cell($html);
                        $tr->cells[] = $cell;
                        $cell = new \html_table_cell($row->provgrades);
                        $tr->cells[] = $cell;
                        $cell = new \html_table_cell($row->finalgrades);
                        $tr->cells[] = $cell;
                        $table->data[] = $tr;
                    }
                }
            }
            $categorygrades = get_grade_category($category, $USER->id);
            $tr = new \html_table_row();
            $tr->attributes['class'] = "letpv_cat" . $category . " letpv_mod" . $course->id . " total";
            $lastcell = new \html_table_cell(get_string('totalgrade', 'local_eudecustom'));
            $tr->cells[] = $lastcell;
            $lastcell = new \html_table_cell('');
            $tr->cells[] = $lastcell;
            $lastcell = new \html_table_cell('');
            $tr->cells[] = $lastcell;
            if ($categorygrades != -1) {
                $lastcell = new \html_table_cell($categorygrades);
            } else {
                $lastcell = new \html_table_cell('-');
            }
            $tr->cells[] = $lastcell;
            $lastcell = new \html_table_cell('');
            $tr->cells[] = $lastcell;
            $table->data[] = $tr;
            $html = html_writer::table($table);
            $response['table'] = $html;
            $response['student'] = '';
        }
    }
    echo json_encode($response);
}