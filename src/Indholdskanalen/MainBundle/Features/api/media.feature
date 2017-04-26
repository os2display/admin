@api
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
  Scenario: Get media
    When I send a "GET" request to "/api/media"
    Then the response status code should be 200
    And  the response should be in JSON
    And  the JSON should be equal to:
      """
      []
      """

  # Scenario: Create media
  #   When I attach files:
  #     | filename  | mimetype  |
  #     | dummy.png | image/png |
  #   And I send a "POST" request to "/api/media" with attachments and body:
  #       """
  #       {
  #       "title": "test image"
  #       }
  #       """
  #   Then the response status code should be 200
  #   And print last JSON response

  # Scenario: Get media
  #   When I send a "GET" request to "/api/media"
  #   Then the response status code should be 200
  #   And  the response should be in JSON
  #   And  the JSON node "" should have 1 element

  @dropSchema
  Scenario: Drop schema
