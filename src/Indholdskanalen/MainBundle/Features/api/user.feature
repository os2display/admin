@api
Feature: admin
  In order to …
  As an api user
  I need to be able to …

  Background:
    Given the following users exist:
      | username | password | roles            |
      | admin    | admin    | ROLE_SUPER_ADMIN |
      | user     | user     | ROLE_USER        |

  @createSchema
  Scenario: Invalid username
    When I sign in with username "no such user" and password "password"
    And  I send a "GET" request to "/api/user"
    Then the response status code should be 401
    And  the JSON should be equal to:
      """
      {"success": false}
      """

  Scenario: Invalid password
    When I sign in with username "admin" and password "user"
    And  I send a "GET" request to "/api/user"
    Then the response status code should be 401
    And  the JSON should be equal to:
      """
      {"success": false}
      """

  Scenario: Get user
    When I sign in with username "admin" and password "admin"
    And  I send a "GET" request to "/api/user"
    Then the response status code should be 200
    And  the response should be in JSON
    And  the JSON node "id" should be equal to 1
    And  the JSON node "is_admin" should be true
    And  the JSON node "is_super_admin" should be equal to 1
    And  the JSON node "roles" should have 1 element
    And  the JSON node "roles[0]" should be equal to "ROLE_SUPER_ADMIN"

  Scenario: Get user
    When I sign in with username "user" and password "user"
    And  I send a "GET" request to "/api/user"
    Then the response status code should be 200
    And  the response should be in JSON
    And  the JSON node "id" should be equal to 2
    And  the JSON node "is_admin" should be false
    And  the JSON node "is_super_admin" should be false
    And  the JSON node "roles" should have 0 elements


  @dropSchema
  Scenario: Drop schema
