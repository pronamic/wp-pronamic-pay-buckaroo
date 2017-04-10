<?php

/**
 * Title: Buckaroo utility class
 * Description:
 * Copyright: Copyright (c) 2005 - 2017
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.2.5
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
	public static function get_invoice_number( $invoice_number, Pronamic_Pay_Payment $payment ) {
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

	/**
	 * Transform flat Buckaroo response into multidimensional array.
	 *
	 * @since 1.2.4
	 * @param array $response
	 * @return array
	 */
	public static function transform_flat_response( $response = array() ) {
		$return = array();

		if ( is_array( $response ) ) {
			foreach ( $response as $flat_key => $value ) {
				unset( $response[ $flat_key ] );

				$is_brq = ( 'BRQ_' === substr( $flat_key, 0, 4 ) );

				// Remove 'BRQ_' from flat key (first part key will be prefixed with 'BRQ_')
				if ( $is_brq ) {
					$flat_key = substr_replace( $flat_key, '', 0, 4 );
				}

				$parts = explode( '_', $flat_key );

				// Prefix first key with BRQ_
				if ( $is_brq && count( $parts ) > 0 ) {
					$parts[0] = sprintf( 'BRQ_%s', $parts[0] );
				}

				$item =& $return;

				// Define key parts as array and set current item
				foreach ( $parts as $key ) {
					if ( ! isset( $item[ $key ] ) ) {
						$item[ $key ] = array();
					}

					$item =& $item[ $key ];
				}

				// Set value of item
				$item = $value;
			}
		}

		return $return;
	}
}
