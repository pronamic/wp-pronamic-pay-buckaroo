<?php

class Pronamic_WP_Pay_Gateways_Buckaroo_Integration {
	public function __construct() {
		$this->id       = 'buckaroo';
		$this->name     = 'Buckaroo - HTML';
		$this->url      = 'https://payment.buckaroo.nl/';
		$this->provider = 'buckaroo';

		// Actions
		$function = array( 'Pronamic_WP_Pay_Gateways_Buckaroo_Listener', 'listen' );

		if ( ! has_action( 'wp_loaded', $function ) ) {
			add_action( 'wp_loaded', $function );	
		}		
	}

	public function get_config_factory_class() {
		return 'Pronamic_WP_Pay_Gateways_Buckaroo_ConfigFactory';
	}

	public function get_config_class() {
		return 'Pronamic_WP_Pay_Gateways_Buckaroo_Config';
	}

	public function get_settings_class() {
		return 'Pronamic_WP_Pay_Gateways_Buckaroo_Settings';
	}

	public function get_gateway_class() {
		return 'Pronamic_WP_Pay_Gateways_Buckaroo_Gateway';
	}
}
