<?php

namespace scalater\HtmlCssToImage;

use GuzzleHttp\Psr7\Response;

class HtmlCssToImage extends HtmlCssToImageClient {
	public function __construct( $user_id, $api_key, $client = null ) {
		parent::__construct( $user_id, $api_key, $client );
	}

	/**
	 * @param string $image_id
	 * @param int|null $height The height of the image. Maximum 5000.
	 * @param int|null $width The width of the image. Maximum 5000.
	 * @param bool $dl Set to true and the image will be served as a downloadable attachment.
	 *
	 * @return array|false|Response
	 */
	public function get_image( string $image_id, int $height = null, int $width = null, bool $dl = false ) {
		$params = array();
		if ( ! empty( $height ) ) {
			$params['height'] = $height;
		}
		if ( ! empty( $width ) ) {
			$params['width'] = $width;
		}
		if ( ! empty( $dl ) ) {
			$params['dl'] = $dl;
		}

		return $this->get( $image_id, $params );
	}

	/**
	 * @param string $html This is the HTML you want to render. You can send an HTML snippet (<div>Your content</div>) or an entire webpage.
	 * @param string $url The fully qualified URL to a public webpage. Such as https://htmlcsstoimage.com. When passed this will override the html param and will generate a screenshot of the url.
	 * @param string $css The CSS for your image. When using with url it will be injected into the page.
	 *
	 * @return array|false|Response|mixed
	 */
	public function post_image( string $html, string $url, string $css ) {
		$source     = ! empty( $html ) ? $html : $url;
		$source_key = ! empty( $html ) ? 'html' : 'url';

		return $this->post( array( $source_key => $source, 'css' => $css, 'ms_delay' => 500 ) );
	}

	/**
	 * @param string $image_id
	 *
	 * @return array|false|Response|mixed
	 */
	public function delete_image( string $image_id ) {
		return $this->delete( $image_id );
	}
}