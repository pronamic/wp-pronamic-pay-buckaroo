<?php

class Pronamic_WP_Pay_Gateways_Buckaroo_GatewayFactoryTest extends WP_UnitTestCase {
	function test_gateway_factory() {
		Pronamic_WP_Pay_GatewayFactory::register( 'Pronamic_WP_Pay_Gateways_Buckaroo_Config', 'Pronamic_WP_Pay_Gateways_Buckaroo_Gateway' );

		$config = new Pronamic_WP_Pay_Gateways_Buckaroo_Config();

		$gateway = Pronamic_WP_Pay_GatewayFactory::create( $config );

		$this->assertInstanceOf( 'Pronamic_WP_Pay_Gateways_Buckaroo_Gateway', $gateway );
	}
}
