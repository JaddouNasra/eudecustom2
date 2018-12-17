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
 * This php defines a class with a form for the payment gallery.
 *
 * @package    local_eudecustom
 * @copyright  2017 Planificacion de Entornos Tecnologicos SL
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Form to hidden data to Payment method.
 *
 * @package    local_eudecustom
 * @copyright  2017 Planificacion de Entornos Tecnologicos SL
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_eudecustom_payment_form extends moodleform {
    // Add elements to form.
    /**
     * Form Definition.
     */
    public function definition() {
        global $version;
        global $params;
        global $signature;
        global $SESSION;
        $mform = $this->_form;

        $mform->addElement('hidden', 'user', $SESSION->user);
        $mform->setType('user', PARAM_INT);
        $mform->addElement('hidden', 'course', $SESSION->course);
        $mform->setType('course', PARAM_INT);
        $mform->addElement('hidden', 'Ds_SignatureVersion', $version);
        $mform->setType('Ds_SignatureVersion', PARAM_NOTAGS);
        $mform->addElement('hidden', 'Ds_MerchantParameters', $params);
        $mform->setType('Ds_MerchantParameters', PARAM_NOTAGS);
        $mform->addElement('hidden', 'Ds_Signature', $signature);
        $mform->setType('Ds_Signature', PARAM_NOTAGS);
        $this->add_action_buttons(true, get_string('confirmpayment', 'local_eudecustom'));
        $mform->closeHeaderBefore('buttonar');
    }
}
