<?php
/**
 * New/Edit project admin screen.
 *
 * @package GoPublish\Projects
 */

namespace GoPublish\Projects\Admin;

use GoPublish\Projects\PostType;

final class ProjectEditScreen {

	/**
	 * Hooks the edit-screen behavior into WordPress.
	 */
	public function register(): void {

		add_action( 'load-post.php', [ $this, 'load' ] );
		add_action( 'load-post-new.php', [ $this, 'load' ] );

		add_action( 'gpp_load_project_edit', [ $this, 'add_help_tabs' ] );
	}

	/**
	 * Runs on page load. Bails unless viewing the project post type.
	 */
	public function load(): void {

		$screen = get_current_screen();

		if ( empty( $screen->post_type ) || PostType::slug() !== $screen->post_type ) {
			return;
		}

		do_action( 'gpp_load_project_edit' );

		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue' ] );

		add_filter( 'wp_dropdown_users_args', [ $this, 'dropdown_users_args' ], 10, 2 );
	}

	/**
	 * Enqueues the project details meta panel script.
	 */
	public function enqueue(): void {

		$asset_file = \GoPublish\Projects\PATH . 'build/edit-project.asset.php';

		if ( ! file_exists( $asset_file ) ) {
			return;
		}

		$asset = require $asset_file;

		wp_enqueue_script(
			'gpp-edit-project',
			\GoPublish\Projects\URL . 'build/edit-project.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);

		wp_set_script_translations( 'gpp-edit-project', 'gopublish-projects', \GoPublish\Projects\PATH . 'lang' );
	}

	/**
	 * Restricts the "Author" meta box's user drop-down to roles that can
	 * actually edit projects.
	 */
	public function dropdown_users_args( array $args, array $r ): array {
		global $wp_roles, $post;

		if ( 'post_author_override' !== $r['name'] || PostType::slug() !== $post->post_type ) {
			return $args;
		}

		$roles = [];
		$cap   = get_post_type_object( PostType::slug() )->cap->edit_posts;

		foreach ( $wp_roles->roles as $name => $role ) {
			if ( isset( $role['capabilities'][ $cap ] ) && true === $role['capabilities'][ $cap ] ) {
				$roles[] = $name;
			}
		}

		if ( $roles ) {
			$args['who']      = '';
			$args['role__in'] = $roles;
		}

		return $args;
	}

	/**
	 * Adds help tabs to the edit-project screen.
	 */
	public function add_help_tabs(): void {

		$screen = get_current_screen();

		$screen->add_help_tab(
			[
				'id'       => 'title_editor',
				'title'    => esc_html__( 'Title and Editor', 'gopublish-projects' ),
				'callback' => [ $this, 'help_tab_title_editor' ],
			]
		);

		$screen->add_help_tab(
			[
				'id'       => 'project_details',
				'title'    => esc_html__( 'Project Details', 'gopublish-projects' ),
				'callback' => [ $this, 'help_tab_project_details' ],
			]
		);

		$screen->set_help_sidebar( Help::sidebar_text() );
	}

	/**
	 * Displays the title and editor help tab.
	 */
	public function help_tab_title_editor(): void { ?>

		<ul>
			<li><?php _e( "<strong>Title:</strong> Enter a title for your project. After you enter a title, you'll see the permalink below, which you can edit.", 'gopublish-projects' ); ?></li>
			<li><?php _e( '<strong>Editor:</strong> The editor allows you to add or edit content for your project. You can insert text, media, or blocks.', 'gopublish-projects' ); ?></li>
		</ul>
	<?php }

	/**
	 * Displays the project details help tab.
	 */
	public function help_tab_project_details(): void { ?>

		<p>
			<?php esc_html_e( 'The Project Details panel in the editor sidebar allows you to customize the details of your project. All fields are optional.', 'gopublish-projects' ); ?>
		</p>

		<ul>
			<li><?php _e( '<strong>URL:</strong> The URL to the Web site or page associated with the project, such as a client Web site.', 'gopublish-projects' ); ?></li>
			<li><?php _e( '<strong>Client:</strong> The name of the client the project was built for.', 'gopublish-projects' ); ?></li>
			<li><?php _e( '<strong>Location:</strong> A physical location where the project took place (e.g., Highland Home, AL, USA).', 'gopublish-projects' ); ?></li>
			<li><?php _e( '<strong>Start Date:</strong> The date the project began.', 'gopublish-projects' ); ?></li>
			<li><?php _e( '<strong>End Date:</strong> The date the project was completed.', 'gopublish-projects' ); ?></li>
		</ul>
	<?php }
}
