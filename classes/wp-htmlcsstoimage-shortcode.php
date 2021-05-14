<?php

use gfirem\HtmlCssToImage\HtmlCssToImage;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WpHtmlCssToImageShortCode {
	public function __construct() {
		add_shortcode( 'htmlcsstoimage', array( $this, 'htmlcsstoimage_callback' ) );
		add_action( 'wp_footer', array( $this, 'wp_enqueue_scripts' ) );
		add_action( "wp_ajax_nopriv_post_htmlcsstoimage", array( $this, "post_htmlcsstoimage_callback" ) );
		add_action( "wp_ajax_post_htmlcsstoimage", array( $this, "post_htmlcsstoimage_callback" ) );
	}

	public function post_htmlcsstoimage_callback() {
		try {
			if ( ! ( is_array( $_POST ) && defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				die();
			}
			if ( ! isset( $_POST['action'] ) || ! isset( $_POST['nonce'] ) || empty( $_POST['html'] ) ) {
				die();
			}
			if ( ! wp_verify_nonce( $_POST['nonce'], WpHtmlCssToImage::get_slug() . __DIR__ ) ) {
				die();
			}

			$html = stripslashes_deep( $_POST['html'] );
			$css  = '';
			if ( ! empty( $_POST['css'] ) ) {
				$css = stripslashes_deep( $_POST['css'] );
			}

			$user_id = get_option( 'wp_htmlcsstoimage_user_id' );
			$api_key = get_option( 'wp_htmlcsstoimage_api_key' );
			if ( empty( $user_id ) || empty( $api_key ) ) {
				WpHtmlCssToImage::error_log( 'Invalid options' );
			}
			$client   = new HtmlCssToImage( $user_id, $api_key );
			$response = $client->post_image( $html, '', $css );
			if ( ! empty( $response ) && $response['code'] === 200 ) {
				wp_send_json_success( $response );
			} else {
				wp_send_json_error( $response );
			}

		} catch ( Exception $ex ) {
			WpHtmlCssToImage::error_log( $ex->getMessage() );
		}
		die();
	}

	public function htmlcsstoimage_callback( $attr, $content = null ) {
		$params = shortcode_atts( array(
			'target' => '',
			'img_id' => '',
			'size'   => '',
		), $attr );

		$attr_data_img = '';
		if ( ! empty( $params['img_id'] ) ) {
			$attr_data_img = sprintf( 'data-img-id="%s"', $params['img_id'] );
		}
		$attr_target = '';
		if ( ! empty( $params['target'] ) ) {
			$attr_target = sprintf( 'data-target="%s"', $params['target'] );
		}
		$attr_size  = '';
		if ( ! empty( $params['size'] ) ) {
			$sizes      = explode( 'x', $params['size'] );
			$attr_size  = sprintf( 'data-width="%s" data-height="%s"',  $sizes[0], $sizes[1] );
		}
		$attr_have_content = sprintf( 'data-have-content="%s"', ! empty( $content ) );

		return sprintf( '<div class="htmlcsstoimage-container" %s %s %s %s><div id="htmlcsstoimage-content">%s</div><a href="#" class="create-image">%s</a></div>', $attr_size, $attr_target, $attr_data_img, $attr_have_content, $content, __( 'Generate Image', 'wp-htmlcsstoimage' ) );
	}

	public function wp_enqueue_scripts() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX && defined( 'DOING_CRON' ) && DOING_CRON ) {
			return;
		}
		$js_asset  = WpHtmlCssToImage::assets_path( 'script' );
		$css_asset = WpHtmlCssToImage::assets_path( 'style', 'css' );
		wp_enqueue_script( 'wp-htmlcsstoimage-js', $js_asset, array( "jquery" ), WpHtmlCssToImage::get_version(), true );
		wp_enqueue_style( 'wp-htmlcsstoimage-css', $css_asset, array(), WpHtmlCssToImage::get_version() );
		$args = array(
			'admin_url' => admin_url( 'admin-ajax.php' ),
			'nonce'     => wp_create_nonce( WpHtmlCssToImage::get_slug() . __DIR__ ),
		);
		wp_localize_script( 'wp-htmlcsstoimage-js', 'wpHtmlCssToImageObj', $args );
	}
}
