@api @user
Feature: admin
  In order to …
  As an api user
  I need to be able to …

  Background:
    Given the following users exist:
      | username    | password | email                   | roles            |
      | admin       | admin    | admin@example.com       | ROLE_ADMIN       |
      | user        | password | user@example.com        | ROLE_ADMIN       |
      | user-admin  | password | user-admin@example.com  | ROLE_USER_ADMIN  |

    And I sign in with username "admin" and password "admin"
    And I add "Content-Type" header equal to "application/json"

  @createSchema
  Scenario: Get user
    When I send a "GET" request to "/api/user/2"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "username" should be equal to "user"
    And the JSON node "roles" should have 1 element
    And the JSON node "roles" should contain key "ROLE_ADMIN"

  Scenario: Get user
    When I send a "GET" request to "/api/user/3"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "username" should be equal to "user-admin"
    And the JSON node "roles" should have 1 element
    And the JSON node "roles" should contain key "ROLE_USER_ADMIN"

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
    And the JSON node "roles" should have 1 elements
    And the JSON node "roles" should contain key "ROLE_GROUP_ADMIN"

  Scenario: Update user with roles
    When I send a "PUT" request to "/api/user/4" with body:
      """
      {
      "roles": ["ROLE_USER_ADMIN"]
      }
      """
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "id" should be equal to 4
    And the JSON node "roles" should have 1 element
    And the JSON node "roles" should contain key "ROLE_USER_ADMIN"

  Scenario: Update user with roles (associative)
    When I send a "PUT" request to "/api/user/4" with body:
      """
      {
      "roles": {"ROLE_GROUP_ADMIN": "Group administrator"}
      }
      """
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "id" should be equal to 4
    And the JSON node "roles" should have 1 element
    And the JSON node "roles" should contain key "ROLE_GROUP_ADMIN"

  Scenario: Get user
    When I send a "GET" request to "/api/user/4"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "roles" should have 1 element
    And the JSON node "roles" should contain key "ROLE_GROUP_ADMIN"

  Scenario: Create user with default role
    When I send a "POST" request to "/api/user" with body:
      """
      {
      "email": "some-user@example.com"
      }
      """
    Then the response status code should be 201
    And the response should be in JSON
    And the JSON node "id" should be equal to 5
    And the JSON node "roles" should have 1 elements
    And the JSON node "roles" should contain key "ROLE_USER"

  Scenario: Cannot escalate user above own role level
    When I sign in with username "user-admin" and password "password"
    And I send a "PUT" request to "/api/user/4" with body:
      """
      {
      "roles": ["ROLE_SUPER_ADMIN"]
      }
      """
    Then the response status code should be 400

    When I sign in with username "user-admin" and password "password"
    And I send a "PUT" request to "/api/user/4" with body:
      """
      {
      "roles": ["ROLE_ADMIN"]
      }
      """
    Then the response status code should be 400

  Scenario: Can assign role
    When I sign in with username "user-admin" and password "password"
    And I send a "PUT" request to "/api/user/4" with body:
      """
      {
      "roles": ["ROLE_GROUP_ADMIN"]
      }
      """
    Then the response status code should be 200

  Scenario: Non-admin cannot update admin
    When I sign in with username "user-admin" and password "password"
    And I send a "PUT" request to "/api/user/1" with body:
      """
      {
        "username": "adminjensen",
        "email": "adminjensen@example.com",
        "firstname": "Admin",
        "lastname": "Jensen"
      }
      """
    Then the response status code should be 403

  @dropSchema
  Scenario: Drop schema
