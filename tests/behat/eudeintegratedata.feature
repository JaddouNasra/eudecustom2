@local @local_eudecustom
Feature: Test the 'integratedata' feature works.
  In order to integrate previous data
  As an admin
  I need to have logged in once before and fill in the textarea and click 'Integrate text data'

  Background: 
    Given the following "users" exist:
      | username | email                |
      | student1 | student1@example.com |
      | student2 | student2@example.com |
    And the following "categories" exist:
      | name                   | idnumber |
      | Comercio Internacional | CAT1     |
      | Logística              | CAT2     |
    And the following "courses" exist:
      | category | shortname | idnumber |
      | CAT1     | COI.M.NC1 | C1       |
      | CAT1     | COI.M.NC2 | C2       |
      | CAT2     | MI.NC1    | C3       |
      | CAT2     | MI.NC2    | C4       |

  # Given the admin fills correct data in the textarea and click the 'Integrata textarea data' button a notification
  # appears with the message.
  @javascript
  Scenario: Integrate correct data with an admin user.
    Given I log in as "admin"
    And I go to eude integration
    When I set the field "integrationtext" to multiline:
    """
    CREATE;student1@example.com;COI.M.NC1;21/01/2017;1
    CREATE;student2@example.com;COI.M.NC1;21/01/2017;2
    DELETE;student1@example.com;COI.M.NC1
    CREATE;student2@example.com;COI.M.NC2;21/01/2017;4
    """
    And I press "processtext"
    Then I should see "Data integrated successfully"

  # Given the admin fills wrong data in the textarea and click the 'Integrata textarea data' button a notification
  # appears with the message.
  @javascript
  Scenario: Integrate wrong data with an admin user.
    Given I log in as "admin"
    And I go to eude integration
    When I set the field "integrationtext" to multiline:
    """
    CREATE;student1@example.com;Normal course 1;21/01/2017;5
    CREATE;student1@example.com;Normal course 1;21/01/2017;4
    CREATE;student1@example.com;Normal course 1;21/01/2017;4
    CREATE;student1@example.com;Normal course 1;21/01/2017;4
    """
    And I press "processtext"
    Then I should see "Error introducing data"

  # Given a user who is not admin trys to enter the uage a notification stating the user has no permissions to see the
  # content should appear.
  @javascript
  Scenario: Try to access the page with an user without admin role.
    Given I log in as "student1"
    When I go to eude integration
    Then I should see "No permission to see this!"
