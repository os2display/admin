@api @group
Feature: admin
  In order to …
  As an api group
  I need to be able to …

  Background:
    Given the following users exist:
      | username | password | roles            |
      | admin    | admin    | ROLE_SUPER_ADMIN |
      | user     | user     | ROLE_USER        |

  @createSchema
  Scenario: Get groups
    When I sign in with username "admin" and password "admin"
    And I send a "GET" request to "/api/group"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 0 elements

  Scenario: Create group with no title
    When I sign in with username "admin" and password "admin"
    And I send a "POST" request to "/api/group" with body:
      """
      {}
      """
    And print last JSON response
    Then the response status code should be 400

  Scenario: Create group
    When I sign in with username "admin" and password "admin"
    And I send a "POST" request to "/api/group" with body:
      """
      {
      "title": "The first group"
      }
      """
    And print last JSON response
    Then the response status code should be 201
    And the response should be in JSON
    And the JSON node "id" should be equal to 1
    And the JSON node "title" should be equal to "The first group"
    And the JSON node "user_groups" should have 0 elements

  Scenario: Get groups
    When I sign in with username "admin" and password "admin"
    And I send a "GET" request to "/api/group"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 1 elements
    And the JSON node "[0].id" should be equal to 1
    And the JSON node "[0].title" should be equal to "The first group"
    And the JSON node "[0].user_groups" should have 0 elements

    And print last JSON response

  @dropSchema
  Scenario: Drop schema
