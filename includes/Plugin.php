<?php
/**
 * Boots every feature of the plugin.
 *
 * @package GoPublish\Projects
 */

namespace GoPublish\Projects;

final class Plugin {

	/**
	 * Loads translations and wires up every feature class.
	 */
	public static function boot(): void {

		load_plugin_textdomain( 'gopublish-projects', false, dirname( plugin_basename( PATH . 'gopublish-projects.php' ) ) . '/lang' );

		( new PostType() )->register();
		( new Taxonomies() )->register();
		( new Meta() )->register();
		( new Capabilities() )->register();
		( new Rewrite() )->register();
		( new Filters() )->register();

		if ( is_admin() ) {
			( new Admin\ProjectEditScreen() )->register();
			( new Admin\ProjectsListScreen() )->register();
			( new Admin\SettingsPage() )->register();
		}
	}

	/**
	 * Runs on plugin activation. Grants the plugin's capabilities to the
	 * administrator role.
	 */
	public static function activate(): void {

		$role = get_role( 'administrator' );

		if ( $role ) {
			Capabilities::grant( $role );
		}
	}
}
