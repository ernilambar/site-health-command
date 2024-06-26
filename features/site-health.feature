Feature: Site Health tests

  @require-wp-5.4
  Scenario: Run site health checks
    Given a WP install

    When I run `wp site-health check --fields=check,type,status --format=csv`
    Then STDOUT should contain:
      """
      "Plugin Versions",Security,recommended
      """
    And STDOUT should contain:
      """
      "Theme Versions",Security,recommended
      """
    And STDOUT should contain:
      """
      "PHP Default Timezone",Performance,good
      """
    And STDOUT should contain:
      """
      "Background updates",Security,good
      """
    And STDOUT should contain:
      """
      "Authorization header",Security,recommended
      """
    And STDOUT should contain:
      """
      "HTTPS status",Security,good
      """

    When I run `wp site-health check --fields=check,status,type --format=csv --status=good`
    Then STDOUT should not contain:
      """
      ,recommended,
      """

    When I run `wp site-health status`
    Then STDOUT should be:
      """
      recommended
      """
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

    When I try `wp site-health info non-existent-info-section`
    Then STDERR should be:
      """
      Error: Invalid section.
      """

    When I try `wp site-health info wp-constants --all`
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

    When I run `wp site-health info wp-constants --format=csv`
    Then STDOUT should not contain:
      """
      ,private,
      """

    When I run `wp site-health info wp-constants --private --format=csv`
    Then STDOUT should contain:
      """
      ,private,
      """

    When I run `wp site-health info wp-constants --fields=field,private,value --format=csv`
    Then STDOUT should contain:
      """
      ,private,
      """

    When I run `wp site-health info wp-paths-sizes`
    Then STDOUT should not contain:
      """
      loading
      """

    When I run `wp site-health info wp-constants`
    Then STDOUT should be a table containing rows:
      | field      | label      | value     | debug     |
      | WP_HOME    | WP_HOME    | Undefined | undefined |
      | WP_SITEURL | WP_SITEURL | Undefined | undefined |
