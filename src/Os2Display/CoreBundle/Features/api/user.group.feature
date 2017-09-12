@api @user
Feature: admin
  In order to …
  As an api user
  I need to be able to …

  Background:
    Given the following users exist:
      | username | password | roles            | firstname | lastname |
      | admin    | admin    | ROLE_SUPER_ADMIN | Admin     | Jensen   |

    And the following groups exist:
      | title   |
      | Group 1 |

    And I sign in with username "admin" and password "admin"
    And I add "Content-Type" header equal to "application/json"

  @createSchema
  Scenario: Add user
    When I send a "POST" request to "/api/user" with body:
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
    And the JSON node "id" should be equal to 2
    And the JSON node "email" should be equal to "jedinight@tatooine.org"
    And the JSON node "firstname" should be equal to "Jed I"
    And the JSON node "lastname" should be equal to "Night"
    And the JSON node "api_data.permissions" should exist

  Scenario: Add user to group
    When I send a "POST" request to "/api/user/2/group/1" with body:
      """
      {
        "roles": ["ROLE_GROUP_ROLE_USER"]
      }
      """
    Then the response status code should be 201
    And the response should be in JSON
    And the JSON node "roles" should have 1 element

  Scenario: Get user
    When I send a "GET" request to "/api/user/2"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "groups" should have 1 element
    And the JSON node "groups[0].id" should be equal to 1

  Scenario: Update user
    When I send a "PUT" request to "/api/user/2" with body:
      """
      {
        "username": "jedinight",
        "email": "jedinight@tatooine.org",
        "firstname": "Jed II",
        "lastname": "Night"
      }
      """
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "id" should be equal to 2
    And the JSON node "email" should be equal to "jedinight@tatooine.org"
    And the JSON node "firstname" should be equal to "Jed II"
    And the JSON node "lastname" should be equal to "Night"
    And the JSON node "api_data.permissions" should exist
    And the JSON node "groups" should have 1 element

  @dropSchema
  Scenario: Drop schema
