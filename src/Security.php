<?php

namespace Pronamic\WordPress\Pay\Gateways\Buckaroo;

/**
 * Title: Buckaroo security class
 * Description:
 * Copyright: 2005-2019 Pronamic
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 2.0.0
 * @since 1.0.0
 */
class Security {
	/**
	 * Find the signature from an data array
	 *
	 * @param array $data
	 *
	 * @return null or signature value
	 */
	public static function get_signature( $data ) {
		$result = null;

		foreach ( $data as $key => $value ) {
			if ( Util::string_equals( $key, Parameters::SIGNATURE ) ) {
				$result = $value;

				break;
			}
		}

		return $result;
	}

	/**
	 * Filter the data for generating an signature
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public static function filter_data( $data ) {
		$filter = array();

		// List all parameters prefixed with brq_, add_ or cust_, except brq_signature
		foreach ( $data as $key => $value ) {
			if ( ! ( Util::string_starts_with( $key, 'brq_' ) || Util::string_starts_with( $key, 'add_' ) || Util::string_starts_with( $key, 'cust_' ) ) ) {
				continue;
			}

			if ( Util::string_equals( $key, Parameters::SIGNATURE ) ) {
				continue;
			}

			$filter[ $key ] = $value;
		}

		return $filter;
	}

	/**
	 * Sort the specified data array
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public static function sort( $data ) {
		uksort( $data, 'strcasecmp' );

		return $data;
	}

	/**
	 * Create signature
	 *
	 * Please note: When verifying a received signature, first url-decode all the field values.
	 * A signature is always calculated over the non-encoded values (i.e The value “J.+de+Tester” should be decoded to “J. de Tester”).
	 *
	 * @param array $data
	 * @param string $secret_key
	 *
	 * @return string
	 */
	public static function create_signature( $data, $secret_key ) {
		$string = '';

		// 1. List all parameters prefixed with brq_, add_ or cust_, except brq_signature
		$data = self::filter_data( $data );

		// 2. Sort these parameters alphabetically on the parameter name
		$data = self::sort( $data );

		// 3. Concatenate all the parameters
		foreach ( $data as $key => $value ) {
			$string .= $key . '=' . $value;
		}

		// 4. Add the pre-shared secret key at the end of the string
		$string .= $secret_key;

		// 5. Calculate a SHA-1 hash over this string.
		$hash = hash( 'sha1', $string );

		// Return the hash in hexadecimal format
		return $hash;
	}
}
