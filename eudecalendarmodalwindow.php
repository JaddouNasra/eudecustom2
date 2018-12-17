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
require_once($CFG->libdir . '/filelib.php');


require_login(null, false, null, false, true);

$PAGE->set_context(context_system::instance());

$PAGE->requires->jquery();
$PAGE->requires->js_call_amd("local_eudecustom/eude", "calendar");

$html = html_writer::start_tag('div', array('id' => 'modalwrapper', 'class' => 'wrapper'));
$html .= html_writer::start_div('modalcontent');
$html .= html_writer::start_div('row');
// Close window button.
$html .= html_writer::start_div('buttonwrapper text-right');
$html .= html_writer::tag('button', 'x', array('id' => 'closemodalwindowbutton', 'class' => 'btn btn-danger'));
$html .= html_writer::end_div();
// The form starts here.
$html .= html_writer::start_tag('form',
                array('id' => 'form-print-events',
            'name' => 'form-print-events',
            'method' => 'post',
            'action' => 'eudeeventlist.php'));

// Section for the datepickers.
$html .= html_writer::start_div('col-md-6 text-center');
$html .= html_writer::tag('label', get_string('datefrom', 'local_eudecustom'), array('for' => 'categoryname'));
$html .= html_writer::empty_tag('input',
                array('type' => 'text', 'id' => 'startdatemodal',
            'class' => 'startdatemodal inputdate', 'name' => 'startdatemodal', 'placeholder' => 'dd/mm/aaaa'));
$html .= html_writer::end_tag('div');
$html .= html_writer::start_div('col-md-6 text-center');

$html .= html_writer::tag('label', get_string('dateuntil', 'local_eudecustom'), array('for' => 'categoryname'));
$html .= html_writer::empty_tag('input',
                array('type' => 'text', 'id' => 'enddatemodal',
            'class' => 'enddatemodal inputdate', 'name' => 'enddatemodal', 'placeholder' => 'dd/mm/aaaa'));
$html .= html_writer::end_tag('div');

// Section for the events key.
$html .= generate_event_keys('_modal');

// Generate event list button.
$html .= html_writer::start_div('col-md-12');
$html .= html_writer::nonempty_tag('button', get_string('generateeventlist', 'local_eudecustom'),
                array('type' => 'submit',
            'id' => 'generateeventlist', 'name' => 'generateeventlist', 'class' => 'btn btn-default', 'value' => 'Generate'));
$html .= html_writer::end_div();

$html .= html_writer::end_tag('form');

$html .= html_writer::end_div();
$html .= html_writer::end_div();
$html .= html_writer::end_div();

echo $html;
