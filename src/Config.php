<?php

namespace Pronamic\WordPress\Pay\Gateways\Buckaroo;

use Pronamic\WordPress\Pay\Core\GatewayConfig;

/**
 * Title: Buckaroo config
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 2.0.0
 * @since 1.0.0
 */
class Config extends GatewayConfig {
	public $website_key;

	public $secret_key;

	public $excluded_services;

	public $invoice_number;
}
