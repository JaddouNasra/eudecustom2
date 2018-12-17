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
 * Moodle custom renderer class for eudegradesearch view.
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
class eudegradesearch_renderer extends \plugin_renderer_base {

    /**
     * Render the no permissions page.
     * @return string html to output.
     */
    public function eude_nopermission_url() {
        $response = '';
        $response .= $this->header();

        $html = html_writer::start_div('row');
        $html .= html_writer::start_div('col-md-12');
        $html .= html_writer::tag('div', get_string('nopermissions',
                'local_eudecustom'), array('id' => 'nopermissions', 'name' => 'nopermissions', 'class' => 'alert alert-danger'));
        $html .= html_writer::end_div();
        $html .= html_writer::end_div();

        $response .= $html;
        $response .= $this->footer();
        return $response;
    }

    /**
     * Render the intensive modules matriculation dates custom page for eude.
     * @param array $data all the data related to this view.
     * @param string $sesskey string with the session key.
     * @return string html to output.
     */
    public function eude_gradesearch_page($data, $sesskey) {

        $response = '';
        $response .= $this->header();

        // Form section.
        $html = html_writer::start_div('row');
        $html .= html_writer::start_div('col-md-12');
        $html .= html_writer::tag('label', get_string('searchgradesmsg', 'local_eudecustom'),
                array('for' => 'form-eude-search-grades'));
        $html .= html_writer::start_tag('form',
                array('id' => 'form-eude-search-grades', 'name' => 'form-eude-search-grades', 'method' => 'post'));
        // Sesskey hidden field.
        $html .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => $sesskey));

        // Select for categories.
        $html .= html_writer::start_div('col-md-12');
        $html .= html_writer::start_div('col-md-2');
        $html .= html_writer::tag('label', get_string('category', 'local_eudecustom'), array('for' => 'categoryname'));
        $html .= html_writer::end_div();
        $html .= html_writer::start_div('col-md-4');
        $html .= html_writer::select($data->categories, 'categoryname',
                array('id' => 'categoryname'), '-- ' . get_string('category', 'local_eudecustom') . ' --');
        $html .= html_writer::end_div();
        $html .= html_writer::end_div();

        // Select for courses.
        $html .= html_writer::start_div('col-md-12');
        $html .= html_writer::start_div('col-md-2');
        $html .= html_writer::tag('label', get_string('module', 'local_eudecustom'), array('for' => 'coursename'));
        $html .= html_writer::end_div();
        $html .= html_writer::start_div('col-md-4');
        $html .= html_writer::select($data->courses, 'coursename',
                array('id' => 'coursename'), get_string('module', 'local_eudecustom'));
        $html .= html_writer::end_div();
        $html .= html_writer::end_div();

        // Select for students.
        $html .= html_writer::start_div('col-md-12');
        $html .= html_writer::start_div('col-md-2');
        $html .= html_writer::tag('label', get_string('student', 'local_eudecustom'), array('for' => 'studentname'));
        $html .= html_writer::end_div();
        $html .= html_writer::start_div('col-md-4');
        $html .= html_writer::select($data->students, 'studentname',
                array('id' => 'studentname'), get_string('student', 'local_eudecustom'));
        $html .= html_writer::end_div();
        $html .= html_writer::end_div();

        // Go to user grades button.
        $html .= html_writer::start_div('col-md-12');
        $html .= html_writer::start_div('col-md-6');
        $html .= html_writer::link('', get_string('buttongrades', 'local_eudecustom'),
                array('id' => 'usergrades', 'class' => 'btn btn-default'));
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
