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
 * Moodle custom renderer class for teacher control panel view.
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
class eudeteachercontrolpanel_renderer extends \plugin_renderer_base {

    /**
     * Render the no permissions page.
     * @return string html to output.
     */
    public function eude_nopermission_page() {
        $response = '';
        $response .= $this->header();

        $html = html_writer::start_div('row');
        $html .= html_writer::start_div('col-md-12');
        $html .= html_writer::tag('div', get_string('nopermissiontoshow',
                'error'), array('id' => 'nopermissions', 'name' => 'nopermissions', 'class' => 'alert alert-danger'));
        $html .= html_writer::end_div();
        $html .= html_writer::end_div();

        $response .= $html;
        $response .= $this->footer();
        return $response;
    }

    /** Render repeated list of direct access.
     * @param object $course
     * @return string $html html to output.
     */
    public function eude_print_course_list($course) {

        // Define strings.
        $strnotices = get_string('notices', 'local_eudecustom');
        $strforums = get_string('forums', 'local_eudecustom');
        $strassigns = get_string('assigns', 'local_eudecustom');
        $straccess = get_string('access', 'local_eudecustom');

        // Count forums and assigns.

        $fcount = $strforums . ' (' . count($course->forums) . ')';
        $ascount = $strassigns . ' (' . count($course->assigns) . ')';

        $html = html_writer::start_tag('li', array('class' => 'panelitem'));
        $html .= html_writer::start_div('itemname');
        $html .= html_writer::start_tag('a', array('class' => 'enterlink',
                                                'href' => "../../course/view.php?id=$course->id",
                                                'title' => "$course->fullname"));
        $html .= html_writer::span($course->shortname);
        $html .= html_writer::end_tag('a');
        $html .= html_writer::end_div();
        // Select.
        $html .= html_writer::start_div('itemselect col-md-3');
        $html .= html_writer::start_tag('select', array('class' => 'linkselect', 'id' => 'prevlinkselect',
                                                        'course' => $course->id, 'notice' => $course->notices->id));
        $html .= html_writer::tag('option', $straccess, array('value' => 0));
        $html .= html_writer::tag('option', $strnotices, array('value' => 1));
        $html .= html_writer::tag('option', $fcount, array('value' => 2));
        $html .= html_writer::tag('option', $ascount, array('value' => 3));
        $html .= html_writer::end_tag('select');
        $html .= html_writer::end_div();
        // End select.
        $html .= html_writer::end_tag('li');

        return $html;
    }

    /**
     * Render the direct access of the control panel.
     * @param array $data all the data related to this view.
     * @return string html to output.
     */
    public function eude_teachercontrolpanel_page($data) {
        global $USER;

        $response = '';
        $response .= $this->header();
        $html = html_writer::start_div('row eude_panel_bg'); // Start content row.
        $html .= html_writer::start_div('col-md-12'); // Start main col.
        $html .= html_writer::tag('h3', format_string($USER->firstname . " " . $USER->lastname)); // Teacher name.

        // First row.
        $html .= html_writer::start_div('row'); // First table row.
        $mytitle = get_string('mycourses', 'local_eudecustom');
        $html .= html_writer::start_div('col-md-6 itemlist'); // First col.
        $html .= html_writer::start_div('row');
        $html .= html_writer::tag('h4', $mytitle); // Col title.
        $html .= html_writer::end_div();
        // My courses.
        $html .= html_writer::start_div('row row-panel-list');
        foreach ($data->categories as $key => $value) {
            $html .= html_writer::start_tag('li', array('class' => 'panelitem'));
            $html .= html_writer::start_div('itemname');
            $html .= html_writer::start_tag('a', array('class' => 'enterlink',
                                                    'href' => "../../course/index.php?categoryid=$key",
                                                    'title' => "$value"));
            $html .= html_writer::span($value);
            $html .= html_writer::end_tag('a');
            $html .= html_writer::end_div();
            $html .= html_writer::end_tag('li');
        }
        $html .= html_writer::end_div();
        // End My courses.
        $html .= html_writer::end_div(); // End First col.
        $prevtitle = get_string('prevcourses', 'local_eudecustom');
        $html .= html_writer::start_div('col-md-6 itemlist'); // Second col.
        $html .= html_writer::start_div('row');
        $html .= html_writer::tag('h4', $prevtitle); // Col title.
        $html .= html_writer::end_div();
        // Previous Courses.
        $html .= html_writer::start_div('row row-panel-list');
        foreach ($data->courses as $key => $value) {
            foreach ($value as $course) {
                if (isset($course->id) && $key == 'prev') {
                    $print = $this->eude_print_course_list($course);
                    $html .= $print;
                }
            }
        }
        $html .= html_writer::end_div();
        // End Previous modules.
        $html .= html_writer::end_div(); // End Second col.
        $html .= html_writer::end_div(); // End first table row.
        // End first row.

        // Second row.
        $html .= html_writer::start_div('row'); // Second table row.
        $actualtitle = get_string('actualcourses', 'local_eudecustom');
        $html .= html_writer::start_div('col-md-6 itemlist'); // First col.
        $html .= html_writer::start_div('row');
        $html .= html_writer::tag('h4', $actualtitle); // Col title.
        $html .= html_writer::end_div();
        // Actual modules'.
        $html .= html_writer::start_div('row row-panel-list');
        foreach ($data->courses as $key => $value) {
            foreach ($value as $course) {
                if (isset($course->id) && $key == 'actual') {
                    $print = $this->eude_print_course_list($course);
                    $html .= $print;
                }
            }
        }
        $html .= html_writer::end_div();
        // End actual modules.
        $html .= html_writer::end_div(); // End First col.
        $nexttitle = get_string('nextcourses', 'local_eudecustom');
        $html .= html_writer::start_div('col-md-6 itemlist'); // Second col.
        $html .= html_writer::start_div('row');
        $html .= html_writer::tag('h4', $nexttitle); // Col title.
        $html .= html_writer::end_div();
        // Incoming modules.
        $html .= html_writer::start_div('row row-panel-list');
        foreach ($data->courses as $key => $value) {
            foreach ($value as $course) {
                if (isset($course->id) && $key == 'next') {
                    $print = $this->eude_print_course_list($course);
                    $html .= $print;
                }
            }
        }
        $html .= html_writer::end_div();
        // End incoming modules.
        $html .= html_writer::end_div(); // End Second col.
        $html .= html_writer::end_div(); // End Second table row.
        // End Second row.

        $html .= html_writer::end_div(); // End main col.
        $html .= html_writer::end_div(); // End content row.

        $response .= $html;
        $response .= $this->footer();

        return $response;
    }
}
