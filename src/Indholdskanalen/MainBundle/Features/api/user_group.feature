@api @usergroup
Feature: admin
  In order to …
  As an api group
  I need to be able to …

  Background:
    Given the following users exist:
      | username | password | roles            |
      | admin    | admin    | ROLE_SUPER_ADMIN |

    And I sign in with username "admin" and password "admin"
    And I add "Content-Type" header equal to "application/json"

  @createSchema
  Scenario: Add user to group
    When I send a "POST" request to "/api/group" with body:
      """
      {
        "title": "Group 1"
      }
      """
    Then the response status code should be 201
    And the response should be in JSON
    And the JSON node "id" should be equal to 1

    When I send a "POST" request to "/api/user" with body:
      """
      {
        "email": "user@example.com",
        "firstname": "1",
        "lastname": "1"
      }
      """
    Then the response status code should be 201
    And the response should be in JSON
    And the JSON node "id" should be equal to 2

    When I send a "POST" request to "/api/user/2/group/1" with body:
      """
      {}
      """
    Then the response status code should be 201
    And the response should be in JSON
    And the JSON node "roles" should have 0 elements

    When I send a "POST" request to "/api/user/2/group/1" with body:
      """
      {
        "roles": ["ROLE_GROUP_ROLE_ADMIN"]
      }
      """
    Then the response status code should be 201
    And the response should be in JSON
    And the JSON node "roles" should have 1 element
    And the JSON node "roles[0]" should be equal to "ROLE_GROUP_ROLE_ADMIN"

  Scenario: Get user's groups
    When I send a "GET" request to "/api/user/2"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "id" should be equal to 2
    And the JSON node "groups" should have 1 element
    And the JSON node "groups[0].id" should be equal to 1

    # When I send a "PUT" request to "/api/user/2/group/1" with body:
    #   """
    #   {
    #     "role": "ROLE_GROUP_ROLE_ADMIN"
    #   }
    #   """
    # And print last JSON response
    # Then the response status code should be 200
    # And the JSON node "id" should be equal to 1
    # And the JSON node "group.id" should be equal to 1
    # And the JSON node "user.id" should be equal to 2
    # And the JSON node "role" should be equal to "ROLE_GROUP_ROLE_ADMIN"

  Scenario: Get user's groups
    When I send a "GET" request to "/api/user/2"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "id" should be equal to 2
    And the JSON node "groups" should have 1 element
    And the JSON node "groups[0].id" should be equal to 1

  Scenario: Update user's roles in group
    When I send a "PUT" request to "/api/user/2/group/1" with body:
      """
      {
        "roles": ["ROLE_GROUP_ROLE_ADMIN", "ROLE_TEST"]
      }
      """
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "roles" should have 2 elements
    And the JSON node "roles[0]" should be equal to "ROLE_GROUP_ROLE_ADMIN"
    And the JSON node "roles[1]" should be equal to "ROLE_TEST"

  Scenario: Get user's roles in group
    When I send a "GET" request to "/api/user/2/group/1"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "roles" should have 2 elements
    And the JSON node "roles[0]" should be equal to "ROLE_GROUP_ROLE_ADMIN"
    And the JSON node "roles[1]" should be equal to "ROLE_TEST"

  Scenario: Get user's groups
    When I send a "GET" request to "/api/user/2/group"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 2 elements
    And the JSON node "[0].role" should be equal to "ROLE_GROUP_ROLE_ADMIN"
    And the JSON node "[0].group.id" should be equal to 1
    And the JSON node "[1].role" should be equal to "ROLE_TEST"
    And the JSON node "[1].group.id" should be equal to 1

  Scenario: Remove user from group
    When I send a "DELETE" request to "/api/user/2/group/1"
    Then the response status code should be 204

  @dropSchema
  Scenario: Drop schema
