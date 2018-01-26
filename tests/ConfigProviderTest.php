<?php

use Pronamic\WordPress\Pay\Core\ConfigProvider;

class Pronamic_WP_Pay_Gateways_Buckaroo_ConfigProviderTest extends WP_UnitTestCase {
	public function test_gateway_factory() {
		ConfigProvider::register( 'buckaroo', 'Pronamic_WP_Pay_Gateways_Buckaroo_ConfigFactory' );

		$config = ConfigProvider::get_config( 'buckaroo', -1 );

		$this->assertInstanceOf( 'Pronamic_WP_Pay_Gateways_Buckaroo_Config', $config );
	}
}
