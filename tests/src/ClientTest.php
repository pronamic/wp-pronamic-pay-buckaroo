<?php
/**
 * Client test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2021 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Buckaroo
 */

namespace Pronamic\WordPress\Pay\Gateways\Buckaroo;

use Pronamic\WordPress\Http\Factory;
use WP_Error;
use WP_Http;

/**
 * Title: Buckaroo client tests
 * Description:
 * Copyright: 2005-2021 Pronamic
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.4
 */
class ClientTest extends \WP_UnitTestCase {
	/**
	 * Setup.
	 */
	public function setUp() {
		parent::setUp();

		$this->factory = new Factory();
	}

	/**
	 * Test client.
	 */
	public function test_client() {
		$client = new Client();
		$client->set_website_key( getenv( 'BUCKAROO_WEBSITE_KEY' ) );
		$client->set_secret_key( getenv( 'BUCKAROO_SECRET_KEY' ) );

		$this->factory->fake( 'https://testcheckout.buckaroo.nl/nvp/?op=TransactionRequestSpecification', __DIR__ . '/../http/testcheckout-buckaroo-nl-nvp-op-TransactionRequestSpecification-ok.http' );

		$issuers = $client->get_issuers();

		$this->assertInternalType( 'array', $issuers );
	}
}
