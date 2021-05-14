<?php

use scalater\HtmlCssToImage\HtmlCssToImage;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;

class GetImageTest extends \Codeception\Test\Unit {
	protected $image_id = 'bd6b0b94-cb7e-4913-ac65-94abafebb84023423';
	protected $base_url = 'https://hcti.io/v1/image/';
	protected $response_header = array(
		'content-type'      => 'application/json; charset=utf-8',
		'x-renders-allowed' => 50,
		'x-renders-used'    => 4,
	);

	protected function get_mock_client( Response $response ): HtmlCssToImage {
		$mock         = new MockHandler( array( $response ) );
		$handlerStack = HandlerStack::create( $mock );
		$client = new Client( [ 'handler' => $handlerStack, 'base_uri' => 'http://localhost' ] );

		return new HtmlCssToImage( 'user_id', 'api_key', $client );
	}

	public function testPostImage() {
		$image_response        = new stdClass();
		$image_response->url   = $this->base_url . $this->image_id;
		$mock_create_image     = new Response( 200, $this->response_header, json_encode( $image_response ) );
		$htmlcsstoimage_client = $this->get_mock_client( $mock_create_image );
		$response              = $htmlcsstoimage_client->post_image( '<div></div>', '', '.div{color:red;}' );
		$body                  = $response->getBody();
		$this->assertJson( $body );
		$body_response = json_decode( $body );
		$this->assertEquals( $this->base_url . $this->image_id, $body_response->url );
		$this->assertEquals( 50, $response->getHeaderLine( 'x-renders-allowed' ) );
		$this->assertEquals( 4, $response->getHeaderLine( 'x-renders-used' ) );
		$this->assertEquals( 200, $response->getStatusCode() );
	}

	public function testGetImage() {
		$image_response        = new stdClass();
		$image_response->url   = $this->base_url . $this->image_id;
		$mock_get_image        = new Response( 200, $this->response_header, json_encode( $image_response ) );
		$htmlcsstoimage_client = $this->get_mock_client( $mock_get_image );
		$response              = $htmlcsstoimage_client->get_image( $this->image_id );
		$body                  = $response->getBody();
		$this->assertJson( $body );
		$body_response = json_decode( $body );
		$this->assertEquals( $this->base_url . $this->image_id, $body_response->url );
		$this->assertEquals( 50, $response->getHeaderLine( 'x-renders-allowed' ) );
		$this->assertEquals( 4, $response->getHeaderLine( 'x-renders-used' ) );
		$this->assertEquals( 200, $response->getStatusCode() );
	}

	public function testGetImageError() {
		$image_response             = new stdClass();
		$image_response->error      = 'Bad Request';
		$image_response->statusCode = 400;
		$image_response->message    = 'HTML is Required';
		$mock_image_error           = new Response( 400, $this->response_header, json_encode( $image_response ) );
		$htmlcsstoimage_client      = $this->get_mock_client( $mock_image_error );
		$response                   = $htmlcsstoimage_client->get_image( 'image_id_invalid' );
		$this->assertEquals( 'Bad Request', $response['error'] );
	}

	public function testDeleteImage() {
		$mock_delete_image     = new Response( 202, $this->response_header );
		$htmlcsstoimage_client = $this->get_mock_client( $mock_delete_image );
		$response              = $htmlcsstoimage_client->delete_image( $this->image_id );
		$this->assertEquals( 50, $response->getHeaderLine( 'x-renders-allowed' ) );
		$this->assertEquals( 4, $response->getHeaderLine( 'x-renders-used' ) );
		$this->assertEquals( 202, $response->getStatusCode() );
	}
}