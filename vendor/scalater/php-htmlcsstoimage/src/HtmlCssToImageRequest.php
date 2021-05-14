<?php

namespace scalater\HtmlCssToImage;

use GuzzleHttp\Psr7\Response;

interface HtmlCssToImageRequest {
	public function auth(): array;

	public function headers(): array;

	public function get( $image_id, $fields = array() );

	public function post( $fields );

	public function delete( $image_id );

	public function result( Response $response );
}
