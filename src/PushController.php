<?php
/**
 * Push Controller
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2021 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\DigiWallet
 */

namespace Pronamic\WordPress\Pay\Gateways\Buckaroo;

use Pronamic\WordPress\Pay\Plugin;

/**
 * Push Controller
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class PushController {
	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function setup() {
		\add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );

		\add_action( 'wp_loaded', array( $this, 'wp_loaded' ) );
	}

	/**
	 * REST API init.
	 *
	 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
	 * @link https://developer.wordpress.org/reference/hooks/rest_api_init/
	 * @return void
	 */
	public function rest_api_init() {
		\register_rest_route(
			Integration::REST_ROUTE_NAMESPACE,
			'/push',
			array(
				'methods'             => array(
					'GET',
					'POST',
				),
				'callback'            => array( $this, 'rest_api_buckaroo_push' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * REST API Buckaroo push handler.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return object
	 * @throws \Exception Throws exception when something unexpected happens ;-).
	 */
	public function rest_api_buckaroo_push( \WP_REST_Request $request ) {
		if ( $request->is_json_content_type() ) {
			return $this->handle_json_push( $request );
		}

		$content_type = $request->get_content_type();

		if ( null !== $content_type && 'application/x-www-form-urlencoded' === $content_type['value'] ) {
			return $this->handle_http_post_push( $request );
		}

		return new \WP_Error(
			'pronamic_pay_buckaroo_push_unknown_content_type',
			\sprintf(
				'Unknown Buckaroo push request content type: %s.',
				$request->get_header( 'Content-Type' )
			),
			array( 'status' => 500 )
		);
	}

	/**
	 * Handle JSON push.
	 *
	 * @link https://dev.buckaroo.nl/PaymentMethods/Description/ideal
	 * @param \WP_REST_Request $request Request.
	 */
	private function handle_json_push( \WP_REST_Request $request ) {
		$json = $request->get_body();

		$data = \json_decode( $json );

		/**
		 * Process Refunds.
		 *
		 * @link https://support.buckaroo.nl/categorie%C3%ABn/integratie/transactietypes-overzicht
		 * @link https://dev.buckaroo.nl/PaymentMethods/Description/ideal
		 */
		foreach ( $data->Transaction->RelatedTransactions as $related_transaction ) {
			if ( 'refund' === $related_transaction->RelationType ) {
				$key = $related_transaction->RelatedTransactionKey;
			}
		}

		$transaction_key = $data->Transaction->Key;

		return $this->handle_transcation_key( $transaction_key );
	}

	/**
	 * Handle HTTP POST push.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return object
	 */
	public function handle_http_post_push( \WP_REST_Request $request ) {
		$parameters = $request->get_params();

		$parameters = \array_change_key_case( $parameters, \CASE_LOWER );

		if ( ! \array_key_exists( 'brq_transactions', $parameters ) ) {
			return new \WP_Error(
				'rest_buckaroo_no_transactions_parameter',
				\__( 'The BRQ_TRANSACTIONS parameter is missing from the Buckaroo push request.', 'pronamic_ideal ' )
			);
		}

		/**
		 * The unique key for the transaction
		 * Important: the payment response also contains a parameter named
		 * brq_transactions, but may contain multiple transaction keys.
		 * The same field in the push response will always contain one single
		 * transaction key. For consistence, both fields have the same name.
		 *
		 * @link https://www.pronamic.nl/wp-content/uploads/2013/04/BPE-3.0-Gateway-HTML.1.02.pdf
		 */
		$transaction_key = $parameters['brq_transactions'];

		return $this->handle_transcation_key( $transaction_key );
	}

	/**
	 * Handle JSON request for specified transaction key.
	 *
	 * @param string $transaction_key Transaction key.
	 * @return object
	 */
	private function handle_transcation_key( $transaction_key ) {
		$payment = \get_pronamic_payment_by_meta( '_pronamic_payment_buckaroo_transaction_key', $transaction_key );

		if ( null === $payment ) {
			return new \WP_Error(
				'rest_buckaroo_unknown_transaction',
				\sprintf(
					/* translators: %s: Buckaroo transaction key. */
					\__( 'Unable to found payment for transaction key: %s.', 'pronamic_ideal ' ),
					$transaction_key
				),
				array(
					'status' => 400,
					'data'   => $data,
				)
			);
		}

		// Add note.
		$note = \__( 'Push URL requested by Buckaroo.', 'pronamic_ideal' );

		$payment->add_note( $note );

		// Log webhook request.
		\do_action( 'pronamic_pay_webhook_log_payment', $payment );

		// Update payment.
		Plugin::update_payment( $payment, false );

		return \rest_ensure_response(
			array(
				'success'         => true,
				'transaction_key' => $transaction_key,
			)
		);
	}

	/**
	 * WordPress loaded, check for deprecated webhook call.
	 *
	 * @link https://github.com/WordPress/WordPress/blob/5.3/wp-includes/rest-api.php#L277-L309
	 * @return void
	 */
	public function wp_loaded() {
		if ( ! filter_has_var( INPUT_GET, 'buckaroo_push' ) ) {
			return;
		}

		\rest_get_server()->serve_request( '/pronamic-pay/buckaroo/v1/push' );

		exit;
	}
}
