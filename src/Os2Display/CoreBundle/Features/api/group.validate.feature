@api @group
Feature: admin
  In order to …
  As an api group
  I need to be able to …

  Background:
    Given the following users exist:
      | username | password | roles            |
      | admin    | admin    | ROLE_SUPER_ADMIN |

    And the following groups exist:
      | title           |
      | The first group |
      | Another group   |
      | A group         |

    And I sign in with username "admin" and password "admin"
    And I add "Content-Type" header equal to "application/json"

  @createSchema
  Scenario: Cannot create duplicate group
    When I send a "POST" request to "/api/group" with body:
      """
      {
        "title": "A group"
      }
      """
    Then the response status code should be 409

  Scenario: Update group with conflict
    When I send a "PUT" request to "/api/group/2" with body:
      """
      {
      "title": "A group"
      }
      """
    Then the response status code should be 409

  Scenario: Update group with empty title
    When I send a "PUT" request to "/api/group/2" with body:
      """
      {
      "title": ""
      }
      """
    Then the response status code should be 400

  Scenario: Create group with empty title
    When I send a "POST" request to "/api/group" with body:
      """
      {
      "title": ""
      }
      """
    Then the response status code should be 400

  @dropSchema
  Scenario: Drop schema
