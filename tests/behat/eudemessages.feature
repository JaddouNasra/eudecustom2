@local @local_eudecustom @javascript
Feature: Write a message
    In order to communicate with other users

  Background: 
    
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | user1    | User      | 1        | user1@example.com    |
      | user2    | User      | 2        | user2@example.com    |
      | user3    | User      | 3        | user3@example.com    |
      | user4    | User      | 4        | user4@example.com    |
    And the following "categories" exist:
      | name | idnumber |
      | Cat1 | CAT1     |
      | Cat2 | CAT2     |
    And the following "courses" exist:
      | category | shortname | idnumber |
      | CAT1     | C1.M.M01  | C1       |
      | CAT2     | C2.M.M02  | C2       |
    And the following "course enrolments" exist:
      | user     | course    | role           | 
      | user1    | C1.M.M01  | editingteacher |
      | user1    | C2.M.M02  | editingteacher |
      | user2    | C1.M.M01  | student        |
      | user2    | C2.M.M02  | student        |
      | user3    | C1.M.M01  | student        |
      | user3    | C2.M.M02  | student        |
      | user4    | C1.M.M01  | editingteacher |
      | user4    | C2.M.M02  | editingteacher |

  Scenario: Send a message to students
    Given I log in as "user1"
    And I go to eudemessages
    And I set the field "categoryname" to "Cat1"
    And I set the field "coursename" to "C1.M.M01"
    And I set the field "subjectname" to "Grades"
    And I set the field "destinatarioname" to "Active students"
    And I set the field "messagetext" to "This is a message for my students"
    And I press "sendmessage"
    And I log out
    And I log in as "user2"
    When I follow "Messages" in the user menu
    Then I should see "User 1"
    And I log out
    And I log in as "user3"
    When I follow "Messages" in the user menu
    Then I should see "User 1"
    And I log out

  Scenario: Send a message to Another teacher
    Given I log in as "user1"
    And I go to eudemessages
    And I set the field "categoryname" to "Cat2"
    And I set the field "coursename" to "C2.M.M02"
    And I set the field "subjectname" to "Problem"
    And I set the field "destinatarioname" to "Editing Teacher: User 4"
    And I set the field "messagetext" to "This is a problem notice to another teacher"
    And I press "sendmessage"
    And I log out
    And I log in as "user4"
    When I follow "Messages" in the user menu
    Then I should see "User 1"
    And I log out
  
  Scenario: Leave receiver option empty
    Given I log in as "user1"
    And I go to eudemessages
    And I set the field "categoryname" to "Cat2"
    And I set the field "coursename" to "C2.M.M02"
    And I set the field "subjectname" to "Problem"
    And I set the field "messagetext" to "This is a problem notice to another teacher"
    And I press "sendmessage"
    Then I should see "Select all the required fields."
    And I log out

  Scenario: Leave text area empty
    Given I log in as "user1"
    And I go to eudemessages
    And I set the field "categoryname" to "Cat2"
    And I set the field "coursename" to "C2.M.M02"
    And I set the field "subjectname" to "Problem"
    And I set the field "destinatarioname" to "Editing Teacher: User 4"
    And I press "sendmessage"
    Then I should see "Select all the required fields."
    And I log out
    
  Scenario: Go to Messages through the custom link
    Given I log in as "user2"
    And I go to eudemessages
    When I follow "searchmessage"
    Then I should see "messages"
    And I log out
  