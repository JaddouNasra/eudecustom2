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
 * Moodle custom renderer class for eudemessages view.
 *
 * @package    local_eudecustom
 * @copyright  2017 Planificacion de Entornos Tecnologicos SL
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_eudecustom\output;

defined('MOODLE_INTERNAL') || die;

use \html_writer;
use renderable;

/**
 * Renderer for eude custom actions plugin.
 *
 * @copyright  2017 Planificacion de Entornos Tecnologicos SL
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class eudemessages_renderer extends \plugin_renderer_base {

    /**
     * Render the messages custom page for eude.
     *
     * @param array $data all the data related to this view.
     * @param string $sesskey key for this session.
     * @return string html to output.
     */
    public function eude_messages_page($data, $sesskey) {
        global $CFG;
        global $OUTPUT;
        global $USER;
        $response = '';
        $response .= $this->header();

        $html = html_writer::start_div('row');
        $html .= html_writer::start_div('col-md-12');
        $html .= html_writer::start_div('row');

            // Search button.
            $html .= html_writer::start_div('col-md-4 col-md-offset-8');
            $html .= html_writer::link($CFG->wwwroot . '/message/index.php', get_string('searchusermsg', 'local_eudecustom'),
                array('id' => 'searchmessage', 'class' => 'btn btn-default messageslink'));
            $html .= html_writer::end_div();

        // User avatar section.
        $html .= html_writer::div($OUTPUT->user_picture($USER, array('size' => 50)), 'col-md-1');

        // Messages section.
        $html .= html_writer::start_div('col-md-10');
        // Header and sender.
        $html .= html_writer::div(get_string('headmessages', 'local_eudecustom'), 'col-md-12');
        $html .= html_writer::div($data->sender, 'col-md-12');

        // Form.
        $html .= html_writer::start_div('col-md-12');
        $html .= html_writer::start_tag('form',
                array('id' => 'form-eude-messages', 'name' => 'form-eude-messages', 'method' => 'post'));
        $html .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => $sesskey));

        // Dropdown menus.
        $html .= html_writer::start_div('col-md-12');
        $html .= html_writer::tag('label', get_string('labeldropdownmsg', 'local_eudecustom'),
                array('for' => 'categoryname'));
        $html .= html_writer::end_div();
        // Select for categories.
        $html .= html_writer::start_div('col-md-3');
        $html .= html_writer::select($data->categories, 'categoryname',
                array('id' => 'categoryname'), '-- ' . get_string('category', 'local_eudecustom') . ' --');
        $html .= html_writer::end_div();
        // Select for courses.
        $html .= html_writer::start_div('col-md-3');
        $html .= html_writer::select(array(), 'coursename',
                array('id' => 'coursename'), '-- ' . get_string('module', 'local_eudecustom') . ' --');
        $html .= html_writer::end_div();
        // Select for subjects.
        $html .= html_writer::start_div('col-md-3');
        $html .= html_writer::select($data->subjects, 'subjectname',
                array('id' => 'subjectname'), '-- ' . get_string('subject', 'local_eudecustom') . ' --');
        $html .= html_writer::end_div();
        // Select for receivers.
        $html .= html_writer::start_div('col-md-3');
        $html .= html_writer::select(array(), 'destinatarioname',
                array('id' => 'destinatarioname'), '-- ' . get_string('userto', 'local_eudecustom') . ' --');
        $html .= html_writer::end_div();

        // Textarea.
        $html .= html_writer::start_div('col-md-12');
        $html .= html_writer::tag('label', get_string('labeltextarea', 'local_eudecustom'), array('for' => 'messagetext'));
        $html .= html_writer::end_div();
        $html .= html_writer::start_div('col-md-12');
        $html .= html_writer::tag('textarea', '', array('id' => 'messagetext', 'name' => 'messagetext'));
        $html .= html_writer::end_div();

        // Send button.
        $html .= html_writer::start_div('col-md-4');
        $html .= html_writer::nonempty_tag('button', get_string('sendmsg', 'local_eudecustom'), array('type' => 'submit',
            'id' => 'sendmessage', 'name' => 'sendmessage', 'class' => 'btn btn-default', 'value' => 'Enviar'));
        $html .= html_writer::end_div();

        // End of form.
        $html .= html_writer::end_tag('form');
        $html .= html_writer::end_div();
        $html .= html_writer::end_div();
        $html .= html_writer::div('', 'col-md-1');
        $html .= html_writer::end_div();
        $html .= html_writer::end_div();
        $html .= html_writer::end_div();

        $response .= $html;
        $response .= $this->footer();
        return $response;
    }

}
