<?php
/**
 * Plugin settings screen.
 *
 * @package GoPublish\Projects
 */

namespace GoPublish\Projects\Admin;

use GoPublish\Projects\PostType;
use GoPublish\Projects\Taxonomies;
use GoPublish\Projects\Settings;

final class SettingsPage {

	/**
	 * The settings page's hook suffix, set once the menu item is added.
	 */
	private string $page = '';

	/**
	 * Hooks the settings screen into WordPress.
	 */
	public function register(): void {

		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
	}

	/**
	 * Adds the settings submenu page.
	 */
	public function admin_menu(): void {

		$this->page = add_submenu_page(
			'edit.php?post_type=' . PostType::slug(),
			esc_html__( 'Portfolio Settings', 'gopublish-projects' ),
			esc_html__( 'Settings', 'gopublish-projects' ),
			apply_filters( 'gpp_settings_capability', 'manage_options' ),
			'gpp-settings',
			[ $this, 'settings_page' ]
		);

		if ( $this->page ) {
			add_action( 'admin_init', [ $this, 'register_settings' ] );
			add_action( "load-{$this->page}", [ $this, 'add_help_tabs' ] );
		}
	}

	/**
	 * Registers the plugin's settings, sections, and fields.
	 */
	public function register_settings(): void {

		register_setting( Settings::OPTION, Settings::OPTION, [ $this, 'validate_settings' ] );

		add_settings_section( 'general', esc_html__( 'General Settings', 'gopublish-projects' ), [ $this, 'section_general' ], $this->page );
		add_settings_section( 'permalinks', esc_html__( 'Permalinks', 'gopublish-projects' ), [ $this, 'section_permalinks' ], $this->page );

		add_settings_field( 'portfolio_title', esc_html__( 'Title', 'gopublish-projects' ), [ $this, 'field_portfolio_title' ], $this->page, 'general' );
		add_settings_field( 'portfolio_description', esc_html__( 'Description', 'gopublish-projects' ), [ $this, 'field_portfolio_description' ], $this->page, 'general' );

		add_settings_field( 'portfolio_rewrite_base', esc_html__( 'Portfolio Base', 'gopublish-projects' ), [ $this, 'field_portfolio_rewrite_base' ], $this->page, 'permalinks' );
		add_settings_field( 'project_rewrite_base', esc_html__( 'Project Slug', 'gopublish-projects' ), [ $this, 'field_project_rewrite_base' ], $this->page, 'permalinks' );
		add_settings_field( 'category_rewrite_base', esc_html__( 'Category Slug', 'gopublish-projects' ), [ $this, 'field_category_rewrite_base' ], $this->page, 'permalinks' );
		add_settings_field( 'tag_rewrite_base', esc_html__( 'Tag Slug', 'gopublish-projects' ), [ $this, 'field_tag_rewrite_base' ], $this->page, 'permalinks' );
		add_settings_field( 'author_rewrite_base', esc_html__( 'Author Slug', 'gopublish-projects' ), [ $this, 'field_author_rewrite_base' ], $this->page, 'permalinks' );
	}

	/**
	 * Validates and sanitizes the submitted settings.
	 */
	public function validate_settings( array $settings ): array {

		$settings['portfolio_rewrite_base'] = $settings['portfolio_rewrite_base'] ? trim( strip_tags( $settings['portfolio_rewrite_base'] ), '/' ) : 'portfolio';
		$settings['project_rewrite_base']   = $settings['project_rewrite_base'] ? trim( strip_tags( $settings['project_rewrite_base'] ), '/' ) : '';
		$settings['category_rewrite_base']  = $settings['category_rewrite_base'] ? trim( strip_tags( $settings['category_rewrite_base'] ), '/' ) : '';
		$settings['tag_rewrite_base']       = $settings['tag_rewrite_base'] ? trim( strip_tags( $settings['tag_rewrite_base'] ), '/' ) : '';
		$settings['author_rewrite_base']    = $settings['author_rewrite_base'] ? trim( strip_tags( $settings['author_rewrite_base'] ), '/' ) : '';
		$settings['portfolio_title']        = $settings['portfolio_title'] ? strip_tags( $settings['portfolio_title'] ) : esc_html__( 'Portfolio', 'gopublish-projects' );

		$settings['portfolio_description'] = stripslashes( wp_filter_post_kses( addslashes( $settings['portfolio_description'] ) ) );

		/* Handle permalink conflicts. */

		if ( ! $settings['project_rewrite_base'] && ! $settings['category_rewrite_base'] ) {
			$settings['category_rewrite_base'] = 'categories';
		}

		if ( ! $settings['project_rewrite_base'] && ! $settings['tag_rewrite_base'] ) {
			$settings['tag_rewrite_base'] = 'tags';
		}

		if ( ! $settings['project_rewrite_base'] && ! $settings['author_rewrite_base'] ) {
			$settings['author_rewrite_base'] = 'authors';
		}

		if ( ! $settings['category_rewrite_base'] && ! $settings['tag_rewrite_base'] ) {
			$settings['tag_rewrite_base'] = 'tags';
		}

		if ( ! $settings['category_rewrite_base'] && ! $settings['author_rewrite_base'] ) {
			$settings['author_rewrite_base'] = 'authors';
		}

		if ( ! $settings['author_rewrite_base'] && ! $settings['tag_rewrite_base'] ) {
			$settings['tag_rewrite_base'] = 'tags';
		}

		return $settings;
	}

	public function section_general(): void { ?>

		<p class="description">
			<?php esc_html_e( 'General portfolio settings for your site.', 'gopublish-projects' ); ?>
		</p>
	<?php }

	public function field_portfolio_title(): void { ?>

		<label>
			<input type="text" class="regular-text" name="<?php echo esc_attr( Settings::OPTION ); ?>[portfolio_title]" value="<?php echo esc_attr( Settings::title() ); ?>" />
			<br />
			<span class="description"><?php esc_html_e( 'The name of your portfolio. May be used for the portfolio page title and other places, depending on your theme.', 'gopublish-projects' ); ?></span>
		</label>
	<?php }

	public function field_portfolio_description(): void {

		wp_editor(
			Settings::description(),
			'gpp_portfolio_description',
			[
				'textarea_name'    => Settings::OPTION . '[portfolio_description]',
				'drag_drop_upload' => true,
				'editor_height'    => 150,
			]
		); ?>

		<p>
			<span class="description"><?php esc_html_e( 'Your portfolio description. This may be shown by your theme on the portfolio page.', 'gopublish-projects' ); ?></span>
		</p>
	<?php }

	public function section_permalinks(): void { ?>

		<p class="description">
			<?php esc_html_e( 'Set up custom permalinks for the portfolio section on your site.', 'gopublish-projects' ); ?>
		</p>
	<?php }

	public function field_portfolio_rewrite_base(): void { ?>

		<label>
			<code><?php echo esc_url( home_url( '/' ) ); ?></code>
			<input type="text" class="regular-text code" name="<?php echo esc_attr( Settings::OPTION ); ?>[portfolio_rewrite_base]" value="<?php echo esc_attr( Settings::portfolio_rewrite_base() ); ?>" />
		</label>
	<?php }

	public function field_project_rewrite_base(): void { ?>

		<label>
			<code><?php echo esc_url( home_url( Settings::portfolio_rewrite_base() . '/' ) ); ?></code>
			<input type="text" class="regular-text code" name="<?php echo esc_attr( Settings::OPTION ); ?>[project_rewrite_base]" value="<?php echo esc_attr( Settings::project_rewrite_base() ); ?>" />
		</label>
	<?php }

	public function field_category_rewrite_base(): void { ?>

		<label>
			<code><?php echo esc_url( home_url( Settings::portfolio_rewrite_base() . '/' ) ); ?></code>
			<input type="text" class="regular-text code" name="<?php echo esc_attr( Settings::OPTION ); ?>[category_rewrite_base]" value="<?php echo esc_attr( Settings::category_rewrite_base() ); ?>" />
		</label>
	<?php }

	public function field_tag_rewrite_base(): void { ?>

		<label>
			<code><?php echo esc_url( home_url( Settings::portfolio_rewrite_base() . '/' ) ); ?></code>
			<input type="text" class="regular-text code" name="<?php echo esc_attr( Settings::OPTION ); ?>[tag_rewrite_base]" value="<?php echo esc_attr( Settings::tag_rewrite_base() ); ?>" />
		</label>
	<?php }

	public function field_author_rewrite_base(): void { ?>

		<label>
			<code><?php echo esc_url( home_url( Settings::portfolio_rewrite_base() . '/' ) ); ?></code>
			<input type="text" class="regular-text code" name="<?php echo esc_attr( Settings::OPTION ); ?>[author_rewrite_base]" value="<?php echo esc_attr( Settings::author_rewrite_base() ); ?>" />
		</label>
	<?php }

	/**
	 * Renders the settings page.
	 */
	public function settings_page(): void {

		if ( isset( $_GET['settings-updated'] ) ) {
			flush_rewrite_rules();
		} ?>

		<div class="wrap">
			<h1><?php esc_html_e( 'Portfolio Settings', 'gopublish-projects' ); ?></h1>

			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php settings_fields( Settings::OPTION ); ?>
				<?php do_settings_sections( $this->page ); ?>
				<?php submit_button( esc_attr__( 'Update Settings', 'gopublish-projects' ), 'primary' ); ?>
			</form>

		</div><!-- .wrap -->
	<?php }

	/**
	 * Adds help tabs to the settings screen.
	 */
	public function add_help_tabs(): void {

		$screen = get_current_screen();

		$screen->add_help_tab(
			[
				'id'       => 'general',
				'title'    => esc_html__( 'General Settings', 'gopublish-projects' ),
				'callback' => [ $this, 'help_tab_general' ],
			]
		);

		$screen->add_help_tab(
			[
				'id'       => 'permalinks',
				'title'    => esc_html__( 'Permalinks', 'gopublish-projects' ),
				'callback' => [ $this, 'help_tab_permalinks' ],
			]
		);

		$screen->set_help_sidebar( Help::sidebar_text() );
	}

	public function help_tab_general(): void { ?>

		<ul>
			<li><?php _e( '<strong>Title:</strong> Allows you to set the title for the portfolio section on your site. This is generally shown on the portfolio projects archive, but themes and other plugins may use it in other ways.', 'gopublish-projects' ); ?></li>
			<li><?php _e( '<strong>Description:</strong> This is the description for your portfolio. Some themes may display this on the portfolio projects archive.', 'gopublish-projects' ); ?></li>
		</ul>
	<?php }

	public function help_tab_permalinks(): void { ?>

		<ul>
			<li><?php _e( '<strong>Portfolio Base:</strong> The primary URL for the portfolio section on your site. It lists your portfolio projects.', 'gopublish-projects' ); ?></li>
			<li>
				<?php _e( '<strong>Project Slug:</strong> The slug for single portfolio projects. You can use something custom, leave this field empty, or use one of the following tags:', 'gopublish-projects' ); ?>
				<ul>
					<li><?php printf( esc_html__( '%s - The project author name.', 'gopublish-projects' ), '<code>%author%</code>' ); ?></li>
					<li><?php printf( esc_html__( '%s - The project category.', 'gopublish-projects' ), '<code>%' . Taxonomies::category_slug() . '%</code>' ); ?></li>
					<li><?php printf( esc_html__( '%s - The project tag.', 'gopublish-projects' ), '<code>%' . Taxonomies::tag_slug() . '%</code>' ); ?></li>
				</ul>
			</li>
			<li><?php _e( '<strong>Category Slug:</strong> The base slug used for portfolio category archives.', 'gopublish-projects' ); ?></li>
			<li><?php _e( '<strong>Tag Slug:</strong> The base slug used for portfolio tag archives.', 'gopublish-projects' ); ?></li>
			<li><?php _e( '<strong>Author Slug:</strong> The base slug used for portfolio author archives.', 'gopublish-projects' ); ?></li>
		</ul>
	<?php }
}
