ernilambar/site-health-command
==============================



[![Testing](https://github.com/ernilambar/site-health-command/actions/workflows/testing.yml/badge.svg)](https://github.com/ernilambar/site-health-command/actions/workflows/testing.yml)

Quick links: [Using](#using) | [Installing](#installing) | [Contributing](#contributing) | [Support](#support)

## Using

This package implements the following commands:

### wp site-health check

Run site health checks.

~~~
wp site-health check [--fields=<fields>] [--format=<format>]
~~~

**OPTIONS**

	[--fields=<fields>]
		Limit the output to specific fields.

	[--format=<format>]
		Render output in a particular format.
		---
		default: table
		options:
		  - table
		  - csv
		  - json
		  - yaml
		---

**EXAMPLES**

    # Run site health checks.
    $ wp site-health check
    +-------------------+-------------+-------------+----------------------------------------------------------+
    | check             | type        | status      | label                                                    |
    +-------------------+-------------+-------------+----------------------------------------------------------+
    | WordPress Version | Performance | good        | Your version of WordPress (6.5.2) is up to date          |
    | Plugin Versions   | Security    | recommended | You should remove inactive plugins                       |
    | Theme Versions    | Security    | recommended | You should remove inactive themes                        |
    | PHP Version       | Performance | good        | Your site is running the current version of PHP (8.2.18) |



### wp site-health info

Displays site health info.

~~~
wp site-health info [<section>] [--all] [--fields=<fields>] [--format=<format>] [--private]
~~~

**OPTIONS**

	[<section>]
		Section slug.

	[--all]
		Displays info for all sections.

	[--fields=<fields>]
		Limit the output to specific fields.

	[--format=<format>]
		Render output in a particular format.
		---
		default: table
		options:
		  - table
		  - csv
		  - json
		  - yaml
		---

	[--private]
		Display private fields. Disabled by default.

**EXAMPLES**

    # List site health info.
    $ wp site-health info wp-constants
    +---------------------+---------------------+-----------+-----------+
    | field               | label               | value     | debug     |
    +---------------------+---------------------+-----------+-----------+
    | WP_HOME             | WP_HOME             | Undefined | undefined |
    | WP_SITEURL          | WP_SITEURL          | Undefined | undefined |
    | WP_MEMORY_LIMIT     | WP_MEMORY_LIMIT     | 40M       |           |
    | WP_MAX_MEMORY_LIMIT | WP_MAX_MEMORY_LIMIT | 256M      |           |



### wp site-health list-info-sections

List site health info sections.

~~~
wp site-health list-info-sections [--fields=<fields>] [--format=<format>]
~~~

**OPTIONS**

	[--fields=<fields>]
		Limit the output to specific fields.

	[--format=<format>]
		Render output in a particular format.
		---
		default: table
		options:
		  - table
		  - csv
		  - json
		  - yaml
		---

**EXAMPLES**

    # List site health info sections.
    $ wp site-health list-info-sections
    +------------------------+---------------------+
    | label                  | section             |
    +------------------------+---------------------+
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
    +------------------------+---------------------+



### wp site-health status

Check site health status.

~~~
wp site-health status 
~~~

**EXAMPLES**

    # Check site health status.
    $ wp site-health status
    good

## Installing

Installing this package requires WP-CLI v2.11 or greater. Update to the latest stable release with `wp cli update`.

Once you've done so, you can install the latest stable version of this package with:

```bash
wp package install ernilambar/site-health-command:@stable
```

To install the latest development version of this package, use the following command instead:

```bash
wp package install ernilambar/site-health-command:dev-master
```

## Contributing

We appreciate you taking the initiative to contribute to this project.

Contributing isn’t limited to just code. We encourage you to contribute in the way that best fits your abilities, by writing tutorials, giving a demo at your local meetup, helping other users with their support questions, or revising our documentation.

For a more thorough introduction, [check out WP-CLI's guide to contributing](https://make.wordpress.org/cli/handbook/contributing/). This package follows those policy and guidelines.

### Reporting a bug

Think you’ve found a bug? We’d love for you to help us get it fixed.

Before you create a new issue, you should [search existing issues](https://github.com/ernilambar/site-health-command/issues?q=label%3Abug%20) to see if there’s an existing resolution to it, or if it’s already been fixed in a newer version.

Once you’ve done a bit of searching and discovered there isn’t an open or fixed issue for your bug, please [create a new issue](https://github.com/ernilambar/site-health-command/issues/new). Include as much detail as you can, and clear steps to reproduce if possible. For more guidance, [review our bug report documentation](https://make.wordpress.org/cli/handbook/bug-reports/).

### Creating a pull request

Want to contribute a new feature? Please first [open a new issue](https://github.com/ernilambar/site-health-command/issues/new) to discuss whether the feature is a good fit for the project.

Once you've decided to commit the time to seeing your pull request through, [please follow our guidelines for creating a pull request](https://make.wordpress.org/cli/handbook/pull-requests/) to make sure it's a pleasant experience. See "[Setting up](https://make.wordpress.org/cli/handbook/pull-requests/#setting-up)" for details specific to working on this package locally.

## Support

GitHub issues aren't for general support questions, but there are other venues you can try: https://wp-cli.org/#support


*This README.md is generated dynamically from the project's codebase using `wp scaffold package-readme` ([doc](https://github.com/wp-cli/scaffold-package-command#wp-scaffold-package-readme)). To suggest changes, please submit a pull request against the corresponding part of the codebase.*
