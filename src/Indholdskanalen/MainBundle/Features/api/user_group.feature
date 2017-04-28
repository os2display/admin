@api @usergroup @user @group
Feature: admin
  In order to …
  As an api group
  I need to be able to …

  Background:
    Given the following users exist:
      | username | password | roles            |
      | admin    | admin    | ROLE_SUPER_ADMIN |

    And I sign in with username "admin" and password "admin"

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
    And the JSON node "id" should be equal to 1
    And the JSON node "group.id" should be equal to 1
    And the JSON node "user.id" should be equal to 2
    And the JSON node "role" should not exist

    When I send a "GET" request to "/api/user/2"
    Then the response status code should be 200
    And the JSON node "id" should be equal to 2
    And the JSON node "user_groups" should have 1 element
    And the JSON node "user_groups[0].id" should be equal to 1

    # When I send a "PUT" request to "/api/user/2/group/1" with body:
    #   """
    #   {
    #     "role": "ROLE_GROUP_GROUP_ADMIN"
    #   }
    #   """
    # And print last JSON response
    # Then the response status code should be 200
    # And the JSON node "id" should be equal to 1
    # And the JSON node "group.id" should be equal to 1
    # And the JSON node "user.id" should be equal to 2
    # And the JSON node "role" should be equal to "ROLE_GROUP_GROUP_ADMIN"

  @dropSchema
  Scenario: Drop schema
