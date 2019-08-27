<?php

namespace Pronamic\WordPress\Pay\Gateways\Buckaroo;

use Pronamic\WordPress\Pay\Core\Gateway as Core_Gateway;
use Pronamic\WordPress\Pay\Core\PaymentMethods as Core_PaymentMethods;
use Pronamic\WordPress\Pay\Core\Server;
use Pronamic\WordPress\Pay\Payments\Payment;

/**
 * Title: Buckaroo gateway
 * Description:
 * Copyright: 2005-2019 Pronamic
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 2.0.2
 * @since 1.0.0
 */
class Gateway extends Core_Gateway {
	/**
	 * Client.
	 *
	 * @var Client
	 */
	protected $client;

	/**
	 * Constructs and initializes an Buckaroo gateway
	 *
	 * @param Config $config Config.
	 */
	public function __construct( Config $config ) {
		parent::__construct( $config );

		$this->set_method( self::METHOD_HTML_FORM );

		$this->client = new Client();
		$this->client->set_website_key( $config->website_key );
		$this->client->set_secret_key( $config->secret_key );
		$this->client->set_excluded_services( $config->excluded_services );
		$this->client->set_invoice_number( $config->invoice_number );
		$this->client->set_push_url( add_query_arg( 'buckaroo_push', '', home_url( '/' ) ) );

		if ( self::MODE_TEST === $config->mode ) {
			$this->client->set_payment_server_url( Client::GATEWAY_TEST_URL );
		}
	}

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

	/**
	 * Get supported payment methods
	 *
	 * @see Pronamic_WP_Pay_Gateway::get_supported_payment_methods()
	 */
	public function get_supported_payment_methods() {
		return array(
			Core_PaymentMethods::BANK_TRANSFER,
			Core_PaymentMethods::BANCONTACT,
			Core_PaymentMethods::CREDIT_CARD,
			Core_PaymentMethods::GIROPAY,
			Core_PaymentMethods::IDEAL,
			Core_PaymentMethods::PAYPAL,
			Core_PaymentMethods::SOFORT,
		);
	}

	/**
	 * Start
	 *
	 * @param Payment $payment Payment.
	 *
	 * @see Core_Gateway::start()
	 */
	public function start( Payment $payment ) {
		$payment->set_action_url( $this->client->get_payment_server_url() );

		$payment_method = $payment->get_method();

		switch ( $payment_method ) {
			case Core_PaymentMethods::IDEAL:
				$this->client->set_payment_method( PaymentMethods::IDEAL );
				$this->client->set_ideal_issuer( $payment->get_issuer() );

				break;
			case Core_PaymentMethods::CREDIT_CARD:
				$this->client->add_requested_service( PaymentMethods::AMERICAN_EXPRESS );
				$this->client->add_requested_service( PaymentMethods::MAESTRO );
				$this->client->add_requested_service( PaymentMethods::MASTERCARD );
				$this->client->add_requested_service( PaymentMethods::VISA );

				break;
			case Core_PaymentMethods::BANK_TRANSFER:
			case Core_PaymentMethods::BANCONTACT:
			case Core_PaymentMethods::MISTER_CASH:
			case Core_PaymentMethods::GIROPAY:
			case Core_PaymentMethods::PAYPAL:
			case Core_PaymentMethods::SOFORT:
				$this->client->set_payment_method( PaymentMethods::transform( $payment_method ) );

				break;
			default:
				if ( '0' !== $payment_method ) {
					// Leap of faith if the WordPress payment method could not transform to a Buckaroo method?
					$this->client->set_payment_method( $payment_method );
				}

				break;
		}

		// Locale.
		$locale = '';

		if ( null !== $payment->get_customer() ) {
			$locale = $payment->get_customer()->get_locale();
		}

		// Buckaroo uses 'nl-NL' instead of 'nl_NL'.
		$culture = str_replace( '_', '-', $locale );

		$this->client->set_payment_id( $payment->get_id() );
		$this->client->set_culture( $culture );
		$this->client->set_currency( $payment->get_total_amount()->get_currency()->get_alphabetic_code() );
		$this->client->set_description( $payment->get_description() );
		$this->client->set_amount( $payment->get_total_amount()->get_value() );
		$this->client->set_invoice_number( Util::get_invoice_number( $this->client->get_invoice_number(), $payment ) );
		$this->client->set_return_url( $payment->get_return_url() );
		$this->client->set_return_cancel_url( $payment->get_return_url() );
		$this->client->set_return_error_url( $payment->get_return_url() );
		$this->client->set_return_reject_url( $payment->get_return_url() );
	}

	/**
	 * Get output HTML
	 *
	 * @since 1.1.1
	 * @see Pronamic_WP_Pay_Gateway::get_output_html()
	 */
	public function get_output_fields() {
		return $this->client->get_fields();
	}

	/**
	 * Update status of the specified payment
	 *
	 * @param Payment $payment Payment.
	 */
	public function update_status( Payment $payment ) {
		$method = Server::get( 'REQUEST_METHOD', FILTER_SANITIZE_STRING );

		$data = array();

		switch ( $method ) {
			case 'GET':
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$data = $_GET;

				break;
			case 'POST':
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				$data = $_POST;

				break;
		}

		$data = Util::urldecode( $data );

		$data = stripslashes_deep( $data );

		$data = $this->client->verify_request( $data );

		if ( $data ) {
			$payment->set_transaction_id( $data[ Parameters::PAYMENT ] );
			$payment->set_status( Statuses::transform( $data[ Parameters::STATUS_CODE ] ) );
			$payment->set_consumer_iban( $data[ Parameters::SERVICE_IDEAL_CONSUMER_IBAN ] );
			$payment->set_consumer_bic( $data[ Parameters::SERVICE_IDEAL_CONSUMER_BIC ] );
			$payment->set_consumer_name( $data[ Parameters::SERVICE_IDEAL_CONSUMER_NAME ] );

			$labels = array(
				Parameters::PAYMENT                       => __( 'Payment', 'pronamic_ideal' ),
				Parameters::PAYMENT_METHOD                => __( 'Payment Method', 'pronamic_ideal' ),
				Parameters::STATUS_CODE                   => __( 'Status Code', 'pronamic_ideal' ),
				Parameters::STATUS_CODE_DETAIL            => __( 'Status Code Detail', 'pronamic_ideal' ),
				Parameters::STATUS_MESSAGE                => __( 'Status Message', 'pronamic_ideal' ),
				Parameters::INVOICE_NUMBER                => __( 'Invoice Number', 'pronamic_ideal' ),
				Parameters::AMOUNT                        => __( 'Amount', 'pronamic_ideal' ),
				Parameters::CURRENCY                      => __( 'Currency', 'pronamic_ideal' ),
				Parameters::TIMESTAMP                     => __( 'Timestamp', 'pronamic_ideal' ),
				Parameters::SERVICE_IDEAL_CONSUMER_ISSUER => __( 'Service iDEAL Consumer Issuer', 'pronamic_ideal' ),
				Parameters::SERVICE_IDEAL_CONSUMER_NAME   => __( 'Service iDEAL Consumer Name', 'pronamic_ideal' ),
				Parameters::SERVICE_IDEAL_CONSUMER_IBAN   => __( 'Service iDEAL Consumer IBAN', 'pronamic_ideal' ),
				Parameters::SERVICE_IDEAL_CONSUMER_BIC    => __( 'Service iDEAL Consumer BIC', 'pronamic_ideal' ),
				Parameters::TRANSACTIONS                  => __( 'Transactions', 'pronamic_ideal' ),
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
