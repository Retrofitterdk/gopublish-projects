<?php
/**
 * Registers project meta fields for the block editor / REST API.
 *
 * @package GoPublish\Projects
 */

namespace GoPublish\Projects;

final class Meta {

	/**
	 * Hooks meta registration into WordPress.
	 */
	public function register(): void {

		add_action( 'init', [ $this, 'register_meta' ] );
	}

	/**
	 * Registers the project meta fields.
	 *
	 * `url` is sanitized as a URL; the rest are plain strings with tags stripped.
	 */
	public function register_meta(): void {

		$post_type = PostType::slug();

		register_post_meta(
			$post_type,
			'url',
			[
				'sanitize_callback' => 'esc_url_raw',
				'auth_callback'     => [ $this, 'authorize' ],
				'single'            => true,
				'type'              => 'string',
				'show_in_rest'      => true,
			]
		);

		foreach ( [ 'client', 'location', 'start_date', 'end_date' ] as $key ) {

			register_post_meta(
				$post_type,
				$key,
				[
					'sanitize_callback' => 'wp_strip_all_tags',
					'auth_callback'     => [ $this, 'authorize' ],
					'single'            => true,
					'type'              => 'string',
					'show_in_rest'      => true,
				]
			);
		}
	}

	/**
	 * Auth callback for the project meta fields. Allows editing via the REST API
	 * (and therefore the block editor's document settings panel) for anyone who
	 * can edit the project the meta belongs to.
	 */
	public function authorize( bool $allowed, string $meta_key, int $post_id ): bool {

		return current_user_can( 'edit_post', $post_id );
	}
}
