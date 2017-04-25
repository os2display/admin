Feature: admin
  In order to …
  As a client …
  I need to be able to …

  Background:
    Given the following users exist:
      | username | password | roles            |
      | admin    | admin    | ROLE_SUPER_ADMIN |
      | user     | user     | ROLE_USER        |

  @createSchema
  Scenario: Login
    When I go to "/"
    Then I should be on "/login"

    When I fill in "_username" with "admin"
    And  fill in "_password" with "admin"
    And  press "_submit"
    Then I should be on "/#/channel-overview"

    When I follow "Slides"
    Then I should be on "/#/slide-overview"
    And  I should see "Opret slide"

    When I follow "Skærme"
    Then I should be on "/#/screen-overview"

    When I follow "Medier"
    Then I should be on "/#/media-overview"

    When I follow "Kanaler"
    Then I should be on "/#/channel-overview"
    And  I should see "Opret kanal"

    # When I click "Opret kanal"
    # And  wait for 2 seconds
    # Then I should be on "/#/channel"
    # # And  see an "h1" element containing "Opret kanal"
    # And  see "Opret kanal" in an "h1" element

    # When I fill in "[data-ng-model='channel.title']" with "My first channel"
    # And  press "Fortsæt"
    # Then I should be on "/#/channel"

    # When I wait 1 second
    # Then I should see "Tilføj slides"

    # When I click "Tilføj slides"
    # And  wait 2 seconds
    # Then I should see "Vælg slides"

    # Logout
    When I wait for 1 second
    And  I click the ".hamburger-menu-toggle" element
    Then I should see "Log ud"

    # When I click "Log ud"
    # Then I should be on "/logout"

  @dropSchema
  Scenario: Drop schema
