<?php

namespace Pronamic\WordPress\Pay\Gateways\Buckaroo;

use Pronamic\WordPress\Pay\Core\GatewayConfig;

/**
 * Title: Buckaroo config
 * Description:
 * Copyright: 2005-2022 Pronamic
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 2.0.0
 * @since 1.0.0
 */
class Config extends GatewayConfig {
	/**
	 * Host.
	 * 
	 * @var string
	 */
	private $host;

	/**
	 * Website key.
	 *
	 * @var string|null
	 */
	public $website_key;

	/**
	 * Secret key.
	 *
	 * @var string|null
	 */
	public $secret_key;

	/**
	 * Excluded services.
	 *
	 * @var string|null
	 */
	public $excluded_services;

	/**
	 * Invoice number.
	 *
	 * @var string|null
	 */
	public $invoice_number;

	/**
	 * Construct config.
	 */
	public function __construct() {
		$this->host = 'checkout.buckaroo.nl';
	}

	/**
	 * Get host.
	 * 
	 * @return string
	 */
	public function get_host() {
		return $this->host;
	}

	/**
	 * Set host.
	 * 
	 * @param string $host Host.
	 */
	public function set_host( $host ) {
		$this->host = $host;
	}

	/**
	 * Get website key.
	 *
	 * @return string|null
	 */
	public function get_website_key() {
		return $this->website_key;
	}

	/**
	 * Get secret key.
	 *
	 * @return string|null
	 */
	public function get_secret_key() {
		return $this->secret_key;
	}

	/**
	 * Get excluded services.
	 *
	 * @return string|null
	 */
	public function get_excluded_services() {
		return $this->excluded_services;
	}

	/**
	 * Get invoice number.
	 *
	 * @return string|null
	 */
	public function get_invoice_number() {
		return $this->invoice_number;
	}
}
