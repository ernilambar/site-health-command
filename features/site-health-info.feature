Feature: Site Health Info tests

  @require-wp-5.4
  Scenario: Site Health Info sections
    Given a WP install

    When I run `wp site-health info sections`
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
