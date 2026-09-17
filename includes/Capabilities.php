<?php
/**
 * Project capabilities: activation-time role grants, plus optional Members
 * plugin integration for pretty capability labels.
 *
 * @package GoPublish\Projects
 */

namespace GoPublish\Projects;

final class Capabilities {

	/**
	 * Every capability the plugin grants to the administrator role on activation
	 * (and revokes on uninstall). Excludes meta caps like `edit_post`/`read_post`,
	 * which are never assigned to roles directly.
	 */
	public static function all(): array {

		return [
			// Project caps.
			'create_portfolio_projects',
			'edit_portfolio_projects',
			'edit_others_portfolio_projects',
			'read_private_portfolio_projects',
			'delete_portfolio_projects',
			'delete_private_portfolio_projects',
			'delete_published_portfolio_projects',
			'delete_others_portfolio_projects',
			'edit_private_portfolio_projects',
			'edit_published_portfolio_projects',
			'publish_portfolio_projects',

			// Category caps.
			'assign_portfolio_categories',
			'delete_portfolio_categories',
			'edit_portfolio_categories',
			'manage_portfolio_categories',

			// Tag caps.
			'assign_portfolio_tags',
			'delete_portfolio_tags',
			'edit_portfolio_tags',
			'manage_portfolio_tags',
		];
	}

	/**
	 * Grants every project capability to the given role.
	 */
	public static function grant( \WP_Role $role ): void {

		foreach ( self::all() as $cap ) {
			$role->add_cap( $cap );
		}
	}

	/**
	 * Removes every project capability from the given role.
	 */
	public static function revoke( \WP_Role $role ): void {

		foreach ( self::all() as $cap ) {
			$role->remove_cap( $cap );
		}
	}

	/**
	 * Hooks the optional Members-plugin integration into WordPress. These actions
	 * only fire if the Members plugin is active, so this is safe either way.
	 */
	public function register(): void {

		add_action( 'members_register_cap_groups', [ $this, 'register_cap_groups' ] );
		add_action( 'members_register_caps', [ $this, 'register_caps' ] );
	}

	/**
	 * Overwrites the cap group registered within the Members plugin so its
	 * label reads "Portfolio".
	 */
	public function register_cap_groups(): void {

		$group = members_get_cap_group( 'type-' . PostType::slug() );

		if ( $group ) {
			$group->label = __( 'Portfolio', 'gopublish-projects' );
		}
	}

	/**
	 * Registers capabilities with the Members plugin so each one gets a
	 * readable label instead of its raw slug.
	 */
	public function register_caps(): void {

		$group = sprintf( 'type-%s', PostType::slug() );

		$labels = [
			'create_portfolio_projects'           => __( 'Create Projects', 'gopublish-projects' ),
			'edit_portfolio_projects'             => __( 'Edit Projects', 'gopublish-projects' ),
			'edit_others_portfolio_projects'      => __( "Edit Others' Projects", 'gopublish-projects' ),
			'read_private_portfolio_projects'     => __( 'Read Private Projects', 'gopublish-projects' ),
			'delete_portfolio_projects'           => __( 'Delete Projects', 'gopublish-projects' ),
			'delete_private_portfolio_projects'   => __( 'Delete Private Projects', 'gopublish-projects' ),
			'delete_published_portfolio_projects' => __( 'Delete Published Projects', 'gopublish-projects' ),
			'delete_others_portfolio_projects'    => __( "Delete Others' Projects", 'gopublish-projects' ),
			'edit_private_portfolio_projects'     => __( 'Edit Private Projects', 'gopublish-projects' ),
			'edit_published_portfolio_projects'   => __( 'Edit Published Projects', 'gopublish-projects' ),
			'publish_portfolio_projects'          => __( 'Publish Projects', 'gopublish-projects' ),

			'assign_portfolio_categories' => __( 'Assign Project Categories', 'gopublish-projects' ),
			'delete_portfolio_categories' => __( 'Delete Project Categories', 'gopublish-projects' ),
			'edit_portfolio_categories'   => __( 'Edit Project Categories', 'gopublish-projects' ),
			'manage_portfolio_categories' => __( 'Manage Project Categories', 'gopublish-projects' ),

			'assign_portfolio_tags' => __( 'Assign Project Tags', 'gopublish-projects' ),
			'delete_portfolio_tags' => __( 'Delete Project Tags', 'gopublish-projects' ),
			'edit_portfolio_tags'   => __( 'Edit Project Tags', 'gopublish-projects' ),
			'manage_portfolio_tags' => __( 'Manage Project Tags', 'gopublish-projects' ),
		];

		foreach ( $labels as $name => $label ) {
			members_register_cap( $name, [ 'label' => $label, 'group' => $group ] );
		}
	}
}
