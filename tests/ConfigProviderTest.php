<?php

class Pronamic_WP_Pay_Gateways_Buckaroo_ConfigProviderTest extends WP_UnitTestCase {
	function test_gateway_factory() {
		Pronamic_WP_Pay_ConfigProvider::register( 'buckaroo', 'Pronamic_WP_Pay_Gateways_Buckaroo_ConfigFactory' );

		$config = Pronamic_WP_Pay_ConfigProvider::get_config( 'buckaroo', -1 );

		$this->assertInstanceOf( 'Pronamic_WP_Pay_Gateways_Buckaroo_Config', $config );
	}
}
