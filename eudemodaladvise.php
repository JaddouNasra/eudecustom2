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
 * Moodle modal window with data neeeded for the payment gallery .
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
require_once($CFG->libdir . '/pagelib.php');
require_once($CFG->libdir . '/filelib.php');

require_login(null, false, null, false, true);

$PAGE->set_context(context_system::instance());

$PAGE->requires->jquery();

global $CFG;

$studentid = null;
if (optional_param('studentid', 0, PARAM_INT)) {
    $studentid = optional_param('studentid', 0, PARAM_INT);
}
$price = $CFG->local_eudecustom_intensivemoduleprice;
if (is_siteadmin($USER->id)) {
    $price = 0;
}
$html = html_writer::start_div('', array('id' => 'contenedor'));
$html .= html_writer::tag('button', 'x', array('class' => 'letpv_cerrar'));
$html .= html_writer::start_div('', array('id' => 'contenido'));
$html .= html_writer::tag('h3',
        get_string('pricenotify', 'local_eudecustom') . $price . ' €.');
$html .= html_writer::tag('p', get_string('continuewarning', 'local_eudecustom'));

$html .= html_writer::start_tag('p');
$html .= html_writer::tag('span', get_string('continuewarning2', 'local_eudecustom'), array('class' => 'eudecustomtpvwarning'));
$html .= html_writer::tag('a', get_string('eudeemail1', 'local_eudecustom'), array('href' => 'mailto:orientador1@eude.es'));
$html .= html_writer::tag('span', get_string('continuewarning3', 'local_eudecustom'), array('class' => 'eudecustomtpvwarning'));
$html .= html_writer::tag('a', get_string('eudeemail2', 'local_eudecustom'), array('href' => 'mailto:orientador2@eude.es'));
$html .= html_writer::end_tag('p');

$html .= html_writer::start_tag('form', array(
            'class' => 'form-amount',
            'method' => 'post'));
$html .= html_writer::start_div('form-group');

$html = add_tpv_hidden_inputs($html, $studentid);
$html .= html_writer::end_div();
$html .= html_writer::end_tag('form');
$html .= html_writer::end_div();

echo $html;

