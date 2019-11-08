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

/**
 * Title: Buckaroo client tests
 * Description:
 * Copyright: 2005-2019 Pronamic
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 */
class ClientTest extends \WP_UnitTestCase {
	/**
	 * Test client.
	 */
	public function test_client() {
		$client = new Client();
		$client->set_website_key( getenv( 'BUCKAROO_WEBSITE_KEY' ) );
		$client->set_secret_key( getenv( 'BUCKAROO_SECRET_KEY' ) );

		$issuers = $client->get_issuers();

		$this->assertInternalType( 'array', $issuers );
	}
}
