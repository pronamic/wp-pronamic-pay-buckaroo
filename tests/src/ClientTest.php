<?php
/**
 * Client test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Buckaroo
 */

namespace Pronamic\WordPress\Pay\Gateways\Buckaroo;

use WP_Error;
use WP_Http;

/**
 * Title: Buckaroo client tests
 * Description:
 * Copyright: 2005-2019 Pronamic
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.4
 */
class ClientTest extends \WP_UnitTestCase {
	/**
	 * Mock HTTP responses.
	 *
	 * @var array
	 */
	private $mock_http_responses;

	/**
	 * Setup.
	 */
	public function setUp() {
		parent::setUp();

		$this->mock_http_responses = array();

		// Mock HTTP response.
		add_filter( 'pre_http_request', array( $this, 'pre_http_request' ), 10, 3 );
	}

	/**
	 * Mock HTTP response.
	 *
	 * @param string $url  URL.
	 * @param string $file File with HTTP response.
	 */
	public function mock_http_response( $url, $file ) {
		$this->mock_http_responses[ $url ] = $file;
	}

	/**
	 * Pre HTTP request
	 *
	 * @link https://github.com/WordPress/WordPress/blob/3.9.1/wp-includes/class-http.php#L150-L164
	 *
	 * @param false|array|WP_Error $preempt Whether to preempt an HTTP request's return value. Default false.
	 * @param array                $r       HTTP request arguments.
	 * @param string               $url     The request URL.
	 *
	 * @return array
	 */
	public function pre_http_request( $preempt, $r, $url ) {
		if ( ! isset( $this->mock_http_responses[ $url ] ) ) {
			return $preempt;
		}

		$file = $this->mock_http_responses[ $url ];

		unset( $this->mock_http_responses[ $url ] );

		$response = file_get_contents( $file, true );

		$processed_response = WP_Http::processResponse( $response );

		$processed_headers = WP_Http::processHeaders( $processed_response['headers'], $url );

		$processed_headers['body'] = $processed_response['body'];

		return $processed_headers;
	}

	/**
	 * Test client.
	 */
	public function test_client() {
		$client = new Client();
		$client->set_website_key( getenv( 'BUCKAROO_WEBSITE_KEY' ) );
		$client->set_secret_key( getenv( 'BUCKAROO_SECRET_KEY' ) );

		$this->mock_http_response( 'https://testcheckout.buckaroo.nl/nvp/?op=TransactionRequestSpecification', __DIR__ . '/../http/testcheckout-buckaroo-nl-nvp-op-TransactionRequestSpecification-ok.http' );

		$issuers = $client->get_issuers();

		$this->assertInternalType( 'array', $issuers );
	}
}
