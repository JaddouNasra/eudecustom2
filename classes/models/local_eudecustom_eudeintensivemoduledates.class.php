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
 * This php defines a class with data for eudeintensivemoduledates_renderer.
 *
 * @package    local_eudecustom
 * @copyright  2017 Planificacion de Entornos Tecnologicos SL
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Class with data for eudeintensivemoduledates_renderer.
 *
 * @package    local_eudecustom
 * @copyright  2017 Planificacion de Entornos Tecnologicos SL
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_eudecustom_eudeintensivemoduledates {

    /** @var array asociative array with structure id=>shortname of courses. */
    public $courses;

    /** @var array asociative array with matriculation dates for intensive module. */
    public $intensivecoursedates;

    /**
     * Constructor.
     *
     * @param array $courses asociative array with structure id=>shortname of courses
     * @param array $intensivecoursedates asociative array with matriculation dates for intensive module.
     */
    public function __construct ($courses = null, $intensivecoursedates = null) {
        global $DB;
        if ($courses) {
            $this->courses = $courses;
        } else {
            $sql = "SELECT c.*
                      FROM {course} c
                     WHERE c.shortname LIKE 'MI.%'
                  ORDER BY c.startdate ASC";
            $records = $DB->get_records_sql($sql);
            $courses = array();
            foreach ($records as $record) {
                if ($DB->record_exists('local_eudecustom_call_date', array('courseid' => $record->id))) {
                    $data = $DB->get_record('local_eudecustom_call_date', array('courseid' => $record->id));
                    $data->fecha1 = date('d/m/Y', $data->fecha1);
                    $data->fecha2 = date('d/m/Y', $data->fecha2);
                    $data->fecha3 = date('d/m/Y', $data->fecha3);
                    $data->fecha4 = date('d/m/Y', $data->fecha4);
                } else {
                    $data = new stdClass();
                    $data->courseid = $record->id;
                    $data->fecha1 = null;
                    $data->fecha2 = null;
                    $data->fecha3 = null;
                    $data->fecha4 = null;
                }
                $data->shortname = $record->shortname;
                $courses[$record->id] = $data;
            }
            $this->courses = $courses;
        }
        $this->intensivecoursedates = $intensivecoursedates;
    }

}
