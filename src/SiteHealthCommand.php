<?php

namespace WP_CLI\SiteHealth;

use WP_CLI;
use WP_CLI\Utils;
use WP_CLI_Command;
use WP_Debug_Data;
use WP_Site_Health;

/**
 * Manage Site Health
 *
 * @package wp-cli
 */
class SiteHealthCommand extends WP_CLI_Command {

	/**
	 * @var WP_Site_Health $instance Instance of WP_Site_Health class.
	 */
	protected $instance;

	/**
	 * @var array $info Debug info.
	 */
	protected $info;

	/**
	 * @var array $obj_fields Default fields to display for each test.
	 */
	protected $obj_fields = array(
		'check',
		'type',
		'status',
		'label',
	);

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( ! class_exists( 'WP_Site_Health' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-site-health.php';
		}

		if ( ! class_exists( 'WP_Debug_Data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-debug-data.php';
		}

		$this->instance = WP_Site_Health::get_instance();
		$this->info     = WP_Debug_Data::debug_data();
	}

	/**
	 * Run site health checks.
	 *
	 * ## OPTIONS
	 *
	 * [--format=<format>]
	 * : Render output in a particular format.
	 * ---
	 * default: table
	 * options:
	 *   - table
	 *   - csv
	 *   - json
	 *   - yaml
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     # Run site health checks.
	 *     $ wp site-health check
	 *     +-------------------+-------------+-------------+----------------------------------------------------------+
	 *     | check             | type        | status      | label                                                    |
	 *     +-------------------+-------------+-------------+----------------------------------------------------------+
	 *     | WordPress Version | Performance | good        | Your version of WordPress (6.5.2) is up to date          |
	 *     | Plugin Versions   | Security    | recommended | You should remove inactive plugins                       |
	 *     | Theme Versions    | Security    | recommended | You should remove inactive themes                        |
	 *     | PHP Version       | Performance | good        | Your site is running the current version of PHP (8.2.18) |
	 */
	public function check( $args, $assoc_args ) {
		$checks = $this->get_checks();

		$results = $this->run_checks( $checks );

		$format = Utils\get_flag_value( $assoc_args, 'format', 'table' );

		$formatter = $this->get_formatter( $assoc_args );

		$formatter->display_items( $results );
	}

	/**
	 * Check site health status.
	 *
	 * ## EXAMPLES
	 *
	 *     # Check site health status.
	 *     $ wp site-health status
	 *     good
	 */
	public function status() {
		$site_status = '';

		$checks = $this->get_checks();

		$results = $this->run_checks( $checks );

		$count_details = $this->get_status_count_details( $results );

		if ( $count_details['total'] > 0 ) {
			if ( $count_details['critical'] > 1 ) {
				$site_status = 'critical';
			} else {
				$good_percent = ( $count_details['good'] * 100 ) / $count_details['total'];

				if ( $good_percent < 80 ) {
					$site_status = 'recommended';
				} else {
					$site_status = 'good';
				}
			}
		}

		WP_CLI::line( $site_status );
	}

	/**
	 * List site health info sections.
	 *
	 * ## OPTIONS
	 *
	 * [--format=<format>]
	 * : Render output in a particular format.
	 * ---
	 * default: table
	 * options:
	 *   - table
	 *   - csv
	 *   - json
	 *   - yaml
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     # List site health info sections.
	 *     $ wp site-health list-info-sections
	 *     +------------------------+---------------------+
	 *     | label                  | section             |
	 *     +------------------------+---------------------+
	 *     | WordPress              | wp-core             |
	 *     | Directories and Sizes  | wp-paths-sizes      |
	 *     | Drop-ins               | wp-dropins          |
	 *     | Active Theme           | wp-active-theme     |
	 *     | Parent Theme           | wp-parent-theme     |
	 *     | Inactive Themes        | wp-themes-inactive  |
	 *     | Must Use Plugins       | wp-mu-plugins       |
	 *     | Active Plugins         | wp-plugins-active   |
	 *     | Inactive Plugins       | wp-plugins-inactive |
	 *     | Media Handling         | wp-media            |
	 *     | Server                 | wp-server           |
	 *     | Database               | wp-database         |
	 *     | WordPress Constants    | wp-constants        |
	 *     | Filesystem Permissions | wp-filesystem       |
	 *     +------------------------+---------------------+
	 *
	 * @subcommand list-info-sections
	 */
	public function list_info_sections( $args, $assoc_args ) {
		$sections = $this->get_sections();

		$format = Utils\get_flag_value( $assoc_args, 'format', 'table' );

		$assoc_args['fields'] = [ 'label', 'section' ];

		$formatter = $this->get_formatter( $assoc_args );

		$formatter->display_items( $sections );
	}

	/**
	 * Displays site health info.
	 *
	 * ## OPTIONS
	 *
	 * [<section>]
	 * : Section slug.
	 *
	 * [--all]
	 * : Displays info for all sections.
	 *
	 * [--fields=<fields>]
	 * : Limit the output to specific fields.
	 *
	 * [--format=<format>]
	 * : Render output in a particular format.
	 * ---
	 * default: table
	 * options:
	 *   - table
	 *   - csv
	 *   - json
	 *   - yaml
	 * ---
	 *
	 * [--private]
	 * : Display private fields. Disabled by default.
	 *
	 * ## EXAMPLES
	 *
	 *     # List site health info.
	 *     $ wp site-health list-info-sections
	 *     +------------------------+---------------------+
	 *     | label                  | section             |
	 *     +------------------------+---------------------+
	 *     | WordPress              | wp-core             |
	 *     | Directories and Sizes  | wp-paths-sizes      |
	 *     | Drop-ins               | wp-dropins          |
	 *     | Active Theme           | wp-active-theme     |
	 *     | Parent Theme           | wp-parent-theme     |
	 *     | Inactive Themes        | wp-themes-inactive  |
	 *     | Must Use Plugins       | wp-mu-plugins       |
	 *     | Active Plugins         | wp-plugins-active   |
	 *     | Inactive Plugins       | wp-plugins-inactive |
	 *     | Media Handling         | wp-media            |
	 *     | Server                 | wp-server           |
	 *     | Database               | wp-database         |
	 *     | WordPress Constants    | wp-constants        |
	 *     | Filesystem Permissions | wp-filesystem       |
	 *     +------------------------+---------------------+
	 */
	public function info( $args, $assoc_args ) {
		$section = reset( $args );

		$all = Utils\get_flag_value( $assoc_args, 'all', false );

		if ( empty( $section ) && ! $all ) {
			WP_CLI::error( 'Please specify a section, or use the --all flag.' );
		}

		$private = Utils\get_flag_value( $assoc_args, 'private', false );

		$default_fields = [ 'field', 'label', 'value', 'debug' ];

		if ( $private ) {
			$default_fields = [ 'field', 'private', 'label', 'value', 'debug' ];
		}

		if ( $all ) {
			$all_sections = $this->get_sections();

			$sections = wp_list_pluck( $all_sections, 'section' );

			$details = [];

			foreach ( $sections as $section ) {
				$details = array_merge( $details, $this->get_section_info( $section, $assoc_args ) );
			}
		} else {
			$details = $this->get_section_info( $section, $assoc_args );
		}

		if ( ! $private ) {
			$details = wp_list_filter( $details, [ 'private' => false ] );
		}

		if ( empty( $assoc_args['fields'] ) ) {
			$assoc_args['fields'] = $default_fields;
		}

		$formatter = $this->get_formatter( $assoc_args );

		$formatter->display_items( $details );
	}

	protected function get_sections() {
		$sections = [];

		foreach ( $this->info as $info_key => $info_item ) {
			$sections[] = [
				'label'   => $info_item['label'],
				'section' => $info_key,
			];
		}

		return $sections;
	}

	protected function get_section_info( $section ) {
		$details = [];

		if ( ! isset( $this->info[ $section ] ) ) {
			return $details;
		}

		foreach ( $this->info[ $section ]['fields'] as $field_key => $field ) {
			$details[] = array(
				'field'   => $field_key,
				'section' => $section,
				'label'   => $field['label'],
				'value'   => $field['value'],
				'debug'   => isset( $field['debug'] ) ? $field['debug'] : null,
				'private' => isset( $field['private'] ) ? (bool) $field['private'] : false,
			);
		}

		return $details;
	}

	protected function run_checks( $checks ) {
		$results = [];

		if ( ! empty( $checks ) ) {

			foreach ( $checks as $check ) {
				$result = [
					'check'       => $check['label'],
					'status'      => '',
					'label'       => '',
					'test'        => '',
					'description' => '',
					'type'        => '',
				];

				if ( 'direct' === $check['check_type'] ) {
					if ( is_string( $check['test'] ) ) {
						$test_function = sprintf( 'get_test_%s', $check['test'] );

						if ( method_exists( $this->instance, $test_function ) && is_callable( array( $this->instance, $test_function ) ) ) {
							$test_result = $this->instance->$test_function();

							$result = array_merge(
								$result,
								array(
									'status'      => $test_result['status'],
									'label'       => $test_result['label'],
									'test'        => $test_result['test'],
									'description' => wp_strip_all_tags( $test_result['description'] ),
									'type'        => $test_result['badge']['label'],
								)
							);
						}
					}
				}

				$results[] = $result;
			}
		}

		return $results;
	}

	protected function get_checks() {
		$checks = [];

		$all_checks = WP_Site_Health::get_tests();

		if ( empty( $all_checks ) ) {
			return $checks;
		}

		foreach ( $all_checks as $check_type => $check_items ) {
			foreach ( $check_items as $check_item ) {
				$checks[] = array_merge( array( 'check_type' => $check_type ), $check_item );
			}
		}

		return $checks;
	}

	private function get_status_count_details( $results ) {
		$output = [
			'critical'    => count( wp_list_filter( $results, [ 'status' => 'critical' ] ) ),
			'recommended' => count( wp_list_filter( $results, [ 'status' => 'recommended' ] ) ),
			'good'        => count( wp_list_filter( $results, [ 'status' => 'good' ] ) ),
		];

		$output['total'] = array_sum( $output );

		return $output;
	}

	/**
	 * Get Formatter object based on supplied parameters.
	 *
	 * @param array $assoc_args Parameters passed to command. Determines formatting.
	 * @return Formatter
	 */
	protected function get_formatter( &$assoc_args ) {
		if ( ! empty( $assoc_args['fields'] ) ) {
			if ( is_string( $assoc_args['fields'] ) ) {
				$fields = explode( ',', $assoc_args['fields'] );
			} else {
				$fields = $assoc_args['fields'];
			}
		} else {
			$fields = $this->obj_fields;
		}
		return new WP_CLI\Formatter( $assoc_args, $fields );
	}
}
