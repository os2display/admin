@api @channel
Feature: admin
  In order to …
  As a client …
  I need to be able to …

  Background:
    Given the following users exist:
      | username | password | roles            |
      | admin    | admin    | ROLE_SUPER_ADMIN |
      | user     | user     | ROLE_USER        |

    And the following groups exist:
      | title   |
      | Group 1 |
      | Group 2 |
      | Group 3 |

    And I sign in with username "user" and password "user"

  @createSchema
  Scenario: Get channels
    When I send a "GET" request to "/api/channel"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON should be equal to:
      """
      []
      """

  Scenario: Create channel
    When I send a "POST" request to "/api/channel" with body:
    """
    {
      "id": null,
      "title": "The first channel",
      "slides": []
    }
    """
    Then the response status code should be 200

  Scenario: Get channels
    When I send a "GET" request to "/api/channel"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 1 element
    And the JSON node "[0].id" should be equal to 1
    And the JSON node "[0].title" should be equal to "The first channel"
    And the JSON node "[0].slides" should have 0 elements

  Scenario: Update channel
    When I send a "POST" request to "/api/channel" with body:
    """
    {
      "id": 1,
      "title": "The first channel (updated)",
      "slides": []
    }
    """
    Then the response status code should be 200

  Scenario: Get channels
    When I send a "GET" request to "/api/channel"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 1 element
    And the JSON node "[0].id" should be equal to 1
    And the JSON node "[0].title" should be equal to "The first channel (updated)"
    And the JSON node "[0].slides" should have 0 elements
    And the SQL query "SELECT * FROM ik_grouping WHERE entity_type = 'Indholdskanalen\\MainBundle\\Entity\\Channel'" should return 0 element

  Scenario: Add channel to group
    When I send a "POST" request to "/api/channel" with body:
    """
    {
      "id": 1,
      "title": "The first channel (updated)",
      "slides": [],
      "groups": [1, 2]
    }
    """
    Then the response status code should be 200

  Scenario: Get channels
    When I send a "GET" request to "/api/channel"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 1 element
    And the JSON node "[0].id" should be equal to 1
    And the JSON node "[0].groups" should have 2 elements
    And the JSON node "[0].groups[0].id" should be equal to 1
    And the JSON node "[0].groups[1].id" should be equal to 2
    And the SQL query "SELECT * FROM ik_grouping WHERE entity_type = 'Indholdskanalen\\MainBundle\\Entity\\Channel'" should return 2 elements

  Scenario: Add channel to group
    When I send a "POST" request to "/api/channel" with body:
    """
    {
      "id": 1,
      "title": "The first channel (updated)",
      "slides": [],
      "groups": [1, { "id": 2 }]
    }
    """
    Then the response status code should be 200

  Scenario: Get channels
    When I send a "GET" request to "/api/channel"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 1 element
    And the JSON node "[0].id" should be equal to 1
    And the JSON node "[0].groups" should have 2 elements
    And the JSON node "[0].groups[0].id" should be equal to 1
    And the JSON node "[0].groups[1].id" should be equal to 2
    And the SQL query "SELECT * FROM ik_grouping WHERE entity_type = 'Indholdskanalen\\MainBundle\\Entity\\Channel'" should return 2 elements

  Scenario: Remove channel from group
    When I send a "POST" request to "/api/channel" with body:
    """
    {
      "id": 1,
      "title": "The first channel (updated)",
      "slides": [],
      "groups": [2]
    }
    """
    Then the response status code should be 200

  Scenario: Get channels
    When I send a "GET" request to "/api/channel"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 1 element
    And the JSON node "[0].id" should be equal to 1
    And the JSON node "[0].groups" should have 1 element
    And the JSON node "[0].groups[0].id" should be equal to 2
    And the SQL query "SELECT * FROM ik_grouping WHERE entity_type = 'Indholdskanalen\\MainBundle\\Entity\\Channel'" should return 1 element

  Scenario: Remove channel
    When I send a "DELETE" request to "/api/channel/1"
    Then the response status code should be 200
    And the SQL query "SELECT * FROM ik_grouping WHERE entity_type = 'Indholdskanalen\\MainBundle\\Entity\\Channel'" should return 0 elements

  Scenario: Create channel in group
    When I send a "POST" request to "/api/channel" with body:
      """
      {
        "id": null,
        "title": "Channel in group",
        "slides": [],
        "groups": [2]
      }
      """
    Then the response status code should be 200

  Scenario: Get channels
    When I send a "GET" request to "/api/channel"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 1 element
    And the JSON node "[0].id" should be equal to 2
    And the JSON node "[0].title" should be equal to "Channel in group"
    And the JSON node "[0].slides" should have 0 elements
    And the JSON node "[0].groups" should have 1 element
    And the JSON node "[0].groups[0].id" should be equal to 2

  @dropSchema
  Scenario: Drop schema
