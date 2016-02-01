<?php

/**
 * Title: Buckaroo client
 * Description:
 * Copyright: Copyright (c) 2005 - 2016
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.2.0
 * @since 1.0.0
 */
class Pronamic_WP_Pay_Gateways_Buckaroo_Client {
	/**
	 * Gateway URL
	 *
	 * @var string
	 */
	const GATEWAY_URL = 'https://checkout.buckaroo.nl/html/';

	/**
	 * Gateway test URL
	 *
	 * @var string
	 */
	const GATEWAY_TEST_URL = 'https://testcheckout.buckaroo.nl/html/';

	//////////////////////////////////////////////////

	/**
	 * Indicator for the iDEAL payment method
	 *
	 * @var string
	 */
	const PAYMENT_METHOD_IDEAL = 'ideal';

	//////////////////////////////////////////////////

	/**
	 * The payment server URL
	 *
	 * @var string
	 */
	private $payment_server_url;

	//////////////////////////////////////////////////

	/**
	 * The amount
	 *
	 * @var int
	 */
	private $amount;

	//////////////////////////////////////////////////

	/**
	 * The website key
	 *
	 * @var string
	 */
	private $website_key;

	//////////////////////////////////////////////////

	/**
	 * The secret key
	 *
	 * @var string
	 */
	private $secret_key;

	//////////////////////////////////////////////////

	/**
	 * The payment method
	 *
	 * @var string
	 */
	private $payment_method;

	//////////////////////////////////////////////////

	/**
	 * The country code (culture)
	 *
	 * @var string
	 */
	private $culture;

	//////////////////////////////////////////////////

	/**
	 * The currency
	 *
	 * @var string
	 */
	private $currency;

	//////////////////////////////////////////////////

	/**
	 * The invoice number
	 *
	 * @var string
	 */
	private $invoice_number;

	//////////////////////////////////////////////////

	/**
	 * The description
	 *
	 * @var string
	 */
	private $description;

	//////////////////////////////////////////////////

	/**
	 * The return url
	 *
	 * @var string
	 */
	private $return_url;

	//////////////////////////////////////////////////

	/**
	 * The return reject url
	 *
	 * @var string
	 */
	private $return_reject_url;

	//////////////////////////////////////////////////

	/**
	 * The return error url
	 *
	 * @var string
	 */
	private $return_error_url;

	//////////////////////////////////////////////////

	/**
	 * The return cancel url
	 *
	 * @var string
	 */
	private $return_cancel_url;

	//////////////////////////////////////////////////

	/**
	 * Requested services
	 *
	 * @var array
	 */
	private $requested_services;

	//////////////////////////////////////////////////

	/**
	 * Constructs and initialize a iDEAL kassa object
	 */
	public function __construct() {
		$this->set_payment_server_url( self::GATEWAY_URL );

		$this->requested_services = array();
	}

	//////////////////////////////////////////////////

	/**
	 * Get the payment server URL
	 *
	 * @return string the payment server URL
	 */
	public function get_payment_server_url() {
		return $this->payment_server_url;
	}

	/**
	 * Set the payment server URL
	 *
	 * @param string $url an URL
	 */
	public function set_payment_server_url( $url ) {
		$this->payment_server_url = $url;
	}

	//////////////////////////////////////////////////

	public function get_website_key() {
		return $this->website_key;
	}

	public function set_website_key( $website_key ) {
		$this->website_key = $website_key;
	}

	//////////////////////////////////////////////////

	public function get_secret_key() {
		return $this->secret_key;
	}

	public function set_secret_key( $secret_key ) {
		$this->secret_key = $secret_key;
	}

	//////////////////////////////////////////////////

	public function get_payment_method() {
		return $this->payment_method;
	}

	public function set_payment_method( $payment_method ) {
		$this->payment_method = $payment_method;
	}

	//////////////////////////////////////////////////

	public function get_requested_services() {
		return $this->requested_services;
	}

	public function add_requested_service( $service ) {
		$this->requested_services[] = $service;
	}

	//////////////////////////////////////////////////

	public function get_culture() {
		return $this->culture;
	}

	public function set_culture( $culture ) {
		$this->culture = $culture;
	}

	//////////////////////////////////////////////////

	public function get_currency() {
		return $this->currency;
	}

	public function set_currency( $currency ) {
		$this->currency = $currency;
	}

	//////////////////////////////////////////////////
	// Payment
	//////////////////////////////////////////////////

	public function get_invoice_number() {
		return $this->invoice_number;
	}

	public function set_invoice_number( $invoice_number ) {
		$this->invoice_number = $invoice_number;
	}

	//////////////////////////////////////////////////

	public function get_description() {
		return $this->description;
	}

	public function set_description( $description ) {
		$this->description = $description;
	}

	//////////////////////////////////////////////////

	public function get_amount() {
		return $this->amount;
	}

	public function set_amount( $amount ) {
		$this->amount = $amount;
	}

	//////////////////////////////////////////////////
	// URL's
	//////////////////////////////////////////////////

	/**
	 * Get return URL
	 *
	 * @return string
	 */
	public function get_return_url() {
		return $this->return_url;
	}

	/**
	 * Set return URL
	 *
	 * @param string $url
	 */
	public function set_return_url( $url ) {
		$this->return_url = $url;
	}

	//////////////////////////////////////////////////

	/**
	 * Get return reject URL
	 *
	 * @return string
	 */
	public function get_return_reject_url() {
		return $this->return_reject_url;
	}

	/**
	 * Set return reject URL
	 *
	 * @param string $url
	 */
	public function set_return_reject_url( $url ) {
		$this->return_reject_url = $url;
	}

	//////////////////////////////////////////////////

	/**
	 * Get return error URL
	 *
	 * @return string
	 */
	public function get_return_error_url() {
		return $this->return_error_url;
	}

	/**
	 * Set return error URL
	 *
	 * @param string $url
	 */
	public function set_return_error_url( $url ) {
		$this->return_error_url = $url;
	}

	//////////////////////////////////////////////////

	/**
	 * Get return cancel URL
	 *
	 * @return string
	 */
	public function get_return_cancel_url() {
		return $this->return_cancel_url;
	}

	/**
	 * Set return cancel URL
	 *
	 * @param string $url
	 */
	public function set_return_cancel_url( $url ) {
		$this->return_cancel_url = $url;
	}

	//////////////////////////////////////////////////

	/**
	 * Get HTML fields
	 *
	 * @since 1.1.1
	 * @return string
	 */
	public function get_fields() {
		$data = array(
			Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::WEBSITE_KEY        => $this->get_website_key(),
			Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::INVOICE_NUMBER     => $this->get_invoice_number(),
			Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::AMOUNT             => number_format( $this->get_amount(), 2, '.', '' ),
			Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::CURRENCY           => $this->get_currency(),
			Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::CULTURE            => $this->get_culture(),
			Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::DESCRIPTION        => $this->get_description(),
			Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::PAYMENT_METHOD     => $this->get_payment_method(),
			Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::RETURN_URL         => $this->get_return_url(),
			Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::RETURN_REJECT_URL  => $this->get_return_reject_url(),
			Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::RETURN_ERROR_URL   => $this->get_return_error_url(),
			Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::RETURN_CANCEL_URL  => $this->get_return_cancel_url(),
			Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::REQUESTED_SERVICES => implode( ',', $this->get_requested_services() ),
		);

		$signature = Pronamic_WP_Pay_Gateways_Buckaroo_Security::create_signature( $data, $this->get_secret_key() );

		$data[ Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::SIGNATURE ] = $signature;

		return $data;
	}

	//////////////////////////////////////////////////

	/**
	 * Verify request  Buckaroo
	 */
	public function verify_request( $data ) {
		$result = false;

		$signature = Pronamic_WP_Pay_Gateways_Buckaroo_Security::get_signature( $data );

		$signature_check = Pronamic_WP_Pay_Gateways_Buckaroo_Security::create_signature( $data, $this->get_secret_key() );

		if ( 0 === strcasecmp( $signature, $signature_check ) ) {
			$data = array_change_key_case( $data, CASE_LOWER );

			$result = filter_var_array( $data, array(
				Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::PAYMENT                       => FILTER_SANITIZE_STRING,
				Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::PAYMENT_METHOD                => FILTER_SANITIZE_STRING,
				Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::STATUS_CODE                   => FILTER_VALIDATE_INT,
				Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::STATUS_CODE_DETAIL            => FILTER_SANITIZE_STRING,
				Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::STATUS_MESSAGE                => FILTER_SANITIZE_STRING,
				Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::INVOICE_NUMBER                => FILTER_SANITIZE_STRING,
				Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::AMOUNT                        => FILTER_VALIDATE_FLOAT,
				Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::CURRENCY                      => FILTER_SANITIZE_STRING,
				Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::TIMESTAMP                     => FILTER_SANITIZE_STRING,
				Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::SERVICE_IDEAL_CONSUMER_ISSUER => FILTER_SANITIZE_STRING,
				Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::SERVICE_IDEAL_CONSUMER_NAME   => FILTER_SANITIZE_STRING,
				Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::SERVICE_IDEAL_CONSUMER_IBAN   => FILTER_SANITIZE_STRING,
				Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::SERVICE_IDEAL_CONSUMER_BIC    => FILTER_SANITIZE_STRING,
				Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::TRANSACTIONS                  => FILTER_SANITIZE_STRING,
			) );
		}

		return $result;
	}
}
