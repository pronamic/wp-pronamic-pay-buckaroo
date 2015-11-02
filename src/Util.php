<?php

/**
 * Title: Buckaroo utility class
 * Description:
 * Copyright: Copyright (c) 2005 - 2015
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0.0
 */
class Pronamic_WP_Pay_Gateways_Buckaroo_Util {
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
