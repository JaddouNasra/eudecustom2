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
 * Moodle custom renderer class for tpv_ko and tpv_ok views.
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
class eudetpvmessages_renderer extends \plugin_renderer_base {

    /**
     * Render the error in payment page.
     *
     * @return string html to output.
     */
    public function eude_tpv_ko_page() {
        $response = '';
        $response .= $this->header();

        $html = html_writer::start_div('row');
        $html .= html_writer::start_div('col-md-12');
        $html .= html_writer::tag('div', get_string('paymenterror_desc', 'local_eudecustom'),
                array('id' => 'paymenterror', 'name' => 'paymenterror', 'class' => 'alert alert-danger'));
        $html .= html_writer::start_tag('a', array('href' => 'eudeprofile.php'));
        $html .= html_writer::tag('button', get_string('return', 'local_eudecustom'), array('class' => 'btn btn-primary'));
        $html .= html_writer::end_div();
        $html .= html_writer::end_div();

        $response .= $html;
        $response .= $this->footer();
        return $response;
    }

    /**
     * Render the payment correct page.
     *
     * @param array $data all the data related to this view.
     * @return string html to output.
     */
    public function eude_tpv_ok_page($data) {
        global $CFG;

        $response = '';
        $response .= $this->header();

        $html = html_writer::start_div('row');
        $html .= html_writer::start_div('col-md-12');
        $html .= html_writer::tag('p', get_string('paymentcomplete_desc', 'local_eudecustom'));
        $html .= html_writer::tag('p', get_string('student', 'local_eudecustom') .': '. $data->user);
        $html .= html_writer::tag('p', get_string('module', 'local_eudecustom') .': '. $data->module);
        if ($data->module == 'ok') {
            $html .= html_writer::tag('p',
                format_string(get_string('price', 'local_eudecustom') . $CFG->local_eudecustom_intensivemoduleprice . ' €'));
        }
        $html .= html_writer::start_tag('a', array('href' => 'eudeprofile.php'));
        $html .= html_writer::tag('button', get_string('return', 'local_eudecustom'), array('class' => 'btn btn-primary'));
        $html .= html_writer::end_div();
        $html .= html_writer::end_div();

        $response .= $html;
        $response .= $this->footer();
        return $response;
    }
}
