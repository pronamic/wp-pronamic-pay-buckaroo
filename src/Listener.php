<?php

namespace Pronamic\WordPress\Pay\Gateways\Buckaroo;

use Pronamic\WordPress\Pay\Core\Server;
use Pronamic\WordPress\Pay\Plugin;

/**
 * Title: Buckaroo listener
 * Description:
 * Copyright: 2005-2019 Pronamic
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 2.0.2
 * @since 1.0.0
 */
class Listener {
	/**
	 * Listen.
	 */
	public static function listen() {
		if ( ! filter_has_var( INPUT_GET, 'buckaroo_push' ) ) {
			return;
		}

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

		$data = array_change_key_case( $data, CASE_LOWER );

		$payment_id = null;

		if ( isset(
			$data[ Parameters::ADD_PRONAMIC_PAYMENT_ID ],
			$data[ Parameters::STATUS_CODE ]
		) ) {
			$payment_id = $data[ Parameters::ADD_PRONAMIC_PAYMENT_ID ];
		} elseif ( isset(
			$data[ Parameters::INVOICE_NUMBER ],
			$data[ Parameters::STATUS_CODE ]
		) ) {
			// Fallback for payments started with plugin version <= 4.5.5.
			$payment_id = $data[ Parameters::INVOICE_NUMBER ];
		}

		if ( $payment_id ) {
			$payment = get_pronamic_payment( $payment_id );

			if ( null === $payment ) {
				return;
			}

			// Add note.
			$note = sprintf(
				/* translators: %s: Buckaroo */
				__( 'Webhook requested by %s.', 'pronamic_ideal' ),
				__( 'Buckaroo', 'pronamic_ideal' )
			);

			$payment->add_note( $note );

			// Log webhook request.
			do_action( 'pronamic_pay_webhook_log_payment', $payment );

			// Update payment.
			Plugin::update_payment( $payment );
		}
	}
}
