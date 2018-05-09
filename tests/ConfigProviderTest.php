<?php

namespace Pronamic\WordPress\Pay\Gateways\Buckaroo;

use Pronamic\WordPress\Pay\Core\ConfigProvider;

class ConfigProviderTest extends \WP_UnitTestCase {
	public function test_gateway_factory() {
		ConfigProvider::register( 'buckaroo', __NAMESPACE__ . '\ConfigFactory' );

		$config = ConfigProvider::get_config( 'buckaroo', -1 );

		$this->assertInstanceOf( __NAMESPACE__ . '\Config', $config );
	}
}
