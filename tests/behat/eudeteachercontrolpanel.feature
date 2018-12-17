@local @local_eudecustom @javascript
Feature: View my courses sorted by enrolment dates
    and get access to some activities

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
    And the following "activities" exist:
      | activity |  idnumber    | type   | course      | 
      | forum    |     F1       | news   | CAT1.M.HS   |
      | forum    |     F2       | news   | CAT1.M.GS   |
      | forum    |     F3       | news   | MI.GYH      |
      | forum    |     F4       | news   | CAT2.M.TT   |
      | forum    |     F1       | general| CAT1.M.HS   |
      | forum    |     F2       | general| CAT1.M.GS   |
      | forum    |     F3       | general| MI.GYH      |
      | forum    |     F4       | general| CAT2.M.TT   |
    And the following "activities" exist:
      | activity    |  idnumber    | course       | timeavailable | duedate     | section |
      | assign      |     A1       |  CAT1.M.HS   | 86400         | 1496440000  | 1       |
      | assign      |     A2       |  CAT1.M.GS   | 86400         | 1543610000  | 1       |
      | assign      |     A3       |  MI.GYH      | 86400         | 1496400000  | 1       |
      

Scenario: Check course distribution
    Given I log in as "user1"
    And I go to eudeteachercontrolpanel
    # Categories should appear in "my courses section".
    And I should see "Cat1" in the "//div[@class='row eude_panel_bg']/div/div[1]/div[1]/div[2]/li[1]/div/a/span" "xpath_element"
    # Course 4 doesnt have a student enrolment, so it should appear in actual section.
    And I should see "CAT2.M.TT" in the "//div[@class='row eude_panel_bg']/div/div[2]/div[1]" "xpath_element"
    # Course 3 is intensive, so it should appear in actual section.
    And I should see "MI.GYH" in the "//div[@class='row eude_panel_bg']/div/div[2]/div[1]" "xpath_element"
    # Course 2 depends on today's date.
    And I should visualize "CAT1.M.GS" with 1512082860 and 4162834860
    And I log out

Scenario: Link to announcement forums
    Given I log in as "user1"
    And I go to eudeteachercontrolpanel
    And I set the field with xpath "//div[@class='row eude_panel_bg']/div/div[2]/div[1]/div[2]/li[1]/div[2]/select[@class='linkselect']" to "1"
    And I should see "announcements"
    And I log out

Scenario: Link to normal forums
    Given I log in as "user1"
    And I go to eudeteachercontrolpanel
    And I set the field with xpath "//div[@class='row eude_panel_bg']/div/div[2]/div[1]/div[2]/li[1]/div[2]/select[@class='linkselect']" to "2"
    And I should see "General forums"
    And I log out

Scenario: Link to assignments
    Given I log in as "user1"
    And I go to eudeteachercontrolpanel
    And I set the field with xpath "//div[@class='row eude_panel_bg']/div/div[2]/div[1]/div[2]/li[1]/div[2]/select[@class='linkselect']" to "3"
    And I should see "Assignments"
    And I log out
      
      