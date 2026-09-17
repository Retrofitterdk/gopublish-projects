<?php
/**
 * Plugin settings: portfolio title/description and permalink base slugs.
 *
 * @package GoPublish\Projects
 */

namespace GoPublish\Projects;

final class Settings {

	/**
	 * The option name the settings are stored under.
	 */
	public const OPTION = 'gpp_settings';

	/**
	 * The portfolio title.
	 */
	public static function title(): string {

		return apply_filters( 'gpp_portfolio_title', self::get( 'portfolio_title' ) );
	}

	/**
	 * The portfolio description.
	 */
	public static function description(): string {

		return apply_filters( 'gpp_portfolio_description', self::get( 'portfolio_description' ) );
	}

	/**
	 * The portfolio rewrite base. Used for the project archive and as a prefix
	 * for taxonomy, author, and any other slugs.
	 */
	public static function portfolio_rewrite_base(): string {

		return apply_filters( 'gpp_portfolio_rewrite_base', self::get( 'portfolio_rewrite_base' ) );
	}

	/**
	 * The project rewrite base. Used for single projects.
	 */
	public static function project_rewrite_base(): string {

		return apply_filters( 'gpp_project_rewrite_base', self::get( 'project_rewrite_base' ) );
	}

	/**
	 * The category rewrite base. Used for category archives.
	 */
	public static function category_rewrite_base(): string {

		return apply_filters( 'gpp_category_rewrite_base', self::get( 'category_rewrite_base' ) );
	}

	/**
	 * The tag rewrite base. Used for tag archives.
	 */
	public static function tag_rewrite_base(): string {

		return apply_filters( 'gpp_tag_rewrite_base', self::get( 'tag_rewrite_base' ) );
	}

	/**
	 * The author rewrite base. Used for author archives.
	 */
	public static function author_rewrite_base(): string {

		return apply_filters( 'gpp_author_rewrite_base', self::get( 'author_rewrite_base' ) );
	}

	/**
	 * The default category term ID, used to force a term selection when the
	 * permalink structure requires one.
	 */
	public static function default_category(): int {

		return apply_filters( 'gpp_default_category', 0 );
	}

	/**
	 * The default tag term ID, used to force a term selection when the
	 * permalink structure requires one.
	 */
	public static function default_tag(): int {

		return apply_filters( 'gpp_default_tag', 0 );
	}

	/**
	 * Reads a single setting, falling back to its default.
	 *
	 * @return mixed
	 */
	public static function get( string $setting ) {

		$defaults = self::defaults();
		$settings = wp_parse_args( get_option( self::OPTION, $defaults ), $defaults );

		return $settings[ $setting ] ?? false;
	}

	/**
	 * Default settings for the plugin.
	 */
	public static function defaults(): array {

		return [
			'portfolio_title'        => __( 'Portfolio', 'gopublish-projects' ),
			'portfolio_description'  => '',
			'portfolio_rewrite_base' => 'portfolio',
			'project_rewrite_base'   => 'projects',
			'category_rewrite_base'  => 'categories',
			'tag_rewrite_base'       => 'tags',
			'author_rewrite_base'    => 'authors',
		];
	}
}
