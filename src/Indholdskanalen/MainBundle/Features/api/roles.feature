@api @roles
Feature: admin
  In order to …
  As an api user
  I need to be able to …

  Background:
    Given the following users exist:
      | username          | email             | password |
      | admin             | admin@example.com | admin    |

    When I sign in with username "admin" and password "admin"
    And I add "Content-Type" header equal to "application/json"

  @createSchema
  Scenario: Get user roles
    When I send a "GET" request to "/api/user/roles"
    Then the response status code should be 200
    And the response should be in JSON
    And print last JSON response

  Scenario: Get group roles
    When I send a "GET" request to "/api/group/roles"
    Then the response status code should be 200
    And the response should be in JSON
    And print last JSON response

  @dropSchema
  Scenario: Drop schema
