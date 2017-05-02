Feature: admin
  In order to …
  As a client …
  I need to be able to …

  Background:
    Given the following users exist:
      | username | password | roles            |
      | admin    | admin    | ROLE_SUPER_ADMIN |
      | user     | user     | ROLE_USER        |

    And I sign in with username "user" and password "user"

  @createSchema
  Scenario: Get channels
    When I send a "GET" request to "/api/channel"
    Then the response status code should be 200
    And  the response should be in JSON
    And  the JSON should be equal to:
      """
      []
      """

  Scenario: Create channel
    When I send a "POST" request to "/api/channel" with body:
    """
    {
      "id": null,
      "occurrences": [ { "startDate": "2000-01-01", "endDate": "2001-01-01" } ]
    }
    """
    Then the response status code should be 201

  Scenario: Get channels
    When I send a "GET" request to "/api/channel"
    Then the response status code should be 200
    And  the response should be in JSON
    And  the JSON node "" should have 0 elements

  @dropSchema
  Scenario: Drop schema
