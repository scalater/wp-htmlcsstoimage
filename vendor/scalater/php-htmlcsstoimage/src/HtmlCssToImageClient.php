<?php

namespace scalater\HtmlCssToImage;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;

class HtmlCssToImageClient implements HtmlCssToImageRequest {
	/**
	 * @var string
	 */
	private $user_id;
	/**
	 * @var string
	 */
	private $api_key;
	/**
	 * @var Client Used for testing only
	 */
	private $client;
	/**
	 * @var Client
	 */
	private $guzzle_client;
	/**
	 * @var string
	 */
	public $api = 'https://hcti.io/v1/image/';

	public function __construct( $user_id, $api_key, $client = null ) {
		$this->user_id = $user_id;
		$this->api_key = $api_key;
		$this->client  = $client;

		if ( empty( $this->client ) ) {
			$this->guzzle_client = new Client();
		} else {
			$this->guzzle_client = $this->client;
		}
	}

	/**
	 * @return array
	 */
	public function headers(): array {
		return array( 'Content-Type' => 'application/json; charset=utf-8' );
	}

	/**
	 * @return array
	 */
	public function auth(): array {
		return array( $this->user_id, $this->api_key, 'Basic' );
	}

	/**
	 * @param $image_id
	 * @param array $fields
	 *
	 * @return array|false|Response
	 */
	public function get( $image_id, $fields = array() ) {
		try {
			$response = $this->guzzle_client->request( 'GET', $this->api . $image_id, array( 'query' => $fields, 'stream' => true ) );

			return $this->result( $response, true );

		} catch ( ClientException $e ) {
			return (array) json_decode( $e->getResponse()->getBody()->getContents() );
		} catch ( GuzzleException $e ) {
			error_log( 'gfirem::HtmlCssToImage::' . $e->getMessage() );

			return false;
		}
	}

	/**
	 * @param array $fields
	 *
	 * @return array|false|Response
	 */
	public function post( $fields ) {
		$body = \json_encode( $fields, JSON_PRETTY_PRINT );

		try {
			$response = $this->guzzle_client->request( 'POST', $this->api, array(
				'body'    => $body,
				'headers' => $this->headers(),
				'auth'    => $this->auth()
			) );

			return $this->result( $response );

		} catch ( ClientException $e ) {
			return (array) json_decode( $e->getResponse()->getBody()->getContents() );
		} catch ( GuzzleException $e ) {
			error_log( 'gfirem::HtmlCssToImage::' . $e->getMessage() );

			return false;
		}
	}

	/**
	 * @param string $image_id
	 *
	 * @return array|false|Response
	 */
	public function delete( $image_id ) {
		try {
			$response = $this->guzzle_client->request( 'DELETE', $this->api . $image_id,
				array( 'auth' => $this->auth() ) );

			return $this->result( $response );

		} catch ( ClientException $e ) {
			return (array) json_decode( $e->getResponse()->getBody()->getContents() );
		} catch ( GuzzleException $e ) {
			error_log( 'gfirem::HtmlCssToImage::' . $e->getMessage() );

			return false;
		}
	}

	/**
	 * @param Response $response
	 * @param bool $get_body
	 *
	 * @return array|false|Response
	 */
	public function result( Response $response, $get_body = false ) {
		if ( ! empty( $this->client ) ) {
			return $response;
		} else {
			if ( $get_body ) {
				$result['content'] = $response->getBody()->getContents();
			} else {
				$result = json_decode( (string) $response->getBody(), true );
			}

			$result['code'] = $response->getStatusCode();

			return $result;
		}
	}
}