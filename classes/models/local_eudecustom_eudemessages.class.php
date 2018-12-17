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
 * This php defines a class with data for eudemessages_renderer.
 *
 * @package    local_eudecustom
 * @copyright  2017 Planificacion de Entornos Tecnologicos SL
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class with data for eudemessages_renderer.
 *
 * @package    local_eudecustom
 * @copyright  2017 Planificacion de Entornos Tecnologicos SL
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_eudecustom_eudemessages {
    /** @var array asociative array with structure id=>name of categories. */
    public $categories;
    /** @var array asociative array with structure id=>name of courses. */
    public $courses;
    /** @var array asociative array with structure value=>description of subjects. */
    public $subjects;
    /** @var array asociative array with structure value=>name of receivers. */
    public $receivers;
    /** @var string string with the sender name and email. */
    public $sender;

    /**
     * Constructor.
     *
     * @param array $categories asociative array with structure id=>name of categories
     * @param array $courses asociative array with structure id=>name of courses
     * @param array $subjects asociative array with structure value=>description of subjects
     * @param array $receivers asociative array with structure value=>name of receivers
     * @param string $sender string with the sender name and email
     */
    public function __construct($categories, $courses = array(), $subjects = array(), $receivers = array(), $sender = '') {
        $this->categories = $categories;
        $this->courses = $courses;
        $this->subjects = $subjects;
        $this->subjects = $receivers;
        $this->sender = $sender;
    }
}