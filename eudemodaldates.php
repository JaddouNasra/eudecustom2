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
 * Moodle modal window with call dates for payment gallery.
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
require_once($CFG->libdir.'/filelib.php');

require_login(null, false, null, false, true);

$PAGE->set_context(context_system::instance());

$PAGE->requires->jquery();

$html = html_writer::start_div('', array('id' => 'contenedor'));
$html .= html_writer::tag('button', 'x', array('class' => 'letpv_cerrar'));
$courseid = optional_param('idcourse', 0, PARAM_INT);
$course = $DB->get_record('course', array('id' => $courseid));
$idname = explode('.M.', $course->shortname);
$namecourse = explode('[', $course->shortname);
if (isset($namecourse[0])) {
    $idname = explode('.M.', $namecourse[0]);
} else {
    $idname = explode('.M.', $namecourse);
}
$modulo = $DB->get_record('course', array('shortname' => 'MI.'.$idname[1]));

$fechas = $DB->get_record('local_eudecustom_call_date', array('courseid' => $modulo->id));

$html .= html_writer::start_tag('form',
            array('id' => 'fechas', 'name' => 'fechas', 'method' => 'post', 'action' => 'payment.php'));
$html .= html_writer::start_div('', array('id' => 'contenido'));
$html .= html_writer::tag('h3', get_string('selectcalldate', 'local_eudecustom'));
$html .= html_writer::start_div('form-group');
$html .= html_writer::tag('label', get_string('startingcalldate', 'local_eudecustom'));
$sql = "SELECT *
          FROM {local_eudecustom_call_date} f
          JOIN {course} c
         WHERE f.courseid = c.id
               AND c.category = :category
      ORDER BY fecha1 ASC
               LIMIT 1";
$startconv = $DB->get_record_sql($sql, array('category' => $modulo->category));
$today = time();
$weekinseconds = 604800;
$editable = $today + $weekinseconds;
if ($editable < $startconv->fecha1) {
    $options[1] = date("d/m/o", $fechas->fecha1);
}
if ($editable < $startconv->fecha2) {
    $options[2] = date("d/m/o", $fechas->fecha2);
}
if ($editable < $startconv->fecha3) {
    $options[3] = date("d/m/o", $fechas->fecha3);
}
if ($editable < $startconv->fecha4) {
    $options[4] = date("d/m/o", $fechas->fecha4);
}
if ($options) {
    $enrol = $DB->get_record('enrol', array('courseid' => $modulo->id, 'enrol' => 'manual'));
    if ($DB->get_record('user_enrolments', array('enrolid' => $enrol->id, 'userid' => $USER->id))) {
        $enrol = $DB->get_record('enrol', array('courseid' => $modulo->id, 'enrol' => 'manual'));
    } else {
        $enrol = $DB->get_record('enrol', array('courseid' => $modulo->id, 'enrol' => 'self'));
    }
    if ($DB->get_record('user_enrolments', array('enrolid' => $enrol->id, 'userid' => $USER->id))) {
        $actual = $DB->get_record('user_enrolments', array('enrolid' => $enrol->id, 'userid' => $USER->id));
        $html .= html_writer::select($options, 'letpv_date', array(
                                                    'id' => 'fechas',
                                                    'form' => 'fechas'),
                                                    '');
    } else {
        $html .= html_writer::select($options, 'letpv_date', array(
                                                    'id' => 'fechas',
                                                    'form' => 'fechas'),
                                                    '');
    }
    $studentid = null;
    if (optional_param('studentid', 0, PARAM_INT)) {
        $studentid = optional_param('studentid', 0, PARAM_INT);
    }
    // Print hidden inputs.
    $html = add_tpv_hidden_inputs($html, $studentid);

    $html .= html_writer::end_tag('form');
    $html .= html_writer::end_div();
} else {
    $html .= html_writer::tag('h3', get_string('nocalldates', 'local_eudecustom'));
    $html .= html_writer::tag('p', get_string('nocallcontinue', 'local_eudecustom'));
}
$html .= html_writer::end_div();

echo $html;
