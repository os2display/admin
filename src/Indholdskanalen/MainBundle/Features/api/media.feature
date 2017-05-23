@api @media
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
      | Group 4 |

    And I sign in with username "user" and password "user"

  @createSchema
  Scenario: Get media
    When I send a "GET" request to "/api/media"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 0 elements

  Scenario: Create media
    When I send a "POST" request to "/api/media" with parameters:
      | key   | value          |
      | title | image name     |
      | file  | @image-000.png |
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON should be equal to:
      """
      [1]
      """

  Scenario: Get media
    When I send a "GET" request to "/api/media"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 1 element
    And the JSON node "[0].name" should be equal to "image name"

  Scenario: Delete media
    When I send a "DELETE" request to "/api/media/1"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON should be equal to:
      """
      []
      """

    When I send a "GET" request to "/api/media"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 0 elements

  Scenario: Create media in group
    When I send a "POST" request to "/api/media" with parameters:
      | key    | value           |
      | title  | image in groups |
      | groups | [2, 3]          |
      | file   | @image-000.png  |
    Then the response status code should be 200
    And the JSON should be equal to:
      """
      [2]
      """

  Scenario: Get media
    When I send a "GET" request to "/api/media"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 1 element
    And the JSON node "[0].name" should be equal to "image in groups"
    And the JSON node "[0].groups" should have 2 elements
    And the JSON node "[0].groups[0].id" should be equal to 2
    And the JSON node "[0].groups[1].id" should be equal to 3

  Scenario: Update media in group
    When I send a "PUT" request to "/api/media/2" with body:
      """
      {
        "groups": [{"id": 1}, {"id": 2}, {"id": 3}, {"id": 4}]
      }
      """
    Then the response status code should be 200
    And the JSON node "id" should be equal to 2
    When I send a "GET" request to "/api/media/2"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "groups" should have 4 elements

  @dropSchema
  Scenario: Drop schema
