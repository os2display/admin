@api @channel @grouping
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
      | username  | roles            | groups                  |
      | admin     | ROLE_SUPER_ADMIN |                         |
      | user      | ROLE_USER        |                         |
      | groupuser | ROLE_USER        | 1:ROLE_GROUP_ROLE_ADMIN |

    And I add "Content-Type" header equal to "application/json"

  @createSchema
  Scenario: Get channels
    When I authenticate as "admin"
    And I send a "GET" request to "/api/channel"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON should be equal to:
      """
      []
      """

  Scenario: Create channel in group 1
    When I authenticate as "admin"
    And I send a "POST" request to "/api/channel" with body:
    """
    {
      "id": null,
      "title": "The first channel",
      "slides": [],
      "groups": [1]
    }
    """
    Then the response status code should be 200

  Scenario: Get channels as "user"
    When I authenticate as "user"
    And I send a "GET" request to "/api/channel"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 0 elements

  Scenario: Get channels as "groupuser"
    When I authenticate as "groupuser"
    And I send a "GET" request to "/api/channel"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 1 element

  Scenario: Add "user" to group 1
    When I authenticate as "admin"
    And I send a "POST" request to "/api/user/2/group/1" with body:
    """
    {
      "roles": ["ROLE_GROUP_ROLE_USER"]
    }
    """
    Then the response status code should be 201
    And the JSON node "roles" should have 1 element
    And the JSON node "roles[0]" should be equal to "ROLE_GROUP_ROLE_USER"

  Scenario: Get channels as "user"
    When I authenticate as "user"
    And I send a "GET" request to "/api/channel"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 1 element

  Scenario: Create channel
    When I authenticate as "user"
    When I send a "POST" request to "/api/channel" with body:
    """
    {
      "id": null,
      "title": "My first channel",
      "slides": []
    }
    """
    Then the response status code should be 200

  Scenario: Get channels as "user"
    When I authenticate as "user"
    And I send a "GET" request to "/api/channel"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 2 elements

  @dropSchema
  Scenario: Drop schema
