<?php
/**
 * Registers the project category and tag taxonomies.
 *
 * @package GoPublish\Projects
 */

namespace GoPublish\Projects;

final class Taxonomies {

	/**
	 * Hooks taxonomy registration and admin-facing filters into WordPress.
	 */
	public function register(): void {

		add_action( 'init', [ $this, 'register_taxonomies' ], 9 );

		add_filter( 'term_updated_messages', [ $this, 'term_updated_messages' ], 5 );
	}

	/**
	 * The project category taxonomy slug.
	 */
	public static function category_slug(): string {

		return apply_filters( 'gpp_category_taxonomy', 'portfolio_category' );
	}

	/**
	 * The project tag taxonomy slug.
	 */
	public static function tag_slug(): string {

		return apply_filters( 'gpp_tag_taxonomy', 'portfolio_tag' );
	}

	/**
	 * Registers the category and tag taxonomies.
	 */
	public function register_taxonomies(): void {

		$cat_args = [
			'public'            => true,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'show_in_rest'      => true,
			'show_tagcloud'     => true,
			'show_admin_column' => true,
			'hierarchical'      => true,
			'query_var'         => self::category_slug(),
			'capabilities'      => $this->category_capabilities(),
			'labels'            => $this->category_labels(),

			'rewrite' => [
				'slug'         => Rewrite::category_rewrite_slug(),
				'with_front'   => false,
				'hierarchical' => false,
				'ep_mask'      => EP_NONE,
			],
		];

		$tag_args = [
			'public'            => true,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'show_in_rest'      => true,
			'show_tagcloud'     => true,
			'show_admin_column' => true,
			'hierarchical'      => false,
			'query_var'         => self::tag_slug(),
			'capabilities'      => $this->tag_capabilities(),
			'labels'            => $this->tag_labels(),

			'rewrite' => [
				'slug'         => Rewrite::tag_rewrite_slug(),
				'with_front'   => false,
				'hierarchical' => false,
				'ep_mask'      => EP_NONE,
			],
		];

		register_taxonomy( self::category_slug(), PostType::slug(), apply_filters( 'gpp_category_taxonomy_args', $cat_args ) );
		register_taxonomy( self::tag_slug(), PostType::slug(), apply_filters( 'gpp_tag_taxonomy_args', $tag_args ) );
	}

	/**
	 * Capability map for the category taxonomy.
	 */
	private function category_capabilities(): array {

		$caps = [
			'manage_terms' => 'manage_portfolio_categories',
			'edit_terms'   => 'edit_portfolio_categories',
			'delete_terms' => 'delete_portfolio_categories',
			'assign_terms' => 'assign_portfolio_categories',
		];

		return apply_filters( 'gpp_category_capabilities', $caps );
	}

	/**
	 * Capability map for the tag taxonomy.
	 */
	private function tag_capabilities(): array {

		$caps = [
			'manage_terms' => 'manage_portfolio_tags',
			'edit_terms'   => 'edit_portfolio_tags',
			'delete_terms' => 'delete_portfolio_tags',
			'assign_terms' => 'assign_portfolio_tags',
		];

		return apply_filters( 'gpp_tag_capabilities', $caps );
	}

	/**
	 * Labels for the category taxonomy.
	 */
	private function category_labels(): array {

		$labels = [
			'name'                  => __( 'Project Categories', 'gopublish-projects' ),
			'singular_name'         => __( 'Project Category', 'gopublish-projects' ),
			'menu_name'             => __( 'Categories', 'gopublish-projects' ),
			'name_admin_bar'        => __( 'Category', 'gopublish-projects' ),
			'search_items'          => __( 'Search Categories', 'gopublish-projects' ),
			'popular_items'         => __( 'Popular Categories', 'gopublish-projects' ),
			'all_items'             => __( 'All Categories', 'gopublish-projects' ),
			'edit_item'             => __( 'Edit Category', 'gopublish-projects' ),
			'view_item'             => __( 'View Category', 'gopublish-projects' ),
			'update_item'           => __( 'Update Category', 'gopublish-projects' ),
			'add_new_item'          => __( 'Add New Category', 'gopublish-projects' ),
			'new_item_name'         => __( 'New Category Name', 'gopublish-projects' ),
			'not_found'             => __( 'No categories found.', 'gopublish-projects' ),
			'no_terms'              => __( 'No categories', 'gopublish-projects' ),
			'items_list_navigation' => __( 'Categories list navigation', 'gopublish-projects' ),
			'items_list'            => __( 'Categories list', 'gopublish-projects' ),

			// Hierarchical only.
			'select_name'       => __( 'Select Category', 'gopublish-projects' ),
			'parent_item'       => __( 'Parent Category', 'gopublish-projects' ),
			'parent_item_colon' => __( 'Parent Category:', 'gopublish-projects' ),
		];

		return apply_filters( 'gpp_category_labels', $labels );
	}

	/**
	 * Labels for the tag taxonomy.
	 */
	private function tag_labels(): array {

		$labels = [
			'name'                  => __( 'Project Tags', 'gopublish-projects' ),
			'singular_name'         => __( 'Project Tag', 'gopublish-projects' ),
			'menu_name'             => __( 'Tags', 'gopublish-projects' ),
			'name_admin_bar'        => __( 'Tag', 'gopublish-projects' ),
			'search_items'          => __( 'Search Tags', 'gopublish-projects' ),
			'popular_items'         => __( 'Popular Tags', 'gopublish-projects' ),
			'all_items'             => __( 'All Tags', 'gopublish-projects' ),
			'edit_item'             => __( 'Edit Tag', 'gopublish-projects' ),
			'view_item'             => __( 'View Tag', 'gopublish-projects' ),
			'update_item'           => __( 'Update Tag', 'gopublish-projects' ),
			'add_new_item'          => __( 'Add New Tag', 'gopublish-projects' ),
			'new_item_name'         => __( 'New Tag Name', 'gopublish-projects' ),
			'not_found'             => __( 'No tags found.', 'gopublish-projects' ),
			'no_terms'              => __( 'No tags', 'gopublish-projects' ),
			'items_list_navigation' => __( 'Tags list navigation', 'gopublish-projects' ),
			'items_list'            => __( 'Tags list', 'gopublish-projects' ),

			// Non-hierarchical only.
			'separate_items_with_commas' => __( 'Separate tags with commas', 'gopublish-projects' ),
			'add_or_remove_items'        => __( 'Add or remove tags', 'gopublish-projects' ),
			'choose_from_most_used'      => __( 'Choose from the most used tags', 'gopublish-projects' ),
		];

		return apply_filters( 'gpp_tag_labels', $labels );
	}

	/**
	 * Filters the term-updated admin messages.
	 */
	public function term_updated_messages( array $messages ): array {

		$messages[ self::category_slug() ] = [
			0 => '',
			1 => __( 'Category added.', 'gopublish-projects' ),
			2 => __( 'Category deleted.', 'gopublish-projects' ),
			3 => __( 'Category updated.', 'gopublish-projects' ),
			4 => __( 'Category not added.', 'gopublish-projects' ),
			5 => __( 'Category not updated.', 'gopublish-projects' ),
			6 => __( 'Categories deleted.', 'gopublish-projects' ),
		];

		$messages[ self::tag_slug() ] = [
			0 => '',
			1 => __( 'Tag added.', 'gopublish-projects' ),
			2 => __( 'Tag deleted.', 'gopublish-projects' ),
			3 => __( 'Tag updated.', 'gopublish-projects' ),
			4 => __( 'Tag not added.', 'gopublish-projects' ),
			5 => __( 'Tag not updated.', 'gopublish-projects' ),
			6 => __( 'Tags deleted.', 'gopublish-projects' ),
		];

		return $messages;
	}
}
