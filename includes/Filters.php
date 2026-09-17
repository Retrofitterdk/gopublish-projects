<?php
/**
 * Front-end filters: titles, permalinks, and forced term selection.
 *
 * @package GoPublish\Projects
 */

namespace GoPublish\Projects;

final class Filters {

	/**
	 * Hooks the surviving front-end filters into WordPress.
	 */
	public function register(): void {

		add_filter( 'document_title_parts', [ $this, 'document_title_parts' ], 5 );
		add_filter( 'post_type_archive_title', [ $this, 'post_type_archive_title' ], 5, 2 );
		add_filter( 'get_the_archive_title', [ $this, 'archive_title' ], 5 );
		add_filter( 'get_the_archive_description', [ $this, 'archive_description' ], 5 );
		add_filter( 'post_type_link', [ $this, 'post_type_link' ], 10, 2 );
		add_filter( 'author_link', [ $this, 'author_link' ], 10, 3 );

		add_action( 'save_post', [ $this, 'force_term_selection' ] );
	}

	/**
	 * Filters the document title on author archives.
	 */
	public function document_title_parts( array $title ): array {

		if ( Query::is_author() ) {
			$title['title'] = Query::single_author_title();
		}

		return $title;
	}

	/**
	 * Allows the project post type to use a custom "archive_title" label, which
	 * core doesn't support directly.
	 */
	public function post_type_archive_title( string $title, string $post_type ): string {

		return PostType::slug() === $post_type ? get_post_type_object( PostType::slug() )->labels->archive_title : $title;
	}

	/**
	 * Filters the archive title. Needed in addition to `post_type_archive_title`
	 * because core prefixes things like "Archives:" onto the title.
	 */
	public function archive_title( string $title ): string {

		if ( Query::is_author() ) {
			$title = Query::single_author_title();
		} elseif ( Query::is_project_archive() ) {
			$title = post_type_archive_title( '', false );
		}

		return $title;
	}

	/**
	 * Filters the archive description.
	 */
	public function archive_description( string $desc ): string {

		if ( Query::is_author() ) {
			$desc = get_the_author_meta( 'description', get_query_var( 'author' ) );
		} elseif ( Query::is_project_archive() && ! $desc ) {
			$desc = Settings::description();
		}

		return $desc;
	}

	/**
	 * Fills the category/tag/author rewrite tags into a project's permalink.
	 */
	public function post_type_link( string $post_link, \WP_Post $post ): string {

		if ( PostType::slug() !== $post->post_type ) {
			return $post_link;
		}

		$cat_taxonomy = Taxonomies::category_slug();
		$tag_taxonomy = Taxonomies::tag_slug();

		$author = $category = $tag = '';

		if ( false !== strpos( $post_link, "%{$cat_taxonomy}%" ) ) {
			$terms = get_the_terms( $post, $cat_taxonomy );

			if ( $terms ) {
				usort( $terms, '_usort_terms_by_ID' );
				$category = $terms[0]->slug;
			}
		}

		if ( false !== strpos( $post_link, "%{$tag_taxonomy}%" ) ) {
			$terms = get_the_terms( $post, $tag_taxonomy );

			if ( $terms ) {
				usort( $terms, '_usort_terms_by_ID' );
				$tag = $terms[0]->slug;
			}
		}

		if ( false !== strpos( $post_link, '%author%' ) ) {
			$authordata = get_userdata( $post->post_author );
			$author     = $authordata->user_nicename;
		}

		$rewrite_tags = [ '%portfolio_category%', '%portfolio_tag%', '%author%' ];
		$map_tags     = [ $category, $tag, $author ];

		return str_replace( $rewrite_tags, $map_tags, $post_link );
	}

	/**
	 * Points a project's author link at the project author archive instead of
	 * the regular author archive.
	 */
	public function author_link( string $url, int $author_id, string $nicename ): string {

		return Query::is_project() ? Query::author_url( $author_id ) : $url;
	}

	/**
	 * Forces the first available term to be selected when the permalink
	 * structure requires a category/tag but the project doesn't have one set.
	 */
	public function force_term_selection( int $post_id ): void {

		if ( ! Query::is_project( $post_id ) ) {
			return;
		}

		$project_base = Rewrite::project_rewrite_slug();
		$cat_tax      = Taxonomies::category_slug();
		$tag_tax      = Taxonomies::tag_slug();

		if ( false !== strpos( $project_base, "%{$cat_tax}%" ) ) {
			$this->set_term_if_none( $post_id, $cat_tax, Settings::default_category() );
		}

		if ( false !== strpos( $project_base, "%{$tag_tax}%" ) ) {
			$this->set_term_if_none( $post_id, $tag_tax, Settings::default_tag() );
		}
	}

	/**
	 * Sets the first available term of the taxonomy on the post if it doesn't
	 * already have one.
	 */
	private function set_term_if_none( int $post_id, string $taxonomy, int $default = 0 ): void {

		$terms = wp_get_post_terms( $post_id, $taxonomy );

		if ( $terms ) {
			return;
		}

		$new_term = false;

		if ( $default ) {
			$new_term = get_term( $default, $taxonomy );
		}

		if ( ! $new_term || is_wp_error( $new_term ) ) {
			$available = get_terms( $taxonomy, [ 'number' => 1 ] );
			$new_term  = $available ? array_shift( $available ) : false;
		}

		if ( ! $new_term ) {
			return;
		}

		$tax_object = get_taxonomy( $taxonomy );
		$slug_or_id = $tax_object->hierarchical ? $new_term->term_id : $new_term->slug;

		wp_set_post_terms( $post_id, $slug_or_id, $taxonomy, true );
	}
}
