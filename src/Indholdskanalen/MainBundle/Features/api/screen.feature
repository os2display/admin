@api @screen
Feature: admin
  In order to …
  As a client …
  I need to be able to …

  Background:
    Given the following groups exist:
      | title   |
      | Group 1 |
      | Group 2 |
      | Group 3 |

    And the following users exist:
      | username | password | roles            | groups                                 |
      | admin    | admin    | ROLE_SUPER_ADMIN |                                        |
      | user     | user     | ROLE_USER        | 1: GROUP_ROLE_USER, 2: GROUP_ROLE_USER |

    And I sign in with username "user" and password "user"

  @createSchema
  Scenario: Get screens
    When I send a "GET" request to "/api/screen"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON should be equal to:
      """
      []
      """

  Scenario: Create screen
    When I send a "POST" request to "/api/screen" with body:
    """
    {
      "id": null,
      "title": "The first screen",
      "description": "Description of The first screen"
    }
    """
    Then the response status code should be 200

  Scenario: Get screens
    When I send a "GET" request to "/api/screen"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 1 element
    And the JSON node "[0].id" should be equal to 1
    And the JSON node "[0].title" should be equal to "The first screen"
    And the JSON node "[0].description" should be equal to "Description of The first screen"

  Scenario: Update screen
    When I send a "POST" request to "/api/screen" with body:
    """
    {
      "id": 1,
      "title": "The first screen (updated)",
      "description": "Description of The first screen"
    }
    """
    Then the response status code should be 200

  Scenario: Get screens
    When I send a "GET" request to "/api/screen"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 1 element
    And the JSON node "[0].id" should be equal to 1
    And the JSON node "[0].title" should be equal to "The first screen (updated)"
    And the SQL query "SELECT * FROM ik_grouping WHERE entity_type = 'Indholdskanalen\\MainBundle\\Entity\\Screen'" should return 0 element

  Scenario: Add screen to group
    When I send a "POST" request to "/api/screen" with body:
    """
    {
      "id": 1,
      "title": "The first screen (updated)",
      "description": "Description of The first screen",
      "groups": [1, 2]
    }
    """
    Then the response status code should be 200

  Scenario: Get screens
    When I send a "GET" request to "/api/screen"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 1 element
    And the JSON node "[0].id" should be equal to 1
    And the JSON node "[0].groups" should have 2 elements
    And the JSON node "[0].groups[0].id" should be equal to 1
    And the JSON node "[0].groups[1].id" should be equal to 2
    And the SQL query "SELECT * FROM ik_grouping WHERE entity_type = 'Indholdskanalen\\MainBundle\\Entity\\Screen'" should return 2 elements

  Scenario: Remove screen from group
    When I send a "POST" request to "/api/screen" with body:
    """
    {
      "id": 1,
      "title": "The first screen (updated)",
      "description": "Description of The first screen",
      "groups": [2]
    }
    """
    Then the response status code should be 200

  Scenario: Get screens
    When I send a "GET" request to "/api/screen"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 1 element
    And the JSON node "[0].id" should be equal to 1
    And the JSON node "[0].groups" should have 1 element
    And the JSON node "[0].groups[0].id" should be equal to 2
    And the SQL query "SELECT * FROM ik_grouping WHERE entity_type = 'Indholdskanalen\\MainBundle\\Entity\\Screen'" should return 1 element

  Scenario: Remove screen
    When I send a "DELETE" request to "/api/screen/1"
    Then the response status code should be 200
    And the SQL query "SELECT * FROM ik_grouping WHERE entity_type = 'Indholdskanalen\\MainBundle\\Entity\\Screen'" should return 0 elements

  Scenario: Create screen in group
    When I send a "POST" request to "/api/screen" with body:
      """
      {
        "id": null,
        "title": "Screen in group",
        "description": "Screen in group",
        "groups": [2]
      }
      """
    Then the response status code should be 200

  Scenario: Get screens
    When I send a "GET" request to "/api/screen"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 1 element
    And the JSON node "[0].id" should be equal to 2
    And the JSON node "[0].title" should be equal to "Screen in group"

  @dropSchema
  Scenario: Drop schema
