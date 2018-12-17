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
 * Moodle custom renderer class for eudeintegration view.
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
class eudeintegration_renderer extends \plugin_renderer_base {

    /**
     * Render the no permissions page.
     * @return string html to output.
     */
    public function eude_nopermission_page () {
        $response = '';
        $response .= $this->header();

        $html = html_writer::start_div('row');
        $html .= html_writer::start_div('col-md-12');
        $html .= html_writer::tag('div', get_string('nopermissiontoshow', 'error'),
                        array('id' => 'nopermissions', 'name' => 'nopermissions', 'class' => 'alert alert-danger'));
        $html .= html_writer::end_div();
        $html .= html_writer::end_div();

        $response .= $html;
        $response .= $this->footer();
        return $response;
    }

    /**
     * Render the intensive modules matriculation dates custom page for eude.
     *
     * @param string $sesskey key for this session.
     * @return string html to output.
     */
    public function eude_integration_page ($sesskey) {
        $response = '';
        $response .= $this->header();

        // Form section.
        $html = html_writer::start_div('row');
        $html .= html_writer::start_div('col-md-12');
        $html .= html_writer::tag('label', get_string('integrationmsg', 'local_eudecustom'),
                        array('for' => 'form-eude-integration-form'));
        $html .= html_writer::start_tag('form',
                        array('id' => 'form-eude-integration-form', 'name' => 'form-eude-integration-form', 'method' => 'get'));
        // Sesskey hidden field.
        $html .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => $sesskey));

        // Textarea.
        $html .= html_writer::start_div('col-md-12');
        $html .= html_writer::tag('label', get_string('labelintegrationtextarea', 'local_eudecustom'),
                        array('for' => 'integrationtext'));
        $html .= html_writer::start_div('col-md-12');
        $html .= html_writer::tag('textarea', '', array('id' => 'integrationtext', 'name' => 'integrationtext'));
        $html .= html_writer::end_div();
        // Process data from textarea button.
        $html .= html_writer::start_div('col-md-12');
        $html .= html_writer::nonempty_tag('button', get_string('processtextmsg', 'local_eudecustom'),
                        array('type' => 'submit',
                    'id' => 'processtext', 'name' => 'processtext', 'class' => 'btn btn-default', 'value' => 'processtext'));
        $html .= html_writer::end_div();
        $html .= html_writer::end_div();
        // End of form.
        $html .= html_writer::end_tag('form');
        $html .= html_writer::end_div();
        $html .= html_writer::end_div();

        $response .= $html;
        $response .= $this->footer();
        return $response;
    }
}
