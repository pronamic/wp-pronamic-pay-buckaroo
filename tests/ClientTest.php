<?php

namespace Pronamic\WordPress\Pay\Gateways\Buckaroo;

class ClientTest extends \WP_UnitTestCase {
	public function test_client() {
		$client = new Client();
		$client->set_website_key( getenv( 'BUCKAROO_WEBSITE_KEY' ) );
		$client->set_secret_key( getenv( 'BUCKAROO_SECRET_KEY' ) );

		$issuers = $client->get_issuers();

		$this->assertInternalType( 'array', $issuers );
	}
}
