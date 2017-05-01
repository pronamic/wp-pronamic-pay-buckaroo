<?php

/**
 * Title: Buckaroo gateway
 * Description:
 * Copyright: Copyright (c) 2005 - 2017
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.2.8
 * @since 1.0.0
 */
class Pronamic_WP_Pay_Gateways_Buckaroo_Gateway extends Pronamic_WP_Pay_Gateway {
	/**
	 * Slug of this gateway
	 *
	 * @var string
	 */
	const SLUG = 'buckaroo';

	/////////////////////////////////////////////////

	/**
	 * Constructs and initializes an Buckaroo gateway
	 *
	 * @param Pronamic_WP_Pay_Gateways_Buckaroo_Config $config
	 */
	public function __construct( Pronamic_WP_Pay_Gateways_Buckaroo_Config $config ) {
		parent::__construct( $config );

		$this->set_method( Pronamic_WP_Pay_Gateway::METHOD_HTML_FORM );
		$this->set_has_feedback( true );
		$this->set_amount_minimum( 0.01 );
		$this->set_slug( self::SLUG );

		$this->client = new Pronamic_WP_Pay_Gateways_Buckaroo_Client();
		$this->client->set_website_key( $config->website_key );
		$this->client->set_secret_key( $config->secret_key );
		$this->client->set_excluded_services( $config->excluded_services );
		$this->client->set_invoice_number( $config->invoice_number );
		$this->client->set_push_url( add_query_arg( 'buckaroo_push', '', home_url( '/' ) ) );

		if ( 'test' === $config->mode ) {
			$this->client->set_payment_server_url( Pronamic_WP_Pay_Gateways_Buckaroo_Client::GATEWAY_TEST_URL );
		}
	}

	/////////////////////////////////////////////////

	/**
	 * Get issuers.
	 *
	 * @since 1.2.4
	 * @see Pronamic_WP_Pay_Gateway::get_issuers()
	 */
	public function get_issuers() {
		$groups = array();

		$result = $this->client->get_issuers();

		if ( $result ) {
			$groups[] = array(
				'options' => $result,
			);

			return $groups;
		}

		$this->error = $this->client->get_error();
	}

	/////////////////////////////////////////////////

	/**
	 * Get issuer field.
	 *
	 * @return array
	 */
	public function get_issuer_field() {
		if ( Pronamic_WP_Pay_PaymentMethods::IDEAL === $this->get_payment_method() ) {
			return array(
				'id'       => 'pronamic_ideal_issuer_id',
				'name'     => 'pronamic_ideal_issuer_id',
				'label'    => __( 'Choose your bank', 'pronamic_ideal' ),
				'required' => true,
				'type'     => 'select',
				'choices'  => $this->get_transient_issuers(),
			);
		}
	}

	/////////////////////////////////////////////////

	/**
	 * Get supported payment methods
	 *
	 * @see Pronamic_WP_Pay_Gateway::get_supported_payment_methods()
	 */
	public function get_supported_payment_methods() {
		return array(
			Pronamic_WP_Pay_PaymentMethods::IDEAL,
			Pronamic_WP_Pay_PaymentMethods::CREDIT_CARD,
			Pronamic_WP_Pay_PaymentMethods::BANCONTACT,
		);
	}

	/////////////////////////////////////////////////

	/**
	 * Start
	 *
	 * @param Pronamic_Pay_PaymentDataInterface $data
	 * @param Pronamic_Pay_Payment              $payment
	 *
	 * @see Pronamic_WP_Pay_Gateway::start()
	 */
	public function start( Pronamic_Pay_Payment $payment ) {
		$payment->set_action_url( $this->client->get_payment_server_url() );

		$payment_method = $payment->get_method();

		switch ( $payment_method ) {
			case Pronamic_WP_Pay_PaymentMethods::IDEAL :
				$this->client->set_payment_method( Pronamic_WP_Pay_Gateways_Buckaroo_PaymentMethods::IDEAL );
				$this->client->set_ideal_issuer( $payment->get_issuer() );

				break;
			case Pronamic_WP_Pay_PaymentMethods::CREDIT_CARD :
				$this->client->add_requested_service( Pronamic_WP_Pay_Gateways_Buckaroo_PaymentMethods::AMERICAN_EXPRESS );
				$this->client->add_requested_service( Pronamic_WP_Pay_Gateways_Buckaroo_PaymentMethods::MAESTRO );
				$this->client->add_requested_service( Pronamic_WP_Pay_Gateways_Buckaroo_PaymentMethods::MASTERCARD );
				$this->client->add_requested_service( Pronamic_WP_Pay_Gateways_Buckaroo_PaymentMethods::VISA );

				break;
			case Pronamic_WP_Pay_PaymentMethods::BANCONTACT :
			case Pronamic_WP_Pay_PaymentMethods::MISTER_CASH :
				$this->client->set_payment_method( Pronamic_WP_Pay_Gateways_Buckaroo_PaymentMethods::BANCONTACT_MISTER_CASH );

				break;
			default :
				if ( '0' !== $payment_method ) {
					// Leap of faith if the WordPress payment method could not transform to a Buckaroo method?
					$this->client->set_payment_method( $payment_method );
				}

				break;
		}

		// Buckaroo uses 'nl-NL' instead of 'nl_NL'
		$culture = str_replace( '_', '-', $payment->get_locale() );

		$this->client->set_payment_id( $payment->get_id() );
		$this->client->set_culture( $culture );
		$this->client->set_currency( $payment->get_currency() );
		$this->client->set_description( $payment->get_description() );
		$this->client->set_amount( $payment->get_amount() );
		$this->client->set_invoice_number( Pronamic_WP_Pay_Gateways_Buckaroo_Util::get_invoice_number( $this->client->get_invoice_number(), $payment ) );
		$this->client->set_return_url( $payment->get_return_url() );
		$this->client->set_return_cancel_url( $payment->get_return_url() );
		$this->client->set_return_error_url( $payment->get_return_url() );
		$this->client->set_return_reject_url( $payment->get_return_url() );
	}

	/////////////////////////////////////////////////

	/**
	 * Get output HTML
	 *
	 * @since 1.1.1
	 * @see Pronamic_WP_Pay_Gateway::get_output_html()
	 */
	public function get_output_fields() {
		return $this->client->get_fields();
	}

	/////////////////////////////////////////////////

	/**
	 * Update status of the specified payment
	 *
	 * @param Pronamic_Pay_Payment $payment
	 */
	public function update_status( Pronamic_Pay_Payment $payment ) {
		$method = filter_var( $_SERVER['REQUEST_METHOD'], FILTER_SANITIZE_STRING );

		$data = array();

		switch ( $method ) {
			case 'GET':
				$data = $_GET;

				break;
			case 'POST':
				$data = $_POST; // WPCS: CSRF OK

				break;
		}

		$data = Pronamic_WP_Pay_Gateways_Buckaroo_Util::urldecode( $data );

		$data = stripslashes_deep( $data );

		$data = $this->client->verify_request( $data );

		if ( $data ) {
			$payment->set_transaction_id( $data[ Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::PAYMENT ] );
			$payment->set_status( Pronamic_WP_Pay_Gateways_Buckaroo_Statuses::transform( $data[ Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::STATUS_CODE ] ) );
			$payment->set_consumer_iban( $data[ Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::SERVICE_IDEAL_CONSUMER_IBAN ] );
			$payment->set_consumer_bic( $data[ Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::SERVICE_IDEAL_CONSUMER_BIC ] );
			$payment->set_consumer_name( $data[ Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::SERVICE_IDEAL_CONSUMER_NAME ] );

			$labels = array(
				Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::PAYMENT                       => __( 'Payment', 'pronamic_ideal' ),
				Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::PAYMENT_METHOD                => __( 'Payment Method', 'pronamic_ideal' ),
				Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::STATUS_CODE                   => __( 'Status Code', 'pronamic_ideal' ),
				Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::STATUS_CODE_DETAIL            => __( 'Status Code Detail', 'pronamic_ideal' ),
				Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::STATUS_MESSAGE                => __( 'Status Message', 'pronamic_ideal' ),
				Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::INVOICE_NUMBER                => __( 'Invoice Number', 'pronamic_ideal' ),
				Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::AMOUNT                        => __( 'Amount', 'pronamic_ideal' ),
				Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::CURRENCY                      => __( 'Currency', 'pronamic_ideal' ),
				Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::TIMESTAMP                     => __( 'Timestamp', 'pronamic_ideal' ),
				Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::SERVICE_IDEAL_CONSUMER_ISSUER => __( 'Service iDEAL Consumer Issuer', 'pronamic_ideal' ),
				Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::SERVICE_IDEAL_CONSUMER_NAME   => __( 'Service iDEAL Consumer Name', 'pronamic_ideal' ),
				Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::SERVICE_IDEAL_CONSUMER_IBAN   => __( 'Service iDEAL Consumer IBAN', 'pronamic_ideal' ),
				Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::SERVICE_IDEAL_CONSUMER_BIC    => __( 'Service iDEAL Consumer BIC', 'pronamic_ideal' ),
				Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::TRANSACTIONS                  => __( 'Transactions', 'pronamic_ideal' ),
			);

			$note = '';

			$note .= '<p>';
			$note .= __( 'Buckaroo data:', 'pronamic_ideal' );
			$note .= '</p>';

			$note .= '<dl>';

			foreach ( $labels as $key => $label ) {
				if ( isset( $data[ $key ] ) ) {
					$note .= sprintf( '<dt>%s</dt>', esc_html( $label ) );
					$note .= sprintf( '<dd>%s</dd>', esc_html( $data[ $key ] ) );
				}
			}

			$note .= '</dl>';

			$payment->add_note( $note );
		}
	}
}
