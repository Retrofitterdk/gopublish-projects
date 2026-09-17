<?php
/**
 * Plugin Name: Go:Publish Projects
 * Plugin URI:  https://github.com/Retrofitterdk/gopublish-projects
 * Description: Portfolio manager for block-theme WordPress sites. Only loosely based on Justin Tadlock's Custom Content Portfolio — no backwards compatibility with it is implied or supported.
 * Version:     0.1.0
 * Author:      Retrofitter
 * Author URI:  https://github.com/Retrofitterdk/gopublish-projects
 * Text Domain: gopublish-projects
 * Domain Path: /lang
 * Requires PHP: 7.4
 * Requires at least: 5.8
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package   GoPublish\Projects
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace GoPublish\Projects;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( __NAMESPACE__ . '\PATH', plugin_dir_path( __FILE__ ) );
define( __NAMESPACE__ . '\URL', plugin_dir_url( __FILE__ ) );

spl_autoload_register(
	function ( string $class ): void {

		$prefix = __NAMESPACE__ . '\\';

		if ( 0 !== strpos( $class, $prefix ) ) {
			return;
		}

		$relative = substr( $class, strlen( $prefix ) );
		$file     = PATH . 'includes/' . str_replace( '\\', '/', $relative ) . '.php';

		if ( file_exists( $file ) ) {
			require $file;
		}
	}
);

register_activation_hook( __FILE__, [ Plugin::class, 'activate' ] );

add_action( 'plugins_loaded', [ Plugin::class, 'boot' ] );
