<?php

namespace Pronamic\WordPress\Pay\Gateways\Buckaroo;

use Pronamic\WordPress\Pay\Core\Statuses as CoreStatuses;

/**
 * Title: Buckaroo statuses constants tests
 * Description:
 * Copyright: 2005-2019 Pronamic
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 2.0.0
 * @link https://www.mollie.nl/support/documentatie/betaaldiensten/ideal/en/
 */
class StatusesTest extends \WP_UnitTestCase {
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
			array( Statuses::PAYMENT_SUCCESS, CoreStatuses::SUCCESS ),
			// Failure
			array( Statuses::PAYMENT_FAILURE, CoreStatuses::FAILURE ),
			array( Statuses::VALIDATION_FAILURE, CoreStatuses::FAILURE ),
			array( Statuses::TECHNICAL_ERROR, CoreStatuses::FAILURE ),
			array( Statuses::PAYMENT_REJECTED, CoreStatuses::FAILURE ),
			// Open
			array( Statuses::WAITING_FOR_USER_INPUT, CoreStatuses::OPEN ),
			array( Statuses::WAITING_FOR_PROCESSOR, CoreStatuses::OPEN ),
			array( Statuses::WAITING_ON_CONSUMER_ACTION, CoreStatuses::OPEN ),
			array( Statuses::PAYMENT_ON_HOLD, CoreStatuses::OPEN ),
			// Cancelled
			array( Statuses::CANCELLED_BY_CONSUMER, CoreStatuses::CANCELLED ),
			array( Statuses::CANCELLED_BY_MERCHANT, CoreStatuses::CANCELLED ),
			// Other
			array( 'not existing status', null ),
		);
	}
}
