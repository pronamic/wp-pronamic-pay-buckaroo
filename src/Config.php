<?php

/**
 * Title: Buckaroo config
 * Description:
 * Copyright: Copyright (c) 2005 - 2017
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.2.3
 * @since 1.0.0
 */
class Pronamic_WP_Pay_Gateways_Buckaroo_Config extends Pronamic_WP_Pay_GatewayConfig {
	public $website_key;

	public $secret_key;

	public $excluded_services;

	public $invoice_number;

	public function get_gateway_class() {
		return 'Pronamic_WP_Pay_Gateways_Buckaroo_Gateway';
	}
}
