<?php
/**
 * Registers the "project" post type.
 *
 * @package GoPublish\Projects
 */

namespace GoPublish\Projects;

final class PostType {

	/**
	 * Hooks the post type registration and admin-facing filters into WordPress.
	 */
	public function register(): void {

		add_action( 'init', [ $this, 'register_post_type' ] );

		add_filter( 'enter_title_here', [ $this, 'enter_title_here' ], 10, 2 );

		add_filter( 'bulk_post_updated_messages', [ $this, 'bulk_post_updated_messages' ], 5, 2 );
		add_filter( 'post_updated_messages', [ $this, 'post_updated_messages' ], 5 );
	}

	/**
	 * The project post type slug.
	 */
	public static function slug(): string {

		return apply_filters( 'gpp_project_post_type', 'portfolio_project' );
	}

	/**
	 * Registers the project post type.
	 */
	public function register_post_type(): void {

		$args = [
			'description'         => Settings::description(),
			'public'              => true,
			'publicly_queryable'  => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'exclude_from_search' => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => true,
			'menu_position'       => null,
			'menu_icon'           => 'dashicons-portfolio',
			'can_export'          => true,
			'delete_with_user'    => false,
			'hierarchical'        => false,
			'has_archive'         => Settings::portfolio_rewrite_base(),
			'query_var'           => self::slug(),
			'capability_type'     => 'portfolio_project',
			'map_meta_cap'        => true,
			'capabilities'        => $this->capabilities(),
			'labels'              => $this->labels(),

			'rewrite' => [
				'slug'       => Rewrite::project_rewrite_slug(),
				'with_front' => false,
				'pages'      => true,
				'feeds'      => true,
				'ep_mask'    => EP_PERMALINK,
			],

			'supports' => [
				'title',
				'editor',
				'excerpt',
				'author',
				'thumbnail',
				'custom-fields', // Required for the project details meta panel in the block editor.
			],
		];

		register_post_type( self::slug(), apply_filters( 'gpp_project_post_type_args', $args ) );
	}

	/**
	 * Capability map for the project post type.
	 */
	private function capabilities(): array {

		$caps = [
			// Meta caps (don't assign these to roles).
			'edit_post'   => 'edit_portfolio_project',
			'read_post'   => 'read_portfolio_project',
			'delete_post' => 'delete_portfolio_project',

			// Primitive/meta caps.
			'create_posts' => 'create_portfolio_projects',

			// Primitive caps used outside of map_meta_cap().
			'edit_posts'         => 'edit_portfolio_projects',
			'edit_others_posts'  => 'edit_others_portfolio_projects',
			'publish_posts'      => 'publish_portfolio_projects',
			'read_private_posts' => 'read_private_portfolio_projects',

			// Primitive caps used inside of map_meta_cap().
			'read'                   => 'read',
			'delete_posts'           => 'delete_portfolio_projects',
			'delete_private_posts'   => 'delete_private_portfolio_projects',
			'delete_published_posts' => 'delete_published_portfolio_projects',
			'delete_others_posts'    => 'delete_others_portfolio_projects',
			'edit_private_posts'     => 'edit_private_portfolio_projects',
			'edit_published_posts'   => 'edit_published_portfolio_projects',
		];

		return apply_filters( 'gpp_project_capabilities', $caps );
	}

	/**
	 * Labels for the project post type.
	 */
	private function labels(): array {

		$labels = [
			'name'                  => __( 'Projects', 'gopublish-projects' ),
			'singular_name'         => __( 'Project', 'gopublish-projects' ),
			'menu_name'             => __( 'Portfolio', 'gopublish-projects' ),
			'name_admin_bar'        => __( 'Project', 'gopublish-projects' ),
			'add_new'               => __( 'New Project', 'gopublish-projects' ),
			'add_new_item'          => __( 'Add New Project', 'gopublish-projects' ),
			'edit_item'             => __( 'Edit Project', 'gopublish-projects' ),
			'new_item'              => __( 'New Project', 'gopublish-projects' ),
			'view_item'             => __( 'View Project', 'gopublish-projects' ),
			'view_items'            => __( 'View Projects', 'gopublish-projects' ),
			'search_items'          => __( 'Search Projects', 'gopublish-projects' ),
			'not_found'             => __( 'No projects found', 'gopublish-projects' ),
			'not_found_in_trash'    => __( 'No projects found in trash', 'gopublish-projects' ),
			'all_items'             => __( 'Projects', 'gopublish-projects' ),
			'featured_image'        => __( 'Project Image', 'gopublish-projects' ),
			'set_featured_image'    => __( 'Set project image', 'gopublish-projects' ),
			'remove_featured_image' => __( 'Remove project image', 'gopublish-projects' ),
			'use_featured_image'    => __( 'Use as project image', 'gopublish-projects' ),
			'insert_into_item'      => __( 'Insert into project', 'gopublish-projects' ),
			'uploaded_to_this_item' => __( 'Uploaded to this project', 'gopublish-projects' ),
			'filter_items_list'     => __( 'Filter projects list', 'gopublish-projects' ),
			'items_list_navigation' => __( 'Projects list navigation', 'gopublish-projects' ),
			'items_list'            => __( 'Projects list', 'gopublish-projects' ),

			// Custom label; WordPress doesn't have anything to handle this.
			'archive_title' => Settings::title(),
		];

		return apply_filters( 'gpp_project_labels', $labels );
	}

	/**
	 * Custom "Enter title here" placeholder.
	 */
	public function enter_title_here( string $title, \WP_Post $post ): string {

		return self::slug() === $post->post_type ? esc_html__( 'Enter project title', 'gopublish-projects' ) : $title;
	}

	/**
	 * Custom post-updated messages on the edit project screen.
	 */
	public function post_updated_messages( array $messages ): array {
		global $post, $post_ID;

		if ( self::slug() !== $post->post_type ) {
			return $messages;
		}

		$permalink   = get_permalink( $post_ID );
		$preview_url = get_preview_post_link( $post );

		// Translators: Scheduled project date format. See https://www.php.net/date.
		$scheduled_date = date_i18n( __( 'M j, Y @ H:i', 'gopublish-projects' ), strtotime( $post->post_date ) );

		$preview_link   = sprintf( ' <a target="_blank" href="%1$s">%2$s</a>', esc_url( $preview_url ), esc_html__( 'Preview project', 'gopublish-projects' ) );
		$scheduled_link = sprintf( ' <a target="_blank" href="%1$s">%2$s</a>', esc_url( $permalink ), esc_html__( 'Preview project', 'gopublish-projects' ) );
		$view_link      = sprintf( ' <a href="%1$s">%2$s</a>', esc_url( $permalink ), esc_html__( 'View project', 'gopublish-projects' ) );

		$messages[ self::slug() ] = [
			1 => esc_html__( 'Project updated.', 'gopublish-projects' ) . $view_link,
			4 => esc_html__( 'Project updated.', 'gopublish-projects' ),
			// Translators: %s is the date and time of the revision.
			5 => isset( $_GET['revision'] ) ? sprintf( esc_html__( 'Project restored to revision from %s.', 'gopublish-projects' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => esc_html__( 'Project published.', 'gopublish-projects' ) . $view_link,
			7 => esc_html__( 'Project saved.', 'gopublish-projects' ),
			8 => esc_html__( 'Project submitted.', 'gopublish-projects' ) . $preview_link,
			9 => sprintf( esc_html__( 'Project scheduled for: %s.', 'gopublish-projects' ), "<strong>{$scheduled_date}</strong>" ) . $scheduled_link,
			10 => esc_html__( 'Project draft updated.', 'gopublish-projects' ) . $preview_link,
		];

		return $messages;
	}

	/**
	 * Custom bulk post-updated messages on the manage projects screen.
	 */
	public function bulk_post_updated_messages( array $messages, array $counts ): array {

		$type = self::slug();

		$messages[ $type ]['updated']   = _n( '%s project updated.', '%s projects updated.', $counts['updated'], 'gopublish-projects' );
		$messages[ $type ]['locked']    = _n( '%s project not updated, somebody is editing it.', '%s projects not updated, somebody is editing them.', $counts['locked'], 'gopublish-projects' );
		$messages[ $type ]['deleted']   = _n( '%s project permanently deleted.', '%s projects permanently deleted.', $counts['deleted'], 'gopublish-projects' );
		$messages[ $type ]['trashed']   = _n( '%s project moved to the Trash.', '%s projects moved to the trash.', $counts['trashed'], 'gopublish-projects' );
		$messages[ $type ]['untrashed'] = _n( '%s project restored from the Trash.', '%s projects restored from the trash.', $counts['untrashed'], 'gopublish-projects' );

		return $messages;
	}
}
