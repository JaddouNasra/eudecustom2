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
 * This php defines a class with data for eudeprofile_renderer.
 *
 * @package    local_eudecustom
 * @copyright  2017 Planificacion de Entornos Tecnologicos SL
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Class with data for eudeprofile_renderer.
 *
 * @package    local_eudecustom
 * @copyright  2017 Planificacion de Entornos Tecnologicos SL
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_eudecustom_eudeprofile {

    /** @var string with the shortname of a course. */
    public $name;

    /** @var string with the fullname of a course. */
    public $desc;

    /** @var string with the category of a course. */
    public $cat;

    /** @var int with the grades for the course. */
    public $grades;

    /** @var int with the grades for the intensive course matching the normal course. */
    public $gradesint;

    /** @var string to determine the content to display in a field (usually ('insideweek',
     * 'outweek', 'notenroled')) */
    public $action;

    /** @var string with the text to display depending of $this->action. */
    public $actiontitle;

    /** @var string for the id to display depending of $this->action. */
    public $actionid;

    /** @var string for the class to display depending of $this->action. */
    public $actionclass;

    /**
     * Constructor.
     *
     * @param string $name shortname of a course
     * @param string $cat category of a course
     * @param int $grades final grade of the course for the current user
     * @param int $gradesint final grade of the intensive module course for the current user
     */
    public function __construct($name = '', $cat = '', $grades = 0, $gradesint = 0) {
        $this->name = $name;
        $this->cat = $cat;
        $this->grades = $grades;
        $this->gradesint = $gradesint;
    }

}
