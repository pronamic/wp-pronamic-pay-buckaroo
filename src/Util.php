<?php

namespace Pronamic\WordPress\Pay\Gateways\Buckaroo;

use Pronamic\WordPress\Pay\Payments\Payment;

/**
 * Title: Buckaroo utility class
 * Description:
 * Copyright: 2005-2022 Pronamic
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 2.0.0
 * @since 1.0.0
 */
class Util {
	/**
	 * Get invoice number.
	 *
	 * @param string $invoice_number
	 * @param Payment $payment
	 *
	 * @return string
	 */
	public static function get_invoice_number( $invoice_number, Payment $payment ) {
		// Replacements definition
		$replacements = array(
			'{order_id}'   => $payment->get_order_id(),
			'{payment_id}' => $payment->get_id(),
		);

		// Find and replace
		$invoice_number = str_replace(
			array_keys( $replacements ),
			array_values( $replacements ),
			$invoice_number,
			$count
		);

		// Make sure there is an dynamic part in the order ID
		if ( 0 === $count ) {
			$invoice_number .= $payment->get_id();
		}

		return $invoice_number;
	}

	/**
	 * Get first service name from transaction.
	 *
	 * @param object $transaction Transaction object.
	 * @return string|null
	 */
	public static function get_transaction_service( $transaction ) {
		if ( \property_exists( $transaction, 'Services' ) ) {
			foreach ( $transaction->Services as $service ) {
				if ( ! \property_exists( $service, 'Name' ) ) {
					continue;
				}

				return $service->Name;
			}
		}

		return null;
	}
}
