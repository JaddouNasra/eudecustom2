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
 * This php defines a class with data for eudegradesearch_renderer.
 *
 * @package    local_eudecustom
 * @copyright  2017 Planificacion de Entornos Tecnologicos SL
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class with data for eudegradesearch_renderer.
 *
 * @package    local_eudecustom
 * @copyright  2017 Planificacion de Entornos Tecnologicos SL
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_eudecustom_eudegradesearch {
    /** @var array asociative array with structure id=>name of categories. */
    public $categories;
    /** @var array asociative array with structure id=>shortname of courses. */
    public $courses;
    /** @var array asociative array with structure id=>firstname of users. */
    public $students;

    /**
     * Constructor.
     *
     * @param array $categories asociative array with structure id=>name of categories
     * @param array $courses asociative array with structure id=>shortname of courses
     * @param array $students asociative array with structure id=>firstname of users
     */
    public function __construct($categories, $courses = array(), $students = array()) {
        $this->categories = $categories;
        $this->courses = $courses;
        $this->students = $students;
    }
}
