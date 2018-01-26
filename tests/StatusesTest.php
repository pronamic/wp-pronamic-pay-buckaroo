<?php

use Pronamic\WordPress\Pay\Core\Statuses as Core_Statuses;
use Pronamic\WordPress\Pay\Gateways\Buckaroo\Statuses;

/**
 * Title: Buckaroo statuses constants tests
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0.0
 * @see https://www.mollie.nl/support/documentatie/betaaldiensten/ideal/en/
 */
class Pronamic_WP_Pay_Gateways_Buckaroo_StatusesTest extends WP_UnitTestCase {
	/**
	 * Test transform.
	 *
	 * @dataProvider statusMatrixProvider
	 */
	public function testTransform( $buckaroo_status, $expected ) {
		$status = Statuses::transform( $buckaroo_status );

		$this->assertEquals( $expected, $status );
	}

	public function statusMatrixProvider() {
		return array(
			// Success
			array( Statuses::PAYMENT_SUCCESS, Core_Statuses::SUCCESS ),
			// Failure
			array( Statuses::PAYMENT_FAILURE, Core_Statuses::FAILURE ),
			array( Statuses::VALIDATION_FAILURE, Core_Statuses::FAILURE ),
			array( Statuses::TECHNICAL_ERROR, Core_Statuses::FAILURE ),
			array( Statuses::PAYMENT_REJECTED, Core_Statuses::FAILURE ),
			// Open
			array( Statuses::WAITING_FOR_USER_INPUT, Core_Statuses::OPEN ),
			array( Statuses::WAITING_FOR_PROCESSOR, Core_Statuses::OPEN ),
			array( Statuses::WAITING_ON_CONSUMER_ACTION, Core_Statuses::OPEN ),
			array( Statuses::PAYMENT_ON_HOLD, Core_Statuses::OPEN ),
			// Cancelled
			array( Statuses::CANCELLED_BY_CONSUMER, Core_Statuses::CANCELLED ),
			array( Statuses::CANCELLED_BY_MERCHANT, Core_Statuses::CANCELLED ),
			// Other
			array( 'not existing status', null ),
		);
	}
}
