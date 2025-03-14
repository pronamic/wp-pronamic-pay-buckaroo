<?php

namespace Pronamic\WordPress\Pay\Gateways\Buckaroo;

use Pronamic\WordPress\Money\Money;
use Pronamic\WordPress\Pay\Banks\BankAccountDetails;
use Pronamic\WordPress\Pay\Core\Gateway as Core_Gateway;
use Pronamic\WordPress\Pay\Core\PaymentMethod;
use Pronamic\WordPress\Pay\Core\PaymentMethods as Core_PaymentMethods;
use Pronamic\WordPress\Pay\Core\PaymentMethodsCollection;
use Pronamic\WordPress\Pay\Payments\Payment;
use Pronamic\WordPress\Pay\Payments\PaymentStatus;
use Pronamic\WordPress\Pay\Refunds\Refund;
use WP_Error;

/**
 * Title: Buckaroo gateway
 * Description:
 * Copyright: 2005-2024 Pronamic
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 2.0.4
 * @since 1.0.0
 */
class Gateway extends Core_Gateway {
	/**
	 * Config
	 *
	 * @var Config
	 */
	protected $config;

	/**
	 * Constructs and initializes a Buckaroo gateway
	 *
	 * @param Config $config Config.
	 */
	public function __construct( Config $config ) {
		parent::__construct();

		$this->config = $config;

		$this->set_method( self::METHOD_HTTP_REDIRECT );

		// Supported features.
		$this->supports = [
			'payment_status_request',
			'refunds',
			'webhook',
			'webhook_log',
			'webhook_no_config',
		];

		// Methods.
		$this->register_payment_method( new PaymentMethod( Core_PaymentMethods::AMERICAN_EXPRESS ) );
		$this->register_payment_method( new PaymentMethod( Core_PaymentMethods::BANK_TRANSFER ) );
		$this->register_payment_method( new PaymentMethod( Core_PaymentMethods::BANCONTACT ) );
		$this->register_payment_method( new PaymentMethod( Core_PaymentMethods::CREDIT_CARD ) );
		$this->register_payment_method( new PaymentMethod( Core_PaymentMethods::GIROPAY ) );
		$this->register_payment_method( new PaymentMethod( Core_PaymentMethods::IDEAL ) );
		$this->register_payment_method( new PaymentMethod( Core_PaymentMethods::MAESTRO ) );
		$this->register_payment_method( new PaymentMethod( Core_PaymentMethods::MASTERCARD ) );
		$this->register_payment_method( new PaymentMethod( Core_PaymentMethods::PAYPAL ) );
		$this->register_payment_method( new PaymentMethod( Core_PaymentMethods::SOFORT ) );
		$this->register_payment_method( new PaymentMethod( Core_PaymentMethods::V_PAY ) );
		$this->register_payment_method( new PaymentMethod( Core_PaymentMethods::VISA ) );
	}

	/**
	 * Get payment methods.
	 *
	 * @param array<string, mixed> $args Query arguments.
	 * @return PaymentMethodsCollection
	 */
	public function get_payment_methods( array $args = [] ): PaymentMethodsCollection {
		try {
			$this->maybe_enrich_payment_methods();
		} catch ( \Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			// No problem.
		}

		return parent::get_payment_methods( $args );
	}

	/**
	 * Get credit card payment methods.
	 *
	 * @return string[]
	 */
	private function get_credit_card_payment_methods() {
		return [
			Core_PaymentMethods::AMERICAN_EXPRESS,
			Core_PaymentMethods::MAESTRO,
			Core_PaymentMethods::MASTERCARD,
			Core_PaymentMethods::VISA,
		];
	}

	/**
	 * Maybe enrich payment methods.
	 *
	 * @return void
	 */
	private function maybe_enrich_payment_methods() {
		$cache_key = 'pronamic_pay_buckaroo_transaction_specifications_' . \md5( (string) \wp_json_encode( $this->config ) );

		$buckaroo_transaction_specifications = \get_transient( $cache_key );

		if ( false === $buckaroo_transaction_specifications ) {
			$buckaroo_transaction_specifications = $this->request_transaction_specifications();

			\set_transient( $cache_key, $buckaroo_transaction_specifications, \DAY_IN_SECONDS );
		}

		foreach ( $this->payment_methods as $payment_method ) {
			$payment_method->set_status( 'inactive' );
		}

		$services = is_object( $buckaroo_transaction_specifications ) && property_exists( $buckaroo_transaction_specifications, 'Services' ) ? $buckaroo_transaction_specifications->Services : null;

		if ( null !== $services ) {
			foreach ( $services as $service ) {
				$payment_method_id = PaymentMethods::from_buckaroo_to_pronamic( $service->Name );

				if ( null === $payment_method_id ) {
					continue;
				}

				$payment_method = $this->get_payment_method( $payment_method_id );

				if ( null === $payment_method ) {
					continue;
				}

				$payment_method->set_status( 'active' );
			}
		}

		/**
		 * Credit card.
		 */
		$credit_card_payment_methods = parent::get_payment_methods(
			[
				'id'     => $this->get_credit_card_payment_methods(),
				'status' => [ '', 'active' ],
			]
		);

		if ( count( $credit_card_payment_methods ) > 0 ) {
			$payment_method = $this->get_payment_method( Core_PaymentMethods::CREDIT_CARD );

			if ( null !== $payment_method ) {
				$payment_method->set_status( 'active' );
			}
		}
	}

	/**
	 * Request transaction specifications.
	 *
	 * @link https://github.com/search?q=org%3Abuckaroo-it+specifications&type=code
	 * @link https://dev.buckaroo.nl/Playground
	 * @return object
	 */
	private function request_transaction_specifications() {
		$object = $this->request( 'POST', 'Transaction/Specifications', (object) [] );

		return $object;
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
		 * Currency.
		 */
		$currency_code = $payment->get_total_amount()->get_currency()->get_alphabetic_code();

		/**
		 * Push URL.
		 */
		$push_url = \rest_url( Integration::REST_ROUTE_NAMESPACE . '/push' );

		/**
		 * Filters the Buckaroo push URL.
		 *
		 * If you want to debug the Buckaroo report URL you can use this filter
		 * to override the push URL. You could for example use a service like
		 * https://webhook.site/ to inspect the push requests from Buckaroo.
		 *
		 * @param string $push_url Buckaroo push URL.
		 */
		$push_url = \apply_filters( 'pronamic_pay_buckaroo_push_url', $push_url );

		/**
		 * JSON Transaction.
		 *
		 * @link https://testcheckout.buckaroo.nl/json/Docs/Api/POST-json-Transaction
		 */
		$data = (object) [
			'Currency'                  => $currency_code,
			/**
			 * The debit amount for the request. This is in decimal format,
			 * with a point as the decimal separator. For example, if the
			 * currency is specified as EUR, sending “1” will mean that 1 euro
			 * will be paid. “1.00” is also 1 euro. “0.01” means 1 cent.
			 * Please note, a transaction must have either a debit amount or a
			 * credit amount and it cannot have both.
			 *
			 * @link https://dev.buckaroo.nl/Apis
			 */
			'AmountDebit'               => $payment->get_total_amount()->number_format( null, '.', '' ),
			'Description'               => $payment->get_description(),
			'Invoice'                   => Util::get_invoice_number( (string) $this->config->get_invoice_number(), $payment ),
			'ReturnURL'                 => $payment->get_return_url(),
			'ReturnURLCancel'           => \add_query_arg(
				'buckaroo_return_url_cancel',
				true,
				$payment->get_return_url()
			),
			'ReturnURLError'            => \add_query_arg(
				'buckaroo_return_url_error',
				true,
				$payment->get_return_url()
			),
			'ReturnURLReject'           => \add_query_arg(
				'buckaroo_return_url_reject',
				true,
				$payment->get_return_url()
			),
			/**
			 * Push URL.
			 *
			 * When provided, this push URL overrides all the push URLs as configured in the payment plaza under websites for the associated website key
			 *
			 * @link https://dev.buckaroo.nl/Apis
			 */
			'PushURL'                   => $push_url,
			/**
			 * Push URL Failure.
			 *
			 * When provided, this push URL overrides the push URL for failed transactions as configured in the payment plaza under websites for the associated website key.
			 *
			 * @link https://dev.buckaroo.nl/Apis
			 */
			'PushURLFailure'            => $push_url,
			/**
			 * Services.
			 *
			 * Specifies which service (can be a payment method and/or additional service) is being called upon in the request.
			 *
			 * @link https://dev.buckaroo.nl/Apis
			 */
			'Services'                  => (object) [
				'ServiceList' => [],
			],
			/**
			 * Continue On Incomplete.
			 *
			 * Specifies if a redirecturl to a payment form will be returned to
			 * which a customer should be sent if no paymentmethod is selected
			 * or if any required parameter which the customer may provide is
			 * missing or incorrect. Possible Values:
			 *
			 * · No: This is the default. The request will fail if not all the
			 * needed information is provided.
			 *
			 * · RedirectToHTML: A redirect to the HTML gateway is provided if
			 * a recoverable problems are detected in the request. The customer
			 * can then provide the needed information there.
			 *
			 * @link https://dev.buckaroo.nl/Apis
			 * @link https://testcheckout.buckaroo.nl/json/Docs/Api/POST-json-Transaction
			 * @link https://testcheckout.buckaroo.nl/json/Docs/ResourceModel?modelName=ContinueOnIncomplete
			 */
			'ContinueOnIncomplete'      => 'RedirectToHTML',
			/**
			 * Services Excluded For Client.
			 *
			 * If no primary service is provided and ContinueOnIncomplete is
			 * set, this list of comma separated servicescodes can be used to
			 * limit the number of services from which the customer may choose
			 * once he is redirected to the payment form. Services which are
			 * entered in this field are not selectable.
			 * This field is optional.
			 *
			 * @link https://dev.buckaroo.nl/Apis
			 * @link https://testcheckout.buckaroo.nl/json/Docs/Api/POST-json-Transaction
			 */
			'ServicesExcludedForClient' => $this->config->get_excluded_services(),
			/**
			 * Custom parameters.
			 *
			 * @link https://testcheckout.buckaroo.nl/json/Docs/Api/POST-json-Transaction
			 */
			'CustomParameters'          => [
				(object) [
					'Name'  => 'pronamic_payment_id',
					'Value' => $payment->get_id(),
				],
			],
		];

		/**
		 * Client IP.
		 *
		 * In this field the IP address of the customer (or employee) for which
		 * the action is being performed can be passed. Please note, If this
		 * field is not sent to our gateway, your server IP address will be
		 * used as the clientIP. This may result in unwanted behaviour for
		 * anti-fraud checks. Also, certain payment methods perform checks on
		 * the IP address, if an IP address is overused, the request could be
		 * blocked. This field is sent in the following format, where
		 * type 0 = IPv4 and type 1 = IPv6:
		 * "ClientIP": { "Type": 0, "Address": "0.0.0.0" },
		 *
		 * @link https://testcheckout.buckaroo.nl/json/Docs/Api/POST-json-Transaction
		 * @link https://stackoverflow.com/questions/1448871/how-to-know-which-version-of-the-internet-protocol-ip-a-client-is-using-when-c/1448901
		 */
		$customer = $payment->get_customer();

		if ( null !== $customer ) {
			$ip_address = $customer->get_ip_address();

			if ( null !== $ip_address ) {
				$data->ClientIP = (object) [
					'Type'    => false === \strpos( $ip_address, ':' ) ? 0 : 1,
					'Address' => $ip_address,
				];
			}
		}

		/**
		 * Payment method.
		 *
		 * @link https://testcheckout.buckaroo.nl/json/Docs/Api/POST-json-Transaction
		 * @link https://testcheckout.buckaroo.nl/json/Docs/ResourceModel?modelName=ServicesRequest
		 * @link https://testcheckout.buckaroo.nl/json/Docs/ResourceModel?modelName=ServiceRequest
		 */
		switch ( $payment->get_payment_method() ) {
			/**
			 * Payment method American Express.
			 *
			 * @link
			 */
			case Core_PaymentMethods::AMERICAN_EXPRESS:
				$data->Services->ServiceList[] = (object) [
					'Action' => 'Pay',
					'Name'   => PaymentMethods::AMERICAN_EXPRESS,
				];

				break;
			/**
			 * Payment method creditcard.
			 *
			 * @link https://dev.buckaroo.nl/PaymentMethods/Description/creditcards#pay
			 */
			case Core_PaymentMethods::CREDIT_CARD:
				$payment_methods = $this->get_payment_methods(
					[
						'id'     => $this->get_credit_card_payment_methods(),
						'status' => [ '', 'active' ],
					]
				);

				foreach ( $payment_methods as $payment_method ) {
					$data->Services->ServiceList[] = (object) [
						'Action' => 'Pay',
						'Name'   => PaymentMethods::transform( $payment_method->get_id() ),
					];
				}

				break;
			/**
			 * Payment method iDEAL.
			 *
			 * @link https://dev.buckaroo.nl/PaymentMethods/Description/ideal#pay
			 */
			case Core_PaymentMethods::IDEAL:
				$data->Services->ServiceList[] = (object) [
					'Action' => 'Pay',
					'Name'   => 'ideal',
				];

				break;
			/**
			 * Payment method transfer.
			 *
			 * @link https://dev.buckaroo.nl/PaymentMethods/Description/transfer#pay
			 */
			case Core_PaymentMethods::BANK_TRANSFER:
				$data->Services->ServiceList[] = (object) [
					'Action' => 'Pay',
					'Name'   => 'transfer',
				];

				break;
			/**
			 * Payment method Bancontact.
			 *
			 * @link https://dev.buckaroo.nl/PaymentMethods/Description/bancontact#pay
			 */
			case Core_PaymentMethods::BANCONTACT:
			case Core_PaymentMethods::MISTER_CASH:
				$data->Services->ServiceList[] = (object) [
					'Action' => 'Pay',
					'Name'   => 'bancontactmrcash',
				];

				break;
			/**
			 * Payment method Maestro.
			 *
			 * @link
			 */
			case Core_PaymentMethods::MAESTRO:
				$data->Services->ServiceList[] = (object) [
					'Action' => 'Pay',
					'Name'   => PaymentMethods::MAESTRO,
				];

				break;
			/**
			 * Payment method Mastercard.
			 *
			 * @link
			 */
			case Core_PaymentMethods::MASTERCARD:
				$data->Services->ServiceList[] = (object) [
					'Action' => 'Pay',
					'Name'   => PaymentMethods::MASTERCARD,
				];

				break;
			/**
			 * Payment method Giropay.
			 *
			 * @link https://dev.buckaroo.nl/PaymentMethods/Description/giropay#pay
			 */
			case Core_PaymentMethods::GIROPAY:
				$data->Services->ServiceList[] = (object) [
					'Action' => 'Pay',
					'Name'   => 'giropay',
				];

				break;
			/**
			 * Payment method PayPal.
			 *
			 * @link https://dev.buckaroo.nl/PaymentMethods/Description/paypal#pay
			 */
			case Core_PaymentMethods::PAYPAL:
				$data->Services->ServiceList[] = (object) [
					'Action' => 'Pay',
					'Name'   => 'paypal',
				];

				break;
			/**
			 * Payment method Sofort.
			 *
			 * @link https://dev.buckaroo.nl/PaymentMethods/Description/sofort#pay
			 */
			case Core_PaymentMethods::SOFORT:
				$data->Services->ServiceList[] = (object) [
					'Action' => 'Pay',
					'Name'   => 'sofortueberweisung',
				];

				break;
			/**
			 * Payment method V PAY.
			 *
			 * @link https://dev.buckaroo.nl/PaymentMethods/Description/creditcards#top
			 */
			case Core_PaymentMethods::V_PAY:
				$data->Services->ServiceList[] = (object) [
					'Action' => 'Pay',
					'Name'   => PaymentMethods::V_PAY,
				];

				break;
			/**
			 * Payment method Visa.
			 *
			 * @link https://dev.buckaroo.nl/PaymentMethods/Description/creditcards#top
			 */
			case Core_PaymentMethods::VISA:
				$data->Services->ServiceList[] = (object) [
					'Action' => 'Pay',
					'Name'   => PaymentMethods::VISA,
				];

				break;
		}

		/**
		 * Request.
		 */
		$object = $this->request( 'POST', 'Transaction', $data );

		/**
		 * Buckaroo keys.
		 *
		 * @link https://testcheckout.buckaroo.nl/json/Docs/ResourceModel?modelName=TransactionResponse
		 */
		if ( \property_exists( $object, 'Key' ) ) {
			$payment->set_transaction_id( $object->Key );
		}

		if ( \property_exists( $object, 'PaymentKey' ) ) {
			$payment->set_meta( 'buckaroo_transaction_payment_key', $object->PaymentKey );
		}

		/**
		 * Request Errors.
		 *
		 * @link https://testcheckout.buckaroo.nl/json/Docs/Api/POST-json-Transaction
		 */
		if ( \property_exists( $object, 'RequestErrors' ) && null !== $object->RequestErrors ) {
			$exception = null;

			foreach ( $object->RequestErrors as $errors ) {
				foreach ( $errors as $error ) {
					// Add exception.
					$exception = new \Exception( $error->ErrorMessage, 0, $exception );
				}
			}

			if ( null !== $exception ) {
				throw $exception;
			}
		}

		/**
		 * Required Action.
		 */
		if (
			\property_exists( $object, 'RequiredAction' )
				&&
			null !== $object->RequiredAction
		) {
			if ( 'Redirect' !== $object->RequiredAction->Name ) {
				throw new \Exception(
					\sprintf(
						'Unsupported Buckaroo action: %s',
						$object->RequiredAction->Name
					)
				);
			}

			// Set action URL.
			if ( \property_exists( $object->RequiredAction, 'RedirectURL' ) ) {
				$payment->set_action_url( $object->RequiredAction->RedirectURL );
			}
		}

		// Failure.
		if ( \property_exists( $object, 'Status' ) && \property_exists( $object->Status, 'Code' ) ) {
			$status = Statuses::transform( (string) $object->Status->Code->Code );

			if ( PaymentStatus::FAILURE === $status ) {
				throw new \Exception(
					\sprintf(
						/* translators: 1: payment provider name, 2: status message, 3: status sub message*/
						__( 'Unable to create payment at gateway: %1$s%2$s', 'pronamic_ideal' ),
						$object->Status->Code->Description,
						\property_exists( $object->Status, 'SubCode' ) ? ' – ' . $object->Status->SubCode->Description : ''
					)
				);
			}
		}
	}

	/**
	 * JSON API Request.
	 *
	 * @param string      $method   HTTP request method.
	 * @param string      $endpoint JSON API endpoint.
	 * @param object|null $data     Data.
	 * @return object
	 */
	public function request( $method, $endpoint, $data = null ) {
		$host = $this->config->get_host();

		/**
		 * Authentication.
		 *
		 * The HMAC SHA256 is calculated over a concatenated string (as raw data/binary/bytes) of the following values: WebsiteKey, requestHttpMethod, requestUri, requestTimeStamp, nonce, requestContentBase64String. See the next table for more information about these values. Please note: the Base64 hash should be a string of 44 characters. If yours is longer, it is probably in hexadecimal format.
		 *
		 * @link https://dev.buckaroo.nl/Apis/Description/json
		 * @link https://testcheckout.buckaroo.nl/json/Docs/Authentication
		 */
		$website_key         = $this->config->website_key;
		$request_http_method = $method;
		$request_uri         = $host . '/json/' . $endpoint;
		$request_timestamp   = \strval( \time() );
		$nonce               = \wp_generate_password( 32 );
		$request_content     = null === $data ? '' : \wp_json_encode( $data );

		$values = \implode(
			'',
			[
				$website_key,
				$request_http_method,
				\strtolower( \rawurlencode( $request_uri ) ),
				$request_timestamp,
				$nonce,
				// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
				null === $data ? '' : \base64_encode( \md5( (string) $request_content, true ) ),
			]
		);

		$hash = \hash_hmac( 'sha256', $values, (string) $this->config->secret_key, true );

		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$hmac = \base64_encode( $hash );

		$authorization = \sprintf(
			'hmac %s:%s:%s:%s',
			$this->config->website_key,
			$hmac,
			$nonce,
			$request_timestamp
		);

		$response = \Pronamic\WordPress\Http\Facades\Http::request(
			'https://' . $request_uri,
			[
				'method'  => $request_http_method,
				'headers' => [
					'Authorization' => $authorization,
					'Content-Type'  => 'application/json',
					'Software'      => $this->get_software_header(),
				],
				'body'    => $request_content,
			]
		);

		try {
			$object = $response->json();
		} catch ( \Exception $e ) {
			// JSON error.
			$json_error = \json_last_error();

			// Check authorization error.
			if ( \JSON_ERROR_NONE !== $json_error && 400 === $response->status() ) {
				throw new \Exception( $response->body() );
			}

			// Re-throw original response exception.
			throw $e;
		}

		/**
		 * OK.
		 */
		return (object) $object;
	}

	/**
	 * Get software header.
	 *
	 * @link https://docs.buckaroo.io/docs/authentication
	 * @link https://github.com/pronamic/wp-pronamic-pay-buckaroo/issues/9
	 * @return string
	 */
	private function get_software_header() {
		return (string) \wp_json_encode(
			[
				'PlatformName'    => 'WordPress',
				'PlatformVersion' => \get_bloginfo( 'version' ),
				'ModuleSupplier'  => 'Pronamic',
				'ModuleName'      => 'PronamicPay',
				'ModuleVersion'   => \pronamic_pay_plugin()->get_version(),
			]
		);
	}

	/**
	 * Update status of the specified payment
	 *
	 * @link https://testcheckout.buckaroo.nl/json/Docs/Api/GET-json-Transaction-Status-transactionKey
	 * @param Payment $payment Payment.
	 */
	public function update_status( Payment $payment ) {
		$transaction_key = $payment->get_transaction_id();

		if ( empty( $transaction_key ) ) {
			return;
		}

		$result = $this->request( 'GET', 'Transaction/Status/' . $transaction_key );

		if (
			\property_exists( $result, 'Status' )
				&&
			\property_exists( $result->Status, 'Code' )
				&&
			\property_exists( $result->Status->Code, 'Code' )
		) {
			$payment->set_status( Statuses::transform( \strval( $result->Status->Code->Code ) ) );
		}

		/**
		 * Consumer bank details.
		 */
		$consumer_bank_details = $payment->get_consumer_bank_details();

		if ( null === $consumer_bank_details ) {
			$consumer_bank_details = new BankAccountDetails();

			$payment->set_consumer_bank_details( $consumer_bank_details );
		}

		/**
		 * Services.
		 */
		if ( \property_exists( $result, 'Services' ) ) {
			$services = $result->Services;

			if ( null !== $services ) {
				foreach ( $services as $service ) {
					foreach ( $service->Parameters as $parameter ) {
						if ( 'consumerName' === $parameter->Name ) {
							$consumer_bank_details->set_name( $parameter->Value );
						}

						if ( \in_array(
							$parameter->Name,
							[
								/**
								 * Payment method iDEAL.
								 *
								 * @link https://dev.buckaroo.nl/PaymentMethods/Description/ideal
								 */
								'consumerIBAN',
								/**
								 * Payment method Sofort.
								 *
								 * @link https://dev.buckaroo.nl/PaymentMethods/Description/sofort
								 */
								'CustomerIBAN',
							],
							true
						) ) {
							$consumer_bank_details->set_iban( $parameter->Value );
						}

						if ( \in_array(
							$parameter->Name,
							[
								/**
								 * Payment method iDEAL.
								 *
								 * @link https://dev.buckaroo.nl/PaymentMethods/Description/ideal
								 */
								'consumerName',
								/**
								 * Payment method Sofort.
								 *
								 * @link https://dev.buckaroo.nl/PaymentMethods/Description/sofort
								 */
								'CustomerBIC',
							],
							true
						) ) {
							$consumer_bank_details->set_bic( $parameter->Value );
						}
					}
				}
			}
		}

		/**
		 * Refunds.
		 *
		 * @link https://testcheckout.buckaroo.nl/json/Docs/Api/GET-json-Transaction-RefundInfo-transactionKey
		 */
		$result = $this->request( 'GET', 'Transaction/RefundInfo/' . $transaction_key );

		if (
			\property_exists( $result, 'RefundedAmount' )
				&&
			\property_exists( $result, 'RefundCurrency' )
				&&
			! empty( $result->RefundedAmount )
		) {
			$refunded_amount = new Money( $result->RefundedAmount, $result->RefundCurrency );

			$payment->set_refunded_amount( $refunded_amount );
		}
	}

	/**
	 * Create refund.
	 *
	 * @param Refund $refund Refund.
	 * @return void
	 */
	public function create_refund( Refund $refund ) {
		$payment = $refund->get_payment();
		$amount  = $refund->get_amount();

		$transaction_id = $payment->get_transaction_id();

		$original_transaction = $this->request( 'GET', 'Transaction/Status/' . $transaction_id );

		if ( ! \is_object( $original_transaction ) ) {
			throw new \Exception(
				sprintf(
					/* translators: %s: transaction key */
					__( 'Unable to create refund for transaction with transaction key: %s', 'pronamic_ideal' ),
					$transaction_id
				)
			);
		}

		$service_name = Util::get_transaction_service( $original_transaction );

		if ( null === $service_name ) {
			throw new \Exception(
				sprintf(
					/* translators: %s: transaction key */
					__( 'Unable to create refund for transaction without service name. Transaction key: %s', 'pronamic_ideal' ),
					$transaction_id
				)
			);
		}

		// Invoice.
		$invoice = Util::get_invoice_number( (string) $this->config->get_invoice_number(), $payment );

		// Refund request.
		$data = (object) [
			'Channel'                => 'Web',
			'Currency'               => $amount->get_currency()->get_alphabetic_code(),
			/**
			 * The credit amount for the request. This is in decimal format,
			 * with a point as the decimal separator. For example, if the
			 * currency is specified as EUR, sending “1” will mean that 1 euro
			 * will be paid. “1.00” is also 1 euro. “0.01” means 1 cent.
			 * Please note, a transaction must have either a debit amount or a
			 * credit amount and it cannot have both.
			 *
			 * @link https://dev.buckaroo.nl/Apis
			 */
			'AmountCredit'           => $amount->number_format( null, '.', '' ),
			'Invoice'                => $invoice,
			'OriginalTransactionKey' => $transaction_id,
			'Services'               => [
				'ServiceList' => [
					[
						'Name'   => $service_name,
						'Action' => 'Refund',
					],
				],
			],
		];

		$result = $this->request( 'POST', 'Transaction', $data );

		// Check refund object.
		if ( ! \is_object( $result ) ) {
			throw new \Exception( 'Unexpceted response from Buckaroo.' );
		}

		// Check refund status.
		if ( \property_exists( $result, 'Status' ) && \property_exists( $result->Status, 'Code' ) ) {
			$status = Statuses::transform( (string) $result->Status->Code->Code );

			if ( PaymentStatus::SUCCESS !== $status ) {
				throw new \Exception(
					\sprintf(
						/* translators: 1: payment provider name, 2: status message, 3: status sub message*/
						__( 'Unable to create refund at %1$s gateway: %2$s%3$s', 'pronamic_ideal' ),
						__( 'Buckaroo', 'pronamic_ideal' ),
						$result->Status->Code->Description,
						\property_exists( $result->Status, 'SubCode' ) ? ' – ' . $result->Status->SubCode->Description : ''
					)
				);
			}
		}

		if ( \property_exists( $result, 'Key' ) ) {
			$refund->psp_id = $result->Key;
		}

		// Update payment refunded amount.
		$result = $this->request( 'GET', 'Transaction/RefundInfo/' . $transaction_id );

		if (
			\property_exists( $result, 'RefundedAmount' )
				&&
			\property_exists( $result, 'RefundCurrency' )
				&&
			! empty( $result->RefundedAmount )
		) {
			$refunded_amount = new Money( $result->RefundedAmount, $result->RefundCurrency );

			$payment->set_refunded_amount( $refunded_amount );
		}
	}
}
