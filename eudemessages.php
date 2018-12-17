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
require_once(__DIR__ . '/../../config.php');

// Restrict access if the plugin is not active.
if (is_callable('mr_off') && mr_off('eudecustom', '_MR_LOCAL')) {
    die("Plugin not enabled.");
}

require_once($CFG->dirroot . '/message/lib.php');
require_once($CFG->libdir . '/messagelib.php');
require_once($CFG->libdir . '/pagelib.php');
require_once(__DIR__ . '/utils.php');
require_once(__DIR__ . "/classes/models/local_eudecustom_eudemessages.class.php");

require_login(null, false, null, false, true);

global $USER;
global $OUTPUT;
global $CFG;

// Check to redirect to the proper message view.
if (optional_param('searchmessage', null, PARAM_TEXT) == 'searchmessage') {

    if (!confirm_sesskey()) {
        print_error('Bad Session Key');
    } else {
        // If the search parameters return an existing user -> redirect to a conversation.
        if ($record = $DB->record_exists('user', array(
            'firstname' => optional_param('searchmessageuserfirstname', null, PARAM_TEXT),
            'lastname' => optional_param('searchmessageuserlastname', null, PARAM_TEXT)
                ))) {
            $record = $DB->get_record('user', array(
                'firstname' => optional_param('searchmessageuserfirstname', null, PARAM_TEXT),
                'lastname' => optional_param('searchmessageuserlastname', null, PARAM_TEXT)
            ));

            $url = new moodle_url($CFG->wwwroot . '/message/index.php', array('user2' => $record->id));
            redirect($url);
        } else {
            // If the search parameters dont return an existing user -> a notification appears.
            $notification = get_string('usernotfound', 'local_eudecustom') . ' ' .
                    optional_param('searchmessageuserfirstname', null, PARAM_TEXT) . ' ' .
                    optional_param('searchmessageuserlastname', null, PARAM_TEXT);
            redirect($CFG->wwwroot . '/local/eudecustom/eudemessages.php', $notification,
                    null, \core\output\notification::NOTIFY_ERROR);
        }
    }
}
// Set up the page.
$title = get_string('headmessages', 'local_eudecustom');
$pagetitle = $title;
$url = new moodle_url("/local/eudecustom/eudemessages.php");

$PAGE->set_context(context_system::instance());
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('standard');

$PAGE->requires->jquery();
$PAGE->requires->js_call_amd("local_eudecustom/eude", "message");
$PAGE->requires->js_call_amd("local_eudecustom/eude", "menu");
$PAGE->requires->css("/local/eudecustom/style/eudecustom_style.css");

$output = $PAGE->get_renderer('local_eudecustom', 'eudemessages');

// We send the message with the data from the form.
if (optional_param('sendmessage', null, PARAM_TEXT) == 'Enviar') {
    // If some of the requiered fields are not set -> a notification appears.
    if (!optional_param('coursename', null, PARAM_INT) || !optional_param('subjectname', null, PARAM_TEXT) ||
            !optional_param('destinatarioname', null, PARAM_TEXT) || !optional_param('messagetext', null, PARAM_TEXT)) {
        redirect($CFG->wwwroot . '/local/eudecustom/eudemessages.php',
                get_string('missingfields', 'local_eudecustom'), null, \core\output\notification::NOTIFY_ERROR);
    }
    $sitecontext = context_system::instance();
    // If tue user can not sned messages.
    if (!has_capability('moodle/site:sendmessage', $sitecontext)) {
        redirect($CFG->wwwroot . '/local/eudecustom/eudemessages.php',
                get_string('nocapabilitytosendmessages', 'local_eudecustom'), null, \core\output\notification::NOTIFY_ERROR);
    }

    // If all the fields are set -> we send both a message and an email to the receiver.
    if (!confirm_sesskey()) {
        print_error('Bad Session Key');
    } else {
        $userfrom = $DB->get_record('user', array('id' => $USER->id));
        $subject = 'Asunto: ' . format_string(optional_param('subjectname', null, PARAM_TEXT));
        $messagehtml = format_string(optional_param('messagetext', null, PARAM_TEXT));
        $message = $subject . '<br> ' . $messagehtml;
        $format = FORMAT_HTML;
        // If the chosen receiver is some type of students -> send a message to all the users with that role.
        $roletype = optional_param('destinatarioname', null, PARAM_TEXT);
        if ($roletype == 'student') {
            $role = $DB->get_record('role', array('shortname' => $roletype));
            $coursecontext = context_course::instance(optional_param('coursename', null, PARAM_INT));
            $students = get_role_users($role->id, $coursecontext);
            foreach ($students as $key => $value) {
                $userto = $DB->get_record('user', array('id' => $value->id));
                message_post_message($userfrom, $userto, $message, $format);
            }
        } else {
            $userto = $DB->get_record('user', array('id' => optional_param('destinatarioname', null, PARAM_INT)));
            message_post_message($userfrom, $userto, $message, $format);
        }
    }
}

$sesskey = sesskey();

$categories = array();
$categories = get_user_categories($USER->id, false);
$categories = array_flip($categories);

$subjects = get_samoo_subjects();

$sender = get_string('sender', 'local_eudecustom') . ": " . $USER->firstname . ' ' .
        $USER->lastname . ' (' . $USER->email . ')';

$data = new local_eudecustom_eudemessages($categories, array(), array(), array(), $sender);
$data->subjects = $subjects;

echo $output->eude_messages_page($data, $sesskey);
