@api @bug
Feature: Channel groups
  In order to …
  As a client …
  I need to be able to …

  Background:
    Given the following groups exist:
      | title   |
      | Group 1 |
      | Group 2 |
      | Group 3 |
      | Group 4 |

    And the following users exist:
      | username | password | roles     | groups                                                      |
      | user1    | user1    | ROLE_USER | 1: GROUP_ROLE_ADMIN, 2: GROUP_ROLE_USER, 3: GROUP_ROLE_USER |
      | user2    | user2    | ROLE_USER | 1: GROUP_ROLE_USER, 2: GROUP_ROLE_USER, 4: GROUP_ROLE_USER  |

  @createSchema
  Scenario: Get channels
    When I sign in with username "user1" and password "user1"
    And I send a "GET" request to "/api/channel"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON should be equal to:
      """
      []
      """

  Scenario: Create channel in groups
    When I sign in with username "user1" and password "user1"
    And I send a "POST" request to "/api/channel" with body:
      """
      {
        "id": null,
        "title": "Channel in groups",
        "slides": [],
        "groups": [1, 2, 3]
      }
      """
    Then the response status code should be 200

    # Check channel is created
    When I send a "GET" request to "/api/channel"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 1 element
    And the JSON node "[0].id" should be equal to 1
    And the JSON node "[0].title" should be equal to "Channel in groups"
    And the JSON node "[0].slides" should have 0 elements
    And the JSON node "[0].groups" should have 3 elements
    And the JSON node "[0].groups[0].id" should be equal to 1
    And the JSON node "[0].groups[1].id" should be equal to 2
    And the JSON node "[0].groups[2].id" should be equal to 3

  Scenario: Update group
    When I sign in with username "user2" and password "user2"
    And I send a "POST" request to "/api/channel" with body:
      """
      {
        "id": 1,
        "title": "Channel in groups (updated)",
        "slides": [],
        "groups": [1]
      }
      """
    Then the response status code should be 200

    # Check channel has two groups
    When I send a "GET" request to "/api/channel/1"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "groups" should have 2 elements
    And the JSON node "groups[0].id" should be equal to 1
    And the JSON node "groups[1].id" should be equal to 3

  Scenario: Update group
    When I sign in with username "user2" and password "user2"
    And I send a "POST" request to "/api/channel" with body:
      """
      {
        "id": 1,
        "title": "Channel in groups (updated)",
        "slides": [],
        "groups": [1, 4]
      }
      """
    Then the response status code should be 200

    # Check channel has two groups
    When I send a "GET" request to "/api/channel/1"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "groups" should have 3 elements
    And the JSON node "groups[0].id" should be equal to 1
    And the JSON node "groups[1].id" should be equal to 3
    And the JSON node "groups[2].id" should be equal to 4

  Scenario: Update group
    When I sign in with username "user1" and password "user1"
    And I send a "POST" request to "/api/channel" with body:
      """
      {
        "id": 1,
        "title": "Channel in groups (updated)",
        "slides": [],
        "groups": [2]
      }
      """
    Then the response status code should be 200

    # Check channel has two groups
    When I send a "GET" request to "/api/channel/1"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "groups" should have 2 elements
    And the JSON node "groups" should contain 1 element with "id" equal to 2
    And the JSON node "groups" should contain 1 element with "id" equal to 4

  @dropSchema
  Scenario: Drop schema
