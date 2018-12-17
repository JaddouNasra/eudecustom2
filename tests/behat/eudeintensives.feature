@local @local_eudecustom
Feature: Prueba
  In order to validate my credentials in the system
  As a user student
  I want to navigate into the system
  
  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | student3 | Student   | 3        | student3@example.com |
    And the following "categories" exist:
      | name        | idnumber  |
      | RRHH        | 1         |
      | MBA         | 2         |
      | Intensives  | 3         |
    And the following "courses" exist:
      | fullname    | shortname | format | category |
      | Course 0    | RRHH.M.C0 | weeks  | 1        |
      | Course 1    | MBA.M.C1  | weeks  | 2        |
      | Course 2    | MBA.M.C2  | weeks  | 2        |
      | MI.Course 1 | MI.C1     | weeks  | 3        |
      | MI.Course 2 | MI.C2     | weeks  | 3        |
    And the following "course enrolments" exist:
      | user     | course    | role    | timestart  |
      | student1 | RRHH.M.C0   | student | 1480192416 |
      | student2 | RRHH.M.C0   | student | 1480192416 |
      | student1 | MBA.M.C1    | student | 1490192416 |
      | student2 | MBA.M.C1    | student | 1490192416 |
      | student3 | MBA.M.C1    | student | 1490192416 |
      | student1 | MBA.M.C2    | student | 1490192416 |
      | student1 | MI.C1 | student | 1450192416 |
      | student3 | MI.C1 | student | 1530192416 |
    And I log in as "admin"
    And I navigate to "Plugins > Local plugins > Eude custom actions" in site administration
    And I select "6" from the "id_s__local_eudecustom_intensivemodulechecknumber" singleselect
    And I select "3" from the "id_s__local_eudecustom_totalenrolsinincurse" singleselect
    And I set the field "id_s__local_eudecustom_intensivemoduleprice" to "50"
    And I set the field "id_s__local_eudecustom_tpv_url_tpvv" to "https://sis.redsys.es/sis/realizarPago"
    And I press "Save changes"
    And I add intensive enrols
    And I add matriculation dates
    And I log out
    
  @javascript
  Scenario: View intensives modules like a student
    Given I log in as "student1"
    When I go to intensives
    And I select "RRHH" from the "menucategoryname" singleselect
    And I wait "1" seconds
    And I select "MBA" from the "menucategoryname" singleselect
    And I wait "2" seconds
    And I press "Early Access"
    And I press "x"
    Then I should see "Early Access"

  @javascript
  Scenario: View intensives modules like a student
    Given I log in as "student2"
    When I go to intensives
    And I select "RRHH" from the "menucategoryname" singleselect
    And I wait "1" seconds
    And I select "MBA" from the "menucategoryname" singleselect
    And I wait "2" seconds
    And I press "Early Access"
    And I press "Continue"
    Then I should see "Intensives"
    
  @javascript
  Scenario: View intensives modules like a student
    Given I log in as "student3"
    When I go to intensives
    And I select "-- Program --" from the "menucategoryname" singleselect
    And I wait "2" seconds
    And I select "MBA" from the "menucategoryname" singleselect
    And I wait "1" seconds
    And I click on the element with xpath "//i[@class='fa fa-pencil-square-o abrirFechas']"
    And I wait "1" seconds
    Then I should see "Intensives"