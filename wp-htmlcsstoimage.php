<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 * Plugin Name: HtmlCssToImage
 * Plugin URI: https://scalater.com/
 * Description: WP plugin to connect with htmlcsstoimage.com service to generate images from html and css
 * Version: 1.0.0'
 * Requires at least: 4.6
 * Tested up to: 6.1.1
 * Requires PHP: 7.4
 * Stable tag: 1.0.0
 * Author: Scalater Team
 * Author URI: https://scalater.com/
 * License: GPLv2 or later
 * Network: false
 * Text Domain: wp-htmlcsstoimage
 * Domain Path: /languages
 *
 *****************************************************************************
 *
 * This script is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 ****************************************************************************
 */

namespace SCALATER\HTML2IMAGE;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once 'vendor/autoload.php';
require_once 'bootstrap.php';

if ( ! init_plugin( __NAMESPACE__, __FILE__, 'wp-htmlcsstoimage' ) ) {
	return;
}

if ( ! function_exists( 'htm_fs' ) ) {
	// Create a helper function for easy SDK access.
	function sca_wp_htm_freemius() {
		global $sca_wp_htm_fs;

		if ( ! isset( $sca_wp_htm_fs ) ) {
			// Include Freemius SDK.
			require_once dirname( __FILE__ ) . '/includes/freemius/start.php';

			$sca_wp_htm_fs = fs_dynamic_init(
				[
					'id'                  => '11995',
					'slug'                => 'htmlcsstoimage',
					'type'                => 'plugin',
					'public_key'          => 'pk_c63eda8092135f9188712045d6ca5',
					'is_premium'          => false,
					'has_addons'          => false,
					'has_paid_plans'      => false,
					'menu'                => [
						'account'        => true,
						'support'        => false,
					],
				]
			);
		}

		return $sca_wp_htm_fs;
	}

	// Init Freemius.
	sca_wp_htm_freemius();
	// Signal that SDK was initiated.
	do_action( 'wp-htmlcsstoimage-freemius-loaded' );
}

add_action( 'scalater/admin', [ Admin::class, 'instance' ] );
add_action( 'scalater/init', [ ShortCode::class, 'instance' ] );

