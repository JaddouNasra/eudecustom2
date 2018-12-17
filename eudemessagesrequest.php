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

require_login(null, false, null, false, true);

global $DB;

if (optional_param('messagecat', 0, PARAM_INT)) {
    $category = optional_param('messagecat', 0, PARAM_INT);


    $data = get_user_shortname_courses($USER->id, $category);
    $response = "";
    foreach ($data as $option) {
        $response .= html_writer::tag('option', $option->shortname, array('value' => $option->id));
    }
    echo json_encode($response);
}

if (optional_param('messagecourse', 0, PARAM_INT)) {
    $course = optional_param('messagecourse', 0, PARAM_INT);
    $coursecat = $DB->get_record('course', array('id' => $course));

     $sql = 'SELECT ra.userid, r.shortname, u.firstname, u.lastname
              FROM {role_assignments} ra
              JOIN {role} r ON r.id = ra.roleid
              JOIN {context} cxt ON cxt.id = ra.contextid
              JOIN {user} u ON u.id = ra.userid
             WHERE cxt.contextlevel = :context
               AND cxt.instanceid = :course
          ORDER BY r.shortname';

    $data = $DB->get_records_sql($sql, array(
        'context' => CONTEXT_COURSE,
        'course' => $course
    ));

    $students = false;
    $response = "";
    foreach ($data as $option) {
        switch ($option->shortname) {
            case 'student':
                if (!$students) {
                    $response .= html_writer::tag('option', get_string('student', 'local_eudecustom'), array('value' => 'student'));
                    $students = true;
                }
                break;
            case 'studentval':
                break;
            default:
                $response .= html_writer::tag('option',
                    get_string($option->shortname, 'local_eudecustom') . ": " . $option->firstname . " " . $option->lastname,
                    array('value' => $option->userid));
                break;
        }
    }
    if ($manager = get_role_manager($coursecat->category)) {
                $response .= html_writer::tag('option',
                    get_string('responsablemaster', 'local_eudecustom') . ": " . $manager->firstname . " " . $manager->lastname,
                    array('value' => $manager->id));
    }

    echo json_encode($response);
}