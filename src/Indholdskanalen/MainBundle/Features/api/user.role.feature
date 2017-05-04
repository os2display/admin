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
    And the JSON node "roles[0]" should be equal to "ROLE_ADMIN"
    And the JSON node "roles[1]" should be equal to "ROLE_USER"
    And the JSON node "roles[2]" should be equal to "ROLE_GROUP_ADMIN"
    And the JSON node "roles[3]" should be equal to "ROLE_USER_ADMIN"

  Scenario: Get user
    When I send a "GET" request to "/api/user/3"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "username" should be equal to "group-admin"
    And the JSON node "roles" should have 2 elements
    And the JSON node "roles[0]" should be equal to "ROLE_GROUP_ADMIN"
    And the JSON node "roles[1]" should be equal to "ROLE_USER"

  @dropSchema
  Scenario: Drop schema
