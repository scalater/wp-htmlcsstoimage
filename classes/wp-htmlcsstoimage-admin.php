<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WpHtmlCssToImageAdmin {
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'create_setting_page' ) );
		add_action( 'admin_init', array( $this, 'settings_init' ) );
	}

	public function create_setting_page() {
		add_options_page( __( 'HtmlCssToImage', 'wp-htmlcsstoimage' ), __( 'HtmlCssToImage', 'wp-htmlcsstoimage' ), 'manage_options', WpHtmlCssToImage::get_slug(), array( $this, 'wp_htmlcsstoimage_page' ) );
	}

	public function wp_htmlcsstoimage_page() {
		?>
		<div class="wrap">

			<div id="icon-options-general" class="icon32"><br></div>
			<h2> <?php _e( 'WpHtmlCssToImage', 'wp-htmlcsstoimage' ); ?></h2>
			<div style="overflow: auto;">
				<span style="font-size: 13px; float:right;"><?php _e( 'Proudly brought to you by ', 'wp-htmlcsstoimage' ); ?><a href="https://www.scalater.com/" target="_new">Scalater</a>.</span>
			</div>

			<form method="post" action="options.php">
				<?php wp_nonce_field( 'update-options' ); ?>
				<?php settings_fields( 'wp_htmlcsstoimage_option' ); ?>
				<?php do_settings_sections( 'wp_htmlcsstoimage_option' ); ?>
				<?php submit_button(); ?>
			</form>

		</div>
		<?php
	}

	public function settings_init() {
		add_settings_section( 'wp_htmlcsstoimage_section', '', '', 'wp_htmlcsstoimage_option' );

		add_settings_field( 'wp_htmlcsstoimage_user_id', __( 'User Id', 'wp-htmlcsstoimage' ), array( $this, 'wp_htmlcsstoimage_user_id_cb' ), 'wp_htmlcsstoimage_option', 'wp_htmlcsstoimage_section' );
		add_settings_field( 'wp_htmlcsstoimage_api_key', __( 'API key', 'wp-htmlcsstoimage' ), array( $this, 'wp_htmlcsstoimage_api_key_cb' ), 'wp_htmlcsstoimage_option', 'wp_htmlcsstoimage_section' );
		add_settings_field( 'wp_htmlcsstoimage_header', __( 'Header', 'wp-htmlcsstoimage' ), array( $this, 'wp_htmlcsstoimage_header_cb' ), 'wp_htmlcsstoimage_option', 'wp_htmlcsstoimage_section' );

		register_setting( 'wp_htmlcsstoimage_option', 'wp_htmlcsstoimage_user_id' );
		register_setting( 'wp_htmlcsstoimage_option', 'wp_htmlcsstoimage_api_key' );
		register_setting( 'wp_htmlcsstoimage_option', 'wp_htmlcsstoimage_header' );
	}

	public function wp_htmlcsstoimage_user_id_cb() {
		$value = get_option( 'wp_htmlcsstoimage_user_id' );
		?>
		<p>
			<input type="password" name="wp_htmlcsstoimage_user_id" value="<?php echo isset( $value ) ? esc_attr( $value ) : ''; ?>" style="width: 350px;">
		</p>
		<?php
	}

	public function wp_htmlcsstoimage_api_key_cb() {
		$value = get_option( 'wp_htmlcsstoimage_api_key' );
		?>
		<p>
			<input type="password" name="wp_htmlcsstoimage_api_key" value="<?php echo isset( $value ) ? esc_attr( $value ) : ''; ?>" style="width: 350px;">
		</p>
		<?php
	}

	public function wp_htmlcsstoimage_header_cb() {
		$value = get_option( 'wp_htmlcsstoimage_header' );
		?>
		<p>
			<textarea style="width: 550px;" rows="20" name="wp_htmlcsstoimage_header"><?php echo esc_textarea( $value ) ?></textarea>
		</p>
		<?php
	}
}
