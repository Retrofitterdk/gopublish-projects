<?php
/**
 * Manage projects admin screen.
 *
 * @package GoPublish\Projects
 */

namespace GoPublish\Projects\Admin;

use GoPublish\Projects\PostType;
use GoPublish\Projects\Taxonomies;

final class ProjectsListScreen {

	/**
	 * Hooks the manage-projects screen behavior into WordPress.
	 */
	public function register(): void {

		add_action( 'load-edit.php', [ $this, 'load' ] );

		add_action( 'gpp_load_manage_projects', [ $this, 'add_help_tabs' ] );
	}

	/**
	 * Runs on page load. Bails unless viewing the project post type.
	 */
	public function load(): void {

		$screen       = get_current_screen();
		$project_type = PostType::slug();

		if ( empty( $screen->post_type ) || $project_type !== $screen->post_type ) {
			return;
		}

		do_action( 'gpp_load_manage_projects' );

		add_action( 'restrict_manage_posts', [ $this, 'categories_dropdown' ] );
		add_action( 'restrict_manage_posts', [ $this, 'tags_dropdown' ] );

		add_filter( "manage_edit-{$project_type}_columns", [ $this, 'columns' ] );
		add_action( "manage_{$project_type}_posts_custom_column", [ $this, 'custom_column' ], 10, 2 );

		add_action( 'admin_head', [ $this, 'print_styles' ] );
	}

	/**
	 * Prints admin column-width styles.
	 */
	public function print_styles(): void { ?>

		<style type="text/css">@media only screen and (min-width: 783px) {
			.fixed .column-thumbnail { width: 100px; }
			.fixed .column-taxonomy-<?php echo esc_attr( Taxonomies::category_slug() ); ?>,
			.fixed .column-taxonomy-<?php echo esc_attr( Taxonomies::tag_slug() ); ?> { width: 15%; }
		}</style>
	<?php }

	/**
	 * Renders a categories dropdown below the table nav.
	 */
	public function categories_dropdown(): void {

		$this->terms_dropdown( Taxonomies::category_slug() );
	}

	/**
	 * Renders a tags dropdown below the table nav.
	 */
	public function tags_dropdown(): void {

		$this->terms_dropdown( Taxonomies::tag_slug() );
	}

	/**
	 * Renders a terms dropdown for the given taxonomy.
	 */
	private function terms_dropdown( string $taxonomy ): void {

		wp_dropdown_categories(
			[
				'show_option_all'   => false,
				'show_option_none'  => get_taxonomy( $taxonomy )->labels->all_items,
				'option_none_value' => '',
				'orderby'           => 'name',
				'order'             => 'ASC',
				'show_count'        => true,
				'selected'          => isset( $_GET[ $taxonomy ] ) ? esc_attr( $_GET[ $taxonomy ] ) : '',
				'hierarchical'      => true,
				'name'              => $taxonomy,
				'id'                => '',
				'class'             => 'postform',
				'taxonomy'          => $taxonomy,
				'hide_if_empty'     => true,
				'value_field'       => 'slug',
			]
		);
	}

	/**
	 * Adds custom columns to the projects list screen.
	 */
	public function columns( array $columns ): array {

		$new_columns = [
			'cb'    => $columns['cb'],
			'title' => __( 'Project', 'gopublish-projects' ),
		];

		if ( current_theme_supports( 'post-thumbnails' ) ) {
			$new_columns['thumbnail'] = __( 'Thumbnail', 'gopublish-projects' );
		}

		$columns = array_merge( $new_columns, $columns );

		$columns['title'] = $new_columns['title'];

		return $columns;
	}

	/**
	 * Renders custom column content.
	 */
	public function custom_column( string $column, int $post_id ): void {

		if ( 'thumbnail' === $column && has_post_thumbnail() ) {
			the_post_thumbnail( [ 75, 75 ] );
		}
	}

	/**
	 * Adds help tabs to the manage-projects screen.
	 */
	public function add_help_tabs(): void {

		$screen = get_current_screen();

		$screen->add_help_tab(
			[
				'id'       => 'overview',
				'title'    => esc_html__( 'Overview', 'gopublish-projects' ),
				'callback' => [ $this, 'help_tab_overview' ],
			]
		);

		$screen->add_help_tab(
			[
				'id'       => 'screen_content',
				'title'    => esc_html__( 'Screen Content', 'gopublish-projects' ),
				'callback' => [ $this, 'help_tab_screen_content' ],
			]
		);

		$screen->add_help_tab(
			[
				'id'       => 'available_actions',
				'title'    => esc_html__( 'Available Actions', 'gopublish-projects' ),
				'callback' => [ $this, 'help_tab_available_actions' ],
			]
		);

		$screen->set_help_sidebar( Help::sidebar_text() );
	}

	/**
	 * Displays the overview help tab.
	 */
	public function help_tab_overview(): void { ?>

		<p>
			<?php esc_html_e( 'This screen provides access to all of your portfolio projects. You can customize the display of this screen to suit your workflow.', 'gopublish-projects' ); ?>
		</p>
	<?php }

	/**
	 * Displays the screen content help tab.
	 */
	public function help_tab_screen_content(): void { ?>

		<p>
			<?php esc_html_e( "You can customize the display of this screen's contents in a number of ways:", 'gopublish-projects' ); ?>
		</p>

		<ul>
			<li><?php esc_html_e( 'You can hide/display columns based on your needs and decide how many projects to list per screen using the Screen Options tab.', 'gopublish-projects' ); ?></li>
			<li><?php esc_html_e( 'You can filter the list of projects by post status using the text links in the upper left to show All, Published, Draft, or Trashed projects. The default view is to show all projects.', 'gopublish-projects' ); ?></li>
			<li><?php esc_html_e( 'You can view projects in a simple title list or with an excerpt. Choose the view you prefer by clicking on the icons at the top of the list on the right.', 'gopublish-projects' ); ?></li>
			<li><?php esc_html_e( 'You can refine the list to show only projects in a specific category, with a specific tag, or from a specific month by using the dropdown menus above the projects list. Click the Filter button after making your selection. You also can refine the list by clicking on the project author, category or tag in the posts list.', 'gopublish-projects' ); ?></li>
		</ul>
	<?php }

	/**
	 * Displays the available actions help tab.
	 */
	public function help_tab_available_actions(): void { ?>

		<p>
			<?php esc_html_e( 'Hovering over a row in the projects list will display action links that allow you to manage your project. You can perform the following actions:', 'gopublish-projects' ); ?>
		</p>

		<ul>
			<li><?php _e( '<strong>Edit</strong> takes you to the editing screen for that project. You can also reach that screen by clicking on the project title.', 'gopublish-projects' ); ?></li>
			<li><?php _e( '<strong>Trash</strong> removes your project from this list and places it in the trash, from which you can permanently delete it.', 'gopublish-projects' ); ?></li>
			<li><?php _e( "<strong>Preview</strong> will show you what your draft project will look like if you publish it. View will take you to your live site to view the project. Which link is available depends on your project's status.", 'gopublish-projects' ); ?></li>
		</ul>
	<?php }
}
