<?php
/**
 * Plugin uninstall file.
 *
 * @package GoPublish\Projects
 */

namespace GoPublish\Projects;

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

require_once __DIR__ . '/includes/Capabilities.php';
require_once __DIR__ . '/includes/Settings.php';

delete_option( Settings::OPTION );

$role = get_role( 'administrator' );

if ( $role ) {
	Capabilities::revoke( $role );
}
