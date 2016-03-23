<?php

/**
 * Title: Buckaroo utility class
 * Description:
 * Copyright: Copyright (c) 2005 - 2016
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.2.3
 * @since 1.0.0
 */
class Pronamic_WP_Pay_Gateways_Buckaroo_Util {
	/**
	 * Get invoice number.
	 *
	 * @param string                            $invoice_number
	 * @param Pronamic_Pay_PaymentDataInterface $data
	 * @param Pronamic_Pay_Payment              $payment
	 */
	public static function get_invoice_number( $invoice_number, Pronamic_Pay_PaymentDataInterface $data, Pronamic_Pay_Payment $payment ) {
		// Replacements definition
		$replacements = array(
			'{order_id}'   => $data->get_order_id(),
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
	 * Buckaroo check if the specified string is the specified key
	 *
	 * @param string $string
	 * @param string $value
	 * @return boolean true if match, false otherwise
	 */
	public static function string_equals( $string, $value ) {
		return 0 === strcasecmp( $string, $value );
	}

	/**
	 * Buckaroo check if the key starts with an prefix
	 *
	 * @param string $string
	 * @param string $prefix
	 * @return boolean true if match, false otherwise
	 */
	public static function string_starts_with( $string, $prefix ) {
		$string = substr( $string, 0, strlen( $prefix ) );

		return 0 === strcasecmp( $string, $prefix );
	}

	//////////////////////////////////////////////////

	/**
	 * URL decode array
	 *
	 * @param array $data
	 * @return array
	 */
	public static function urldecode( array $data ) {
		return array_map( 'urldecode', $data );
	}
}
