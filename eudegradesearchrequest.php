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

require_login(null, false, null, false, true);

global $DB;
global $USER;


if (optional_param('cat', 0, PARAM_INT)) {
    $category = optional_param('cat', 0, PARAM_INT);
    $data1 = get_shortname_courses_by_category($USER->id, 'editingteacher', $category);
    $data2 = get_shortname_courses_by_category($USER->id, 'teacher', $category);
    $data3 = get_shortname_courses_by_category($USER->id, 'manager', $category);
    $data = array_merge($data1, $data2, $data3);

    $response = '';
    foreach ($data as $option) {
        $response .= '<option value=' . $option->id . '>' . $option->shortname . '</option>';
    }
    echo json_encode($response);
}

if (optional_param('course', 0, PARAM_INT)) {
    $courseid = optional_param('course', 0, PARAM_INT);
    $data = get_course_students($courseid, 'student');
    $response = '';
    foreach ($data as $option) {
        $response .= '<option value=' . $option->id . '>' . $option->lastname . ', ' . $option->firstname . '</option>';
    }
    echo json_encode($response);
}