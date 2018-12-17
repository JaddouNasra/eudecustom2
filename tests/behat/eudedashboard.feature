@local @local_eudecustom @javascript
Feature: View my custom dashboard
    In order to see my custom dashboard
    As a user student or as a user teacher
    I want to navigate into the system

Background: 
    
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | user1    | User      | 1        | user1@example.com    |
      | user2    | User      | 2        | user2@example.com    |
    And the following "categories" exist:
      | name | idnumber |
      | Cat1 | CAT1     |
      | Cat2 | CAT2     |
    And the following "courses" exist:
      | category | shortname   | fullname    | idnumber |
      | CAT1     | CAT1.M.HS   | CAT1.M.HS   | C1       |
      | CAT1     | CAT1.M.GS   | CAT1.M.GS   | C2       |
      | CAT2     | MI.GYH      | MI.GYH      | C3       |
      | CAT2     | CAT2.M.TT   | CAT2.M.TT   | C4       |
    And the following "course enrolments" exist:
      | user     | course         | role           | timestart        | timeend       |
      | user1    | CAT1.M.HS      | editingteacher | 1389081600       | 0             |
      | user1    | CAT1.M.GS      | editingteacher | 1389081600       | 0             |
      | user1    | MI.GYH         | editingteacher | 1389081600       | 0             |
      | user1    | CAT2.M.TT      | editingteacher | 1389081600       | 0             |
      | user2    | CAT1.M.HS      | student        | 1496268000       | 1496440799    |
      | user2    | CAT1.M.GS      | student        | 1512082860       | 4162834860    |
      | user2    | MI.GYH         | student        | 1496268000       | 1496440799    |
      

Scenario: See my dashboard as a teacher
    Given I log in as "user1"
    And I go to eudedashboard
    # I should see the courses i am enroled as editing teacher
    Then I should see "CAT1.M.HS"
    And I should see "CAT1.M.GS"
    And I should see "MI.GYH"
    And I should see "CAT2.M.TT"
    Then I log out

Scenario: See my dashboard as a student
    Given I log in as "user2"
    And I go to eudedashboard
    # I should see the courses i am enroled as student of CAT1
    Then I should see "CAT1.M.HS"
    And I should see "CAT1.M.GS"
    # I change cat in the tab to see the courses of CAT2
    Then I click on "//ul[@id='eudedashboardmyTab']/li[2]/a" "xpath_element"
    And I should see "MI.GYH"
    And I should not see "CAT2.M.TT"
    Then I log out