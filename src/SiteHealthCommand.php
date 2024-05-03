<?php

namespace WP_CLI\SiteHealth;

use WP_CLI;
use WP_CLI\Utils;
use WP_Site_Health;
use WP_CLI_Command;

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
		$this->instance = WP_Site_Health::get_instance();
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
	 *
	 * @param array $args       Indexed array of positional arguments.
	 * @param array $assoc_args Associative array of associative arguments.
	 */
	public function check( $args, $assoc_args ) {
		$checks = $this->get_checks();

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

		$format = Utils\get_flag_value( $assoc_args, 'format', 'table' );

		$formatter = $this->get_formatter( $assoc_args );

		$formatter->display_items( $results );
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
