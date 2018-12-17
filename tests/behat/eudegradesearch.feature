@local @local_eudecustom
Feature: Test the 'gradesearch' feature works.
  In order to see the grades of an user
  As a teacher
  I need to have logged in once before, select the category, course and student fields
  and press the View Student Grades button

  Background: 
    Given the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | User      | 1        | user1@example.com |
      | user2    | User      | 2        | user2@example.com |
    And the following "categories" exist:
      | name | idnumber |
      | Cat1 | CAT1     |
      | Cat2 | CAT2     |
    And the following "courses" exist:
      | category | shortname | idnumber |
      | CAT1     | C1.M1.M01 | C1       |
      | CAT1     | C1.M2.M02 | C2       |
      | CAT2     | C2.M1.M03 | C3       |
      | CAT2     | C2.M2.M04 | C4       |
    And the following "course enrolments" exist:
      | user  | course    | role           |
      | user1 | C1.M1.M01 | editingteacher |
      | user1 | C1.M2.M02 | editingteacher |
      | user1 | C2.M1.M03 | editingteacher |
      | user1 | C2.M2.M04 | editingteacher |
      | user2 | C1.M1.M01 | student        |
      | user2 | C1.M2.M02 | student        |
      | user2 | C2.M1.M03 | student        |
      | user2 | C2.M2.M04 | student        |

  # Given a teacher selects a category, course and a student he press the button to redirect him
  # to the student gradebook
  @javascript
  Scenario: Enter with a user with role teacher in several categories and courses of each categories
    #Given I skip because "The scenario randomly fails"
    Given I log in as "user1"
    When I go to search grades
    Then I should see "Select the options to find a student."
    Given I set the field "categoryname" to "Cat1"
    And I set the field "coursename" to "M01"
    And I set the field "studentname" to "2, User"
    And I follow "View student grades"
    Then I should see "Test course 1: View: User report"
    And I should see "User 2"

  # Given a student tries ot enter this page a message of no permissions to see this content will appear
  @javascript
  Scenario: Enter with a user without role teacher in a course to check the view
    Given I log in as "user2"
    When I go to search grades
    Then I should see "Insufficient permissions to display the content."
