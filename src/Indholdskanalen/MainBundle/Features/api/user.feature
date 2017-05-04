@api @user
Feature: admin
  In order to …
  As an api user
  I need to be able to …

  Background:
    Given the following users exist:
      | username | password | roles            | firstname | lastname |
      | admin    | admin    | ROLE_SUPER_ADMIN | Admin     | Jensen   |
      | user     | user     | ROLE_USER        | John      | Doe      |

    And I add "Content-Type" header equal to "application/json"

  @createSchema
  Scenario: Invalid username
    When I sign in with username "no such user" and password "password"
    And I send a "GET" request to "/api/user/current"
    Then the response status code should be 401
    And the JSON should be equal to:
      """
      {"success": false}
      """

  Scenario: Invalid password
    When I sign in with username "admin" and password "user"
    And I send a "GET" request to "/api/user/current"
    Then the response status code should be 401
    And the JSON should be equal to:
      """
      {"success": false}
      """

  Scenario: Get current user
    When I sign in with username "admin" and password "admin"
    And I send a "GET" request to "/api/user/current"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "id" should be equal to 1
    And the JSON node "is_admin" should be true
    And the JSON node "is_super_admin" should be equal to 1
    And the JSON node "roles" should have 1 element
    And the JSON node "roles[0]" should be equal to "ROLE_SUPER_ADMIN"
    And the JSON node "api_data.permissions" should exist

  Scenario: Get current user
    When I sign in with username "user" and password "user"
    And I send a "GET" request to "/api/user/current"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "id" should be equal to 2
    And the JSON node "is_admin" should be false
    And the JSON node "is_super_admin" should be false
    And the JSON node "roles" should have 0 elements
    And the JSON node "api_data.permissions" should exist

  Scenario: Get users
    When I sign in with username "admin" and password "admin"
    And I send a "GET" request to "/api/user?filter[name]=87&filter[age]=87"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 2 elements

    And the JSON node "[0].id" should be equal to 1
    And the JSON node "[0].is_admin" should be true
    And the JSON node "[0].is_super_admin" should be true
    And the JSON node "[0].roles" should have 1 element
    And the JSON node "[0].roles[0]" should be equal to "ROLE_SUPER_ADMIN"

    And the JSON node "[1].id" should be equal to 2
    And the JSON node "[1].is_admin" should be false
    And the JSON node "[1].is_super_admin" should be false
    And the JSON node "[1].roles" should have 0 elements

  Scenario: Add user (without email)
    When I sign in with username "admin" and password "admin"
    And I send a "POST" request to "/api/user" with body:
      """
      {
        "firstname": "Jed I",
        "lastname": "Night"
      }
      """
    Then the response status code should be 400
    And the response should be in JSON
    And the JSON node "error" should not be null
    And the JSON node "error.message" should not be null
    And the JSON node "error.exception[0].message" should be equal to "Invalid data"
    # And the JSON node "error.exception[0].data" should be equal to "Invalid data"

  Scenario: Add user
    When I sign in with username "admin" and password "admin"
    And I send a "POST" request to "/api/user" with body:
      """
      {
        "username": "jedinight",
        "email": "jedinight@tatooine.org",
        "firstname": "Jed I",
        "lastname": "Night"
      }
      """
    Then the response status code should be 201
    And the response should be in JSON
    And the JSON node "id" should be equal to 3
    And the JSON node "email" should be equal to "jedinight@tatooine.org"
    And the JSON node "firstname" should be equal to "Jed I"
    And the JSON node "lastname" should be equal to "Night"
    And the JSON node "api_data.permissions" should exist

  Scenario: Add user with already existing email
    When I sign in with username "admin" and password "admin"
    And I send a "POST" request to "/api/user" with body:
      """
      {
        "email": "jedinight@tatooine.org",
        "firstname": "Jed II",
        "lastname": "Night"
      }
      """
    Then the response status code should be 409

  Scenario: Get users
    When I sign in with username "admin" and password "admin"
    And I send a "GET" request to "/api/user"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 3 elements

    And the JSON node "[2].id" should be equal to 3
    And the JSON node "[2].email" should be equal to "jedinight@tatooine.org"
    And the JSON node "[2].is_admin" should be false
    And the JSON node "[2].is_super_admin" should be false
    And the JSON node "[2].roles" should have 0 elements

  Scenario: Add user
    When I sign in with username "admin" and password "admin"
    And I send a "POST" request to "/api/user" with body:
      """
      {
        "username": "tahradactyl",
        "email": "tahradactyl@example.com",
        "firstname": "Tahra",
        "lastname": "Dactyl"
      }
      """
    Then the response status code should be 201
    And the response should be in JSON
    And the JSON node "id" should be equal to 4
    And the JSON node "email" should be equal to "tahradactyl@example.com"
    And the JSON node "api_data.permissions" should exist

  Scenario: Get users
    When I sign in with username "admin" and password "admin"
    And I send a "GET" request to "/api/user"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 4 elements

    And the JSON node "[3].id" should be equal to 4
    And the JSON node "[3].email" should be equal to "tahradactyl@example.com"
    And the JSON node "[3].is_admin" should be false
    And the JSON node "[3].is_super_admin" should be false
    And the JSON node "[3].roles" should have 0 elements

  Scenario: Delete user
    When I sign in with username "admin" and password "admin"
    And I send a "DELETE" request to "/api/user/4"
    Then the response status code should be 204

  Scenario: Get users
    When I sign in with username "admin" and password "admin"
    And I send a "GET" request to "/api/user"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 3 elements

  Scenario: Update non-existing user
    When I sign in with username "admin" and password "admin"
    And I send a "PUT" request to "/api/user/87" with body:
      """
      {}
      """
    Then the response status code should be 404

  Scenario: Update user (with empty email)
    When I sign in with username "admin" and password "admin"
    And I send a "PUT" request to "/api/user/3" with body:
      """
      {
        "email": null
      }
      """
    Then the response status code should be 400
    And the response should be in JSON

  Scenario: Update user
    When I sign in with username "admin" and password "admin"
    And I send a "PUT" request to "/api/user/3" with body:
      """
      {
        "firstname": "Darth",
        "lastname": "Vader"
      }
      """
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "id" should be equal to 3
    And the JSON node "firstname" should be equal to "Darth"
    And the JSON node "lastname" should be equal to "Vader"
    And the JSON node "email" should be equal to "jedinight@tatooine.org"
    And the JSON node "api_data.permissions" should exist

  Scenario: Get users
    When I sign in with username "admin" and password "admin"
    And I send a "GET" request to "/api/user"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 3 elements

  Scenario: Delete non-existing user
    When I sign in with username "admin" and password "admin"
    And I send a "DELETE" request to "/api/user/4"
    Then the response status code should be 404

  Scenario: Delete user
    When I sign in with username "admin" and password "admin"
    And I send a "DELETE" request to "/api/user/3"
    Then the response status code should be 204

  @dropSchema
  Scenario: Drop schema
