<?php

class Pronamic_WP_Pay_Gateways_Buckaroo_Integration {
	public function __construct() {
		$this->id       = 'buckaroo';
		$this->name     = 'Buckaroo - HTML';
		$this->url      = 'https://payment.buckaroo.nl/';
		$this->provider = 'buckaroo';
	}

	public function get_config_factory_class() {
		return 'Pronamic_WP_Pay_Gateways_Buckaroo_ConfigFactory';
	}

	public function get_config_class() {
		return 'Pronamic_WP_Pay_Gateways_Buckaroo_Config';
	}

	public function get_settings_class() {
		return 'Pronamic_WP_Pay_Gateways_Buckaroo_GatewaySettings';
	}

	public function get_gateway_class() {
		return 'Pronamic_WP_Pay_Gateways_Buckaroo_Gateway';
	}
}
