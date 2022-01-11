<?php
/**
 * Status test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Buckaroo
 */

namespace Pronamic\WordPress\Pay\Gateways\Buckaroo;

use Pronamic\WordPress\Pay\Payments\PaymentStatus as Core_Statuses;

/**
 * Title: Buckaroo statuses constants tests
 * Description:
 * Copyright: 2005-2022 Pronamic
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 2.0.4
 * @link https://www.mollie.nl/support/documentatie/betaaldiensten/ideal/en/
 */
class StatusesTest extends \WP_UnitTestCase {
	/**
	 * Test transform.
	 *
	 * @param string $buckaroo_status Buckaroo status.
	 * @param string $expected        Expected status.
	 *
	 * @dataProvider transform_provider
	 */
	public function test_transform( $buckaroo_status, $expected ) {
		$status = Statuses::transform( $buckaroo_status );

		$this->assertEquals( $expected, $status );
	}

	/**
	 * Data provider for transform.
	 *
	 * @return array
	 */
	public function transform_provider() {
		return array(
			// Success.
			array( Statuses::PAYMENT_SUCCESS, Core_Statuses::SUCCESS ),
			// Failure.
			array( Statuses::PAYMENT_FAILURE, Core_Statuses::FAILURE ),
			array( Statuses::VALIDATION_FAILURE, Core_Statuses::FAILURE ),
			array( Statuses::TECHNICAL_ERROR, Core_Statuses::FAILURE ),
			array( Statuses::PAYMENT_REJECTED, Core_Statuses::FAILURE ),
			// Open.
			array( Statuses::WAITING_FOR_USER_INPUT, Core_Statuses::OPEN ),
			array( Statuses::WAITING_FOR_PROCESSOR, Core_Statuses::OPEN ),
			array( Statuses::WAITING_ON_CONSUMER_ACTION, Core_Statuses::OPEN ),
			array( Statuses::PAYMENT_ON_HOLD, Core_Statuses::OPEN ),
			// Cancelled.
			array( Statuses::CANCELLED_BY_CONSUMER, Core_Statuses::CANCELLED ),
			array( Statuses::CANCELLED_BY_MERCHANT, Core_Statuses::CANCELLED ),
			// Other.
			array( 'not existing status', null ),
		);
	}
}
