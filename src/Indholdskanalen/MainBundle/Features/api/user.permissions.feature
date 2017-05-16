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

    And the following groups exist:
      | title   |
      | Group 1 |

    And I add "Content-Type" header equal to "application/json"

  @createSchema
  Scenario: Get groups
    When I sign in with username "user" and password "user"
    And I send a "GET" request to "/api/group"
    Then the response status code should be 403

  Scenario: Add user to group
    When I sign in with username "admin" and password "admin"
    And I send a "POST" request to "/api/user/2/group/1" with body:
      """
      {
      "roles": ["ROLE_GROUP_ROLE_ADMIN"]
      }
      """
    Then the response status code should be 201
    And the response should be in JSON
    And the JSON node "roles" should have 1 element

  Scenario: Get groups
    When I sign in with username "user" and password "user"
    And I send a "GET" request to "/api/group"
    Then the response status code should be 200

  @dropSchema
  Scenario: Drop schema
