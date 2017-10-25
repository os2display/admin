@api @user @validation
Feature: admin
  In order to …
  As an api user
  I need to be able to …

  Background:
    Given the following users exist:
      | username          | email             | password | roles      |
      | admin             | admin@example.com | admin    | ROLE_ADMIN |
      | user1@example.com | user1@example.com |          |            |
      | user2@example.com | user2@example.com |          |            |

    When I sign in with username "admin" and password "admin"
    And I add "Content-Type" header equal to "application/json"

  @createSchema
  Scenario: Update user
    When I send a "PUT" request to "/api/user/2" with body:
      """
      {
        "email": "user87@example.com"
      }
      """
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "email" should be equal to "user87@example.com"
    And the JSON node "username" should be equal to "user1@example.com"

  Scenario: Update user with conflict
    When I send a "PUT" request to "/api/user/2" with body:
      """
      {
        "email": "user2@example.com"
      }
      """
    Then the response status code should be 409

  Scenario: Update non-existing user
    When I send a "PUT" request to "/api/user/87" with body:
      """
      {
        "email": "user3@example.com"
      }
      """
    Then the response status code should be 404

  Scenario: Update user with empty email
    When I send a "PUT" request to "/api/user/2" with body:
      """
      {
        "email": ""
      }
      """
    Then the response status code should be 400

  Scenario: Update user with invalid email
    When I send a "PUT" request to "/api/user/2" with body:
      """
      {
        "email": "xxx"
      }
      """
    Then the response status code should be 400

  @dropSchema
  Scenario: Drop schema
