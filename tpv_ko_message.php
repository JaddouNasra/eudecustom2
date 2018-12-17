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
 * Moodle custom url with a message of payment denied.
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

require_once($CFG->dirroot . '/local/eudecustom/utils.php');
require_once(__DIR__ . "/classes/models/local_eudecustom_eudetpvmessages.class.php");

require_login(null, false, null, false, true);

// Set up the page.
$title = get_string('paymenterror', 'local_eudecustom');
$pagetitle = $title;
$url = new moodle_url("/local/eudecustom/tpv_ko.php");

$PAGE->set_context(context_system::instance());
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('standard');

// Load the renderer of the page.
$output = $PAGE->get_renderer('local_eudecustom', 'eudetpvmessages');

// Call the functions of the renderar that prints the content.
echo $output->eude_tpv_ko_page();
