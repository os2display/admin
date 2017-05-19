@api @slide
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
  Scenario: Get slides
    When I send a "GET" request to "/api/slide"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON should be equal to:
      """
      []
      """

  Scenario: Create slide
    When I send a "POST" request to "/api/slide" with body:
    """
    {
      "id": null,
      "title": "The first slide",
      "media": [],
      "channels": []
    }
    """
    Then the response status code should be 200

  Scenario: Get slides
    When I send a "GET" request to "/api/slide"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 1 element
    And the JSON node "[0].id" should be equal to 1
    And the JSON node "[0].title" should be equal to "The first slide"
    And the JSON node "[0].media" should have 0 elements

  Scenario: Update slide
    When I send a "POST" request to "/api/slide" with body:
    """
    {
      "id": 1,
      "title": "The first slide (updated)",
      "media": [],
      "channels": []
    }
    """
    Then the response status code should be 200

  Scenario: Get slides
    When I send a "GET" request to "/api/slide"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 1 element
    And the JSON node "[0].id" should be equal to 1
    And the JSON node "[0].title" should be equal to "The first slide (updated)"
    # And the JSON node "[0].channels" should have 0 elements
    And the SQL query "SELECT * FROM ik_grouping WHERE entity_type = 'Indholdskanalen\\MainBundle\\Entity\\Slide'" should return 0 element

  Scenario: Add slide to group
    When I send a "POST" request to "/api/slide" with body:
    """
    {
      "id": 1,
      "title": "The first slide (updated)",
      "media": [],
      "channels": [],
      "groups": [1, 2]
    }
    """
    Then the response status code should be 200

  Scenario: Get slides
    When I send a "GET" request to "/api/slide"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 1 element
    And the JSON node "[0].id" should be equal to 1
    And the JSON node "[0].groups" should have 2 elements
    And the JSON node "[0].groups[0].id" should be equal to 1
    And the JSON node "[0].groups[1].id" should be equal to 2
    And the SQL query "SELECT * FROM ik_grouping WHERE entity_type = 'Indholdskanalen\\MainBundle\\Entity\\Slide'" should return 2 elements

  Scenario: Remove slide from group
    When I send a "POST" request to "/api/slide" with body:
    """
    {
      "id": 1,
      "title": "The first slide (updated)",
      "media": [],
      "channels": [],
      "groups": [2]
    }
    """
    Then the response status code should be 200

  Scenario: Get slides
    When I send a "GET" request to "/api/slide"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 1 element
    And the JSON node "[0].id" should be equal to 1
    And the JSON node "[0].groups" should have 1 element
    And the JSON node "[0].groups[0].id" should be equal to 2
    And the SQL query "SELECT * FROM ik_grouping WHERE entity_type = 'Indholdskanalen\\MainBundle\\Entity\\Slide'" should return 1 element

  Scenario: Remove slide
    When I send a "DELETE" request to "/api/slide/1"
    Then the response status code should be 200
    And the SQL query "SELECT * FROM ik_grouping WHERE entity_type = 'Indholdskanalen\\MainBundle\\Entity\\Slide'" should return 0 elements

  Scenario: Create slide in group
    When I send a "POST" request to "/api/slide" with body:
      """
      {
        "id": null,
        "title": "Slide in group",
        "media": [],
        "channels": [],
        "groups": [2]
      }
      """
    Then the response status code should be 200

  Scenario: Get slides
    When I send a "GET" request to "/api/slide"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 1 element
    And the JSON node "[0].id" should be equal to 2
    And the JSON node "[0].title" should be equal to "Slide in group"
    And the JSON node "[0].groups" should have 1 element
    And the JSON node "[0].groups[0].id" should be equal to 2

  @dropSchema
  Scenario: Drop schema
