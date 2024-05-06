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

  @require-wp-5.4
  Scenario: Site Health Info sections
    Given a WP install

    When I run `wp site-health list-info-sections`
    Then STDOUT should be a table containing rows:
      | label                  | section             |
      | WordPress              | wp-core             |
      | Directories and Sizes  | wp-paths-sizes      |
      | Drop-ins               | wp-dropins          |
      | Active Theme           | wp-active-theme     |
      | Parent Theme           | wp-parent-theme     |
      | Inactive Themes        | wp-themes-inactive  |
      | Must Use Plugins       | wp-mu-plugins       |
      | Active Plugins         | wp-plugins-active   |
      | Inactive Plugins       | wp-plugins-inactive |
      | Media Handling         | wp-media            |
      | Server                 | wp-server           |
      | Database               | wp-database         |
      | WordPress Constants    | wp-constants        |
      | Filesystem Permissions | wp-filesystem       |

  @require-wp-5.4
  Scenario: Site Health Info by section
    Given a WP install

    When I try `wp site-health info`
    Then STDERR should be:
      """
      Error: Please specify a section, or use the --all flag.
      """

    When I run `wp site-health info wp-constants`
    Then STDOUT should not contain:
      """
      ABSPATH
      """

    When I run `wp site-health info wp-constants --private`
    Then STDOUT should contain:
      """
      ABSPATH
      """

    When I run `wp site-health info wp-constants`
    Then STDOUT should be a table containing rows:
      | field      | label      | value     | debug     |
      | WP_HOME    | WP_HOME    | Undefined | undefined |
      | WP_SITEURL | WP_SITEURL | Undefined | undefined |
