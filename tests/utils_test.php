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
 * COMPONENT External functions unit tests
 *
 * @package    local_eudecustom
 * @copyright  2017 Planificacion de Entornos Tecnologicos SL
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once(__DIR__. '/../utils.php');
require_once(__DIR__.'/../classes/models/local_eudecustom_eudeprofile.class.php');
/**
 * This class is used to run the unit tests
 *
 * @package    local_eudecustom
 * @copyright  2015 Planificacion de Entornos Tecnologicos SL
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_eudecustom_testcase extends advanced_testcase {

    /**
     * Enable the manual enrol plugin.
     *
     * @return bool $manualplugin Return true if is enabled.
     */
    public function enable_enrol_plugin () {
        $manualplugin = enrol_get_plugin('manual');
        return $manualplugin;
    }

    /**
     * Get Student object.
     *
     * @return stdClass $studentrole Object student role record.
     */
    public function get_student_role () {
        global $DB;
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        return $studentrole;
    }

    /**
     * Get Teacher object.
     *
     * @return stdClass $teacherrole Object teacher role record.
     */
    public function get_teacher_role () {
        global $DB;
        $teacherrole = $DB->get_record('role', array('shortname' => 'teacher'));
        return $teacherrole;
    }

    /**
     * Create manual instance to enrol in a course.
     * @param int $courseid Course id.
     *
     * @return stdClass $manualinstance Object type of enrol to be enrolled.
     */
    public function create_manual_instance ($courseid) {
        global $DB;
        $manualinstance = $DB->get_record('enrol', array('courseid' => $courseid, 'enrol' => 'manual'), '*', MUST_EXIST);
        return $manualinstance;
    }

    /**
     * Tests for phpunit.
     */
    public function test_get_categories_with_intensive_modules () {

        $this->resetAfterTest(true);

        // Creating a few categories.
        $category1 = $this->getDataGenerator()->create_category(
                array('name' => 'Category with normal and intensive courses'));
        $category2 = $this->getDataGenerator()->create_category(
                array('name' => 'Category with only normal courses'));
        $category3 = $this->getDataGenerator()->create_category(
                array('name' => 'Category with only intensive courses'));

        // Creating several courses and assign each to one of the categories above.
        $this->getDataGenerator()->create_course(
                array('shortname' => 'CA1.M.Normal course 1', 'category' => $category1->id));
        $this->getDataGenerator()->create_course(
                array('shortname' => 'CA1.M.Normal course 2', 'category' => $category1->id));
        $this->getDataGenerator()->create_course(
                array('shortname' => 'MI.Normal course 1', 'category' => $category1->id));
        $this->getDataGenerator()->create_course(
                array('shortname' => 'MI.Normal course 2', 'category' => $category1->id));
        $this->getDataGenerator()->create_course(
                array('shortname' => 'CA2.M.Normal course 3', 'category' => $category2->id));
        $this->getDataGenerator()->create_course(
                array('shortname' => 'CA2.M.Normal course 4', 'category' => $category2->id));
        $this->getDataGenerator()->create_course(
                array('shortname' => 'MI.Other course 1', 'category' => $category3->id));
        $this->getDataGenerator()->create_course(
                array('shortname' => 'MI.Other course 2', 'category' => $category3->id));

        // Get the function response.
        $result = get_categories_with_intensive_modules();
        // Build an array with the expected result.
        $expectedresult = array($category1->name => $category1->id, $category2->name => $category2->id);

        // Test the function response.
        $this->assertEquals($expectedresult, $result);
        $this->assertCount(2, $result);

    }

    /**
     * Tests for phpunit.
     */
    public function test_get_samoo_subjects () {

        $this->resetAfterTest(true);

        // Get the function response.
        $result = get_samoo_subjects();
        // Build an array with the expected result.
        $expectedresult = array('Calificaciones' => get_string('califications', 'local_eudecustom'),
            'Foro' => get_string('forum', 'local_eudecustom'),
            'Duda' => get_string('doubt', 'local_eudecustom'),
            'Incidencia' => get_string('problem', 'local_eudecustom'),
            'Petición' => get_string('request', 'local_eudecustom'));

        // Test the function response.
        $this->assertEquals($expectedresult, $result);
    }

    /**
     * Tests for phpunit.
     */
    public function test_count_course_matriculations () {
        global $DB;
        $this->resetAfterTest(true);

        // Creating a few users.
        $user1 = $this->getDataGenerator()->create_user(array('username' => 'user1', 'email' => 'user1@test.com'));
        $user2 = $this->getDataGenerator()->create_user(array('username' => 'user2', 'email' => 'user2@test.com'));
        $user3 = $this->getDataGenerator()->create_user(array('username' => 'user3', 'email' => 'user3@test.com'));

        // Creating several categories for future use.
        $category1 = $this->getDataGenerator()->create_category(array('name' => 'Category 1'));

        // Creating several courses to enrol the users.
        $course1 = $this->getDataGenerator()->create_course(array('shortname' => 'Course 1', 'category' => $category1->id));
        $course2 = $this->getDataGenerator()->create_course(array('shortname' => 'Course 2', 'category' => $category1->id));
        $course3 = $this->getDataGenerator()->create_course(array('shortname' => 'Course 3', 'category' => $category1->id));

        // Generating and inserting the records in the db.
        $record1 = new stdClass();
        $record1->user_email = $user1->email;
        $record1->course_shortname = $course1->shortname;
        $record1->category_id = $category1->id;
        $record1->matriculation_date = time();
        $record2 = new stdClass();
        $record2->user_email = $user1->email;
        $record2->course_shortname = $course2->shortname;
        $record2->category_id = $category1->id;
        $record2->matriculation_date = time();
        // Gonna insert 3 matriculations for user1 course1 and 1 matriculation for user1 course2.
        $lastinsertid = $DB->insert_record('local_eudecustom_mat_int', $record1);
        $record1->matriculation_date = time() + 1000;
        $lastinsertid = $DB->insert_record('local_eudecustom_mat_int', $record1);
        $record1->matriculation_date = time() + 2000;
        $lastinsertid = $DB->insert_record('local_eudecustom_mat_int', $record1);
        $record1->matriculation_date = time() + 3000;
        $lastinsertid = $DB->insert_record('local_eudecustom_mat_int', $record2);

        // Test user1 with course1 (Expected results = 3).
        $result = count_course_matriculations($user1->id, $course1->id, $category1->id);
        $this->assertEquals(3, $result);

        // Test user1 with course2 (Expected result = 1).
        $result = count_course_matriculations($user1->id, $course2->id, $category1->id);
        $this->assertEquals(1, $result);

        // Test user2 with course1 (Expected result = 0).
        $result = count_course_matriculations($user2->id, $course1->id, $category1->id);
        $this->assertEquals(0, $result);

        // Test a nonexistent user and a nonexistent course.
        $result = count_course_matriculations($user3->id, $course1->id, $category1->id);
        $this->assertEquals(0, $result);
        $result = count_course_matriculations($user1->id, $course3->id, $category1->id);
        $this->assertEquals(0, $result);
    }

    /**
     * Tests for phpunit.
     */
    public function test_count_total_intensives () {
        global $DB;
        $this->resetAfterTest(true);

        // Creating a few users.
        $user1 = $this->getDataGenerator()->create_user(array('username' => 'user1', 'email' => 'user1@php.com'));
        $user2 = $this->getDataGenerator()->create_user(array('username' => 'user2', 'email' => 'user2@php.com'));

        // Creating several categories for future use.
        $category1 = $this->getDataGenerator()->create_category(array('name' => 'Category 1'));
        $category2 = $this->getDataGenerator()->create_category(array('name' => 'Category 2'));
        $category3 = $this->getDataGenerator()->create_category(array('name' => 'Category 3'));

        // Generating and inserting the records in the db.
        $record1 = new stdClass();
        $record1->user_email = $user1->email;
        $record1->course_category = $category1->id;
        $record1->num_intensive = 5;
        $record2 = new stdClass();
        $record2->user_email = $user1->email;
        $record2->course_category = $category2->id;
        $record2->num_intensive = 2;

        // Insert data for user1 in the table.
        $lastinsertid = $DB->insert_record('local_eudecustom_user', $record1);
        $lastinsertid = $DB->insert_record('local_eudecustom_user', $record2);

        // Test user1 with course_category1 (Expected results = 5).
        $result = count_total_intensives($user1->id, $category1->id);
        $this->assertEquals(5, $result);

        // Test user1 with course_category2 (Expected result = 2).
        $result = count_total_intensives($user1->id, $category2->id);
        $this->assertEquals(2, $result);

        // Test user1 with course_category3 (Expected result = 0).
        $result = count_total_intensives($user1->id, $category3->id);
        $this->assertEquals(0, $result);

        // Test a nonexistent user and a nonexistent course.
        $result = count_total_intensives($user2->id, $category1->id);
        $this->assertEquals(0, $result);
        $result = count_total_intensives($user1->id, $category3->id);
        $this->assertEquals(0, $result);
    }

    /**
     * Tests for phpunit.
     */
    public function test_get_name_categories_by_role () {
        global $DB;
        $this->resetAfterTest(true);

        // Creating a few users.
        $user1 = $this->getDataGenerator()->create_user(array('username' => 'user1', 'email' => 'user1@php.com'));
        $user2 = $this->getDataGenerator()->create_user(array('username' => 'user2', 'email' => 'user2@php.com'));

        // Creating several categories for future use.
        $category1 = $this->getDataGenerator()->create_category(array('name' => 'Category 1'));
        $category2 = $this->getDataGenerator()->create_category(array('name' => 'Category 2'));
        $category3 = $this->getDataGenerator()->create_category(array('name' => 'Category 3'));

        // Creating courses related to the categories above.
        $course1 = $this->getDataGenerator()->create_course(
                array('shortname' => 'Course 1', 'category' => $category1->id));
        $course2 = $this->getDataGenerator()->create_course(
                array('shortname' => 'Course 2', 'category' => $category1->id));
        $course4 = $this->getDataGenerator()->create_course(
                array('shortname' => 'Course 4', 'category' => $category2->id));
        $course5 = $this->getDataGenerator()->create_course(
                array('shortname' => 'Course 5', 'category' => $category2->id));
        $course6 = $this->getDataGenerator()->create_course(
                array('shortname' => 'Course 6', 'category' => $category3->id));

        // Getting the id of the roles.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'teacher'));
        $editingteacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $managerrole = $DB->get_record('role', array('shortname' => 'manager'));

        // Enrol user 1 as a student in course 1 and course 4.
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $course4->id, $studentrole->id, 'manual');

        // Enrol user 1 as a techer in course 2.
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id, $teacherrole->id, 'manual');

        // Enrol user 1 as an editingteacher in course 5 and 6.
        $this->getDataGenerator()->enrol_user($user1->id, $course5->id, $editingteacherrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $course6->id, $editingteacherrole->id, 'manual');

        // Recovering the context of the category 1 and 3.
        $contextcat1 = $DB->get_record('context', array('contextlevel' => CONTEXT_COURSECAT, 'instanceid' => $category1->id));
        $contextcat3 = $DB->get_record('context', array('contextlevel' => CONTEXT_COURSECAT, 'instanceid' => $category3->id));

        // Enroling user2 in category 1 and category 3 as manager.
        $record1 = new stdClass();
        $record1->roleid = $managerrole->id;
        $record1->contextid = $contextcat1->id;
        $record1->userid = $user2->id;
        $lastinsertid = $DB->insert_record('role_assignments', $record1);
        $record2 = new stdClass();
        $record2->roleid = $managerrole->id;
        $record2->contextid = $contextcat3->id;
        $record2->userid = $user2->id;
        $lastinsertid = $DB->insert_record('role_assignments', $record2);

        // Enrol user 2 as teacher in course 4.
        $this->getDataGenerator()->enrol_user($user2->id, $course4->id, $teacherrole->id, 'manual');

        // Test user1 with role student (Expected results array with cats 1 and 2).
        $result = get_name_categories_by_role($user1->id, 'student');
        $expectedresult = array($category1->name => $category1->id, $category2->name => $category2->id);
        $this->assertEquals($expectedresult, $result);

        // Test user1 with role teacher (Expected results array with cat 1).
        $result = get_name_categories_by_role($user1->id, 'teacher');
        $expectedresult = array($category1->name => $category1->id);
        $this->assertEquals($expectedresult, $result);

        // Test user1 with role editingteacher (Expected results array with cats 2 and 3).
        $result = get_name_categories_by_role($user1->id, 'editingteacher');
        $expectedresult = array($category2->name => $category2->id, $category3->name => $category3->id);
        $this->assertEquals($expectedresult, $result);

        // Test user1 with role manager (Expected results empty array).
        $result = get_name_categories_by_role($user1->id, 'manager');
        $expectedresult = array();
        $this->assertEquals($expectedresult, $result);

        // Test user2 with role student (Expected results empty array).
        $result = get_name_categories_by_role($user2->id, 'student');
        $expectedresult = array();
        $this->assertEquals($expectedresult, $result);

        // Test user2 with role teacher (Expected results array with cat 2).
        $result = get_name_categories_by_role($user2->id, 'teacher');
        $expectedresult = array($category2->name => $category2->id);
        $this->assertEquals($expectedresult, $result);

        // Test user2 with role manager (Expected results array with cat 1 and cat 3).
        $result = get_name_categories_by_role($user2->id, 'manager');
        $expectedresult = array($category1->name => $category1->id, $category3->name => $category3->id);
        $this->assertEquals($expectedresult, $result);
    }

    /**
     * Tests for phpunit.
     */
    public function test_get_course_students () {
        global $DB;
        $this->resetAfterTest(true);

        // Creating a few users.
        $user1 = $this->getDataGenerator()->create_user(array('username' => 'user1'));
        $user2 = $this->getDataGenerator()->create_user(array('username' => 'user2'));
        $user3 = $this->getDataGenerator()->create_user(array('username' => 'user3'));
        $user4 = $this->getDataGenerator()->create_user(array('username' => 'user4'));
        $user5 = $this->getDataGenerator()->create_user(array('username' => 'user5'));

        // Creating a few courses.
        $course1 = $this->getDataGenerator()->create_course(
                array('shortname' => 'C01.M.Normal course 1'));
        $course2 = $this->getDataGenerator()->create_course(
                array('shortname' => 'C01.M.Normal course 2'));
        $course3 = $this->getDataGenerator()->create_course(
                array('shortname' => 'MI.Normal course 1'));
        $course4 = $this->getDataGenerator()->create_course(
                array('shortname' => 'MI.Normal course 2'));

        // Getting the id of the roles.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'teacher'));

        // Course 1 enrols: user1 as teacher user2 to user5 as students.
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, $teacherrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user3->id, $course1->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user4->id, $course1->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user5->id, $course1->id, $studentrole->id, 'manual');

        // Course 2 enrols: user1 to user3 as students.
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user2->id, $course2->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user3->id, $course2->id, $studentrole->id, 'manual');

        // Course 3 enrols: user4 and user5 as teachers and user5 also as a student.
        $this->getDataGenerator()->enrol_user($user4->id, $course3->id, $teacherrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user5->id, $course3->id, $teacherrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user5->id, $course3->id, $studentrole->id, 'manual');

        // Test the function with course 1 (Expected results array with 4 users: user2, user3, user4 and user5).
        $result = get_course_students($course1->id, $studentrole->shortname);
        $this->assertCount(4, $result);

        // Test the function with course 2 (Expected results array with 3 users: user1, user2 and user3).
        $result = get_course_students($course2->id, $studentrole->shortname);
        $this->assertCount(3, $result);

        // Test the function with course 3 (Expected results array with 1 user: user5).
        $result = get_course_students($course3->id, $studentrole->shortname);
        $this->assertCount(1, $result);

        // Test the function with course 4 (Expected results empty array).
        $result = get_course_students($course4->id, $studentrole->shortname);
        $this->assertCount(0, $result);
    }

    /**
     * Tests for phpunit.
     */
    public function test_get_user_categories () {
        global $DB;
        $this->resetAfterTest(true);

        // Creating a few users.
        $user1 = $this->getDataGenerator()->create_user(array('username' => 'user1'));
        $user2 = $this->getDataGenerator()->create_user(array('username' => 'user2'));
        $user3 = $this->getDataGenerator()->create_user(array('username' => 'user3'));
        $user4 = $this->getDataGenerator()->create_user(array('username' => 'user4'));

        // Creating several categories for future use.
        $category1 = $this->getDataGenerator()->create_category(array('name' => 'Example Category 1'));
        $category2 = $this->getDataGenerator()->create_category(array('name' => 'Example Category 2'));
        $category3 = $this->getDataGenerator()->create_category(array('name' => 'Example Category 3'));

        // Creating courses related to the categories above.
        $course1 = $this->getDataGenerator()->create_course(
                array('shortname' => 'C1.M.Course 1', 'category' => $category1->id));
        $course2 = $this->getDataGenerator()->create_course(
                array('shortname' => 'C1.M.Course 2', 'category' => $category1->id));
        $course3 = $this->getDataGenerator()->create_course(
                array('shortname' => 'C2.M.Course 3', 'category' => $category2->id));
        $course4 = $this->getDataGenerator()->create_course(
                array('shortname' => 'C2.M.Course 4', 'category' => $category2->id));
        $course5 = $this->getDataGenerator()->create_course(
                array('shortname' => 'C3.M.Course 5', 'category' => $category3->id));

        // Getting the id of the roles.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'teacher'));
        $managerrole = $DB->get_record('role', array('shortname' => 'manager'));

        // Recovering the context of the category 1 and 3.
        $contextcat1 = $DB->get_record('context', array('contextlevel' => CONTEXT_COURSECAT, 'instanceid' => $category1->id));
        $contextcat3 = $DB->get_record('context', array('contextlevel' => CONTEXT_COURSECAT, 'instanceid' => $category3->id));

        // Enrol user 1 as a student in course 1, course 3 and as a teacher in course 5.
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $course3->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $course5->id, $teacherrole->id, 'manual');

        // Enrol user 2 as a student in course 1, and as a teacher in course 2 and course 4.
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user2->id, $course2->id, $teacherrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user2->id, $course4->id, $teacherrole->id, 'manual');

        // Enroling user 4 in category 1 and category 3 as manager.
        $record1 = new stdClass();
        $record1->roleid = $managerrole->id;
        $record1->contextid = $contextcat1->id;
        $record1->userid = $user4->id;
        $lastinsertid = $DB->insert_record('role_assignments', $record1);
        $record2 = new stdClass();
        $record2->roleid = $managerrole->id;
        $record2->contextid = $contextcat3->id;
        $record2->userid = $user4->id;
        $lastinsertid = $DB->insert_record('role_assignments', $record2);

        // Test user1 (Expected results array with cats 1, 2 and 3).
        $result = get_user_categories($user1->id);
        $expectedresult = array($category1->name => $category1->id, $category2->name => $category2->id,
            $category3->name => $category3->id);
        $this->assertEquals($expectedresult, $result);

        // Test user2 (Expected results array with cats 1 and 2).
        $result = get_user_categories($user2->id);
        $expectedresult = array($category1->name => $category1->id, $category2->name => $category2->id);
        $this->assertEquals($expectedresult, $result);

        // Test user3 (Expected results empty array).
        $result = get_user_categories($user3->id);
        $expectedresult = array();
        $this->assertEquals($expectedresult, $result);

        // Test user4 (Expected results array with cats 1 and 3).
        $result = get_user_categories($user4->id);
        $expectedresult = array($category1->name => $category1->id, $category3->name => $category3->id);
        $this->assertEquals($expectedresult, $result);
    }

    /**
     * Tests for phpunit.
     */
    public function test_get_shortname_courses_by_category () {
        global $DB;
        $this->resetAfterTest(true);

        // Creating a few users.
        $user1 = $this->getDataGenerator()->create_user(array('username' => 'user1'));
        $user2 = $this->getDataGenerator()->create_user(array('username' => 'user2'));

        // Creating several categories for future use.
        $category1 = $this->getDataGenerator()->create_category(array('name' => 'Category 1'));
        $category2 = $this->getDataGenerator()->create_category(array('name' => 'Category 2'));
        $category3 = $this->getDataGenerator()->create_category(array('name' => 'Category 3'));

        // Recovering the context of the category 1.
        $contextcat1 = $DB->get_record('context', array('contextlevel' => CONTEXT_COURSECAT, 'instanceid' => $category1->id));

        // Creating courses related to the categories above.
        $course1 = $this->getDataGenerator()->create_course(
                array('shortname' => 'Example C1', 'category' => $category1->id));
        $course2 = $this->getDataGenerator()->create_course(
                array('shortname' => 'Example C2', 'category' => $category1->id));
        $course3 = $this->getDataGenerator()->create_course(
                array('shortname' => 'Example C3', 'category' => $category2->id));
        $course4 = $this->getDataGenerator()->create_course(
                array('shortname' => 'Example C4', 'category' => $category2->id));
        $course5 = $this->getDataGenerator()->create_course(
                array('shortname' => 'Example C5', 'category' => $category3->id));
        $course6 = $this->getDataGenerator()->create_course(
                array('shortname' => 'Example C6', 'category' => $category3->id));

        // Getting the id of the roles.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'teacher'));
        $managerrole = $DB->get_record('role', array('shortname' => 'manager'));

        // Enrol user 1 as a student in course 1 and as a teacher in course 2, course 3 and course 4.
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id, $teacherrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $course3->id, $teacherrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $course4->id, $teacherrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user2->id, $course5->id, $teacherrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user2->id, $course6->id, $teacherrole->id, 'manual');

        // Enroling user2 in category 1 as manager.
        $record1 = new stdClass();
        $record1->roleid = $managerrole->id;
        $record1->contextid = $contextcat1->id;
        $record1->userid = $user2->id;
        $DB->insert_record('role_assignments', $record1);

        // Test the function with user 1, category 1 and role student (Expected results: array with course 1).
        $result = get_shortname_courses_by_category($user1->id, $studentrole->shortname, $category1->id);
        $c1 = new stdclass();
        $c1->shortname = $course1->shortname;
        $c1->id = $course1->id;
        $expectedresult = array($c1->shortname => $c1);
        $this->assertEquals($expectedresult, $result);
        $this->assertCount(1, $result);

        // Test the function with user 1, category 1 and role teacher (Expected results: array with course 2).
        $result = get_shortname_courses_by_category($user1->id, $teacherrole->shortname, $category1->id);
        $c2 = new stdclass();
        $c2->shortname = $course2->shortname;
        $c2->id = $course2->id;
        $expectedresult = array($c2->shortname => $c2);
        $this->assertEquals($expectedresult, $result);
        $this->assertCount(1, $result);

        // Test the function with user 1, category 2 and role teacher (Expected results: array with course 3 and course 4).
        $result = get_shortname_courses_by_category($user1->id, $teacherrole->shortname, $category2->id);
        $c3 = new stdclass();
        $c3->shortname = $course3->shortname;
        $c3->id = $course3->id;
        $c4 = new stdclass();
        $c4->shortname = $course4->shortname;
        $c4->id = $course4->id;
        $expectedresult = array($c3->shortname => $c3, $c4->shortname => $c4);
        $this->assertEquals($expectedresult, $result);
        $this->assertCount(2, $result);

        // Test the function with user 2, category 1 and role manager (Expected results: array with course 1 and course 2).
        $result = get_shortname_courses_by_category($user2->id, $managerrole->shortname, $category1->id);
        $c1 = new stdclass();
        $c1->shortname = $course1->shortname;
        $c1->id = $course1->id;
        $c2 = new stdclass();
        $c2->shortname = $course2->shortname;
        $c2->id = $course2->id;
        $expectedresult = array($c1->shortname => $c1, $c2->shortname => $c2);
        $this->assertEquals($expectedresult, $result);
        $this->assertCount(2, $result);

        // Test the function with user 2, category 1 and role student (Expected results: array with course 1 and course 2).
        $result = get_shortname_courses_by_category($user2->id, $studentrole->shortname, $category1->id);
        $c1 = new stdclass();
        $c1->shortname = $course1->shortname;
        $c1->id = $course1->id;
        $c2 = new stdclass();
        $c2->shortname = $course2->shortname;
        $c2->id = $course2->id;
        $expectedresult = array($c1->shortname => $c1, $c2->shortname => $c2);
        $this->assertEquals($expectedresult, $result);
        $this->assertCount(2, $result);

        // Test the function with user 2, category 2 and role student (Expected results: empty array).
        $result = get_shortname_courses_by_category($user2->id, $studentrole->shortname, $category2->id);
        $expectedresult = array();
        $this->assertEquals($expectedresult, $result);
        $this->assertCount(0, $result);
    }

    /**
     * Tests for phpunit.
     */
    public function test_check_role_manager () {
        global $DB;
        $this->resetAfterTest(true);

        // Creating a few users.
        $user1 = $this->getDataGenerator()->create_user(array('username' => 'user1'));
        $user2 = $this->getDataGenerator()->create_user(array('username' => 'user2'));

        // Creating several categories for future use.
        $category1 = $this->getDataGenerator()->create_category(array('name' => 'Category 1'));
        $category2 = $this->getDataGenerator()->create_category(array('name' => 'Category 2'));
        $category3 = $this->getDataGenerator()->create_category(array('name' => 'Category 3'));

        // Recovering the manager role data.
        $managerrole = $DB->get_record('role', array('shortname' => 'manager'));

        // Recovering the context of the categories.
        $contextcat1 = $DB->get_record('context', array('contextlevel' => CONTEXT_COURSECAT, 'instanceid' => $category1->id));
        $contextcat2 = $DB->get_record('context', array('contextlevel' => CONTEXT_COURSECAT, 'instanceid' => $category2->id));

        // Enroling user1 in category 1 and category 2 as manager.
        $record1 = new stdClass();
        $record1->roleid = $managerrole->id;
        $record1->contextid = $contextcat1->id;
        $record1->userid = $user1->id;
        $DB->insert_record('role_assignments', $record1);
        $record2 = new stdClass();
        $record2->roleid = $managerrole->id;
        $record2->contextid = $contextcat2->id;
        $record2->userid = $user1->id;
        $DB->insert_record('role_assignments', $record2);

        // Test the function with user1 and categories 1 and 2 (Expected results: both true).
        $result = check_role_manager($user1->id, $category1->id);
        $this->assertTrue($result);
        $result = check_role_manager($user1->id, $category2->id);
        $this->assertTrue($result);

        // Test the function with user1 in category 3 and user2 in category 1 (Expected results: both false).
        $result = check_role_manager($user1->id, $category3->id);
        $this->assertFalse($result);
        $result = check_role_manager($user2->id, $category1->id);
        $this->assertFalse($result);
    }

    /**
     * Tests for phpunit.
     */
    public function test_get_role_manager () {
        global $DB;
        $this->resetAfterTest(true);

        // Creating a few users.
        $user1 = $this->getDataGenerator()->create_user(array('username' => 'testuser1'));

        // Creating several categories for future use.
        $category1 = $this->getDataGenerator()->create_category(array('name' => 'Test Category 1'));
        $category2 = $this->getDataGenerator()->create_category(array('name' => 'Test Category 2'));
        $category3 = $this->getDataGenerator()->create_category(array('name' => 'Test Category 3'));

        // Recovering the manager role data.
        $managerrole = $DB->get_record('role', array('shortname' => 'manager'));

        // Recovering the context of the categories.
        $contextcategory1 = $DB->get_record('context', array('contextlevel' => CONTEXT_COURSECAT, 'instanceid' => $category1->id));
        $contextcategory2 = $DB->get_record('context', array('contextlevel' => CONTEXT_COURSECAT, 'instanceid' => $category2->id));

        // Enroling user1 in category 1 and category 2.
        $record1 = new stdClass();
        $record1->roleid = $managerrole->id;
        $record1->contextid = $contextcategory1->id;
        $record1->userid = $user1->id;
        $DB->insert_record('role_assignments', $record1);
        $record2 = new stdClass();
        $record2->roleid = $managerrole->id;
        $record2->contextid = $contextcategory2->id;
        $record2->userid = $user1->id;
        $DB->insert_record('role_assignments', $record2);

        // Test the function with category 1 (Expected results: user1).
        $result = get_role_manager($category1->id);
        $expectedresult = $user1->id;
        $this->assertEquals($expectedresult, $result->id);

        // Test the function with category 2 (Expected results: user1).
        $result = get_role_manager($category2->id);
        $expectedresult = $user1->id;
        $this->assertEquals($expectedresult, $result->id);

        // Test the function with category 3 (Expected results: empty ).
        $result = get_role_manager($category3->id);
        $expectedresult = false;
        $this->assertEquals($expectedresult, $result);
    }

    /**
     * Tests for phpunit.
     */
    public function test_reset_attemps_from_course () {
        global $DB;
        $this->resetAfterTest(true);

        // Creating a few users.
        $user1 = $this->getDataGenerator()->create_user(array('username' => 'user1'));
        $user2 = $this->getDataGenerator()->create_user(array('username' => 'user2'));

        // Creating a few courses.
        $course1 = $this->getDataGenerator()->create_course(
                array('shortname' => 'Normal course 1'));

        // Getting the id of the role student.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        // Enrol the user1 in course 1 as a student.
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, $studentrole->id, 'manual');

        // Creating a quiz and associate it to the courses.
        $quizgen = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $quiz1 = $quizgen->create_instance(array('course' => $course1->id, 'sumgrades' => 2));

        // Creating a question and attach it to the quiz.
        $questgen = $this->getDataGenerator()->get_plugin_generator('core_question');
        $quizcat = $questgen->create_question_category();
        $question = $questgen->create_question('numerical', null, ['category' => $quizcat->id]);
        quiz_add_quiz_question($question->id, $quiz1);

        // Creating an instance of quiz 1 for user 1.
        $quizobj1a = quiz::create($quiz1->id, $user1->id);

        // Set attempts.
        $quba1a = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj1a->get_context());
        $quba1a->set_preferred_behaviour($quizobj1a->get_quiz()->preferredbehaviour);

        $timenow = time();

        // User 1 passes quiz 1.
        $attempt = quiz_create_attempt($quizobj1a, 1, false, $timenow, false, $user1->id);
        quiz_start_new_attempt($quizobj1a, $quba1a, $attempt, 1, $timenow);
        quiz_attempt_save_started($quizobj1a, $quba1a, $attempt);
        $attemptobj = quiz_attempt::create($attempt->id);
        $attemptobj->process_submitted_actions($timenow, false, [1 => ['answer' => '3.14']]);
        $attemptobj->process_finish($timenow, false);

        // Check for user 1 and quiz 1.
        $attempts = quiz_get_user_attempts($quiz1->id, $user1->id, 'all');
        $this->assertCount(1, $attempts);
        $attempt = array_shift($attempts);
        $this->assertEquals(quiz_attempt::FINISHED, $attempt->state);
        $this->assertEquals($user1->id, $attempt->userid);
        $this->assertEquals($quiz1->id, $attempt->quiz);

        $attempts = quiz_get_user_attempts($quiz1->id, $user1->id, 'finished');
        $this->assertCount(1, $attempts);
        $attempt = array_shift($attempts);
        $this->assertEquals(quiz_attempt::FINISHED, $attempt->state);
        $this->assertEquals($user1->id, $attempt->userid);
        $this->assertEquals($quiz1->id, $attempt->quiz);

        $attempts = quiz_get_user_attempts($quiz1->id, $user1->id, 'unfinished');
        $this->assertCount(0, $attempts);

        // Test the function with user1 and course 1 (Expected result: 0 attempts).
        $result = reset_attemps_from_course($user1->id, $course1->id);
        $this->assertTrue($result);
        $attempts = quiz_get_user_attempts($quiz1->id, $user1->id, 'all');
        $this->assertCount(0, $attempts);

        // Test the function with user2 and course 1 (Expected result: 0 attempts).
        $result = reset_attemps_from_course($user2->id, $course1->id);
        $this->assertTrue($result);
        $attempts = quiz_get_user_attempts($quiz1->id, $user2->id, 'all');
        $this->assertCount(0, $attempts);
    }

    /**
     * Tests for phpunit.
     */
    public function test_save_matriculation_dates () {
        global $DB;
        $this->resetAfterTest(true);

        // Creating a few categories.
        $category1 = $this->getDataGenerator()->create_category(
                array('name' => 'Category with normal and intensive courses'));
        $category2 = $this->getDataGenerator()->create_category(
                array('name' => 'Category with only normal courses'));

        // Creating several courses and assign each to one of the categories above.
        $course1 = $this->getDataGenerator()->create_course(
                array('shortname' => 'Normal course 1', 'category' => $category1->id));
        $course2 = $this->getDataGenerator()->create_course(
                array('shortname' => 'Normal course 2', 'category' => $category1->id));
        $course3 = $this->getDataGenerator()->create_course(
                array('shortname' => 'MI.Normal course 1', 'category' => $category2->id));
        $course4 = $this->getDataGenerator()->create_course(
                array('shortname' => 'MI.Normal course 2', 'category' => $category2->id));
        $course5 = $this->getDataGenerator()->create_course(
                array('shortname' => 'Normal course 3', 'category' => $category2->id));

        // Generating and inserting the initial records in the db.
        $today = time();
        $dayinseconds = 86400;
        $record1 = new stdClass();
        $record1->courseid = $course1->id;
        $record1->fecha1 = $today;
        $record1->fecha2 = $today + $dayinseconds;
        $record1->fecha3 = $today + $dayinseconds * 2;
        $record1->fecha4 = $today + $dayinseconds * 3;

        $record2 = new stdClass();
        $record2->courseid = $course2->id;
        $record2->fecha1 = $today + $dayinseconds * 7;
        $record2->fecha2 = $today + $dayinseconds * 8;
        $record2->fecha3 = $today + $dayinseconds * 9;
        $record2->fecha4 = $today + $dayinseconds * 10;

        $lastinsertid = $DB->insert_record('local_eudecustom_call_date', $record1);
        $lastinsertid = $DB->insert_record('local_eudecustom_call_date', $record2);

        // Creating the entry parameters to test the function.
        $newrecord1 = new stdClass();
        $newrecord1->courseid = $course1->id;
        $newrecord1->fecha1 = $today + $dayinseconds * 30;
        $newrecord1->fecha2 = $today + $dayinseconds * 31;
        $newrecord1->fecha3 = $today + $dayinseconds * 32;
        $newrecord1->fecha4 = $today + $dayinseconds * 33;
        $newrecord2 = new stdClass();
        $newrecord2->courseid = $course2->id;
        $newrecord2->fecha1 = $today + $dayinseconds * 60;
        $newrecord2->fecha2 = $today + $dayinseconds * 61;
        $newrecord2->fecha3 = $today + $dayinseconds * 62;
        $newrecord2->fecha4 = $today + $dayinseconds * 63;
        $newrecord3 = new stdClass();
        $newrecord3->courseid = $course3->id;
        $newrecord3->fecha1 = $today;
        $newrecord3->fecha2 = $today;
        $newrecord3->fecha3 = $today;
        $newrecord3->fecha4 = $today;
        $newrecord4 = new stdClass();
        $newrecord4->courseid = $course4->id;
        $newrecord4->fecha1 = $today;
        $newrecord4->fecha2 = $today;
        $newrecord4->fecha3 = $today;
        $newrecord4->fecha4 = $today;
        $newrecord5 = new stdClass();
        $newrecord5->courseid = $course5->id;
        $newrecord5->fecha1 = $today;
        $newrecord5->fecha2 = $today;
        $newrecord5->fecha3 = $today;
        $newrecord5->fecha4 = $today;

        $updatedata = array($newrecord1, $newrecord2);
        $newdata = array($newrecord3, $newrecord4);
        $mixeddata = array($newrecord3, $newrecord4, $newrecord5);
        $emptydata = array();

        // Test the function with prerecorded data in the db so all the changes are updates (Expected result: true).
        $result = save_matriculation_dates($updatedata);
        $this->assertTrue($result);
        $this->assertTrue($DB->record_exists('local_eudecustom_call_date',
                        array('courseid' => $course1->id, 'fecha1' => $today + $dayinseconds * 30,
                    'fecha2' => $today + $dayinseconds * 31, 'fecha3' => $today + $dayinseconds * 32,
                    'fecha4' => $today + $dayinseconds * 33)));
        $this->assertTrue($DB->record_exists('local_eudecustom_call_date',
                        array('courseid' => $course2->id, 'fecha1' => $today + $dayinseconds * 60,
                    'fecha2' => $today + $dayinseconds * 61, 'fecha3' => $today + $dayinseconds * 62,
                    'fecha4' => $today + $dayinseconds * 63)));

        // Test the function with new data for all entries (Expected result: true).
        $result = save_matriculation_dates($newdata);
        $this->assertTrue($result);
        $this->assertTrue($DB->record_exists('local_eudecustom_call_date',
                        array('courseid' => $course3->id, 'fecha1' => $today,
                    'fecha2' => $today, 'fecha3' => $today, 'fecha4' => $today)));
        $this->assertTrue($DB->record_exists('local_eudecustom_call_date',
                        array('courseid' => $course4->id, 'fecha1' => $today,
                    'fecha2' => $today, 'fecha3' => $today, 'fecha4' => $today)));

        // Test the function with 2 updates and 1 insert (Expected result: true).
        $result = save_matriculation_dates($mixeddata);
        $this->assertTrue($result);
        $this->assertTrue($DB->record_exists('local_eudecustom_call_date',
                        array('courseid' => $course3->id, 'fecha1' => $today,
                    'fecha2' => $today, 'fecha3' => $today, 'fecha4' => $today)));
        $this->assertTrue($DB->record_exists('local_eudecustom_call_date',
                        array('courseid' => $course4->id, 'fecha1' => $today,
                    'fecha2' => $today, 'fecha3' => $today, 'fecha4' => $today)));
        $this->assertTrue($DB->record_exists('local_eudecustom_call_date',
                        array('courseid' => $course5->id, 'fecha1' => $today,
                    'fecha2' => $today, 'fecha3' => $today, 'fecha4' => $today)));

        // Test the function with empty data (Expected result: false).
        $result = save_matriculation_dates($emptydata);
        $this->assertFalse($result);
    }

    /**
     * Tests for phpunit.
     */
    public function test_enrol_intensive_user () {
        global $DB;
        $this->resetAfterTest(true);

        // Creating a few users.
        $user1 = $this->getDataGenerator()->create_user(array('username' => 'user1', 'email' => 'user1@php.com'));

        // Creating a category.
        $category1 = $this->getDataGenerator()->create_category(
                array('name' => 'Category with normal and intensive courses'));

        // Creating a few courses.
        $course1 = $this->getDataGenerator()->create_course(
                array('shortname' => 'Course 1', 'category' => $category1->id));

        // Getting the id of the role student.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        // Getting the manual enrolment for course 1.
        $maninstance1 = $DB->get_record('enrol', array('courseid' => $course1->id, 'enrol' => 'manual'), '*', MUST_EXIST);

        // Setting some initial parameters.
        $timestart = time();
        $timeend = time() + 86400;
        $convoc = 2;
        $contextcourse1 = context_course::instance($course1->id);

        // Test the function to enrol user 1 in course 1.
        enrol_intensive_user('manual', $course1->id, $user1->id, $timestart, $timeend, $convoc, $category1->id);
        // Check if the enrolment is created.
        $this->assertTrue($DB->record_exists('user_enrolments', array('userid' => $user1->id, 'enrolid' => $maninstance1->id)));
        // Check new entry in table local_eudecustom_mat_int.
        $this->assertTrue($DB->record_exists('local_eudecustom_mat_int',
                        array('user_email' => $user1->email,
                              'course_shortname' => $course1->shortname,
                              'category_id' => $category1->id,
                              'matriculation_date' => $timestart, 'conv_number' => $convoc)));
        // Check the number of enrolments in table local_eudecustom_user.
        $this->assertTrue($DB->record_exists('local_eudecustom_user',
                        array('user_email' => $user1->email, 'course_category' => $course1->category)));
        $data = $DB->get_record('local_eudecustom_user',
                array('user_email' => $user1->email, 'course_category' => $course1->category));
        $this->assertEquals(1, $data->num_intensive);

        // Test the function to enrol user 1 again in course 1.
        $timestart2 = $timestart + 200000;
        $timeend2 = $timeend + 200000;
        enrol_intensive_user('manual', $course1->id, $user1->id, $timestart2, $timeend2, $convoc, $category1->id);
        // Check if the enrolment is created.
        $this->assertTrue($DB->record_exists('user_enrolments', array('userid' => $user1->id, 'enrolid' => $maninstance1->id)));
        $data2 = $DB->get_record('user_enrolments', array('userid' => $user1->id, 'enrolid' => $maninstance1->id));
        $this->assertEquals($timestart2, $data2->timestart);
        // Check new entry in table local_eudecustom_mat_int.
        $this->assertTrue($DB->record_exists('local_eudecustom_mat_int',
                        array('user_email' => $user1->email,
                              'course_shortname' => $course1->shortname,
                              'category_id' => $category1->id,
                              'matriculation_date' => $timestart2, 'conv_number' => $convoc)));
        // Check is the user is enroled as student.
        $this->assertTrue($DB->record_exists('role_assignments',
                        array('userid' => $user1->id, 'contextid' => $contextcourse1->id, 'roleid' => $studentrole->id)));
        // Check the number of enrolments in table local_eudecustom_user.
        $this->assertTrue($DB->record_exists('local_eudecustom_user',
                        array('user_email' => $user1->email, 'course_category' => $course1->category)));
        $data2 = $DB->get_record('local_eudecustom_user',
                array('user_email' => $user1->email, 'course_category' => $course1->category));
        $this->assertEquals(2, $data2->num_intensive);
    }

    /**
     * Tests for phpunit.
     */
    public function test_add_tpv_hidden_inputs () {
        global $CFG;
        global $USER;

        $this->resetAfterTest(true);

        // Creating a new user.
        $user1 = $this->getDataGenerator()->create_user(array('username' => 'user1'));

        // Logging with user 1.
        $this->setUser($user1);

        // Setting the initial data.
        $initialdata = '';
        // We have to initialize this parameter like this because phpunit didnt map all the settings of the plugins.
        $CFG->local_eudecustom_intensivemoduleprice = 60;
        $expectedresult = html_writer::empty_tag('input',
                        array(
                    'type' => 'hidden',
                    'id' => 'user',
                    'name' => 'user',
                    'class' => 'form-control',
                    'value' => $USER->id));
        $expectedresult .= html_writer::empty_tag('input',
                        array(
                    'type' => 'hidden',
                    'id' => 'letpv_course',
                    'name' => 'course',
                    'class' => 'form-control'));
        $expectedresult .= html_writer::empty_tag('input',
                        array('type' => 'hidden',
                    'id' => 'letpv_amount',
                    'name' => 'amount',
                    'class' => 'form-control',
                    'value' => '60'));
        $expectedresult .= html_writer::empty_tag('input',
                        array('type' => 'hidden',
                    'id' => 'sesskey',
                    'name' => 'sesskey',
                    'class' => 'form-control',
                    'value' => sesskey()));
        $expectedresult .= html_writer::end_div();
        $expectedresult .= html_writer::end_div();
        $expectedresult .= html_writer::empty_tag('input',
                        array(
                    'type' => 'submit',
                    'name' => 'abrirFechas',
                    'class' => 'btn btn-lg btn-primary btn-block abrirFechas letpv_btn',
                    'value' => get_string('continue', 'local_eudecustom')));

        // Testing the function.
        $result = add_tpv_hidden_inputs($initialdata);
        $this->assertEquals($expectedresult, $result);
    }

    /**
     * Tests for phpunit.
     */
    public function test_get_user_all_courses () {

        $this->resetAfterTest();

        $manualplugin = self::enable_enrol_plugin();
        $this->assertNotEmpty($manualplugin);
        $studentrole = self::get_student_role();

        // Create user.
        $user1 = $this->getDataGenerator()->create_user(array('firstname' => "USUARIO 1"));
        $this->assertNotEmpty($user1);

        // Create courses.
        $course1 = $this->getDataGenerator()->create_course(array('shortname' => "C01.M.CURSO"));
        $this->assertNotEmpty($course1);
        $course2 = $this->getDataGenerator()->create_course(array('shortname' => "MI.CURSO"));
        $this->assertNotEmpty($course2);
        $course3 = $this->getDataGenerator()->create_course(array('shortname' => "C01.M.CURSONORMAL"));
        $this->assertNotEmpty($course3);
        $course4 = $this->getDataGenerator()->create_course(array('shortname' => "C01.M.CURSO2"));
        $this->assertNotEmpty($course4);
        $course5 = $this->getDataGenerator()->create_course(array('shortname' => "MI.CURSO2"));
        $this->assertNotEmpty($course5);

        // Enrol user on courses.
        $manualinstance = self::create_manual_instance($course1->id);
        $manualplugin->enrol_user($manualinstance, $user1->id, $studentrole->id, 1493203999, 1494303999);
        $manualinstance2 = self::create_manual_instance($course2->id);
        $manualplugin->enrol_user($manualinstance2, $user1->id, $studentrole->id, 1493103999, 1494302999);
        $manualinstance3 = self::create_manual_instance($course3->id);
        $manualplugin->enrol_user($manualinstance3, $user1->id, $studentrole->id, 1494103999, 1495102999);
        $manualinstance4 = self::create_manual_instance($course4->id);
        $manualplugin->enrol_user($manualinstance4, $user1->id, $studentrole->id, 1493403999, 1494312999);
        $manualinstance5 = self::create_manual_instance($course5->id);
        $manualplugin->enrol_user($manualinstance5, $user1->id, $studentrole->id, 1493153999, 1494402999);
        // Testing the function.
        $data = get_user_all_courses($user1->id);
        $this->assertNotEmpty($data);
        $this->assertCount(3, $data);
        $this->assertEquals($data[$course4->id]->shortname, "C01.M.CURSO2");
        $this->assertEquals($data[$course3->id]->shortname, "C01.M.CURSONORMAL");
        $this->assertEquals($data[$course1->id]->shortname, "C01.M.CURSO");
    }

    /**
     * Tests for phpunit.
     */
    public function test_update_intensive_dates () {
        global $DB;
        $this->resetAfterTest();

        $manualplugin = self::enable_enrol_plugin();
        $this->assertNotEmpty($manualplugin);
        $studentrole = self::get_student_role();

        // Create user, category and courses.
        $user1 = $this->getDataGenerator()->create_user(array('firstname' => "USUARIO 1", 'email' => 'user1@php.com'));
        $this->assertNotEmpty($user1);
        $category1 = $this->getDataGenerator()->create_category(
                array('name' => 'Category with normal and intensive courses'));
        $this->assertNotEmpty($category1);
        $course1 = $this->getDataGenerator()->create_course(array('shortname' => "CTG.M.CURSO", 'category' => $category1->id));
        $this->assertNotEmpty($course1);
        $course2 = $this->getDataGenerator()->create_course(array('shortname' => "MI.CURSO"));
        $this->assertNotEmpty($course2);

        // Add call date data.
        $date = new stdClass();
        $date->id = 10;
        $date->courseid = $course2->id;
        $date->fecha1 = 1495650823;
        $date->fecha2 = 1496150824;
        $date->fecha3 = 1496650825;
        $date->fecha4 = 1497150826;
        $this->assertNotEmpty($date);

        $DB->insert_record('local_eudecustom_call_date', $date, false);

        // Enrol user 1 as a student in course 1 and course 4.
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id, $studentrole->id, 'manual');

        // Initial settings for local_eudecustom_mat_int.
        $matint = new stdClass();
        $matint->user_email = $user1->email;
        $matint->course_shortname = $course2->shortname;
        $matint->category_id = $category1->id;
        $matint->matriculation_date = 150000000;
        $DB->insert_record('local_eudecustom_mat_int', $matint, true);

        // Testing the function with the first call date (Expected matriculation date : 1495650823).
        $record = update_intensive_dates(1, $course1->id, $user1->id);
        $this->assertTrue($record);
        $result = $DB->get_record('local_eudecustom_mat_int',
                array('course_shortname' => $course2->shortname,
                      'user_email' => $user1->email,
                      'category_id' => $category1->id));
        $this->assertEquals($date->fecha1, $result->matriculation_date);

        // Testing the function with the first call date (Expected matriculation date : 1495650823).
        $record = update_intensive_dates(2, $course1->id, $user1->id);
        $this->assertTrue($record);
        $result = $DB->get_record('local_eudecustom_mat_int',
                array('course_shortname' => $course2->shortname,
                      'user_email' => $user1->email,
                      'category_id' => $category1->id));
        $this->assertEquals($date->fecha2, $result->matriculation_date);

        // Testing the function with the first call date (Expected matriculation date : 1495650823).
        $record = update_intensive_dates(3, $course1->id, $user1->id);
        $this->assertTrue($record);
        $result = $DB->get_record('local_eudecustom_mat_int',
                array('course_shortname' => $course2->shortname,
                      'user_email' => $user1->email,
                      'category_id' => $category1->id));
        $this->assertEquals($date->fecha3, $result->matriculation_date);

        // Testing the function with the first call date (Expected matriculation date : 1495650823).
        $record = update_intensive_dates(4, $course1->id, $user1->id);
        $this->assertTrue($record);
        $result = $DB->get_record('local_eudecustom_mat_int',
                array('course_shortname' => $course2->shortname,
                      'user_email' => $user1->email,
                      'category_id' => $category1->id));
        $this->assertEquals($date->fecha4, $result->matriculation_date);
    }

    /**
     * Tests for phpunit.
     */
    public function test_grades () {
        global $DB;
        $this->resetAfterTest();

        $manualplugin = self::enable_enrol_plugin();
        $this->assertNotEmpty($manualplugin);
        $studentrole = self::get_student_role();

        // Create user and courses.
        $user1 = $this->getDataGenerator()->create_user(array('firstname' => "USUARIO 1"));
        $this->assertNotEmpty($user1);
        $course1 = $this->getDataGenerator()->create_course(array('shortname' => "CAT.M.CURSO"));
        $this->assertNotEmpty($course1);
        $course2 = $this->getDataGenerator()->create_course(array('shortname' => "MI.CURSO"));
        $this->assertNotEmpty($course2);
        $course3 = $this->getDataGenerator()->create_course(array('shortname' => "CAT.M.CURSOSINGRADES"));
        $this->assertNotEmpty($course3);

        // Enrol user on courses.
        $manualinstance = self::create_manual_instance($course1->id);
        $manualplugin->enrol_user($manualinstance, $user1->id, $studentrole->id);
        $manualinstance2 = self::create_manual_instance($course2->id);
        $manualplugin->enrol_user($manualinstance2, $user1->id, $studentrole->id);
        $manualinstance3 = self::create_manual_instance($course3->id);
        $manualplugin->enrol_user($manualinstance3, $user1->id, $studentrole->id);

        // Use the function for a course without grades.
        $grade = grades($course1->id, $user1->id);
        $this->assertEmpty($grade);

        // Create grade for course1.
        $grade1 = $this->getDataGenerator()->create_grade_item(array('itemtype' => 'course', 'courseid' => $course1->id));
        $this->assertNotEmpty($grade1);

        $data = new stdClass();
        $data->itemid = $grade1->id;
        $data->finalgrade = 78;
        $data->userid = $user1->id;

        $DB->insert_record('grade_grades', $data, false);

        $gradeprov = grades($course1->id, $user1->id);
        $this->assertNotEmpty($gradeprov);
        $this->assertEquals($gradeprov, 7.8);

        // Create grade for course2.
        $grade2 = $this->getDataGenerator()->create_grade_item(array('itemtype' => 'course', 'courseid' => $course2->id));
        $this->assertNotEmpty($grade2);

        $data2 = new stdClass();
        $data2->itemid = $grade2->id;
        $data2->finalgrade = 82;
        $data2->userid = $user1->id;

        $DB->insert_record('grade_grades', $data2, false);

        $gradefinal = grades($course2->id, $user1->id);
        $this->assertNotEmpty($gradefinal);
        $this->assertEquals($gradefinal, 8.2);

        // Use the function for a course without grades.
        $gradefalse = grades($course3->id, $user1->id);
        $this->assertEmpty($gradefalse);
    }

    /**
     * Tests for phpunit.
     */
    public function test_get_intensivecourse_data () {
        global $DB;
        $this->resetAfterTest();

        $manualplugin = self::enable_enrol_plugin();
        $this->assertNotEmpty($manualplugin);
        $studentrole = self::get_student_role();

        // Create user, category and courses.
        $user1 = $this->getDataGenerator()->create_user(array('firstname' => "USUARIO 1", 'email' => 'user1@testmail.com'));

        $category1 = $this->getDataGenerator()->create_category();

        $course1 = $this->getDataGenerator()->create_course(array('shortname' => "CAT.M.CURSO[-1-]", 'category' => $category1->id));
        $course2 = $this->getDataGenerator()->create_course(array('shortname' => "MI.CURSO", 'category' => $category1->id));
        $course3 = $this->getDataGenerator()->create_course(array('shortname' => "CAT.M.CURSONORMAL",
            'category' => $category1->id));
        $course4 = $this->getDataGenerator()->create_course(array('shortname' => "CAT.M.CURSO2", 'category' => $category1->id));
        $course5 = $this->getDataGenerator()->create_course(array('shortname' => "MI.CURSO2", 'category' => $category1->id));
        $course6 = $this->getDataGenerator()->create_course(array('shortname' => "CAT.M.CURSO3", 'category' => $category1->id));
        $course7 = $this->getDataGenerator()->create_course(array('shortname' => "MI.CURSO3", 'category' => $category1->id));

        // Enrol user on courses.
        $manualinstance = self::create_manual_instance($course1->id);
        $manualplugin->enrol_user($manualinstance, $user1->id, $studentrole->id);
        $manualinstance2 = self::create_manual_instance($course3->id);
        $manualplugin->enrol_user($manualinstance2, $user1->id, $studentrole->id);
        $manualinstance3 = self::create_manual_instance($course4->id);
        $manualplugin->enrol_user($manualinstance3, $user1->id, $studentrole->id);
        $manualinstance4 = self::create_manual_instance($course5->id);
        $manualplugin->enrol_user($manualinstance4, $user1->id, $studentrole->id);
        $manualinstance5 = self::create_manual_instance($course6->id);
        $manualplugin->enrol_user($manualinstance5, $user1->id, $studentrole->id, 1493103999, 1494302999);
        $manualinstance6 = self::create_manual_instance($course7->id);
        $manualplugin->enrol_user($manualinstance6, $user1->id, $studentrole->id, 1493123999, 1494322999);

        // Testing user and courses.
        $this->assertNotEmpty($user1);
        $this->assertNotEmpty($course1);
        $this->assertNotEmpty($course2);
        $this->assertNotEmpty($course3);
        $this->assertNotEmpty($course4);
        $this->assertNotEmpty($course5);
        $this->assertNotEmpty($course6);
        $this->assertNotEmpty($course7);

        // TEST 1: Without grades.
        $data = get_intensivecourse_data($course1, $user1->id);

        $this->assertNotEmpty($data);
        $this->assertEquals("CAT.M.CURSO[-1-]", $data->name);
        $this->assertEquals("-", $data->actions);
        $this->assertEquals(0, $data->attempts);
        $this->assertEquals("-", $data->provgrades);
        $this->assertEquals("-", $data->finalgrades);

        // TEST 2: With grades on normal module.
        $grade1 = $this->getDataGenerator()->create_grade_item(array('itemtype' => 'course', 'courseid' => $course1->id));
        $this->assertNotEmpty($grade1);

        $grades = new stdClass();
        $grades->itemid = $grade1->id;
        $grades->finalgrade = 78;
        $grades->userid = $user1->id;

        $DB->insert_record('grade_grades', $grades, false);

        $data2 = get_intensivecourse_data($course1, $user1->id);
        $this->assertNotEmpty($data2);
        $this->assertEquals("CAT.M.CURSO[-1-]", $data->name);
        $this->assertEquals("-", $data->actions);
        $this->assertEquals(7.8, $data2->provgrades);
        $this->assertEquals(7.8, $data2->finalgrades);

        // TEST 3: With intensive module enrollment without grades.
        $data3 = get_intensivecourse_data($course4, $user1->id);

        $this->assertNotEmpty($data3);
        $this->assertEquals("CAT.M.CURSO2", $data3->name);
        $this->assertEquals("-", $data3->actions);
        $this->assertEquals("-", $data3->provgrades);
        $this->assertEquals("-", $data3->finalgrades);

        // TEST 4: With intensive module enrollment with grades only on normal module.
        $grade4 = $this->getDataGenerator()->create_grade_item(array('itemtype' => 'course', 'courseid' => $course4->id));
        $this->assertNotEmpty($grade4);

        $grades = new stdClass();
        $grades->itemid = $grade4->id;
        $grades->finalgrade = 65;
        $grades->userid = $user1->id;

        $DB->insert_record('grade_grades', $grades, false);

        $data4 = get_intensivecourse_data($course4, $user1->id);
        $this->assertNotEmpty($data4);
        $this->assertEquals("CAT.M.CURSO2", $data4->name);
        $this->assertEquals("-", $data4->actions);
        $this->assertEquals(6.5, $data4->provgrades);
        $this->assertEquals(6.5, $data4->finalgrades);

        // TEST 5: With intensive module enrollment with grades on normal module and intensive module.
        $grade5 = $this->getDataGenerator()->create_grade_item(array('itemtype' => 'course', 'courseid' => $course5->id));
        $this->assertNotEmpty($grade5);

        $grades = new stdClass();
        $grades->itemid = $grade5->id;
        $grades->finalgrade = 72;
        $grades->userid = $user1->id;

        $DB->insert_record('grade_grades', $grades, false);

        $data5 = get_intensivecourse_data($course4, $user1->id);
        $this->assertNotEmpty($data5);
        $this->assertEquals("CAT.M.CURSO2", $data5->name);
        $this->assertEquals("-", $data5->actions);
        $this->assertEquals(6.5, $data5->provgrades);
        $this->assertEquals(7.2, $data5->finalgrades);

        // TEST 6: With intensive module enrollment with grades only on intensive module.
        $matint = new stdClass();
        $matint->user_email = $user1->email;
        $matint->course_shortname = $course7->shortname;
        $matint->category_id = $category1->id;
        $matint->matriculation_date = 1497302999;
        $DB->insert_record('local_eudecustom_mat_int', $matint, true);

        $grade6 = $this->getDataGenerator()->create_grade_item(array('itemtype' => 'course', 'courseid' => $course7->id));
        $this->assertNotEmpty($grade6);

        $grades = new stdClass();
        $grades->itemid = $grade6->id;
        $grades->finalgrade = 90;
        $grades->userid = $user1->id;

        $DB->insert_record('grade_grades', $grades, false);

        $newdata = new stdClass();
        $newdata->useremail = $user1->email;
        $newdata->course_category = $category1->id;
        $newdata->num_intensive = 1;

        $DB->insert_record('local_eudecustom_user', $newdata, false);

        $data6 = get_intensivecourse_data($course6, $user1->id);
        $this->assertNotEmpty($data6);
        $this->assertEquals("CAT.M.CURSO3", $data6->name);
        $this->assertEquals("13/06/2017", $data6->actions);
        $this->assertEquals(1, $data6->attempts);
        $this->assertEquals("-", $data6->provgrades);
    }

    /**
     * Tests for phpunit.
     */
    public function test_configureprofiledata () {

        global $USER;
        global $DB;
        global $CFG;

        $this->resetAfterTest();

        $manualplugin = self::enable_enrol_plugin();
        $this->assertNotEmpty($manualplugin);
        $studentrole = self::get_student_role();

        $today = time();
        $day = 86400;

        // Create user, a category and courses.
        $user1 = $this->getDataGenerator()->create_user(array('firstname' => "USUARIO 1", 'email' => 'user1@php.com'));
        $category1 = $this->getDataGenerator()->create_category();
        // Create courses.
        $course1 = $this->getDataGenerator()->create_course(array('shortname' => "CT.M.CURSO", 'category' => $category1->id));
        $course2 = $this->getDataGenerator()->create_course(array('shortname' => "MI.CURSO", 'category' => $category1->id));
        // Create a course without intensive course.
        $course3 = $this->getDataGenerator()->create_course(array('shortname' => "CT.M.CURSONORMAL", 'category' => $category1->id));
        // Create more courses.
        $course4 = $this->getDataGenerator()->create_course(array('shortname' => "CT.M.CURSO2", 'category' => $category1->id));
        $course5 = $this->getDataGenerator()->create_course(array('shortname' => "MI.CURSO2", 'category' => $category1->id));
        $course6 = $this->getDataGenerator()->create_course(array('shortname' => "CT.M.CURSO3", 'category' => $category1->id));
        $course7 = $this->getDataGenerator()->create_course(array('shortname' => "MI.CURSO3", 'category' => $category1->id));
        $course8 = $this->getDataGenerator()->create_course(array('shortname' => "CT.M.CURSO4", 'category' => $category1->id));
        $course9 = $this->getDataGenerator()->create_course(array('shortname' => "MI.CURSO4", 'category' => $category1->id));
        $course10 = $this->getDataGenerator()->create_course(array('shortname' => "CT.M.CURSO5", 'category' => $category1->id));
        $course11 = $this->getDataGenerator()->create_course(array('shortname' => "MI.CURSO5", 'category' => $category1->id));

        // Enrol courses.
        $manualinstance = self::create_manual_instance($course1->id);
        $manualplugin->enrol_user($manualinstance, $user1->id, $studentrole->id, $today - (2 * $day), $today + (5 * $day));
        $manualinstance2 = self::create_manual_instance($course3->id);
        $manualplugin->enrol_user($manualinstance2, $user1->id, $studentrole->id, $today - (2 * $day), $today + (5 * $day));
        $manualinstance3 = self::create_manual_instance($course4->id);
        $manualplugin->enrol_user($manualinstance3, $user1->id, $studentrole->id, $today + (100 * $day), $today + (107 * $day));
        $manualinstance4 = self::create_manual_instance($course5->id);
        $manualplugin->enrol_user($manualinstance4, $user1->id, $studentrole->id, $today + (100 * $day), $today + (107 * $day));
        $manualinstance5 = self::create_manual_instance($course6->id);
        $manualplugin->enrol_user($manualinstance5, $user1->id, $studentrole->id, $today - (5 * $day), $today + (2 * $day));
        $manualinstance6 = self::create_manual_instance($course7->id);
        $manualplugin->enrol_user($manualinstance6, $user1->id, $studentrole->id, $today - (5 * $day), $today + (2 * $day));

        $this->assertNotEmpty($user1);
        $this->assertNotEmpty($course1);
        $this->assertNotEmpty($course2);
        $this->assertNotEmpty($course3);
        $this->assertNotEmpty($course4);
        $this->assertNotEmpty($course5);
        $this->assertNotEmpty($course6);
        $this->assertNotEmpty($course7);
        $this->assertNotEmpty($course8);
        $this->assertNotEmpty($course9);
        $this->assertNotEmpty($course10);
        $this->assertNotEmpty($course11);

        $USER->id = $user1->id;

        $CFG->local_eudecustom_intensivemodulechecknumber = 6;
        $CFG->local_eudecustom_totalenrolsinincurse = 3;

        // Add matriculation call dates on all courses.
        $fechas2 = new stdClass();
        $fechas2->courseid = $course2->id;
        $fechas2->fecha1 = $today - (2 * $day);
        $fechas2->fecha2 = $today + (30 * $day);
        $fechas2->fecha3 = $today + (60 * $day);
        $fechas2->fecha4 = $today + (100 * $day);
        $DB->insert_record('local_eudecustom_call_date', $fechas2, false);

        $fechas5 = new stdClass();
        $fechas5->courseid = $course5->id;
        $fechas5->fecha1 = $today + (35 * $day);
        $fechas5->fecha2 = $today + (37 * $day);
        $fechas5->fecha3 = $today + (67 * $day);
        $fechas5->fecha4 = $today + (100 * $day);
        $DB->insert_record('local_eudecustom_call_date', $fechas5, false);

        $fechas7 = new stdClass();
        $fechas7->courseid = $course7->id;
        $fechas7->fecha1 = $today - (5 * $day);
        $fechas7->fecha2 = $today + (44 * $day);
        $fechas7->fecha3 = $today + (74 * $day);
        $fechas7->fecha4 = $today + (114 * $day);
        $DB->insert_record('local_eudecustom_call_date', $fechas7, false);

        $fechas9 = new stdClass();
        $fechas9->courseid = $course9->id;
        $fechas9->fecha1 = $today + (19 * $day);
        $fechas9->fecha2 = $today + (51 * $day);
        $fechas9->fecha3 = $today + (81 * $day);
        $fechas9->fecha4 = $today + (121 * $day);
        $DB->insert_record('local_eudecustom_call_date', $fechas9, false);

        $fechas11 = new stdClass();
        $fechas11->courseid = $course11->id;
        $fechas11->fecha1 = $today - (2 * $day);
        $fechas11->fecha2 = $today + (30 * $day);
        $fechas11->fecha3 = $today + (60 * $day);
        $fechas11->fecha4 = $today + (120 * $day);
        $DB->insert_record('local_eudecustom_call_date', $fechas11, false);

        // TEST 1: Without grades.
        $data = configureprofiledata($user1->id, false);
        $this->assertNotEmpty($data);
        $this->assertCount(3, $data);
        $this->assertEquals($data[0]->name, "CT.M.CURSO3");
        $this->assertEquals($data[0]->grades, "-");
        $this->assertEquals($data[0]->gradesint, "-");
        $this->assertEquals($data[0]->action, "insideweek");
        $this->assertEquals($data[0]->actionclass, "abrirFechas");
        $this->assertEquals($data[0]->id, ' letpv_mod' . $course6->id);
        $this->assertEquals($data[0]->attempts, 0);
        $this->assertEquals($data[0]->info, get_string('nogrades', 'local_eudecustom'));
        $this->assertEquals($data[1]->name, "CT.M.CURSO2");
        $this->assertEquals($data[1]->grades, "-");
        $this->assertEquals($data[1]->gradesint, "-");
        $this->assertEquals($data[1]->action, "outweek");
        $this->assertEquals($data[1]->actionclass, "abrirFechas");
        $this->assertEquals($data[1]->id, ' letpv_mod' . $course4->id);
        $this->assertEquals($data[1]->attempts, 0);
        $this->assertEquals($data[2]->name, "CT.M.CURSO");
        $this->assertEquals($data[2]->grades, "-");
        $this->assertEquals($data[2]->gradesint, "-");
        $this->assertEquals($data[2]->action, "notenroled");
        $this->assertEquals($data[2]->actiontitle, "Early Access");
        $this->assertEquals($data[2]->id, ' letpv_mod' . $course1->id);
        $this->assertEquals($data[2]->attempts, 0);

        // TEST 2: With grades on normal module.
        $grade1 = $this->getDataGenerator()->create_grade_item(array('itemtype' => 'course', 'courseid' => $course1->id));
        $this->assertNotEmpty($grade1);

        $grades1 = new stdClass();
        $grades1->itemid = $grade1->id;
        $grades1->finalgrade = 78;
        $grades1->userid = $user1->id;

        $DB->insert_record('grade_grades', $grades1, false);

        $data2 = configureprofiledata($user1->id, false);
        $this->assertNotEmpty($data2);
        $this->assertEquals($data2[2]->grades, "7.80");
        $this->assertEquals($data2[2]->gradesint, "7.80");
        $this->assertEquals($data2[2]->action, "notenroled");
        $this->assertEquals($data2[2]->actiontitle, "Increase grades");

        // TEST 3: With intensive module enrollment without grades.
        $data3 = configureprofiledata($user1->id, false);
        $this->assertNotEmpty($data3);

        // TEST 4: With intensive module enrollment with grades only on normal module.
        $grade4 = $this->getDataGenerator()->create_grade_item(array('itemtype' => 'course', 'courseid' => $course4->id));
        $this->assertNotEmpty($grade4);

        $grades4 = new stdClass();
        $grades4->itemid = $grade4->id;
        $grades4->finalgrade = 65;
        $grades4->userid = $user1->id;

        $DB->insert_record('grade_grades', $grades4, false);

        $data4 = configureprofiledata($user1->id, false);
        $this->assertNotEmpty($data4);
        $this->assertEquals($data4[1]->grades, "6.50");
        $this->assertEquals($data4[1]->gradesint, "6.50");

        // TEST 5: With intensive module enrollment with grades on normal module and intensive module.
        $grade5 = $this->getDataGenerator()->create_grade_item(array('itemtype' => 'course', 'courseid' => $course5->id));
        $this->assertNotEmpty($grade5);

        $grades5 = new stdClass();
        $grades5->itemid = $grade5->id;
        $grades5->finalgrade = 72;
        $grades5->userid = $user1->id;

        $DB->insert_record('grade_grades', $grades5, false);

        $data5 = configureprofiledata($user1->id, false);
        $this->assertNotEmpty($data5);
        $this->assertEquals($data5[1]->grades, "6.50");
        $this->assertEquals($data5[1]->gradesint, "7.20");

        // TEST 6: With intensive module enrollment with grades only on intensive module.
        $matint = new stdClass();
        $matint->user_email = $user1->email;
        $matint->course_shortname = $course7->shortname;
        $matint->category_id = $category1->id;
        $matint->matriculation_date = $today + (12 * $day);
        $DB->insert_record('local_eudecustom_mat_int', $matint, true);

        $grade6 = $this->getDataGenerator()->create_grade_item(array('itemtype' => 'course', 'courseid' => $course7->id));
        $this->assertNotEmpty($grade6);

        $grades6 = new stdClass();
        $grades6->itemid = $grade6->id;
        $grades6->finalgrade = 40;
        $grades6->userid = $user1->id;

        $DB->insert_record('grade_grades', $grades6, false);

        $newdata = new stdClass();
        $newdata->user_email = $user1->email;
        $newdata->course_category = $category1->id;
        $newdata->num_intensive = 1;

        $DB->insert_record('local_eudecustom_user', $newdata, false);
        $numtries = $DB->get_record('local_eudecustom_user',
                array('user_email' => $user1->email, 'course_category' => $category1->id));

        $data6 = configureprofiledata($user1->id, false);
        $this->assertNotEmpty($data6);
        $this->assertEquals($data6[0]->grades, "-");

        // TEST 7: Enrol on normal module without enrollment on intensive module.
        $manualinstance7 = self::create_manual_instance($course8->id);
        $manualplugin->enrol_user($manualinstance7, $user1->id, $studentrole->id, $today - (10 * $day), $today - (2 * $day));

        $data7 = configureprofiledata($user1->id, false);
        $this->assertNotEmpty($data7);
        $this->assertEquals($data7[0]->name, "CT.M.CURSO4");
        $this->assertEquals($data7[0]->grades, "-");
        $this->assertEquals($data7[0]->gradesint, "-");
        $this->assertEquals($data7[0]->action, "notenroled");
        $this->assertEquals($data7[0]->actiontitle, "Early Access");
        $this->assertEquals($data7[0]->attempts, 0);

        // TEST 8: Enrol on intensive module, grade on normal module and 1 attempt.
        $manualinstance8 = self::create_manual_instance($course9->id);
        $manualplugin->enrol_user($manualinstance8, $user1->id, $studentrole->id, $today - (37 * $day), $today - (30 * $day));

        $grade8 = $this->getDataGenerator()->create_grade_item(array('itemtype' => 'course', 'courseid' => $course8->id));
        $this->assertNotEmpty($grade1);

        $grades8 = new stdClass();
        $grades8->itemid = $grade8->id;
        $grades8->finalgrade = 23;
        $grades8->userid = $user1->id;

        $DB->insert_record('grade_grades', $grades8, false);

        $newdata8 = new stdClass();
        $newdata8->id = $numtries->id;
        $newdata8->user_email = $user1->email;
        $newdata8->course_category = $category1->id;
        $newdata8->num_intensive = 2;

        $DB->update_record('local_eudecustom_user', $newdata8, false);

        $data8 = configureprofiledata($user1->id, false);
        $this->assertNotEmpty($data8);
        $this->assertEquals($data8[0]->name, "CT.M.CURSO4");
        $this->assertEquals($data8[0]->grades, "2.30");
        $this->assertEquals($data8[0]->gradesint, "2.30");
        $this->assertEquals($data8[0]->action, "notenroled");
        $this->assertEquals($data8[0]->actiontitle, "Retry module");
        $this->assertEquals($data8[0]->actionclass, 'abrirFechas');
        $this->assertEquals($data8[0]->actionid, 'abrirFechas(' . $course8->id . ',1,1)');

        // TEST 9: Add 3 attempts and a low grade on intensive module.

        $grade9 = $this->getDataGenerator()->create_grade_item(array('itemtype' => 'course', 'courseid' => $course9->id));
        $this->assertNotEmpty($grade9);

        $matint1 = new stdClass();
        $matint1->user_email = $user1->email;
        $matint1->course_shortname = $course9->shortname;
        $matint1->category_id = $category1->id;
        $matint1->matriculation_date = $today - (37 * $day);
        $DB->insert_record('local_eudecustom_mat_int', $matint1, true);

        $matint2 = new stdClass();
        $matint2->user_email = $user1->email;
        $matint2->course_shortname = $course9->shortname;
        $matint2->category_id = $category1->id;
        $matint2->matriculation_date = $today - (37 * $day) + 1000;
        $DB->insert_record('local_eudecustom_mat_int', $matint2, true);

        $grades9 = new stdClass();
        $grades9->itemid = $grade9->id;
        $grades9->finalgrade = 31;
        $grades9->userid = $user1->id;

        $DB->insert_record('grade_grades', $grades9, false);

        $adddata = new stdClass();
        $adddata->id = $numtries->id;
        $adddata->user_email = $user1->email;
        $adddata->course_category = $category1->id;
        $adddata->num_intensive = 9;

        $DB->update_record('local_eudecustom_user', $adddata, false);

        $data9 = configureprofiledata($user1->id, false);
        $this->assertNotEmpty($data9);
        $this->assertEquals($data9[0]->name, "CT.M.CURSO4");
        $this->assertEquals($data9[0]->grades, "2.30");
        $this->assertEquals($data9[0]->gradesint, "3.10");
        $this->assertEquals($data9[0]->action, "notenroled");
        $this->assertEquals($data9[0]->actiontitle, "Retry module");
        $this->assertEquals($data9[0]->actionid, 'abrir(' . $course8->id . ',0,1)');

        // TEST 10: Update num_intensive to 3 but add attemps to 3.
        $addnewdata = new stdClass();
        $addnewdata->id = $numtries->id;
        $addnewdata->user_email = $user1->email;
        $addnewdata->course_category = $category1->id;
        $addnewdata->num_intensive = 3;

        $DB->update_record('local_eudecustom_user', $addnewdata, false);

        $data0 = configureprofiledata($user1->id, false);
        $this->assertNotEmpty($data0);
        $this->assertEquals($data0[0]->name, "CT.M.CURSO4");
        $this->assertEquals($data0[0]->grades, "2.30");
        $this->assertEquals($data0[0]->gradesint, "3.10");
        $this->assertEquals($data0[0]->action, "notenroled");
        $this->assertEquals($data0[0]->actiontitle, "Retry module");
        $this->assertEquals($data0[0]->actionid, 'abrirFechas(' . $course8->id . ',1,1)');

        $matint3 = new stdClass();
        $matint3->user_email = $user1->email;
        $matint3->course_shortname = $course9->shortname;
        $matint3->category_id = $category1->id;
        $matint3->matriculation_date = $today - (37 * $day) + 2000;
        $DB->insert_record('local_eudecustom_mat_int', $matint3, true);

        $data0b = configureprofiledata($user1->id, false);
        $this->assertNotEmpty($data0b);
        $this->assertEquals($data0b[0]->name, "CT.M.CURSO4");
        $this->assertEquals($data0b[0]->grades, "2.30");
        $this->assertEquals($data0b[0]->gradesint, "3.10");
        $this->assertEquals($data0b[0]->action, "notenroled");
        $this->assertEquals($data0b[0]->actiontitle, "Retry module");
        $this->assertEquals($data0b[0]->actionid, 'abrir(' . $course8->id . ',0,1)');

        // TEST 11: Grade 10 on the new course.
        $manualinstance0 = self::create_manual_instance($course10->id);
        $manualplugin->enrol_user($manualinstance0, $user1->id, $studentrole->id, $today - (14 * $day), $today - (7 * $day));

        $manualinstanceint = self::create_manual_instance($course11->id);
        $manualplugin->enrol_user($manualinstanceint, $user1->id, $studentrole->id, $today - (14 * $day), $today - (7 * $day));
        // New items and grades.
        $grade0 = $this->getDataGenerator()->create_grade_item(array('itemtype' => 'course', 'courseid' => $course10->id));
        $this->assertNotEmpty($grade0);
        $grades0 = new stdClass();
        $grades0->itemid = $grade0->id;
        $grades0->finalgrade = 98;
        $grades0->userid = $user1->id;

        $DB->insert_record('grade_grades', $grades0, false);

        $grade0b = $this->getDataGenerator()->create_grade_item(array('itemtype' => 'course', 'courseid' => $course11->id));
        $this->assertNotEmpty($grade0b);

        $grades0b = new stdClass();
        $grades0b->itemid = $grade0b->id;
        $grades0b->finalgrade = 100;
        $grades0b->userid = $user1->id;

        $DB->insert_record('grade_grades', $grades0b, false);

        $data11 = configureprofiledata($user1->id, false);
        $this->assertNotEmpty($data11);
        $this->assertCount(5, $data11);
        $this->assertEquals($data11[0]->grades, "9.80");
        $this->assertEquals($data11[0]->gradesint, "10.00");
        $this->assertEquals($data11[0]->action, "insideweek");
        $this->assertEmpty($data11[0]->actiontitle);
    }

    /**
     * Tests for phpunit.
     */
    public function test_get_user_shortname_courses () {

        global $DB;

        $this->resetAfterTest(true);

        // Creating users.
        $user1 = $this->getDataGenerator()->create_user(
                array('username' => 'unit'));
        $user2 = $this->getDataGenerator()->create_user(
                array('username' => 'unit2'));
        $user3 = $this->getDataGenerator()->create_user(
                array('username' => 'unit3'));

        // Creating categories.
        $category1 = $this->getDataGenerator()->create_category(
                array('name' => 'phpunit category 1'));

        $category2 = $this->getDataGenerator()->create_category(
                array('name' => 'phpunit category 2'));

        $category3 = $this->getDataGenerator()->create_category(
                array('name' => 'phpunit category 3'));

        // Creating courses.
        $course1 = $this->getDataGenerator()->create_course(
                array('shortname' => 'C01.M.phpunit cat1 course1', 'category' => $category1->id));

        $course2 = $this->getDataGenerator()->create_course(
                array('shortname' => 'C01.M.phpunit cat1 course2', 'category' => $category1->id));

        $course3 = $this->getDataGenerator()->create_course(
                array('shortname' => 'C02.M.phpunit cat2 course1', 'category' => $category2->id));

        $course5 = $this->getDataGenerator()->create_course(
                array('shortname' => 'C03.M.phpunit cat3 course1', 'category' => $category3->id));

        // Getting the id of the roles.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        // Enrol user1 in 2 courses of cat 1 and first of cat 2 and 3.
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $course3->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $course5->id, $studentrole->id, 'manual');

        // Test get user1's courses from cat 1.
        $result = get_user_shortname_courses($user1->id, $category1->id);
        $this->assertCount(2, $result);

        $c1 = new stdClass(); // First course.
        $c1->id = $course1->id;
        $c1->shortname = $course1->shortname;

        $c2 = new stdClass(); // Second course.
        $c2->id = $course2->id;
        $c2->shortname = $course2->shortname;
        $expected = array($c1->shortname => $c1, $c2->shortname => $c2);
        $this->assertEquals($expected, $result);

        // Test get user1's courses from cat 2.
        $result = get_user_shortname_courses($user1->id, $category2->id);
        $this->assertCount(1, $result);

        $c3 = new stdClass(); // Second course.
        $c3->id = $course3->id;
        $c3->shortname = $course3->shortname;
        $expected = array($c3->shortname => $c3, $c3->shortname => $c3);
        $this->assertEquals($expected, $result);

        // Test get user1's courses from cat 3.
        $result = get_user_shortname_courses($user1->id, $category3->id);
        $this->assertCount(1, $result);

        $c5 = new stdClass(); // Second course.
        $c5->id = $course5->id;
        $c5->shortname = $course5->shortname;
        $expected = array($c5->shortname => $c5, $c5->shortname => $c5);
        $this->assertEquals($expected, $result);

        // Test get user2's courses from cat 1 (should be 0).
        $result = get_user_shortname_courses($user2->id, $category1->id);
        $this->assertCount(0, $result);

        // Recovering the manager role data.
        $managerrole = $DB->get_record('role', array('shortname' => 'manager'));

        // Recovering the context of the categories.
        $contextcat1 = $DB->get_record('context', array('contextlevel' => CONTEXT_COURSECAT, 'instanceid' => $category1->id));

        // Enrol user2 as manager in cat1.
        $record = new stdClass();
        $record->roleid = $managerrole->id;
        $record->contextid = $contextcat1->id;
        $record->userid = $user3->id;
        $DB->insert_record('role_assignments', $record);

        // Test get the courses where in category1, is manager.
        $result = get_user_shortname_courses($user3->id, $category1->id);
        $this->assertCount(2, $result);

        // Test get the courses where in category2, is manager (should be 0).
        $result = get_user_shortname_courses($user3->id, $category2->id);
        $this->assertCount(0, $result);

    }

    /**
     * Tests for phpunit.
     */
    public function test_get_info_grades () {

        global $DB;

        $this->resetAfterTest();

        $manualplugin = self::enable_enrol_plugin();
        $this->assertNotEmpty($manualplugin);
        $studentrole = self::get_student_role();

        $user1 = $this->getDataGenerator()->create_user(array('firstname' => "USUARIO 1"));

        $course1 = $this->getDataGenerator()->create_course(array('shortname' => "CURSO"));
        $course2 = $this->getDataGenerator()->create_course(array('shortname' => "MI.CURSO"));

        $manualinstance = self::create_manual_instance($course1->id);
        $manualplugin->enrol_user($manualinstance, $user1->id, $studentrole->id, 1493203999, 1494303999);
        $manualinstance2 = self::create_manual_instance($course2->id);
        $manualplugin->enrol_user($manualinstance2, $user1->id, $studentrole->id, 1493103999, 1494302999);

        $grade = $this->getDataGenerator()->create_grade_item(array('itemtype' => 'course', 'courseid' => $course1->id));
        $this->assertNotEmpty($grade);
        $grades = new stdClass();
        $grades->itemid = $grade->id;
        $grades->finalgrade = 88;
        $grades->feedback = 'Texto de informacion';
        $grades->userid = $user1->id;

        $DB->insert_record('grade_grades', $grades, false);

        $info = get_info_grades($course1->id, $user1->id);
        $this->assertNotEmpty($info);
        $this->assertEquals($info, 'Texto de informacion');
    }

    /**
     * Tests for phpunit.
     */
    public function test_integrate_previous_data () {
        global $DB;

        $this->resetAfterTest(true);

        // Creating a few users.
        $user1 = $this->getDataGenerator()->create_user(array('username' => 'user1', 'email' => 'user1@testmail.com'));
        $user2 = $this->getDataGenerator()->create_user(array('username' => 'user2', 'email' => 'user2@testmail.com'));

        // Creating several categories for future use.
        $category1 = $this->getDataGenerator()->create_category(array('name' => 'Comercio Internacional'));
        $category2 = $this->getDataGenerator()->create_category(array('name' => 'Gestión Ambiental'));

        // Creating several courses and assign each to one of the categories above.
        $this->getDataGenerator()->create_course(
                array('shortname' => 'COI.M.NM1', 'category' => $category1->id));
        $this->getDataGenerator()->create_course(
                array('shortname' => 'COI.M.NM2', 'category' => $category1->id));
        $this->getDataGenerator()->create_course(
                array('shortname' => 'MI.NM1', 'category' => $category2->id));
        $this->getDataGenerator()->create_course(
                array('shortname' => 'MI.NM2', 'category' => $category2->id));

        // Creating initial data ($data1 and $data6 are the only strings with correct info).
        $data1 = 'CREATE;user1@testmail.com;COI.M.NM1;21/04/2017;4' . PHP_EOL .
                'CREATE;user2@testmail.com;COI.M.NM1;22/04/2017;4';
        $data2 = 'CREATED;user3@testmail.com;MI.COI.M01;23/04/1970;4;CREATED' . PHP_EOL .
                'user4@testmail.com;MI.COI.M01;24/04/1970;1;';
        $data3 = 'CREATE;user5@testmail.com;MI.COI.M01;25/04/1970;4' . PHP_EOL .
                'DEL;user6@testmail.com;Curso Cron 7';
        $data4 = 'CREATE;user7@testmail.com;MI.COI.M01;27/04/1970;4' . PHP_EOL .
                'CREATE;user8@testmail.com;Normal course 2;28-04-1970;1';
        $data5 = 'CREATE;user9@testmail.com;MI.COI.M01;29/04/1970;4' . PHP_EOL .
                'CREATE;user10@testmail.com;MI.COI.M01;30/04/1970;5';
        /* Test the function with $data1
         * (expected result: 2 entries in local_eudecustom_mat_int and local_eudecustom_user, one for each user)
         */
        $result = integrate_previous_data($data1);
        $expectedmatintrec = $DB->get_records('local_eudecustom_mat_int');
        $expecteduserrecord1 = $DB->get_record('local_eudecustom_user', array('user_email' => $user1->email));
        $expecteduserrecord2 = $DB->get_record('local_eudecustom_user', array('user_email' => $user2->email));

        $this->assertTrue($result);
        $this->assertCount(2, $expectedmatintrec);
        $this->assertEquals($user1->email, $expecteduserrecord1->user_email);
        $this->assertEquals($category1->id, $expecteduserrecord1->course_category);
        $this->assertEquals(1, $expecteduserrecord1->num_intensive);
        $this->assertEquals($user2->email, $expecteduserrecord2->user_email);
        $this->assertEquals($category1->id, $expecteduserrecord2->course_category);
        $this->assertEquals(1, $expecteduserrecord2->num_intensive);

        // Test with $data2 to $data5 (Expected result: false, due to wrong introduced data).
        $result = integrate_previous_data($data2);
        $this->assertFalse($result);

        $result = integrate_previous_data($data3);
        $this->assertFalse($result);

        $result = integrate_previous_data($data4);
        $this->assertFalse($result);

        $result = integrate_previous_data($data5);
        $this->assertFalse($result);
    }

    /**
     * Tests for phpunit.
     */
    public function test_get_usercourses_by_rol () {
        global $DB;

        $this->resetAfterTest(true);

        // Creating users.
        $user1 = $this->getDataGenerator()->create_user(
                array('username' => 'user1'));
        $user2 = $this->getDataGenerator()->create_user(
                array('username' => 'user2'));

        // Creating categories.
        $category1 = $this->getDataGenerator()->create_category(
                array('name' => 'phpunit category 1'));
        $category2 = $this->getDataGenerator()->create_category(
                array('name' => 'phpunit category 2'));
        $category3 = $this->getDataGenerator()->create_category(
                array('name' => 'Intensive Category'));

        // Creating courses.
        $course1 = $this->getDataGenerator()->create_course(
                array('shortname' => 'CAT.M.CS', 'category' => $category1->id, 'fullname' => 'course1 fullname'));
        $course2 = $this->getDataGenerator()->create_course(
                array('shortname' => 'CAT.M.TF', 'category' => $category1->id, 'fullname' => 'course2 fullname'));
        $course3 = $this->getDataGenerator()->create_course(
                array('shortname' => 'CAT.M.YY', 'category' => $category2->id, 'fullname' => 'course3 fullname'));
        $course4 = $this->getDataGenerator()->create_course(
                array('shortname' => 'CAT.M.II', 'category' => $category2->id, 'fullname' => 'course4 fullname'));
        $course5 = $this->getDataGenerator()->create_course(
                array('shortname' => 'OLD-M01', 'category' => $category2->id, 'fullname' => 'Old course 1'));
        $course6 = $this->getDataGenerator()->create_course(
                array('shortname' => 'MI.CS', 'category' => $category3->id, 'fullname' => 'Intensive course 1'));

        // Getting the id of the roles.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'teacher'));

        // Enrolling student and teacher in both courses.
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $course3->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $course4->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $course6->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $course5->id, $studentrole->id, 'manual');

        $this->getDataGenerator()->enrol_user($user2->id, $course1->id, $teacherrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user2->id, $course2->id, $teacherrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user2->id, $course3->id, $teacherrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user2->id, $course4->id, $teacherrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user2->id, $course5->id, $teacherrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user2->id, $course6->id, $teacherrole->id, 'manual');

        // Test1: Get courses from a student (should return 0).
        $result = get_usercourses_by_rol($user1->id);
        $this->assertCount(0, $result);

        // Test2: Get courses from a teacher.
        // Should be only 5 because the function should not get the course 5.
        $result = get_usercourses_by_rol($user2->id);
        $this->assertCount(5, $result);

    }

    /**
     * Tests for phpunit.
     */
    public function test_module_is_intensive () {

        $this->resetAfterTest(true);

        // Creating categories.
        $category1 = $this->getDataGenerator()->create_category(
                array('name' => 'phpunit category 1'));

        // Creating courses.
        $course1 = $this->getDataGenerator()->create_course(
                array('shortname' => 'CAT.M.COD1', 'category' => $category1->id, 'fullname' => 'course1 fullname'));
        $course2 = $this->getDataGenerator()->create_course(
                array('shortname' => 'MI.COD1', 'category' => $category1->id, 'fullname' => 'course2 fullname'));

        // Test1: course1 should return false.
        $result = module_is_intensive($course1->shortname);
        $this->assertEquals(false, $result);

        // Test2: course2 should return true.
        $result = module_is_intensive($course2->shortname);
        $this->assertEquals(true, $result);
    }

    /**
     * Tests for phpunit.
     */
    public function test_get_actual_module () {
        global $DB;

        $this->resetAfterTest(true);

        // Creating users.
        $user1 = $this->getDataGenerator()->create_user(
                array('username' => 'user1'));
        $user2 = $this->getDataGenerator()->create_user(
                array('username' => 'user2'));

        // Creating categories.
        $category1 = $this->getDataGenerator()->create_category(
                array('name' => 'phpunit category 1'));
        $category2 = $this->getDataGenerator()->create_category(
                array('name' => 'phpunit category 2'));
        $category3 = $this->getDataGenerator()->create_category(
                array('name' => 'phpunit category 3'));

        // Creating courses.
        $testcourse1 = $this->getDataGenerator()->create_course(
                array('shortname' => 'CAT1.M.GG', 'category' => $category1->id, 'fullname' => 'course1 fullname'));
        $testcourse2 = $this->getDataGenerator()->create_course(
                array('shortname' => 'CAT1.M.FF', 'category' => $category1->id, 'fullname' => 'course2 fullname'));
        $testcourse3 = $this->getDataGenerator()->create_course(
                array('shortname' => 'CAT2.M.EE', 'category' => $category2->id, 'fullname' => 'course3 fullname'));
        $testcourse4 = $this->getDataGenerator()->create_course(
                array('shortname' => 'CAT2.M.PR', 'category' => $category2->id, 'fullname' => 'course4 fullname'));
        $testcourse5 = $this->getDataGenerator()->create_course(
                array('shortname' => 'CAT3.M.YJS', 'category' => $category3->id, 'fullname' => 'course4 fullname'));

        // Getting the id of the roles.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'teacher'));

        // Enrolling teacher in all courses.
        $this->getDataGenerator()->enrol_user($user1->id, $testcourse1->id, $teacherrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $testcourse2->id, $teacherrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $testcourse3->id, $teacherrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $testcourse4->id, $teacherrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $testcourse5->id, $teacherrole->id, 'manual');

        $prevstart = time() - 20000;
        $prevend = time() + 90000;
        $actualstart = time() - 200;
        $actualend = time() + 90000;
        $nextstart = time() + 60000;
        $nextend = time() + 90000;

        // Enrolling student in c1(prev), c2(actual), c3(actual), c4(next).
        $this->getDataGenerator()->enrol_user($user2->id, $testcourse1->id, $studentrole->id, 'manual', $prevstart, $prevend);
        $this->getDataGenerator()->enrol_user($user2->id, $testcourse2->id, $studentrole->id, 'manual', $actualstart, $actualend);
        $this->getDataGenerator()->enrol_user($user2->id, $testcourse3->id, $studentrole->id, 'manual', $actualstart, $actualend);
        $this->getDataGenerator()->enrol_user($user2->id, $testcourse4->id, $studentrole->id, 'manual', $nextstart, $nextend);
        $this->getDataGenerator()->enrol_user($user2->id, $testcourse5->id, $studentrole->id, 'manual', $nextstart, $nextend);

        // Test1: check cat1. Should return module 2.
        $result = get_actual_module($category1->id, $studentrole->id);
        $this->assertEquals($actualstart, $result);

        // Test2: cat 2 should return module 3.
        $result = get_actual_module($category2->id, $studentrole->id);
        $this->assertEquals($actualstart, $result);

        // Test3: Cat3 should return 0.
        $result = get_actual_module($category3->id, $studentrole->id);
        $this->assertEquals(0, $result);
    }

    /**
     * Tests for phpunit.
     */
    public function test_get_students_course_data () {
        global $DB;

        $this->resetAfterTest(true);

        // Creating users.
        $user1 = $this->getDataGenerator()->create_user(
                array('username' => 'user1'));
        $user2 = $this->getDataGenerator()->create_user(
                array('username' => 'user2'));

        // Creating categories.
        $category1 = $this->getDataGenerator()->create_category(
                array('name' => 'phpunit category 1'));
        $category2 = $this->getDataGenerator()->create_category(
                array('name' => 'intensive category 1'));

        // Creating courses.
        $course1 = $this->getDataGenerator()->create_course(
                array('shortname' => 'CAT1.M.CO1', 'category' => $category1->id, 'fullname' => 'course1 fullname'));
        $course2 = $this->getDataGenerator()->create_course(
                array('shortname' => 'CAT1.M.CO2', 'category' => $category1->id, 'fullname' => 'course2 fullname'));
        $course3 = $this->getDataGenerator()->create_course(
                array('shortname' => 'CAT1.M.CO3', 'category' => $category1->id, 'fullname' => 'course3 fullname'));
        $course4 = $this->getDataGenerator()->create_course(
                array('shortname' => 'CAT1.M.CO4', 'category' => $category1->id, 'fullname' => 'course4 fullname'));
        $course5 = $this->getDataGenerator()->create_course(
                array('shortname' => 'MI.CO1', 'category' => $category2->id, 'fullname' => 'Intensive course1'));

        // Getting the id of the roles.
        $studentroleid = $DB->get_record('role', array('shortname' => 'student'));
        $teacherroleid = $DB->get_record('role', array('shortname' => 'teacher'));

        // Enrolling teacher in all courses.
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, $teacherroleid->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id, $teacherroleid->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $course3->id, $teacherroleid->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $course4->id, $teacherroleid->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $course5->id, $teacherroleid->id, 'manual');

        $prestart = time() - 20000;
        $preend = time() - 10000;
        $actstart = time() - 20;
        $actend = time() + 20;
        $nxtstart = time() + 60000;
        $nxtend = time() + 90000;

        // Enrolling student in c1(prev), c2(actual), c3(next), c5(intensive, next).
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id, $studentroleid->id, 'manual', $prestart, $preend);
        $this->getDataGenerator()->enrol_user($user2->id, $course2->id, $studentroleid->id, 'manual', $actstart, $actend);
        $this->getDataGenerator()->enrol_user($user2->id, $course3->id, $studentroleid->id, 'manual', $nxtstart, $nxtend);
        $this->getDataGenerator()->enrol_user($user2->id, $course5->id, $studentroleid->id, 'manual', $nxtstart, $nxtend);

        $actualmodule = $actstart;

        $c1 = new stdClass();
        $c1->id = $course1->id;
        $c1->shortname = $course1->shortname;
        $c1->fullname = $course1->fullname;
        $c1->timestart = "$prestart";
        $c1->timeend = "$preend";
        $c1->userid = $user2->id;
        $c1->category = $category1->name;
        $c1->date = 'prev';

        $c3 = new stdClass();
        $c3->id = $course3->id;
        $c3->shortname = $course3->shortname;
        $c3->fullname = $course3->fullname;
        $c3->timestart = "$nxtstart";
        $c3->timeend = "$nxtend";
        $c3->userid = $user2->id;
        $c3->category = $category1->name;
        $c3->date = 'next';

        $c4 = new stdClass();
        $c4->id = $course4->id;
        $c4->shortname = $course4->shortname;
        $c4->fullname = $course4->fullname;
        $c4->timestart = '0';
        $c4->timeend = '0';
        $c4->userid = $user1->id;
        $c4->category = $category1->name;
        $c4->date = 'actual';

        $c5 = new stdClass();
        $c5->id = $course5->id;
        $c5->shortname = $course5->shortname;
        $c5->fullname = $course5->fullname;
        $c5->timestart = "$nxtstart";
        $c5->timeend = "$nxtend";
        $c5->userid = $user2->id;
        $c5->category = $category1->name;
        $c5->date = 'actual';

        // Test1: get data from course1.
        $result = get_students_course_data($course1->id, $actualmodule, $studentroleid->id);
        $this->assertEquals($c1->id, $result->id);
        $this->assertEquals($c1->shortname, $result->shortname);
        $this->assertEquals($c1->timestart, $result->timestart);
        $this->assertEquals($c1->timeend, $result->timeend);
        $this->assertEquals($c1->date, $result->date);

        // Test2: get data from course 3.
        $result = get_students_course_data($course3->id, $actualmodule, $studentroleid->id);
        $this->assertEquals($c3->id, $result->id);
        $this->assertEquals($c3->shortname, $result->shortname);
        $this->assertEquals($c3->date, $result->date);

        // Test3: get data from course 4. (no student enrolled, shoud get date->actual.
        $result = get_students_course_data($course4->id, $actualmodule, $studentroleid->id);
        $this->assertEquals($c4->id, $result->id);
        $this->assertEquals($c4->shortname, $result->shortname);
        $this->assertEquals($c4->date, $result->date);

        // Test4: get data from course 5. (Should get date->actual because is intensive).
        $result = get_students_course_data($course5->id, $actualmodule, $studentroleid->id);
        $this->assertEquals($c5->shortname, $result->shortname);
        $this->assertEquals($c5->fullname, $result->fullname);
        $this->assertEquals($c5->timestart, $result->timestart);
        $this->assertEquals($c5->timeend, $result->timeend);
        $this->assertEquals($c5->userid, $result->userid);
        $this->assertEquals($c5->date, $result->date);
    }

    /**
     * Tests for phpunit.
     */
    public function test_add_course_activities () {

        $this->resetAfterTest(true);

        // Creating users.
        $user1 = $this->getDataGenerator()->create_user(
                array('username' => 'unituser1'));

        // Creating categories.
        $category1 = $this->getDataGenerator()->create_category(
                array('name' => 'PHP Unit cat 1'));

        // Creating courses.
        $unitcourse1 = $this->getDataGenerator()->create_course(
                array('shortname' => 'CAT1.M.SS', 'category' => $category1->id, 'fullname' => 'PHP Unit Course 1'));

        // Creating announcements forum and another 2 general ones.
        $ann1 = $this->getDataGenerator()->create_module('forum', array('course' => $unitcourse1->id, 'type' => 'news'));
        $forum1 = $this->getDataGenerator()->create_module('forum', array('course' => $unitcourse1->id, 'type' => 'general'));
        $forum2 = $this->getDataGenerator()->create_module('forum', array('course' => $unitcourse1->id, 'type' => 'general'));

        // Creating 3 assignments.
        $as1 = $this->getDataGenerator()->create_module('assign', array('course' => $unitcourse1->id));
        $as2 = $this->getDataGenerator()->create_module('assign', array('course' => $unitcourse1->id));
        $as3 = $this->getDataGenerator()->create_module('assign', array('course' => $unitcourse1->id));

        // Create course object for param.
        $paramobj = new stdClass();
        $paramobj->id = $unitcourse1->id;
        $paramobj->shortname = $unitcourse1->shortname;
        $paramobj->fullname = $unitcourse1->fullname;
        $paramobj->timestart = 0;
        $paramobj->timeend = 0;
        $paramobj->userid = $user1->id;
        $paramobj->category = $category1->name;
        $paramobj->date = 'next';

        $announce1 = new stdClass();
        $announce1->id = $ann1->id;
        $announce1->name = $ann1->name;
        $announce1->course = $ann1->course;
        $announce1->type = $ann1->type;

        $forumobj1 = new stdClass();
        $forumobj1->id = $forum1->id;
        $forumobj1->name = $forum1->name;
        $forumobj1->course = $forum1->course;
        $forumobj1->type = $forum1->type;

        $forumobj2 = new stdClass();
        $forumobj2->id = $forum2->id;
        $forumobj2->name = $forum2->name;
        $forumobj2->course = $forum2->course;
        $forumobj2->type = $forum2->type;

        $assign1 = new stdClass();
        $assign1->id = $as1->id;
        $assign1->name = $as1->name;
        $assign1->course = $as1->course;

        $assign2 = new stdClass();
        $assign2->id = $as2->id;
        $assign2->name = $as2->name;
        $assign2->course = $as2->course;

        $assign3 = new stdClass();
        $assign3->id = $as3->id;
        $assign3->name = $as3->name;
        $assign3->course = $as3->course;

        $c1 = new stdClass();
        $c1->id = $unitcourse1->id;
        $c1->shortname = $unitcourse1->shortname;
        $c1->fullname = $unitcourse1->fullname;
        $c1->timestart = 0;
        $c1->timeend = 0;
        $c1->userid = $user1->id;
        $c1->category = $category1->name;
        $c1->date = 'next';
        $c1->notices = $announce1;
        $c1->forums = [$forumobj1, $forumobj2];
        $c1->assigns = [$assign1, $assign2, $assign3];

        // Test1: get object with forums and assignments.
        $result = add_course_activities($paramobj);
        $this->assertEquals($c1->notices, $result->notices);
        $this->assertCount(3, $result->assigns);
        $this->assertCount(2, $result->forums);
    }

    /**
     * Tests for phpunit.
     */
    public function test_get_user_courses () {
        global $DB;

        $this->resetAfterTest(true);

        // Creating users.
        $user1 = $this->getDataGenerator()->create_user(
                array('username' => 'user1'));
        $user2 = $this->getDataGenerator()->create_user(
                array('username' => 'user2'));

        // Creating categories.
        $category1 = $this->getDataGenerator()->create_category(
                array('name' => 'phpunit category 1'));
        $category2 = $this->getDataGenerator()->create_category(
                array('name' => 'phpunit category 2'));
        $category3 = $this->getDataGenerator()->create_category(
                array('name' => 'Intensives'));

        // Creating courses.
        $course1 = $this->getDataGenerator()->create_course(
                array('shortname' => 'CAT1.M.C1', 'category' => $category1->id, 'fullname' => 'course1 fullname'));
        $course2 = $this->getDataGenerator()->create_course(
                array('shortname' => 'CAT1.M.C2', 'category' => $category1->id, 'fullname' => 'course2 fullname'));
        $course3 = $this->getDataGenerator()->create_course(
                array('shortname' => 'CAT2.M.C3', 'category' => $category2->id, 'fullname' => 'course3 fullname'));
        $course4 = $this->getDataGenerator()->create_course(
                array('shortname' => 'CAT2.M.C4', 'category' => $category2->id, 'fullname' => 'course4 fullname'));
        $course5 = $this->getDataGenerator()->create_course(
                array('shortname' => 'MI.C1', 'category' => $category3->id, 'fullname' => 'Intensive course'));

        // Creating notices for all courses.
        $ann1 = $this->getDataGenerator()->create_module('forum', array('course' => $course1->id, 'type' => 'news'));
        $ann2 = $this->getDataGenerator()->create_module('forum', array('course' => $course2->id, 'type' => 'news'));
        $ann3 = $this->getDataGenerator()->create_module('forum', array('course' => $course3->id, 'type' => 'news'));
        $ann4 = $this->getDataGenerator()->create_module('forum', array('course' => $course4->id, 'type' => 'news'));
        $ann5 = $this->getDataGenerator()->create_module('forum', array('course' => $course5->id, 'type' => 'news'));

        // Creating forums.
        $forum1 = $this->getDataGenerator()->create_module('forum', array('course' => $course1->id, 'type' => 'general'));
        $forum2 = $this->getDataGenerator()->create_module('forum', array('course' => $course1->id, 'type' => 'general'));
        $forum3 = $this->getDataGenerator()->create_module('forum', array('course' => $course1->id, 'type' => 'general'));
        $forum4 = $this->getDataGenerator()->create_module('forum', array('course' => $course3->id, 'type' => 'general'));

        // Creating assignments.
        $as1 = $this->getDataGenerator()->create_module('assign', array('course' => $course2->id));
        $as2 = $this->getDataGenerator()->create_module('assign', array('course' => $course2->id));
        $as3 = $this->getDataGenerator()->create_module('assign', array('course' => $course5->id));

        // Getting the id of the roles.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'teacher'));

        // Enrolling teacher in all courses.
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, $teacherrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id, $teacherrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $course3->id, $teacherrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $course4->id, $teacherrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $course5->id, $teacherrole->id, 'manual');

        $prevstart = time() - 20000;
        $prevend = time() + 110000;
        $actualstart = time() - 200;
        $actualend = time() + 90200;
        $nextstart = time() + 60000;
        $nextend = time() + 90000;

        // Enrolling student in c1(prev), c2(actual), c3(next), c5(intensive, next).
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id, $studentrole->id, 'manual', $prevstart, $prevend);
        $this->getDataGenerator()->enrol_user($user2->id, $course2->id, $studentrole->id, 'manual', $actualstart, $actualend);
        $this->getDataGenerator()->enrol_user($user2->id, $course3->id, $studentrole->id, 'manual', $actualstart, $actualend);
        $this->getDataGenerator()->enrol_user($user2->id, $course5->id, $studentrole->id, 'manual', $nextstart, $nextend);

        // Creating test objects.
        // Activities.
        $an1 = new stdClass();
        $an1->id = $ann1->id;
        $an1->name = $ann1->name;
        $an1->course = $ann1->course;
        $an1->type = $ann1->type;

        $an2 = new stdClass();
        $an2->id = $ann2->id;
        $an2->name = $ann2->name;
        $an2->course = $ann2->course;
        $an2->type = $ann2->type;

        $an3 = new stdClass();
        $an3->id = $ann3->id;
        $an3->name = $ann3->name;
        $an3->course = $ann3->course;
        $an3->type = $ann3->type;

        $an4 = new stdClass();
        $an4->id = $ann4->id;
        $an4->name = $ann4->name;
        $an4->course = $ann4->course;
        $an4->type = $ann4->type;

        $an5 = new stdClass();
        $an5->id = $ann5->id;
        $an5->name = $ann5->name;
        $an5->course = $ann5->course;
        $an5->type = $ann5->type;

        $f1 = new stdClass();
        $f1->id = $forum1->id;
        $f1->name = $forum1->name;
        $f1->course = $forum1->course;
        $f1->type = $forum1->type;

        $f2 = new stdClass();
        $f2->id = $forum2->id;
        $f2->name = $forum2->name;
        $f2->course = $forum2->course;
        $f2->type = $forum2->type;

        $f3 = new stdClass();
        $f3->id = $forum3->id;
        $f3->name = $forum3->name;
        $f3->course = $forum3->course;
        $f3->type = $forum3->type;

        $f4 = new stdClass();
        $f4->id = $forum4->id;
        $f4->name = $forum4->name;
        $f4->course = $forum4->course;
        $f4->type = $forum4->type;

        $a1 = new stdClass();
        $a1->id = $as1->id;
        $a1->name = $as1->name;
        $a1->course = $as1->course;

        $a2 = new stdClass();
        $a2->id = $as2->id;
        $a2->name = $as2->name;
        $a2->course = $as2->course;

        $a3 = new stdClass();
        $a3->id = $as3->id;
        $a3->name = $as3->name;
        $a3->course = $as3->course;

        // Courses.
        $c1 = new stdClass();
        $c1->id = $course1->id;
        $c1->shortname = $course1->shortname;
        $c1->fullname = $course1->fullname;
        $c1->timestart = "$prevstart";
        $c1->timeend = "$prevend";
        $c1->userid = $user2->id;
        $c1->category = $category1->name;
        $c1->date = 'prev';
        $c1->notices = $an1;
        $c1->forums = [$f1, $f2, $f3];
        $c1->assigns = [];

        $c2 = new stdClass();
        $c2->id = $course2->id;
        $c2->shortname = $course2->shortname;
        $c2->fullname = $course2->fullname;
        $c2->timestart = "$actualstart";
        $c2->timeend = "$actualend";
        $c2->userid = $user2->id;
        $c2->category = $category1->name;
        $c2->date = 'actual';
        $c2->notices = $an2;
        $c2->forums = [];
        $c2->assigns = [$a1, $a2];

        $c3 = new stdClass();
        $c3->id = $course3->id;
        $c3->shortname = $course3->shortname;
        $c3->fullname = $course3->fullname;
        $c3->timestart = "$actualstart";
        $c3->timeend = "$actualend";
        $c3->userid = $user2->id;
        $c3->category = $category2->name;
        $c3->date = 'actual';
        $c3->notices = $an3;
        $c3->forums = [$f4];
        $c3->assigns = [];

        // Has no student enrolled, so should go to actual.
        $c4 = new stdClass();
        $c4->id = $course4->id;
        $c4->shortname = $course4->shortname;
        $c4->fullname = $course4->fullname;
        $c4->timestart = '0';
        $c4->timeend = '0';
        $c4->userid = $user1->id;
        $c4->category = $category2->name;
        $c4->date = 'actual';
        $c4->notices = $an4;
        $c4->forums = [];
        $c4->assigns = [];

        // Is intensive, so it goes to actual.
        $c5 = new stdClass();
        $c5->id = $course5->id;
        $c5->shortname = $course5->shortname;
        $c5->fullname = $course5->fullname;
        $c5->timestart = "$nextstart";
        $c5->timeend = "$nextend";
        $c5->userid = $user2->id;
        $c5->category = $category3->name;
        $c5->date = 'actual';
        $c5->notices = $an5;
        $c5->forums = [];
        $c5->assigns = [$a3];

        // Test1: use teacher id.
        $result = get_user_courses($user1->id);
        $prevarray = [$c1];
        $actualarray = [$c2, $c3, $c4, $c5];
        $nextarray = [];

        $expected = ['actual' => $actualarray, 'prev' => $prevarray, 'next' => $nextarray];

        $this->assertCount(1, $result['prev']);
        $this->assertCount(4, $result['actual']);
        $this->assertCount(0, $result['next']);
        $this->assertEquals($expected['prev'][0]->id, $result['prev'][0]->id);
        $this->assertEquals($expected['prev'][0]->notices, $result['prev'][0]->notices);
        $this->assertCount(3, $result['prev'][0]->forums);
        $this->assertCount(0, $result['prev'][0]->assigns);
        $this->assertEquals($expected['actual'][0]->id, $result['actual'][0]->id);
        $this->assertCount(0, $result['actual'][0]->forums);
        $this->assertCount(2, $result['actual'][0]->assigns);
        $this->assertEquals($expected['actual'][1]->id, $result['actual'][1]->id);
        $this->assertCount(1, $result['actual'][1]->forums);
        $this->assertEquals($expected['actual'][2]->id, $result['actual'][2]->id);
        $this->assertEquals($expected['actual'][3]->id, $result['actual'][3]->id);

        // Test2: use student id.
        $result = get_user_courses($user2->id);
        $this->assertCount(0, $result['prev']);
        $this->assertCount(0, $result['actual']);
        $this->assertCount(0, $result['actual']);
    }

    /**
     * Tests for phpunit.
     */
    public function test_get_grade_category () {

        global $DB;

        $this->resetAfterTest();

        $manualplugin = self::enable_enrol_plugin();
        $this->assertNotEmpty($manualplugin);
        $studentrole = self::get_student_role();

        $user1 = $this->getDataGenerator()->create_user(array('firstname' => "USUARIO 1"));

        // Creating a category.
        $category1 = $this->getDataGenerator()->create_category(
                array('name' => 'Category One'));

        // Creating courses.
        $module1 = $this->getDataGenerator()->create_course(
                array('shortname' => "CAT.M.CURSO1", 'category' => $category1->id));
        $module2 = $this->getDataGenerator()->create_course(
                array('shortname' => "CAT.M.CURSO2", 'category' => $category1->id));
        $module3 = $this->getDataGenerator()->create_course(
                array('shortname' => "CAT.M.CURSO3", 'category' => $category1->id));
        $module4 = $this->getDataGenerator()->create_course(
                array('shortname' => "CAT.M.CURSO4", 'category' => $category1->id));

        $manualinstance = self::create_manual_instance($module1->id);
        $manualplugin->enrol_user($manualinstance, $user1->id, $studentrole->id, 1493203999, 1494303999);
        $manualinstance2 = self::create_manual_instance($module2->id);
        $manualplugin->enrol_user($manualinstance2, $user1->id, $studentrole->id, 1493103999, 1494302999);
        $manualinstance3 = self::create_manual_instance($module3->id);
        $manualplugin->enrol_user($manualinstance3, $user1->id, $studentrole->id, 1493103999, 1494302999);
        $manualinstance4 = self::create_manual_instance($module4->id);
        $manualplugin->enrol_user($manualinstance4, $user1->id, $studentrole->id, 1493103999, 1494302999);

        // Creating grades for each course.
        $grade = $this->getDataGenerator()->create_grade_item(array('itemtype' => 'course', 'courseid' => $module1->id));
        $this->assertNotEmpty($grade);
        $grades = new stdClass();
        $grades->itemid = $grade->id;
        $grades->finalgrade = 88;
        $grades->feedback = 'Texto de informacion';
        $grades->userid = $user1->id;

        $DB->insert_record('grade_grades', $grades, false);

        $grade2 = $this->getDataGenerator()->create_grade_item(array('itemtype' => 'course', 'courseid' => $module2->id));
        $this->assertNotEmpty($grade2);
        $grades2 = new stdClass();
        $grades2->itemid = $grade2->id;
        $grades2->finalgrade = 48;
        $grades2->feedback = 'Texto de informacion';
        $grades2->userid = $user1->id;

        $DB->insert_record('grade_grades', $grades2, false);

        $grade3 = $this->getDataGenerator()->create_grade_item(array('itemtype' => 'course', 'courseid' => $module3->id));
        $this->assertNotEmpty($grade3);
        $grades3 = new stdClass();
        $grades3->itemid = $grade3->id;
        $grades3->finalgrade = 27;
        $grades3->feedback = 'Texto de informacion';
        $grades3->userid = $user1->id;

        $DB->insert_record('grade_grades', $grades3, false);

        $average0 = get_grade_category($category1->id, $user1->id);
        $this->assertNotEmpty($average0);
        $this->assertEquals(-1, $average0);

        $grade4 = $this->getDataGenerator()->create_grade_item(array('itemtype' => 'course', 'courseid' => $module4->id));
        $this->assertNotEmpty($grade4);
        $grades4 = new stdClass();
        $grades4->itemid = $grade4->id;
        $grades4->finalgrade = 62;
        $grades4->feedback = 'Texto de informacion';
        $grades4->userid = $user1->id;

        $DB->insert_record('grade_grades', $grades4, false);

        $average = get_grade_category($category1->id, $user1->id);
        $this->assertNotEmpty($average);
        $this->assertEquals(5.63, $average);
    }

    /**
     * Tests for phpunit.
     */
    public function test_user_repeat_category () {
        global $DB;

        $this->resetAfterTest();

        $manualplugin = self::enable_enrol_plugin();
        $this->assertNotEmpty($manualplugin);
        $studentrole = self::get_student_role();
        $rawgrade = 100;
        $today = time();
        $year = 31557600;
        $pasttime = $today - $year;

        $student1 = $this->getDataGenerator()->create_user(array('firstname' => "USUARIO 1"));

        // Creating a category.
        $category1 = $this->getDataGenerator()->create_category(
                array('name' => 'Category One'));

        // Creating courses.
        $course1 = $this->getDataGenerator()->create_course(
                array('shortname' => "CAT.M.CURSO1", 'category' => $category1->id));
        $course2 = $this->getDataGenerator()->create_course(
                array('shortname' => "CAT.M.CURSO2", 'category' => $category1->id));
        $course3 = $this->getDataGenerator()->create_course(
                array('shortname' => "CAT.M.CURSO3", 'category' => $category1->id));
        $course4 = $this->getDataGenerator()->create_course(
                array('shortname' => "CAT.M.CURSO4", 'category' => $category1->id));

        $manualinstance = self::create_manual_instance($course1->id);
        $manualplugin->enrol_user($manualinstance, $student1->id, $studentrole->id, $today, $today + $year);
        $manualinstance2 = self::create_manual_instance($course2->id);
        $manualplugin->enrol_user($manualinstance2, $student1->id, $studentrole->id, $today + 10000, $today + $year);
        $manualinstance3 = self::create_manual_instance($course3->id);
        $manualplugin->enrol_user($manualinstance3, $student1->id, $studentrole->id, $today + 20000, $today + $year);
        $manualinstance4 = self::create_manual_instance($course4->id);
        $manualplugin->enrol_user($manualinstance4, $student1->id, $studentrole->id, $pasttime - 10000, $pasttime);

        // Creating grades for each course.
        $grade = $this->getDataGenerator()->create_grade_item(array('itemtype' => 'course', 'courseid' => $course1->id));
        $this->assertNotEmpty($grade);
        $grades = new stdClass();
        $grades->itemid = $grade->id;
        $grades->finalgrade = 92;
        $grades->feedback = 'Texto de informacion';
        $grades->userid = $student1->id;

        $DB->insert_record('grade_grades', $grades, false);

        // Grade History course 1.
        $gradehistory = new stdClass();
        $gradehistory->action = 1;
        $gradehistory->oldid = $grade->id;
        $gradehistory->source = 'mod/quiz';
        $gradehistory->timemodified = $today;
        $gradehistory->loggeduser = $student1->id;
        $gradehistory->itemid = $grade->id;
        $gradehistory->userid = $student1->id;
        $gradehistory->rawgrade = $rawgrade;
        $gradehistory->rawgrademax = $rawgrade;
        $gradehistory->rawgrademin = 0;
        $gradehistory->usermodified = $student1->id;
        $gradehistory->finalgrade = $grades->finalgrade;

        $DB->insert_record('grade_grades_history', $gradehistory, false);

        // Grade course 2.
        $grade2 = $this->getDataGenerator()->create_grade_item(array('itemtype' => 'course', 'courseid' => $course2->id));
        $this->assertNotEmpty($grade2);
        $grades2 = new stdClass();
        $grades2->itemid = $grade2->id;
        $grades2->finalgrade = 69;
        $grades2->feedback = 'Texto de informacion';
        $grades2->userid = $student1->id;

        $DB->insert_record('grade_grades', $grades2, false);

        // Grade History course 2.
        $gradehistory2 = new stdClass();
        $gradehistory2->action = 1;
        $gradehistory2->oldid = $grade2->id;
        $gradehistory2->source = 'mod/quiz';
        $gradehistory2->timemodified = $today + 11000;
        $gradehistory2->loggeduser = $student1->id;
        $gradehistory2->itemid = $grade2->id;
        $gradehistory2->userid = $student1->id;
        $gradehistory2->rawgrade = $rawgrade;
        $gradehistory2->rawgrademax = $rawgrade;
        $gradehistory2->rawgrademin = 0;
        $gradehistory2->usermodified = $student1->id;
        $gradehistory2->finalgrade = $grades2->finalgrade;

        $DB->insert_record('grade_grades_history', $gradehistory2, false);

        // Testing with actual courses.
        $result = user_repeat_category($student1->id, $course1->category);
        $this->assertFalse($result);

        // Grade course 3.
        $grade3 = $this->getDataGenerator()->create_grade_item(array('itemtype' => 'course', 'courseid' => $course3->id));
        $this->assertNotEmpty($grade3);
        $grades3 = new stdClass();
        $grades3->itemid = $grade3->id;
        $grades3->finalgrade = 72;
        $grades3->feedback = 'Texto de informacion';
        $grades3->userid = $student1->id;

        $DB->insert_record('grade_grades', $grades3, false);

        // Grade History course 3.
        $gradehistory3 = new stdClass();
        $gradehistory3->action = 1;
        $gradehistory3->oldid = $grade3->id;
        $gradehistory3->source = 'mod/quiz';
        $gradehistory3->timemodified = $today + 21000;
        $gradehistory3->loggeduser = $student1->id;
        $gradehistory3->itemid = $grade3->id;
        $gradehistory3->userid = $student1->id;
        $gradehistory3->rawgrade = $rawgrade;
        $gradehistory3->rawgrademax = $rawgrade;
        $gradehistory3->rawgrademin = 0;
        $gradehistory3->usermodified = $student1->id;
        $gradehistory3->finalgrade = $grades3->finalgrade;

        $DB->insert_record('grade_grades_history', $gradehistory3, false);

        // Grade course 4.
        $grade4 = $this->getDataGenerator()->create_grade_item(array('itemtype' => 'course', 'courseid' => $course4->id));
        $this->assertNotEmpty($grade4);
        $grades4 = new stdClass();
        $grades4->itemid = $grade4->id;
        $grades4->finalgrade = 36;
        $grades4->feedback = 'Texto de informacion';
        $grades4->userid = $student1->id;

        $DB->insert_record('grade_grades', $grades4, false);

        // Grade history course 4.
        $gradehistory4 = new stdClass();
        $gradehistory4->action = 1;
        $gradehistory4->oldid = $grade4->id;
        $gradehistory4->source = 'mod/quiz';
        $gradehistory4->timemodified = $pasttime - 10000;
        $gradehistory4->loggeduser = $student1->id;
        $gradehistory4->itemid = $grade4->id;
        $gradehistory4->userid = $student1->id;
        $gradehistory4->rawgrade = $rawgrade;
        $gradehistory4->rawgrademax = $rawgrade;
        $gradehistory4->rawgrademin = 0;
        $gradehistory4->usermodified = $student1->id;
        $gradehistory4->finalgrade = $grades4->finalgrade;

        $DB->insert_record('grade_grades_history', $gradehistory4, false);

        // Testing with a course convalidate for other year.
        $result2 = user_repeat_category($student1->id, $course1->category);
        $this->assertTrue($result2);
    }

    /**
     * Tests for phpunit.
     */
    public function test_get_dashboard_student_data() {
        global $DB;
        $this->resetAfterTest(true);

        // Creating a user.
        $user1 = $this->getDataGenerator()->create_user(array('username' => 'user1', 'email' => 'user1@php.com'));

        // Creating several categories for future use.
        $category1 = $this->getDataGenerator()->create_category(array('name' => 'Category 1'));
        $category2 = $this->getDataGenerator()->create_category(array('name' => 'Category 2'));
        $category3 = $this->getDataGenerator()->create_category(array('name' => 'Category 3'));

        // Creating courses related to the categories above.
        $course1 = $this->getDataGenerator()->create_course(array('shortname' => 'C1.M.Course 1',
                                                                  'fullname' => 'C1.M.Course 1', 'category' => $category1->id));
        $course2 = $this->getDataGenerator()->create_course(array('shortname' => 'C1.M.Course 2',
                                                                  'fullname' => 'C1.M.Course 2', 'category' => $category1->id));
        $course3 = $this->getDataGenerator()->create_course(array('shortname' => 'C2.M.Course 3',
                                                                  'fullname' => 'C1.M.Course 3', 'category' => $category2->id));
        $course4 = $this->getDataGenerator()->create_course(array('shortname' => 'C2.M.Course 4',
                                                                  'fullname' => 'C1.M.Course 4', 'category' => $category2->id));
        $course5 = $this->getDataGenerator()->create_course(array('shortname' => 'C3.M.Course 5',
                                                                  'fullname' => 'C1.M.Course 5', 'category' => $category3->id));

        // Getting the id of the roles.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));

        // Enrol user 1 as a student in all the courses.
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $course3->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $course4->id, $teacherrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $course5->id, $studentrole->id, 'manual');

        // Test user1 (Expected results = true).
        $result1 = get_dashboard_student_data($user1->id);
        // Array with 3 categories.
        $this->assertCount(3, $result1);
        // Cat 1 has 2 courseinfo objects, cat 2 has 1 courseinfo objects (he is enroled only in c3 as student) and cat 3 has 1.
        $this->assertCount(2, $result1[$category1->id]->courses);
        $this->assertCount(1, $result1[$category2->id]->courses);
        $this->assertCount(1, $result1[$category3->id]->courses);
    }

    /**
     * Tests for phpunit.
     */
    public function test_get_dashboard_teacher_data() {
        global $DB;
        $this->resetAfterTest(true);

        // Creating a user.
        $user1 = $this->getDataGenerator()->create_user(array('username' => 'user1', 'email' => 'user1@php.com'));

        // Creating several categories for future use.
        $category1 = $this->getDataGenerator()->create_category(array('name' => 'Category 1'));
        $category2 = $this->getDataGenerator()->create_category(array('name' => 'Category 2'));
        $category3 = $this->getDataGenerator()->create_category(array('name' => 'Category 3'));

        // Creating courses related to the categories above.
        $course1 = $this->getDataGenerator()->create_course(array('shortname' => 'C1.M.Course 1',
                                                                  'fullname' => 'C1.M.Course 1', 'category' => $category1->id));
        $course2 = $this->getDataGenerator()->create_course(array('shortname' => 'C1.M.Course 2',
                                                                  'fullname' => 'C1.M.Course 2', 'category' => $category1->id));
        $course3 = $this->getDataGenerator()->create_course(array('shortname' => 'C2.M.Course 3',
                                                                  'fullname' => 'C1.M.Course 3', 'category' => $category2->id));
        $course4 = $this->getDataGenerator()->create_course(array('shortname' => 'C2.M.Course 4',
                                                                  'fullname' => 'C1.M.Course 4', 'category' => $category2->id));
        $course5 = $this->getDataGenerator()->create_course(array('shortname' => 'C3.M.Course 5',
                                                                  'fullname' => 'C1.M.Course 5', 'category' => $category3->id));

        // Getting the id of the roles.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));

        // Enrol user 1 as a student in all the courses.
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, $teacherrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $course3->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $course4->id, $teacherrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user1->id, $course5->id, $teacherrole->id, 'manual');

        // Test user1 (Expected results = true).
        $result1 = get_dashboard_teacher_data($user1->id);
        // Array with 3 objects.
        $this->assertCount(3, $result1->courses);
        $this->assertEquals("C1.M.Course 1", $result1->courses[$course1->id]->coursename);
        $this->assertEquals($course1->id, $result1->courses[$course1->id]->courseid);
        $this->assertEquals("C1.M.Course 4", $result1->courses[$course4->id]->coursename);
        $this->assertEquals($category2->name, $result1->courses[$course4->id]->coursecatname);
        $this->assertEquals("C1.M.Course 5", $result1->courses[$course5->id]->coursename);
    }

    /**
     * Tests for phpunit.
     */
    public function test_check_user_is_teacher() {
        global $DB;
        $this->resetAfterTest(true);

        // Creating several categories for future use.
        $category1 = $this->getDataGenerator()->create_category(array('name' => 'Category 1'));
        $category2 = $this->getDataGenerator()->create_category(array('name' => 'Category 2'));

        // Creating a few users.
        $user1 = $this->getDataGenerator()->create_user(array('username' => 'user1'));
        $user2 = $this->getDataGenerator()->create_user(array('username' => 'user2'));
        $user3 = $this->getDataGenerator()->create_user(array('username' => 'user3'));

        // Creating a few courses.
        $course1 = $this->getDataGenerator()->create_course(array('shortname' => 'Course1', 'category' => $category1->id));
        $course2 = $this->getDataGenerator()->create_course(array('shortname' => 'Course2', 'category' => $category2->id));

        // Getting the id of the roles.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));

        // Course 1 enrols: user1 as teacher user2 and user3 as students.
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, $teacherrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user3->id, $course1->id, $studentrole->id, 'manual');

        // Course 2 enrols: user1, user3 as teachers and user2 and student.
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id, $teacherrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user2->id, $course2->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user3->id, $course2->id, $teacherrole->id, 'manual');

        // Test user1 (Expected results = true).
        $result1 = check_user_is_teacher($user1->id);
        $this->assertTrue($result1);

        // Test user2 (Expected results = false).
        $result2 = check_user_is_teacher($user2->id);
        $this->assertFalse($result2);

        // Test user3 (Expected results = true).
        $result3 = check_user_is_teacher($user3->id);
        $this->assertTrue($result3);
    }

    /**
     * Tests for phpunit.
     */
    public function test_get_dashboard_course_imagepath() {
        global $DB;
        global $CFG;

        $this->resetAfterTest(true);

        // Creating two courses.
        $course1 = $this->getDataGenerator()->create_course(array('shortname' => 'Course1'));
        $course2 = $this->getDataGenerator()->create_course(array('shortname' => 'Course2'));
        $context = context_course::instance($course1->id);

        // Setting up the file in the db.
        $image = new stdclass();
        $image->contextid = $context->id;
        $image->component = 'course';
        $image->filearea = 'overviewfiles';
        $image->filesize = 50;
        $image->filename = 'cover.jpeg';
        $image->itemid = 0;
        $image->timecreated = time();
        $image->timemodified = time();
        $image->sortorder = 0;

        $record = $DB->insert_record('files', $image, false);
        $this->assertNotEmpty($record);
        // Testing the function with course1 (expected result: .../cover.jpeg).
        $result1 = get_dashboard_course_imagepath($course1->id);
        $this->assertEquals($CFG->wwwroot . "/pluginfile.php/$image->contextid/course/overviewfiles/$image->filename", $result1);

        // Testing the function with course2 (expected result: .../course_overview_default.png).
        $result2 = get_dashboard_course_imagepath($course2->id);
        $this->assertEquals($CFG->wwwroot . "/local/eudecustom/images/course_overview_default.png", $result2);

    }

    /**
     * This function returns the completion number of an user in a course
     *
     * @param int $userid
     * @param int $courseid
     * @return string $data percent of course completion
     */
    public function test_get_dashboard_course_completion() {
        global $DB;

        $this->resetAfterTest(true);

        // Add a course that supports completion.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));

        // Enrol a user in the course.
        $user = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, $studentrole->id);

        // Add four activities that use completion.
        $assign = $this->getDataGenerator()->create_module('assign', array('course' => $course->id), array('completion' => 1));
        $data = $this->getDataGenerator()->create_module('data', array('course' => $course->id), array('completion' => 1));
        $this->getDataGenerator()->create_module('forum', array('course' => $course->id), array('completion' => 1));
        $this->getDataGenerator()->create_module('forum', array('course' => $course->id), array('completion' => 1));

        // Add an activity that does *not* use completion.
        $this->getDataGenerator()->create_module('assign', array('course' => $course->id));

        // Mark two of them as completed for a user.
        $cmassign = get_coursemodule_from_id('assign', $assign->cmid);
        $cmdata = get_coursemodule_from_id('data', $data->cmid);
        $completion = new completion_info($course);
        $completion->update_state($cmassign, COMPLETION_COMPLETE, $user->id);
        $completion->update_state($cmdata, COMPLETION_COMPLETE, $user->id);

        // Now, mark the course as completed.
        $ccompletion = new completion_completion(array('course' => $course->id, 'userid' => $user->id));
        $ccompletion->mark_complete();

        // Check we have received valid data.
        // The course completion takes priority, so should return 100.
        $this->assertEquals('100', \core_completion\progress::get_course_progress_percentage($course, $user->id));

        // Testing the function with $timestart1 (expected result: "100").
        $result1 = get_dashboard_course_completion($user->id, $course->id);
        $this->assertEquals("100", $result1);

        // Testing the function with $timestart1 (expected result: "100").
        $result2 = get_dashboard_course_completion($user2->id, $course->id);
        $this->assertEquals("", $result2);
    }

    /**
     * Tests for phpunit.
     */
    public function test_check_dashboard_course_incourse() {
        $this->resetAfterTest(true);

        $timestart1 = time() + 100000;
        $timestart2 = time() - 100000;
        $timeend = time() + 600000;

        // Testing the function with $timestart1 (expected result: "").
        $result1 = check_dashboard_course_incourse($timestart1, $timeend);
        $this->assertEquals("", $result1);

        // Testing the function with $timestart1 (expected result: incourse).
        $result2 = check_dashboard_course_incourse($timestart2, $timeend);
        $this->assertEquals(" incourse", $result2);
    }

    /**
     * Tests for phpunit.
     */
    public function test_check_dashboard_course_pending() {
        $this->resetAfterTest(true);

        $timestart1 = time() + 100000;
        $timestart2 = time() - 100000;

        // Testing the function with $timestart1 (expected result: pending).
        $result1 = check_dashboard_course_pending($timestart1);
        $this->assertEquals(" pending", $result1);

        // Testing the function with $timestart1 (expected result: "").
        $result2 = check_dashboard_course_pending($timestart2);
        $this->assertEquals("", $result2);
    }

    /**
     * Tests for phpunit.
     */
    public function test_check_dashboard_course_failed() {
        global $DB;
        $this->resetAfterTest(true);

        $manualplugin = self::enable_enrol_plugin();
        $this->assertNotEmpty($manualplugin);
        $studentrole = self::get_student_role();
        $today = time();
        $year = 31557600;

        $student1 = $this->getDataGenerator()->create_user(array('firstname' => "USUARIO 1"));

        // Creating a category.
        $category1 = $this->getDataGenerator()->create_category(array('name' => 'Category One'));

        // Creating courses.
        $course1 = $this->getDataGenerator()->create_course(array('shortname' => "CAT.M.CURSO1", 'category' => $category1->id));
        $course2 = $this->getDataGenerator()->create_course(array('shortname' => "CAT.M.CURSO2", 'category' => $category1->id));

        $manualinstance = self::create_manual_instance($course1->id);
        $manualplugin->enrol_user($manualinstance, $student1->id, $studentrole->id, $today, $today + $year);
        $manualinstance2 = self::create_manual_instance($course2->id);
        $manualplugin->enrol_user($manualinstance2, $student1->id, $studentrole->id, $today + 10000, $today + $year);

        // Creating grades for each course.
        $grade = $this->getDataGenerator()->create_grade_item(array('itemtype' => 'course', 'courseid' => $course1->id));
        $this->assertNotEmpty($grade);
        $grade->needsupdate = 0;
        $grade->gradepass = 50;
        $DB->update_record('grade_items', $grade);
        $grades = new stdClass();
        $grades->itemid = $grade->id;
        $grades->finalgrade = 92;
        $grades->grademax = 100;
        $grades->feedback = 'Texto de informacion';
        $grades->userid = $student1->id;

        $DB->insert_record('grade_grades', $grades, false);

        // Grade course 2.
        $grade2 = $this->getDataGenerator()->create_grade_item(array('itemtype' => 'course', 'courseid' => $course2->id));
        $grade2->needsupdate = 0;
        $grade2->gradepass = 50;
        $DB->update_record('grade_items', $grade2);
        $this->assertNotEmpty($grade2);
        $grades2 = new stdClass();
        $grades2->itemid = $grade2->id;
        $grades2->finalgrade = 39;
        $grades2->grademax = 100;
        $grades2->feedback = 'Texto de informacion';
        $grades2->userid = $student1->id;

        $DB->insert_record('grade_grades', $grades2, false);

        // Testing the user1 in course1 (expected result: "").
        $result1 = check_dashboard_course_failed($student1->id, $course1->id);
        $this->assertNotEquals(" failed", $result1);

        // Testing the user1 in course2 (expected result: "convalidated").
        $result2 = check_dashboard_course_failed($student1->id, $course2->id);
        $this->assertEquals(" failed", $result2);
    }

    /**
     * Tests for phpunit.
     */
    public function test_check_dashboard_course_passed() {
        global $DB;
        $this->resetAfterTest(true);

        $manualplugin = self::enable_enrol_plugin();
        $this->assertNotEmpty($manualplugin);
        $studentrole = self::get_student_role();
        $today = time();
        $year = 31557600;

        $student1 = $this->getDataGenerator()->create_user(array('firstname' => "USUARIO 1"));

        // Creating a category.
        $category1 = $this->getDataGenerator()->create_category(array('name' => 'Category One'));

        // Creating courses.
        $course1 = $this->getDataGenerator()->create_course(array('shortname' => "CAT.M.CURSO1", 'category' => $category1->id));
        $course2 = $this->getDataGenerator()->create_course(array('shortname' => "CAT.M.CURSO2", 'category' => $category1->id));

        $manualinstance = self::create_manual_instance($course1->id);
        $manualplugin->enrol_user($manualinstance, $student1->id, $studentrole->id, $today, $today + $year);
        $manualinstance2 = self::create_manual_instance($course2->id);
        $manualplugin->enrol_user($manualinstance2, $student1->id, $studentrole->id, $today + 10000, $today + $year);

        // Creating grades for each course.
        $grade = $this->getDataGenerator()->create_grade_item(array('itemtype' => 'course', 'courseid' => $course1->id));
        $this->assertNotEmpty($grade);
        $grade->needsupdate = 0;
        $grade->gradepass = 50;
        $DB->update_record('grade_items', $grade);
        $grades = new stdClass();
        $grades->itemid = $grade->id;
        $grades->finalgrade = 92;
        $grades->feedback = 'Texto de informacion';
        $grades->userid = $student1->id;

        $DB->insert_record('grade_grades', $grades, false);

        // Grade course 2.
        $grade2 = $this->getDataGenerator()->create_grade_item(array('itemtype' => 'course', 'courseid' => $course2->id));
        $grade2->needsupdate = 0;
        $grade2->gradepass = 50;
        $DB->update_record('grade_items', $grade2);
        $this->assertNotEmpty($grade2);
        $grades2 = new stdClass();
        $grades2->itemid = $grade2->id;
        $grades2->finalgrade = 39;
        $grades2->feedback = 'Texto de informacion';
        $grades2->userid = $student1->id;

        $DB->insert_record('grade_grades', $grades2, false);

        // Testing the user1 in course1 (expected result: "").
        $result1 = check_dashboard_course_passed($student1->id, $course1->id);
        $this->assertEquals(" passed", $result1);

        // Testing the user1 in course2 (expected result: "passed").
        $result2 = check_dashboard_course_passed($student1->id, $course2->id);
        $this->assertNotEquals(" passed", $result2);
    }

    /**
     * Tests for phpunit.
     */
    public function test_check_dashboard_course_convalidated() {
        global $DB;
        $this->resetAfterTest(true);

        $manualplugin = self::enable_enrol_plugin();
        $this->assertNotEmpty($manualplugin);
        $studentrole = self::get_student_role();
        $today = time();
        $year = 31557600;

        $student1 = $this->getDataGenerator()->create_user(array('firstname' => "USUARIO 1"));

        // Creating a category.
        $category1 = $this->getDataGenerator()->create_category(array('name' => 'Category One'));

        // Creating courses.
        $course1 = $this->getDataGenerator()->create_course(array('shortname' => "CAT.M.CURSO1", 'category' => $category1->id));
        $course2 = $this->getDataGenerator()->create_course(array('shortname' => "CAT.M.CURSO2", 'category' => $category1->id));

        $manualinstance = self::create_manual_instance($course1->id);
        $manualplugin->enrol_user($manualinstance, $student1->id, $studentrole->id, $today, $today + $year);
        $manualinstance2 = self::create_manual_instance($course2->id);
        $manualplugin->enrol_user($manualinstance2, $student1->id, $studentrole->id, $today + 10000, $today + $year);

        // Creating grades for each course.
        $grade = $this->getDataGenerator()->create_grade_item(array('itemtype' => 'course', 'courseid' => $course1->id));
        $this->assertNotEmpty($grade);
        $grade->needsupdate = 0;
        $grade->gradepass = 50;
        $DB->update_record('grade_items', $grade);
        $grades = new stdClass();
        $grades->itemid = $grade->id;
        $grades->finalgrade = 92;
        $grades->feedback = 'Texto de informacion';
        $grades->userid = $student1->id;

        $DB->insert_record('grade_grades', $grades, false);

        // Grade course 2.
        $grade2 = $this->getDataGenerator()->create_grade_item(array('itemtype' => 'course', 'courseid' => $course2->id));
        $grade2->needsupdate = 0;
        $grade2->gradepass = 50;
        $DB->update_record('grade_items', $grade2);
        $this->assertNotEmpty($grade2);
        $grades2 = new stdClass();
        $grades2->itemid = $grade2->id;
        $grades2->finalgrade = 69;
        $grades2->feedback = 'convalidated';
        $grades2->userid = $student1->id;

        $DB->insert_record('grade_grades', $grades2, false);

        // Testing the user1 in course1 (expected result: "").
        $result1 = check_dashboard_course_convalidated($student1->id, $course1->id);
        $this->assertNotEquals(" convalidated", $result1);

        // Testing the user1 in course2 (expected result: "convalidated").
        $result2 = check_dashboard_course_convalidated($student1->id, $course2->id);
        $this->assertEquals(" convalidated", $result2);
    }

    /**
     * Tests for phpunit.
     */
    public function test_get_dashboard_course_finalgrade() {
        global $DB;
        $this->resetAfterTest(true);

        $manualplugin = self::enable_enrol_plugin();
        $this->assertNotEmpty($manualplugin);
        $studentrole = self::get_student_role();
        $today = time();
        $year = 31557600;

        $student1 = $this->getDataGenerator()->create_user(array('firstname' => "USUARIO 1"));

        // Creating a category.
        $category1 = $this->getDataGenerator()->create_category(array('name' => 'Category One'));

        // Creating courses.
        $course1 = $this->getDataGenerator()->create_course(array('shortname' => "CAT.M.CURSO1", 'category' => $category1->id));
        $course2 = $this->getDataGenerator()->create_course(array('shortname' => "CAT.M.CURSO2", 'category' => $category1->id));

        $manualinstance = self::create_manual_instance($course1->id);
        $manualplugin->enrol_user($manualinstance, $student1->id, $studentrole->id, $today, $today + $year);
        $manualinstance2 = self::create_manual_instance($course2->id);
        $manualplugin->enrol_user($manualinstance2, $student1->id, $studentrole->id, $today + 10000, $today + $year);

        // Creating grades for each course.
        $grade = $this->getDataGenerator()->create_grade_item(array('itemtype' => 'course', 'courseid' => $course1->id));
        $this->assertNotEmpty($grade);
        $grade->needsupdate = 0;
        $DB->update_record('grade_items', $grade);
        $grades = new stdClass();
        $grades->itemid = $grade->id;
        $grades->finalgrade = 92;
        $grades->feedback = 'Texto de informacion';
        $grades->userid = $student1->id;

        $DB->insert_record('grade_grades', $grades, false);

        // Grade course 2.
        $grade2 = $this->getDataGenerator()->create_grade_item(array('itemtype' => 'course', 'courseid' => $course2->id));
        $grade2->needsupdate = 0;
        $DB->update_record('grade_items', $grade2);
        $this->assertNotEmpty($grade2);
        $grades2 = new stdClass();
        $grades2->itemid = $grade2->id;
        $grades2->finalgrade = 69;
        $grades2->feedback = 'Texto de informacion';
        $grades2->userid = $student1->id;

        $DB->insert_record('grade_grades', $grades2, false);

        // Testing the user1 in course1 (expected result: "92.00").
        $result1 = get_dashboard_course_finalgrade($student1->id, $course1->id);
        $this->assertEquals("92.00", $result1);

        // Testing the user1 in course2 (expected result: "69.00").
        $result2 = get_dashboard_course_finalgrade($student1->id, $course2->id);
        $this->assertEquals("69.00", $result2);
    }

    /**
     * Tests for phpunit.
     */
    public function test_get_average_course_completion() {
        $this->resetAfterTest(true);

        // Set up an arrays of objects with a completionstatus parameter.
        $cinfo1 = new stdClass();
        $cinfo1->completionstatus = 40;
        $cinfo2 = new stdClass();
        $cinfo2->completionstatus = 30;
        $cinfo3 = new stdClass();
        $cinfo3->completionstatus = 50;
        $cinfo4 = new stdClass();
        $cinfo4->completionstatus = 60;
        $cinfo5 = new stdClass();
        $cinfo5->completionstatus = "A";

        $cinfoarray1 = array();
        array_push($cinfoarray1, $cinfo1);
        array_push($cinfoarray1, $cinfo2);
        array_push($cinfoarray1, $cinfo3);
        array_push($cinfoarray1, $cinfo4);

        $cinfoarray2 = array();
        array_push($cinfoarray2, $cinfo5);

        // Testing the array 1 (expected result: 40+30+50+60/4 -> 45).
        $result1 = get_average_course_completion($cinfoarray1);
        $this->assertEquals(45, $result1);

        // Testing the array 2 (expected result: 0/1 -> 0).
        $result2 = get_average_course_completion($cinfoarray2);
        $this->assertEquals(0, $result2);
    }

    /**
     * Tests for phpunit.
     */
    public function test_get_next_convocatory() {
        $this->resetAfterTest(true);

        // Set up an arrays of objects with a timestart parameter.
        $today = time();

        $cinfo1 = new stdClass();
        $cinfo1->timestart = $today - 20000;
        $cinfo2 = new stdClass();
        $cinfo2->timestart = $today - 10000;
        $cinfo3 = new stdClass();
        $cinfo3->timestart = $today + 10000;
        $cinfo4 = new stdClass();
        $cinfo4->timestart = $today + 20000;

        $cinfoarray1 = array();
        array_push($cinfoarray1, $cinfo1);
        array_push($cinfoarray1, $cinfo2);
        array_push($cinfoarray1, $cinfo3);
        array_push($cinfoarray1, $cinfo4);

        $cinfoarray2 = array();
        array_push($cinfoarray2, $cinfo3);
        array_push($cinfoarray2, $cinfo4);

        // Testing the array 1 (expected result: "").
        $result1 = get_next_convocatory($cinfoarray1);
        $this->assertEquals("", $result1);

        // Testing the array 2 (expected result: a date).
        $result2 = get_next_convocatory($cinfoarray2);
        $this->assertEquals(date('F Y', $cinfo3->timestart), $result2);
    }

    /**
     * Tests for phpunit.
     */
    public function test_check_dashboard_active_users_in_course() {
        global $DB;
        $this->resetAfterTest(true);

        // Creating a few users.
        $user1 = $this->getDataGenerator()->create_user(array('username' => 'user1'));
        $user2 = $this->getDataGenerator()->create_user(array('username' => 'user2'));
        $user3 = $this->getDataGenerator()->create_user(array('username' => 'user3'));

        // Creating a few courses.
        $course1 = $this->getDataGenerator()->create_course(array('shortname' => 'Course1'));
        $course2 = $this->getDataGenerator()->create_course(array('shortname' => 'Course2'));
        $course3 = $this->getDataGenerator()->create_course(array('shortname' => 'Course3'));

        // Getting the id of the roles.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'teacher'));

        // Preparing starting and ending dates for enrols.
        $today = time();

        // Course 1 enrols: user1 as teacher user2 and user3 as students with active enrols.
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, $teacherrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id, $studentrole->id, 'manual',
                                              $today - 100000, $today + 400000);
        $this->getDataGenerator()->enrol_user($user3->id, $course1->id, $studentrole->id, 'manual',
                                              $today - 100000, $today + 400000);

        // Course 2 enrols: user1 as teacher user2 as active student and user3 as inactive student.
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id, $teacherrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user2->id, $course2->id, $studentrole->id, 'manual',
                                              $today - 100000, $today + 400000);
        $this->getDataGenerator()->enrol_user($user3->id, $course2->id, $studentrole->id, 'manual',
                                              $today - 400000, $today - 100000);

        // Course 3 enrols: user1 as teacher and user2 as an inactive student.
        $this->getDataGenerator()->enrol_user($user1->id, $course3->id, $teacherrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user2->id, $course3->id,
                                              $studentrole->id, 'manual', $today - 400000, $today - 100000);

        // Test course1 (Expected results = "activestudents").
        $result1 = check_dashboard_active_users_in_course($course1->id);
        $this->assertEquals("activestudents", $result1);

        // Test course2 (Expected results = "activestudents").
        $result2 = check_dashboard_active_users_in_course($course2->id);
        $this->assertEquals("activestudents", $result2);

        // Test course3 (Expected results = "").
        $result3 = check_dashboard_active_users_in_course($course3->id);
        $this->assertEquals("", $result3);
    }
}
