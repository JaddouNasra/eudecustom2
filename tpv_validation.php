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
 * Moodle request php to validate payment.
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

require_once($CFG->dirroot . '/lib/weblib.php');
require_once(__DIR__ . '/utils.php');
require_once(__DIR__ . '/api/apiRedsys.php');

require_login(null, false, null, false, true);

global $USER;
global $DB;
global $CFG;
global $SESSION;

if (!confirm_sesskey(sesskey())) {
    print_error('Bad Session Key');
} else {
    $miobj = new RedsysAPI;
    $version = optional_param('Ds_SignatureVersion', 0, PARAM_TEXT);
    $params = optional_param('Ds_MerchantParameters', 0, PARAM_TEXT);
    $sigaturerecibida = optional_param('Ds_Signature', 0, PARAM_TEXT);
    $course = $SESSION->course;
    $SESSION->module = $SESSION->course;
    $decodec = $miobj->decodeMerchantParameters($params);
    $codigorespuesta = $miobj->getParameter('Ds_Response');
    if ($course && $SESSION->tpv) {
        $clavemoduloadmin = $CFG->local_eudecustom_tpv_clave;
        $signaturecalculada = $miobj->createMerchantSignatureNotif($clavemoduloadmin, $params);
        $SESSION->tpv = false;
    }
    if ($signaturecalculada === $sigaturerecibida) {
        $mycourse = $DB->get_record('course', array('id' => $course));
        $namecourse = explode('[', $mycourse->shortname);
        if (isset($namecourse[0])) {
            $nameid = explode('.M.', $namecourse[0]);
        } else {
            $nameid = explode('.M.', $namecourse);
        }
        if (isset($nameid[1])) {
            $mi = $DB->get_record('course', array('shortname' => 'MI.' . $nameid[1]));
            $convnum = $SESSION->date;
            $alldates = $DB->get_record('local_eudecustom_call_date', array('courseid' => $mi->id));
            switch ($convnum) {
                case 1:
                    $newdate = $alldates->fecha1;
                    break;
                case 2:
                    $newdate = $alldates->fecha2;
                    break;
                case 3:
                    $newdate = $alldates->fecha3;
                    break;
                case 4:
                    $newdate = $alldates->fecha4;
                    break;
                default:
                    break;
            }
            // Timeend is timestart + a week in seconds.
            enrol_intensive_user('manual', $mi->id, $USER->id, $newdate, $newdate + 604800, $convnum, $mycourse->category);
            $name = $USER->firstname . ' ' . $USER->lastname;
            $module = $mi->shortname . ' - (' . $mi->fullname . ')';
            $url = new moodle_url($CFG->wwwroot.'/local/eudecustom/tpv_ok.php',
                array('name' => $name, 'module' => $module, 'payment' => 'ok'));
            redirect($url);
        } else {
            $url = new moodle_url($CFG->wwwroot . '/local/eudecustom/tpv_ko_message.php', array());
            redirect($url);
        }
    } else {
        $url = new moodle_url($CFG->wwwroot . '/local/eudecustom/tpv_ko_message.php', array());
        redirect($url);
    }
}


