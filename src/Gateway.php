<?php

namespace Pronamic\WordPress\Pay\Gateways\Buckaroo;

use Pronamic\WordPress\Pay\Banks\BankAccountDetails;
use Pronamic\WordPress\Pay\Core\Gateway as Core_Gateway;
use Pronamic\WordPress\Pay\Core\PaymentMethods as Core_PaymentMethods;
use Pronamic\WordPress\Pay\Core\Server;
use Pronamic\WordPress\Pay\Payments\Payment;

/**
 * Title: Buckaroo gateway
 * Description:
 * Copyright: 2005-2021 Pronamic
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 2.0.4
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
		$this->client->set_website_key( $config->get_website_key() );
		$this->client->set_secret_key( $config->get_secret_key() );
		$this->client->set_excluded_services( $config->get_excluded_services() );
		$this->client->set_invoice_number( $config->get_invoice_number() );
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

		try {
			$result = $this->client->get_issuers();

			$groups[] = array(
				'options' => $result,
			);
		} catch ( \Exception $e ) {
			$this->error = new \WP_Error( 'buckaroo', $e->getMessage() );
		}

		return $groups;
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
		/**
		 * Authentication.
		 * 
		 * The HMAC SHA256 is calculated over a concatenated string (as raw data/binary/bytes) of the following values: WebsiteKey, requestHttpMethod, requestUri, requestTimeStamp, nonce, requestContentBase64String. See the next table for more information about these values. Please note: the Base64 hash should be a string of 44 characters. If yours is longer, it is probably in hexadecimal format.
		 *
		 * @link https://dev.buckaroo.nl/Apis/Description/json
		 */
		$website_key         = $this->config->website_key;
		$request_http_method = 'POST';
		$request_uri         = 'testcheckout.buckaroo.nl/json/datarequest/specifications';
		$request_timestamp   = \strval( \time() );
		$nonce               = \wp_generate_password( 32 );
		$request_content     = '{
  "Services": [
    {
      "Name": "idealqr",
	  "Version": 1
	}
  ]
}';

		$data = \implode(
			'',
			array(
				$website_key,
				$request_http_method,
				$request_uri,
				$request_timestamp,
				$nonce,
				\base64_encode( \md5( $request_content, true ) ),
			)
		);

		$authorization = 'hmac ' . $this->config->website_key . ':' . hash_hmac( 'sha256', $data, $this->config->secret_key ) . ':' . $nonce . ':' . $request_timestamp;

$postArray = array(
    "Currency" => "EUR",
    "AmountDebit" => 10.00,
    "Invoice" => "testinvoice 123",
    "Services" => array(
        "ServiceList" => array(
            array(
                "Action" => "Pay",
                "Name" => "ideal",
                "Parameters" => array(
                    array(
                        "Name" => "issuer",
                        "Value" => "ABNANL2A"
                    )
                )
            )
        )
    )
);


$post = json_encode($postArray);

echo $post . '<br><br>';

$md5  = md5($post, true);
$post = base64_encode($md5);

echo '<b>MD5 from json</b> ' . $md5 . '<br><br>';
echo '<b>base64 from MD5</b> ' . $post . '<br><br>';

$websiteKey = $this->config->website_key;
$test = 'testcheckout.buckaroo.nl/json/Transaction';
$uri        = strtolower(urlencode($test));
$nonce      = 'nonce_' . rand(0000000, 9999999);
$time       = time();

$hmac       = $websiteKey . 'POST' . $uri . $time . $nonce . $post;
$s          = hash_hmac('sha256', $hmac, $this->config->secret_key, true);
$hmac       = base64_encode($s);

$authorization = ("hmac " . $this->config->website_key . ':' . $hmac . ':' . $nonce . ':' . $time);
var_dump($this->config );
var_dump($authorization );
		$test = \Pronamic\WordPress\Http\Facades\Http::request(
			'https://' . $test,
			array(
				'method'  => $request_http_method,
				'headers' => array(
					'Authorization' => $authorization,
					'Content-Type'  => 'application/json',
				),
				'body'    => \json_encode($postArray),
			)
		);

		var_dump( $test );
		exit;

		$payment->set_action_url( $this->client->get_payment_server_url() );
	}

	/**
	 * Get output HTML
	 *
	 * @param Payment $payment Payment.
	 *
	 * @return array
	 *
	 * @see     Core_Gateway::get_output_html()
	 * @since   1.1.1
	 * @version 2.0.4
	 */
	public function get_output_fields( Payment $payment ) {
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
				$this->client->set_payment_method( PaymentMethods::transform( (string) $payment_method ) );

				break;
			default:
				if ( '0' !== $payment_method ) {
					// Leap of faith if the WordPress payment method could not transform to a Buckaroo method?
					$this->client->set_payment_method( $payment_method );
				}

				break;
		}

		// Locale.
		$culture = null;

		$customer = $payment->get_customer();

		if ( null !== $customer ) {
			$locale = $customer->get_locale();

			// Buckaroo uses 'nl-NL' instead of 'nl_NL'.
			if ( ! empty( $locale ) ) {
				$culture = str_replace( '_', '-', $locale );
			}
		}

		$this->client->set_payment_id( (string) $payment->get_id() );
		$this->client->set_culture( $culture );
		$this->client->set_currency( $payment->get_total_amount()->get_currency()->get_alphabetic_code() );
		$this->client->set_description( $payment->get_description() );
		$this->client->set_amount( $payment->get_total_amount()->get_value() );
		$this->client->set_invoice_number( Util::get_invoice_number( (string) $this->client->get_invoice_number(), $payment ) );
		$this->client->set_return_url( $payment->get_return_url() );
		$this->client->set_return_cancel_url( $payment->get_return_url() );
		$this->client->set_return_error_url( $payment->get_return_url() );
		$this->client->set_return_reject_url( $payment->get_return_url() );

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

		if ( false === $data ) {
			return;
		}

		$payment->set_transaction_id( (string) $data[ Parameters::PAYMENT ] );
		$payment->set_status( Statuses::transform( (string) $data[ Parameters::STATUS_CODE ] ) );

		// Consumer bank details.
		$consumer_bank_details = $payment->get_consumer_bank_details();

		if ( null === $consumer_bank_details ) {
			$consumer_bank_details = new BankAccountDetails();

			$payment->set_consumer_bank_details( $consumer_bank_details );
		}

		if ( \array_key_exists( Parameters::SERVICE_IDEAL_CONSUMER_NAME, $data ) ) {
			$consumer_bank_details->set_name( (string) $data[ Parameters::SERVICE_IDEAL_CONSUMER_NAME ] );
		}

		if ( \array_key_exists( Parameters::SERVICE_IDEAL_CONSUMER_IBAN, $data ) ) {
			$consumer_bank_details->set_iban( (string) $data[ Parameters::SERVICE_IDEAL_CONSUMER_IBAN ] );
		}

		if ( \array_key_exists( Parameters::SERVICE_IDEAL_CONSUMER_BIC, $data ) ) {
			$consumer_bank_details->set_bic( (string) $data[ Parameters::SERVICE_IDEAL_CONSUMER_BIC ] );
		}

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
			if ( ! isset( $data[ $key ] ) ) {
				continue;
			}

			$note .= sprintf(
				'<dt>%s</dt><dd>%s</dd>',
				esc_html( $label ),
				esc_html( (string) $data[ $key ] )
			);
		}

		$note .= '</dl>';

		$payment->add_note( $note );
	}
}
