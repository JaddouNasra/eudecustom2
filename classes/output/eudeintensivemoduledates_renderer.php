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
class eudeintensivemoduledates_renderer extends \plugin_renderer_base {

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
     * @param array $data all the data related to this view.
     * @param string $sesskey key for this session.
     * @return string html to output.
     */
    public function eude_intensivemoduledates_page ($data, $sesskey) {
        $response = '';
        $response .= $this->header();

        // Form section.
        $html = html_writer::start_div('row');
        $html .= html_writer::start_div('col-md-12');
        $html .= html_writer::tag('label', get_string('matriculationdatesmsg', 'local_eudecustom'),
                        array('for' => 'form-eude-select-category'));
        $html .= html_writer::start_tag('form',
                        array('id' => 'form-eude-select-category', 'name' => 'form-eude-select-category', 'method' => 'post'));
        // Sesskey hidden field.
        $html .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => $sesskey));
        // Datepickers area.
        $html .= html_writer::start_div('col-md-12', array('id' => 'contenido-fecha-matriculas'));
        $html .= html_writer::start_tag('table', array('class' => 'table table-striped'));
        // Table headers.
        $html .= html_writer::start_tag('thead');
        $html .= html_writer::start_tag('tr');
        // Table header for course shortname.
        $html .= html_writer::start_tag('th', array('class' => 'moduletitle'));
        $html .= html_writer::tag('span', get_string('module', 'local_eudecustom'));
        $html .= html_writer::end_tag('th');
        // Table headers for call dates.
        $html .= html_writer::start_tag('th', array('class' => 'calldatecell'));
        $html .= html_writer::tag('span', get_string('date1', 'local_eudecustom'));
        $html .= html_writer::end_tag('th');
        $html .= html_writer::start_tag('th', array('class' => 'calldatecell'));
        $html .= html_writer::tag('span', get_string('date2', 'local_eudecustom'));
        $html .= html_writer::end_tag('th');
        $html .= html_writer::start_tag('th', array('class' => 'calldatecell'));
        $html .= html_writer::tag('span', get_string('date3', 'local_eudecustom'));
        $html .= html_writer::end_tag('th');
        $html .= html_writer::start_tag('th', array('class' => 'calldatecell'));
        $html .= html_writer::tag('span', get_string('date4', 'local_eudecustom'));
        $html .= html_writer::end_tag('th');
        $html .= html_writer::end_tag('tr');
        $html .= html_writer::end_tag('thead');
        // Each row is a shortname in a label with a hidden input with the course id plus 4 datepickers.
        $html .= html_writer::start_tag('tbody');
        foreach ($data->courses as $coursedata) {
            $html .= html_writer::start_tag('tr', array('class' => 'coursedata'));
            // Column for the shortname label and the hidden input.
            $html .= html_writer::start_tag('td');
            $html .= html_writer::tag('span', format_string($coursedata->shortname),
                    array('class' => 'shortname', 'title' => format_string($coursedata->shortname)));
            $html .= html_writer::empty_tag('input',
                            array('type' => 'hidden',
                                  'id' => format_string($coursedata->shortname . '-shortname'),
                                  'class' => format_string($coursedata->shortname . '-shortname'),
                                  'name' => 'startdatemodal',
                                  'value' => format_string($coursedata->shortname),
                                  'readonly' => 'readonly')
                            );
            $html .= html_writer::end_tag('td');

            // Columns for the datepickers.
            $html .= html_writer::start_tag('td');
            $html .= html_writer::empty_tag('input',
                            array('type' => 'text',
                                  'id' => format_string('date1-' . $coursedata->courseid),
                                  'class' => 'date1 inputdate',
                                  'name' => format_string('date1-' . $coursedata->courseid),
                                  'placeholder' => 'dd/mm/aaaa',
                                  'value' => $coursedata->fecha1)
                            );
            $html .= html_writer::end_tag('td');
            $html .= html_writer::start_tag('td');
            $html .= html_writer::empty_tag('input',
                            array('type' => 'text',
                                  'id' => format_string('date2-' . $coursedata->courseid),
                                  'class' => 'date2 inputdate',
                                  'name' => format_string('date2-' . $coursedata->courseid),
                                  'placeholder' => 'dd/mm/aaaa',
                                  'value' => $coursedata->fecha2)
                            );
            $html .= html_writer::end_tag('td');
            $html .= html_writer::end_tag('td');
            $html .= html_writer::start_tag('td');
            $html .= html_writer::empty_tag('input',
                            array('type' => 'text',
                                  'id' => format_string('date3-' . $coursedata->courseid),
                                  'class' => 'date3 inputdate',
                                  'name' => format_string('date3-' . $coursedata->courseid),
                                  'placeholder' => 'dd/mm/aaaa',
                                  'value' => $coursedata->fecha3)
                            );
            $html .= html_writer::end_tag('td');
            $html .= html_writer::end_tag('td');
            $html .= html_writer::start_tag('td');
            $html .= html_writer::empty_tag('input',
                            array('type' => 'text',
                                  'id' => format_string('date4-' . $coursedata->courseid),
                                  'class' => 'date4 inputdate',
                                  'name' => format_string('date4-' . $coursedata->courseid),
                                  'placeholder' => 'dd/mm/aaaa',
                                  'value' => $coursedata->fecha4)
                            );
            $html .= html_writer::end_tag('td');

            $html .= html_writer::end_tag('tr');
        }

        $html .= html_writer::end_tag('tbody');
        $html .= html_writer::end_tag('table');
        $html .= html_writer::end_div();

        // Generate save dates button.
        $html .= html_writer::start_div('col-md-12');
        $html .= html_writer::nonempty_tag('button', get_string('savechanges', 'local_eudecustom'),
            array('type' => 'submit',
                    'id' => 'savedates',
                  'name' => 'savedates',
                 'class' => 'btn btn-default',
                 'value' => 'savedates'));

        // Generate reset button.
        $html .= html_writer::nonempty_tag('button', get_string('reset', 'local_eudecustom'),
            array('type' => 'submit',
                    'id' => 'resetfechas',
                  'name' => 'resetfechas',
                 'class' => 'btn btn-default',
                 'value' => 'resetfechas'));
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
