@api @roles
Feature: admin
  In order to …
  As an api user
  I need to be able to …

  Background:
    Given the following users exist:
      | username    | email                   | password | roles            |
      | super-admin | super-admin@example.com | password | ROLE_SUPER_ADMIN |
      | admin       | admin@example.com       | password | ROLE_ADMIN       |
      | user        | user@example.com        | password | ROLE_USER        |

    When I sign in with username "super-admin" and password "password"
    And I add "Content-Type" header equal to "application/json"

  @createSchema
  Scenario: Get user roles
    When I send a "GET" request to "/api/user/roles"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 5 elements
    And the JSON node "" should contain key "ROLE_SUPER_ADMIN"
    And the JSON node "" should contain key "ROLE_ADMIN"
    And the JSON node "" should contain key "ROLE_USER"
    And the JSON node "" should contain key "ROLE_GROUP_ADMIN"
    And the JSON node "" should contain key "ROLE_USER_ADMIN"

  Scenario: Get group roles
    When I send a "GET" request to "/api/group/roles"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 2 elements
    And the JSON node "" should contain key "ROLE_GROUP_ROLE_ADMIN"
    And the JSON node "" should contain key "ROLE_GROUP_ROLE_USER"

  Scenario: Get user roles (Danish)
    When I send a "GET" request to "/api/user/roles?locale=da"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 5 elements
    And the JSON node "" should contain key "ROLE_SUPER_ADMIN"
    And the JSON node "" should contain key "ROLE_ADMIN"
    And the JSON node "" should contain key "ROLE_USER"
    And the JSON node "" should contain key "ROLE_GROUP_ADMIN"
    And the JSON node "" should contain key "ROLE_USER_ADMIN"

  Scenario: Get group roles
    When I send a "GET" request to "/api/group/roles?locale=da"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 2 elements
    And the JSON node "" should contain key "ROLE_GROUP_ROLE_ADMIN"
    # @TODO: Check role is translated
    # And the JSON node "" should not contain value "ROLE_GROUP_ROLE_ADMIN"
    And the JSON node "" should contain key "ROLE_GROUP_ROLE_USER"
    # And the JSON node "" should not contain value "ROLE_GROUP_ROLE_USER"

  Scenario: Get roles assignable by user
    When I sign in with username "admin" and password "password"
    And I send a "GET" request to "/api/user/roles"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 4 elements
    And the JSON node "" should contain key "ROLE_ADMIN"
    And the JSON node "" should contain key "ROLE_USER"
    And the JSON node "" should contain key "ROLE_GROUP_ADMIN"
    And the JSON node "" should contain key "ROLE_USER_ADMIN"

  Scenario: Get roles not allowed
    When I sign in with username "user" and password "password"
    And I send a "GET" request to "/api/user/roles"
    Then the response status code should be 403

  @dropSchema
  Scenario: Drop schema
