<?php
/**
 * Shared help-tab content for the admin screens.
 *
 * @package GoPublish\Projects
 */

namespace GoPublish\Projects\Admin;

final class Help {

	/**
	 * The sidebar text shown on every plugin help tab.
	 */
	public static function sidebar_text(): string {

		$docs_link = sprintf( '<li><a href="https://themehybrid.com/docs">%s</a></li>', esc_html__( 'Documentation', 'gopublish-projects' ) );
		$help_link = sprintf( '<li><a href="https://themehybrid.com/board/topics">%s</a></li>', esc_html__( 'Support Forums', 'gopublish-projects' ) );

		return sprintf(
			'<p><strong>%s</strong></p><ul>%s%s</ul>',
			esc_html__( 'For more information:', 'gopublish-projects' ),
			$docs_link,
			$help_link
		);
	}
}
