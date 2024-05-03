<?php

namespace WP_CLI\SiteHealth;

use WP_CLI;

if ( ! class_exists( '\WP_CLI' ) ) {
	return;
}

$wpcli_site_health_autoloader = __DIR__ . '/vendor/autoload.php';

if ( file_exists( $wpcli_site_health_autoloader ) ) {
	require_once $wpcli_site_health_autoloader;
}

WP_CLI::add_command( 'site-health', SiteHealthCommand::class );
