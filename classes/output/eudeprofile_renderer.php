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
 * Moodle custom renderer class for eudeprofile view.
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
class eudeprofile_renderer extends \plugin_renderer_base {

    /**
     * Render the error page if an user is not found or is deleted.
     * @param string $usererror description of the error to display.
     * @return string html to output.
     */
    public function eude_userproblem_page($usererror) {
        $response = '';
        $response .= $this->header();

        $html = html_writer::start_div('row');
        $html .= html_writer::start_div('col-md-12');
        $html .= html_writer::tag('div', $usererror,
                array('id' => 'userproblem', 'name' => 'userproblem', 'class' => 'alert alert-danger'));
        $html .= html_writer::end_div();
        $html .= html_writer::end_div();

        $response .= $html;
        $response .= $this->footer();
        return $response;
    }

    /**
     * Render the error page if an user is not found or is deleted.
     * @param string $mform description of the error to display.
     * @return string html to output.
     */
    public function eude_payment_intensives($mform) {
        $response = '';
        $response .= $this->header();

        $html = $mform->display();

        $response .= $html;
        $response .= $this->footer();
        return $response;
    }

    /**
     * Render the courses profile custom page for eude.
     * @param array $categories array with the categories of the courses this user is enroled.
     * @param array $edit array of eudecustom_profile objects.
     * @param string $selectcat string with category name.
     * @return string html to output.
     */
    public function eude_profile_intensives($categories, $edit = false, $selectcat = null) {
        global $CFG;
        $response = '';
        $response .= $this->header();
        if ($categories) {
            // Select for categories.
            $html = html_writer::tag('label', get_string('choosecategory', 'local_eudecustom'));
            $html .= html_writer::select($categories, 'categoryname',
                array('id' => 'categoryname'), '-- ' . get_string('category', 'local_eudecustom') . ' --');
            // Select for students.
            $html .= html_writer::start_tag('form',
                    array('id' => 'letpv_student', 'name' => 'letpv_student', 'method' => 'post', 'action' => 'eudeprofile.php'));
            $html .= html_writer::end_tag('form');
            if ($selectcat != null) {
                $html .= html_writer::tag('label', $selectcat, array('id' => 'categoryselect', 'style' => 'display:none'));
            }
            // Only Admin can access to edit dates.
            if ($edit == true) {
                // Link to editing dates.
                $html .= html_writer::link($CFG->wwwroot . '/local/eudecustom/eudeintensivemoduledates.php',
                get_string('editdates', 'local_eudecustom'),
                array('class' => 'btn btn-default pull-right'));
            }
            // Table to display courses and additional info.
            $html .= html_writer::start_div('', array('id' => 'letpv_tablecontainer'));
            $html .= html_writer::end_div();
            // Modal window to display additional content.
            $html .= html_writer::start_div('', array('id' => 'letpv_ventana-flotante'));
            $response .= $html;
        }

        $response .= $this->footer();

        return $response;
    }

    /**
     * Render the content of the actions field.
     * @param local_eudecustom_eudeprofile $data instance of the class local_eudecustom_eudeprofile.
     * @return string html to output.
     */
    public function render_action_field($data) {
        $response = '';

        $html = '';
        switch ($data->action) {
            case 'insideweek':
                $html .= html_writer::tag('span', format_string($data->actiontitle), array('class' => 'eudeprofilespan'));
                break;
            case 'outweek':
                $html .= html_writer::tag('span', $data->actiontitle, array('class' => 'eudeprofilespan'));
                $html .= html_writer::tag('i', '·', array('id' => $newd->actionid,
                                            'class' => 'fa fa-pencil-square-o ' . $newd->actionclass,
                                            'aria-hidden' => 'true'));
                break;
            case 'notenroled':
                $html .= html_writer::tag('button', format_string($data->actiontitle),
                        array('class' => $data->actionclass, 'id' => $data->actionid));
                break;
            default:
                break;
        }

        $response .= $html;
        return $response;
    }
}
