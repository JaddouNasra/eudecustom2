@local @local_eudecustom @javascript
Feature: Test the 'intensive module dates' feature works.
  In order to create or modify the intensive call dates of the intensive courses of a category
  As an admin
  I need to go to the intensive dates page, select a category and modify the fields.

  Background: 
    Given the following "users" exist:
      | username | email                |
      | user1    | student1@example.com |
    And the following "categories" exist:
      | name  | idnumber |
      | Cat 1 | CAT1     |
    And the following "courses" exist:
      | category | shortname          | idnumber |
      | CAT1     | Normal course 1    | C1       |
      | CAT1     | Normal course 2    | C2       |
      | CAT1     | MI.Course 1 | C3       |
      | CAT1     | MI.Course 2 | C4       |
    And I set initial dates of intensive modules

  # Given a user without admin role tries to access the page a notification of no permissions should appear.
  @javascript
  Scenario: Check the access for non admin users.
    Given I log in as "user1"
    When I go to eude intensive module dates
    Then I should see "No permission to see this!"

  # Given a user with admin role fill the form and press the save button.
  @javascript
  Scenario: Check the page for admin users.
    Given I log in as "admin"
    When I go to eude intensive module dates
    Then I should see "Select the matriculation dates of the intensive modules."
    Given I set the field with xpath "//tr[@class='coursedata'][2]/td[2]/input" to "05/01/2017"
    And I set the field with xpath "//tr[@class='coursedata'][2]/td[3]/input" to "05/04/2017"
    And I set the field with xpath "//tr[@class='coursedata'][2]/td[4]/input" to "05/08/2017"
    And I set the field with xpath "//tr[@class='coursedata'][2]/td[5]/input" to "05/12/2017"
    Then I press "Save changes"
    And I should see "Select the matriculation dates of the intensive modules."
    And I should not see "Normal course 1"
