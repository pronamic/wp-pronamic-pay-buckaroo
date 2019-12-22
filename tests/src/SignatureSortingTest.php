<?php
/**
 * Signature sorting test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Buckaroo
 */

namespace Pronamic\WordPress\Pay\Gateways\Buckaroo;

/**
 * Title: Buckaroo signature sorting test.
 * Description:
 * Copyright: 2005-2019 Pronamic
 * Company: Pronamic
 *
 * @link http://pronamic.nl/wp-content/uploads/2013/04/BPE-3.0-Gateway-HTML.1.02.pdf
 * @author Remco Tolsma
 * @version 2.0.4
 */
class SignatureSortingTest extends \WP_UnitTestCase {
	/**
	 * Test signature sorting.
	 */
	public function test_signature_sorting() {
		/**
		 * Sort these parameters alphabetically on the parameter name (brq_amount comes before brq_websitekey).
		 *
		 * Note: sorting must be case insensitive (brq_active comes before BRQ_AMOUNT) but casing in parameter names and values must be preserved.
		 */
		$data = array(
			'brq_websitekey' => '123456',
			'BRQ_AMOUNT'     => '25.00',
			'brq_active'     => 'true',
		);

		$expected = array(
			'brq_active'     => 'true',
			'BRQ_AMOUNT'     => '25.00',
			'brq_websitekey' => '123456',
		);

		// Sort.
		$data = Security::sort( $data );

		// Keys.
		$keys_data     = implode( "\n", array_keys( $data ) );
		$keys_expected = implode( "\n", array_keys( $expected ) );

		// Assert.
		$this->assertEquals( $keys_expected, $keys_data );
	}
}
