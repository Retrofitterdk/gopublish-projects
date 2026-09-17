<?php
/**
 * Rewrite rules and permalink slug helpers.
 *
 * @package GoPublish\Projects
 */

namespace GoPublish\Projects;

final class Rewrite {

	/**
	 * Hooks the author-archive rewrite rules into WordPress.
	 */
	public function register(): void {

		add_action( 'init', [ $this, 'add_rewrite_rules' ], 5 );
	}

	/**
	 * Adds rewrite rules for the project author archive.
	 */
	public function add_rewrite_rules(): void {

		$project_type = PostType::slug();
		$author_slug  = self::author_rewrite_slug();

		// Where to place the rewrite rules. If no rewrite base, put them at the bottom.
		$after = Settings::author_rewrite_base() ? 'top' : 'bottom';

		add_rewrite_rule( $author_slug . '/([^/]+)/page/?([0-9]{1,})/?$', 'index.php?post_type=' . $project_type . '&author_name=$matches[1]&paged=$matches[2]', $after );
		add_rewrite_rule( $author_slug . '/([^/]+)/?$', 'index.php?post_type=' . $project_type . '&author_name=$matches[1]', $after );
	}

	/**
	 * The rewrite slug used for single projects.
	 */
	public static function project_rewrite_slug(): string {

		$portfolio_base = Settings::portfolio_rewrite_base();
		$project_base   = Settings::project_rewrite_base();

		$slug = $project_base ? trailingslashit( $portfolio_base ) . $project_base : $portfolio_base;

		return apply_filters( 'gpp_project_rewrite_slug', $slug );
	}

	/**
	 * The rewrite slug used for category archives.
	 */
	public static function category_rewrite_slug(): string {

		$portfolio_base = Settings::portfolio_rewrite_base();
		$category_base  = Settings::category_rewrite_base();

		$slug = $category_base ? trailingslashit( $portfolio_base ) . $category_base : $portfolio_base;

		return apply_filters( 'gpp_category_rewrite_slug', $slug );
	}

	/**
	 * The rewrite slug used for tag archives.
	 */
	public static function tag_rewrite_slug(): string {

		$portfolio_base = Settings::portfolio_rewrite_base();
		$tag_base       = Settings::tag_rewrite_base();

		$slug = $tag_base ? trailingslashit( $portfolio_base ) . $tag_base : $portfolio_base;

		return apply_filters( 'gpp_tag_rewrite_slug', $slug );
	}

	/**
	 * The rewrite slug used for author archives.
	 */
	public static function author_rewrite_slug(): string {

		$portfolio_base = Settings::portfolio_rewrite_base();
		$author_base    = Settings::author_rewrite_base();

		$slug = $author_base ? trailingslashit( $portfolio_base ) . $author_base : $portfolio_base;

		return apply_filters( 'gpp_author_rewrite_slug', $slug );
	}
}
