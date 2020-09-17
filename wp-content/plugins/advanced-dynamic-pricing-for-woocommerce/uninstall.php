<?php

namespace ADP;

use ADP\BaseVersion\Includes\Common\Database;
use ADP\BaseVersion\Includes\Context;

if ( defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	include_once "AutoLoader.php";
	include_once "Factory.php";

	\ADP\AutoLoader::register();

	$path = trailingslashit( dirname( __FILE__ ) );

	$context = new Context();
	// delete tables  only if have value in settings
	if ( $context->get_option( 'uninstall_remove_data' ) ) {
		Database::delete_database();
	}

	$extension_file = $path . 'ProVersion/uninstall.php';
	if ( file_exists( $extension_file ) ) {
		include_once $extension_file;
	}
}