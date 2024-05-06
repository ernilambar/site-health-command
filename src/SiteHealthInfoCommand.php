<?php

namespace WP_CLI\SiteHealth;

use WP_CLI;
use WP_CLI\Utils;
use WP_Debug_Data;
use WP_CLI_Command;

/**
 * Manage Site Health Info
 *
 * @package wp-cli
 */
class SiteHealthInfoCommand extends WP_CLI_Command {

	/**
	 * @var array $info Debug info.
	 */
	protected $info;

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( ! class_exists( 'WP_Debug_Data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-debug-data.php';
		}

		$this->info = WP_Debug_Data::debug_data();
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
	 *     $ wp site-health info sections
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
	public function sections( $args, $assoc_args ) {
		$sections = $this->get_sections();

		$format = Utils\get_flag_value( $assoc_args, 'format', 'table' );

		$assoc_args['fields'] = [ 'label', 'section' ];

		$formatter = $this->get_formatter( $assoc_args );

		$formatter->display_items( $sections );
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
