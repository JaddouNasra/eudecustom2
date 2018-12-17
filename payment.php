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
 * Moodle custom url with a form with the data required for the payment gallery.
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
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->libdir . '/pagelib.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir.'/formslib.php');
require_once(__DIR__ . "/classes/models/local_eudecustom_payment_form.class.php");

require_login(null, false, null, false, true);

global $USER;
global $OUTPUT;
global $CFG;
global $SESSION;

// Set up the page.
$title = get_string('headgrades', 'local_eudecustom');
$pagetitle = $title;
$url = new moodle_url("/local/eudecustom/payment.php");

$PAGE->set_context(context_system::instance());
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('standard');

$PAGE->requires->jquery();
$PAGE->requires->js_call_amd("local_eudecustom/eude", "payment");

// Load the renderer of the page.
$output = $PAGE->get_renderer('local_eudecustom', 'eudeprofile');
$SESSION->user = $USER->id;
$SESSION->course = optional_param('course', 0, PARAM_INT);
$SESSION->date = optional_param('letpv_date', 0, PARAM_INT);
if (optional_param('amount', 1, PARAM_FLOAT) == 0) {
    $mycourse = $DB->get_record('course', array('id' => optional_param('course', 0, PARAM_INT)));
    $namecourse = explode('[', $mycourse->shortname);
    if (isset($namecourse[0])) {
        $idname = explode('.M.', $namecourse[0]);
    } else {
        $idname = explode('.M.', $namecourse);
    }
    if (isset($idname[1])) {
        $mi = $DB->get_record('course', array('shortname' => 'MI.' . $idname[1]));
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
                $newdate = 0;
                break;
        }
        // Timeend is timestart + a week in seconds.
        $userid = $USER->id;
        if (is_siteadmin($USER->id)) {
            $userid = optional_param('user', 0, PARAM_INT);
        }
        enrol_intensive_user('manual', $mi->id, $userid, $newdate, $newdate + 604800, $convnum, $mycourse->category);
        $name = $USER->firstname . ' ' . $USER->lastname;
        $module = $mi->shortname . ' - (' . $mi->fullname . ')';
        $url = new moodle_url($CFG->wwwroot.'/local/eudecustom/tpv_ok.php',
                array('name' => $name, 'module' => $module));
            redirect($url);
    }
} else {
    if ($SESSION->course) {
        include("api/apiRedsys.php");
        $miobj = new RedsysAPI;
        $amount = optional_param('amount', 1, PARAM_FLOAT) * 100;
        $urltpv = $CFG->local_eudecustom_tpv_url_tpvv;
        $version = $CFG->local_eudecustom_tpv_version;
        $clave = $CFG->local_eudecustom_tpv_clave;
        $name = $CFG->local_eudecustom_tpv_name;
        $code = $CFG->local_eudecustom_tpv_code;
        $terminal = $CFG->local_eudecustom_tpv_terminal;
                $order_part1 = str_pad($USER->id, 4, '0', STR_PAD_LEFT);
                $order_part2 = str_pad("_", 12 - (strlen($order_part1)), '' . mt_rand(0, 9999999), STR_PAD_RIGHT);
                $order = substr($order_part1 . $order_part2, 0, 12);
        $currency = '978';
        $consumerlng = '001';
        $transactiontype = '0';
        $urlmerchant = $CFG->wwwroot . '/index.php';
        $urlwebok = $CFG->wwwroot . '/local/eudecustom/tpv_validation.php';
        $urlwebko = $CFG->wwwroot . '/local/eudecustom/tpv_ko.php';
        $miobj->setParameter("DS_MERCHANT_AMOUNT", $amount);
        $miobj->setParameter("DS_MERCHANT_CURRENCY", $currency);
        $miobj->setParameter("DS_MERCHANT_ORDER", $order);
        $miobj->setParameter("DS_MERCHANT_MERCHANTCODE", $code);
        $miobj->setParameter("DS_MERCHANT_TERMINAL", $terminal);
        $miobj->setParameter("DS_MERCHANT_TRANSACTIONTYPE", $transactiontype);
        $miobj->setParameter("DS_MERCHANT_MERCHANTURL", $urlmerchant);
        $miobj->setParameter("DS_MERCHANT_URLOK", $urlwebok);
        $miobj->setParameter("DS_MERCHANT_URLKO", $urlwebko);
        $miobj->setParameter("DS_MERCHANT_MERCHANTNAME", $name);
        $miobj->setParameter("DS_MERCHANT_CONSUMERLANGUAGE", $consumerlng);
        $params = $miobj->createMerchantParameters();
        $signature = $miobj->createMerchantSignature($clave);
        $userid = (integer)$SESSION->user;
        $course = (integer)$SESSION->course;
        $SESSION->tpv = true;
        $mycourse = $DB->get_record('course', array('id' => $course));
        $idname = explode('.', $mycourse->shortname);
        $mi = $DB->get_record('course', array('shortname' => 'MI.'.$idname[1]));
    }

    $mform = new local_eudecustom_payment_form($urltpv, array('user' => $userid, 'course' => $course ));

    // Call the functions of the renderar that prints the content.
    echo $output->header();

    // Form processing and displaying is done here.
    if ($fromform = $mform->get_data()) {
        // In this case you process validated data. $mform->get_data() returns data posted in form.
        $data = new stdClass();
        $data->user = $fromform->user;
        $data->course = $fromform->course;
        $data->Ds_SignatureVersion = $fromform->Ds_SignatureVersion;
        $data->Ds_MerchantParameters = $fromform->Ds_MerchantParameters;

    } else {
        // Displays the form.
        $mform->display();
    }

    echo $output->footer();
}
