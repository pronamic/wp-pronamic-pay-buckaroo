<?php

/**
 * Title: Buckaroo client
 * Description:
 * Copyright: Copyright (c) 2005 - 2017
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.2.8
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

	/**
	 * Gateway Name-Value-Pair URL
	 *
	 * @var string
	 */
	const GATEWAY_NVP_URL = 'https://checkout.buckaroo.nl/nvp/';

	/**
	 * Gateway Name-Value-Pair test URL
	 *
	 * @var string
	 */
	const GATEWAY_NVP_TEST_URL = 'https://testcheckout.buckaroo.nl/nvp/';

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
	 * The iDEAL issuer
	 *
	 * @since 1.2.4
	 * @var string
	 */
	private $ideal_issuer;

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
	 * Push URL
	 *
	 * @var string
	 */
	private $push_url;

	//////////////////////////////////////////////////

	/**
	 * Requested services
	 *
	 * @var array
	 */
	private $requested_services;

	//////////////////////////////////////////////////

	/**
	 * Excluded services
	 *
	 * @var array
	 */
	private $excluded_services;

	//////////////////////////////////////////////////

	/**
	 * Pronamic payment ID
	 *
	 * @var array
	 */
	private $payment_id;

	//////////////////////////////////////////////////

	/**
	 * Error.
	 *
	 * @since 1.2.6
	 * @var WP_Error
	 */
	private $error;

	/////////////////////////////////////////////////

	/**
	 * Constructs and initialize a iDEAL kassa object
	 */
	public function __construct() {
		$this->set_payment_server_url( self::GATEWAY_URL );

		$this->requested_services = array();
	}

	//////////////////////////////////////////////////

	/**
	 * Get error.
	 *
	 * @since 1.2.6
	 * @return WP_Error
	 */
	public function get_error() {
		return $this->error;
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

	/**
	 * Get iDEAL issuer.
	 *
	 * @since 1.2.4
	 * @return string
	 */
	public function get_ideal_issuer() {
		return $this->ideal_issuer;
	}

	/**
	 * Set iDEAL issuer.
	 *
	 * @since 1.2.4
	 * @param string $issuer
	 */
	public function set_ideal_issuer( $issuer ) {
		$this->ideal_issuer = $issuer;
	}

	//////////////////////////////////////////////////

	public function get_requested_services() {
		return $this->requested_services;
	}

	public function add_requested_service( $service ) {
		$this->requested_services[] = $service;
	}

	//////////////////////////////////////////////////

	public function get_excluded_services() {
		return $this->excluded_services;
	}

	public function set_excluded_services( $service ) {
		$this->excluded_services = $service;
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
	 * Get push URL
	 *
	 * @return string
	 */
	public function get_push_url() {
		return $this->push_url;
	}

	/**
	 * Set push URL
	 *
	 * @param string $url
	 */
	public function set_push_url( $url ) {
		$this->push_url = $url;
	}

	//////////////////////////////////////////////////

	/**
	 * Get Pronamic payment ID
	 *
	 * @return string
	 */
	public function get_payment_id() {
		return $this->payment_id;
	}

	/**
	 * Set Pronamic payment ID
	 *
	 * @param string $payment_id
	 */
	public function set_payment_id( $payment_id ) {
		$this->payment_id = $payment_id;
	}

	//////////////////////////////////////////////////

	/**
	 * Get issuers
	 *
	 * @since 1.2.4
	 * @see http://support.buckaroo.nl/index.php/Service_iDEAL#iDEAL_banken_lijst_opvragen
	 * @return array
	 */
	public function get_issuers() {
		$issuers = array();

		$url = add_query_arg( 'op', 'TransactionRequestSpecification', self::GATEWAY_NVP_TEST_URL );

		$data = array(
			'brq_websitekey'        => $this->get_website_key(),
			'brq_services'          => 'ideal',
			'brq_latestversiononly' => 'True',
		);

		$signature = Pronamic_WP_Pay_Gateways_Buckaroo_Security::create_signature( $data, $this->get_secret_key() );

		$data[ Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::SIGNATURE ] = $signature;

		$result = wp_remote_post( $url, array(
			'body' => http_build_query( $data ),
		) );

		$body = wp_remote_retrieve_body( $result );

		wp_parse_str( $body, $data );

		$data = Pronamic_WP_Pay_Gateways_Buckaroo_Util::transform_flat_response( $data );

		$error_msg = __( 'Unable to retrieve issuers from Buckaroo.', 'pronamic_ideal' );

		if ( 200 !== wp_remote_retrieve_response_code( $result ) ) {
			$this->error = new WP_Error( 'buckaroo_error', $error_msg, $data );

			return $issuers;
		}

		if ( isset( $data['BRQ_APIRESULT'] ) && 'Fail' === $data['BRQ_APIRESULT'] ) {
			$this->error = new WP_Error( 'buckaroo_error', sprintf( '%s %s', $error_msg, $data['BRQ_APIERRORMESSAGE'] ), $data );

			return $issuers;
		}

		if ( ! isset( $data['BRQ_SERVICES'] ) ) {
			return $issuers;
		}

		foreach ( $data['BRQ_SERVICES'] as $service ) {
			if ( ! isset( $service['NAME'], $service['VERSION'], $service['ACTIONDESCRIPTION'] ) ) {
				return $issuers;
			}

			if ( Pronamic_WP_Pay_Gateways_Buckaroo_PaymentMethods::IDEAL !== $service['NAME'] ) {
				continue;
			}

			foreach ( $service['ACTIONDESCRIPTION'] as $action ) {
				if ( ! isset( $action['NAME'], $action['REQUESTPARAMETERS'] ) ) {
					return $issuers;
				}

				if ( 'Pay' !== $action['NAME'] ) {
					continue;
				}

				foreach ( $action['REQUESTPARAMETERS'] as $parameter ) {

					if ( ! isset( $parameter['NAME'], $parameter['LISTITEMDESCRIPTION'] ) ) {
						return $issuers;
					}

					if ( 'issuer' !== $parameter['NAME'] ) {
						continue;
					}

					foreach ( $parameter['LISTITEMDESCRIPTION'] as $issuer ) {
						$issuers[ $issuer['VALUE'] ] = $issuer['DESCRIPTION'];
					}

					break;
				}
			}
		}

		return $issuers;
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
			Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::ADD_PRONAMIC_PAYMENT_ID => $this->get_payment_id(),
			Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::WEBSITE_KEY             => $this->get_website_key(),
			Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::INVOICE_NUMBER          => $this->get_invoice_number(),
			Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::AMOUNT                  => number_format( $this->get_amount(), 2, '.', '' ),
			Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::CURRENCY                => $this->get_currency(),
			Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::CULTURE                 => $this->get_culture(),
			Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::DESCRIPTION             => $this->get_description(),
			Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::PAYMENT_METHOD          => $this->get_payment_method(),
			Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::RETURN_URL              => $this->get_return_url(),
			Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::RETURN_REJECT_URL       => $this->get_return_reject_url(),
			Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::RETURN_ERROR_URL        => $this->get_return_error_url(),
			Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::RETURN_CANCEL_URL       => $this->get_return_cancel_url(),
			Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::PUSH_URL                => $this->get_push_url(),
			Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::PUSH_FAILURE_URL        => $this->get_push_url(),
			Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::REQUESTED_SERVICES      => implode( ',', $this->get_requested_services() ),
			Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::EXCLUDED_SERVICES       => $this->get_excluded_services(),
			Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::IDEAL_ISSUER            => $this->get_ideal_issuer(),
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
				Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::ADD_PRONAMIC_PAYMENT_ID       => FILTER_SANITIZE_STRING,
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
