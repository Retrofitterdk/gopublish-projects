<?php
/**
 * Internal query/conditional helpers used by Filters and Rewrite.
 *
 * Not a theme-facing "template tags" API — the plugin targets block themes
 * only, and theme integration happens through blocks/block bindings instead.
 *
 * @package GoPublish\Projects
 */

namespace GoPublish\Projects;

final class Query {

	/**
	 * Resolves a post ID, falling back to the current post in the loop.
	 */
	public static function project_id( $post_id = '' ): int {

		return $post_id ? absint( $post_id ) : get_the_ID();
	}

	/**
	 * Whether the current request is a single project.
	 *
	 * @param mixed $post
	 */
	public static function is_single_project( $post = '' ): bool {

		$is_single = is_singular( PostType::slug() );

		if ( $is_single && $post ) {
			$is_single = is_single( $post );
		}

		return apply_filters( 'gpp_is_single_project', $is_single, $post );
	}

	/**
	 * Whether the current request is the project archive.
	 */
	public static function is_project_archive(): bool {

		return apply_filters( 'gpp_is_project_archive', is_post_type_archive( PostType::slug() ) && ! self::is_author() );
	}

	/**
	 * Whether the given post is a project.
	 */
	public static function is_project( $post_id = '' ): bool {

		$post_id = self::project_id( $post_id );

		return apply_filters( 'gpp_is_project', PostType::slug() === get_post_type( $post_id ), $post_id );
	}

	/**
	 * Whether the project has a "complete" status, derived from its start/end dates.
	 */
	public static function is_project_complete( $post_id = '' ): bool {

		$post_id    = self::project_id( $post_id );
		$completed  = true;
		$start_date = get_post_meta( $post_id, 'start_date', true );
		$end_date   = get_post_meta( $post_id, 'end_date', true );

		if ( $start_date && ! $end_date ) {
			$completed = false;
		} elseif ( $start_date && $end_date ) {
			$completed = mysql2date( 'Ymd', $start_date, false ) < mysql2date( 'Ymd', $end_date, false );
		}

		if ( $end_date ) {
			$completed = date( 'Ymd' ) >= mysql2date( 'Ymd', $end_date, false );
		}

		return apply_filters( 'gpp_is_project_complete', $completed, $post_id );
	}

	/**
	 * Whether the project has an "in progress" status, derived from its start/end dates.
	 */
	public static function is_project_in_progress( $post_id = '' ): bool {

		$post_id     = self::project_id( $post_id );
		$in_progress = false;
		$start_date  = get_post_meta( $post_id, 'start_date', true );
		$end_date    = get_post_meta( $post_id, 'end_date', true );

		if ( $start_date ) {
			$in_progress = true;
		}

		if ( $end_date ) {
			$in_progress = date( 'Ymd' ) < mysql2date( 'Ymd', $end_date, false );
		}

		return apply_filters( 'gpp_is_project_in_progress', $in_progress, $post_id );
	}

	/**
	 * Whether the current request is any portfolio page (archive or single).
	 */
	public static function is_portfolio(): bool {

		return apply_filters( 'gpp_is_portfolio', self::is_archive() || self::is_single_project() );
	}

	/**
	 * Whether the current request is any type of portfolio archive.
	 */
	public static function is_archive(): bool {

		return apply_filters( 'gpp_is_archive', self::is_project_archive() || self::is_author() || self::is_category() || self::is_tag() );
	}

	/**
	 * Whether the current request is a category archive.
	 *
	 * @param mixed $term
	 */
	public static function is_category( $term = '' ): bool {

		return apply_filters( 'gpp_is_category', is_tax( Taxonomies::category_slug(), $term ) );
	}

	/**
	 * Whether the current request is a tag archive.
	 *
	 * @param mixed $term
	 */
	public static function is_tag( $term = '' ): bool {

		return apply_filters( 'gpp_is_tag', is_tax( Taxonomies::tag_slug(), $term ) );
	}

	/**
	 * Whether the current request is a project author archive.
	 *
	 * @param mixed $author
	 */
	public static function is_author( $author = '' ): bool {

		return apply_filters( 'gpp_is_author', is_post_type_archive( PostType::slug() ) && is_author( $author ) );
	}

	/**
	 * The current author archive's display name.
	 */
	public static function single_author_title(): string {

		return apply_filters( 'gpp_single_author_title', get_the_author_meta( 'display_name', absint( get_query_var( 'author' ) ) ) );
	}

	/**
	 * The project author archive URL for the given user.
	 */
	public static function author_url( int $user_id = 0 ): string {
		global $wp_rewrite, $authordata;

		$url = '';

		if ( ! $user_id && is_object( $authordata ) ) {
			$user_id = $authordata->ID;
		}

		if ( $user_id ) {

			$nicename = get_the_author_meta( 'user_nicename', $user_id );

			if ( $wp_rewrite->using_permalinks() ) {
				$url = home_url( user_trailingslashit( trailingslashit( Rewrite::author_rewrite_slug() ) . $nicename ) );
			} else {
				$url = add_query_arg( [ 'post_type' => PostType::slug(), 'author_name' => $nicename ], home_url( '/' ) );
			}
		}

		return apply_filters( 'gpp_author_url', $url, $user_id );
	}
}
