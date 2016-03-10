<?php

class Pronamic_WP_Pay_Gateways_Buckaroo_ClientTest extends WP_UnitTestCase {
	function test_client() {
		$client = new Pronamic_WP_Pay_Gateways_Buckaroo_Client();
		$client->set_website_key( getenv( 'BUCKAROO_WEBSITE_KEY' ) );
		$client->set_secret_key( getenv( 'BUCKAROO_SECRET_KEY' ) );

		$client->get_issuers();
	}
}
