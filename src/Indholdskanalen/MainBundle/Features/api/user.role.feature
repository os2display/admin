@api @user
Feature: admin
  In order to …
  As an api user
  I need to be able to …

  Background:
    Given the following users exist:
      | username    | password | roles      |
      | admin       | admin    | ROLE_ADMIN |
      | user        | password | ROLE_ADMIN |
      | group-admin | password | ROLE_GROUP_ADMIN |

    And I sign in with username "admin" and password "admin"
    And I add "Content-Type" header equal to "application/json"

  @createSchema
  Scenario: Get user
    When I send a "GET" request to "/api/user/2"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "username" should be equal to "user"
    And the JSON node "roles" should have 4 elements
    And the JSON node "roles" should contain value "ROLE_ADMIN"
    And the JSON node "roles" should contain value "ROLE_USER"
    And the JSON node "roles" should contain value "ROLE_GROUP_ADMIN"
    And the JSON node "roles" should contain value "ROLE_USER_ADMIN"

  Scenario: Get user
    When I send a "GET" request to "/api/user/3"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "username" should be equal to "group-admin"
    And the JSON node "roles" should have 2 elements
    And the JSON node "roles" should contain value "ROLE_GROUP_ADMIN"
    And the JSON node "roles" should contain value "ROLE_USER"

  Scenario: Create user with roles
    When I send a "POST" request to "/api/user" with body:
      """
      {
      "email": "group-admin@example.com",
      "roles": ["ROLE_GROUP_ADMIN"]
      }
      """
    Then the response status code should be 201
    And the response should be in JSON
    And the JSON node "id" should be equal to 4
    And the JSON node "roles" should have 2 elements
    And the JSON node "roles" should contain value "ROLE_GROUP_ADMIN"

  Scenario: Update user with roles
    When I send a "PUT" request to "/api/user/4" with body:
      """
      {
      "roles": ["ROLE_ADMIN"]
      }
      """
    Then the response status code should be 200
    And the response should be in JSON

    And print last JSON response
    And the JSON node "roles" should have 4 elements
    And the JSON node "roles" should contain value "ROLE_ADMIN"
    And the JSON node "roles" should contain value "ROLE_GROUP_ADMIN"
    And the JSON node "roles" should contain value "ROLE_USER_ADMIN"

  Scenario: Get user
    When I send a "GET" request to "/api/user/4"
    Then the response status code should be 200
    And the response should be in JSON

    And print last JSON response
    And the JSON node "roles" should have 4 elements
    And the JSON node "roles" should contain value "ROLE_ADMIN"
    And the JSON node "roles" should contain value "ROLE_GROUP_ADMIN"
    And the JSON node "roles" should contain value "ROLE_USER_ADMIN"

  @dropSchema
  Scenario: Drop schema
