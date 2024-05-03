Feature: Site Health tests

  @require-wp-5.4
  Scenario: Run site health checks
    Given a WP install

    When I run `wp site-health check`
    Then STDOUT should not be empty
    And STDERR should be empty
    And the return code should be 0

    When I run `wp site-health status`
    Then STDOUT should not be empty
    And STDERR should be empty
    And the return code should be 0
