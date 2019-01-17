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
 * Version details.
 *
 * @package local_eudecustom
 * @copyright  2017 Planificacion de Entornos Tecnologicos SL
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2018090400;        // The current plugin version (Date: YYYYMMDDXX).
$plugin->requires  = 2015051100;        // Requires this Moodle version.
$plugin->component = 'local_eudecustom'; // Full name of the plugin (used for diagnostics).
$plugin->maturity = MATURITY_STABLE; // This is considered as ready for production sites.
$plugin->release = 'v1.0.1'; // This is our first version.
$plugin->dependencies = array(
    'block_reports' => ANY_VERSION,   // The MoodleRoom block reports must be present (any version).
    'theme_snap' => ANY_VERSION // The theme SNAP must be present (any version).
);
