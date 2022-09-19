<?php

namespace Pronamic\WordPress\Pay\Gateways\Buckaroo;

use PHPUnit\Framework\TestCase;
use Pronamic\WordPress\Pay\Payments\Payment;

class UtilTest extends TestCase {

	/**
	 * Test get invoice number.
	 *
	 * @param int             $payment_id     Payment ID.
	 * @param int|null|string $order_id       Order ID.
	 * @param string          $invoice_number Invoice number text.
	 * @param string          $expected       Expected result.
	 * @return void
	 * @dataProvider invoice_number_provider
	 */
	public function test_get_invoice_number( $payment_id, $order_id, $invoice_number, $expected ) {
		// Setup payment.
		$payment = new Payment();

		$payment->set_id( $payment_id );
		$payment->order_id = $order_id;

		// Assertion.
		$this->assertEquals( $expected, Util::get_invoice_number( $invoice_number, $payment ) );
	}

	/**
	 * Invoice number data provider.
	 *
	 * @return array[]
	 */
	public function invoice_number_provider() {
		return [
			[ 99, 12345, 'invoice', 'invoice99' ],
			[ 99, 12345, '{payment_id}', '99' ],
			[ 99, null, '{order_id}', '' ],
			[ 99, 12345, '{order_id}', '12345' ],
			[ 99, 12345, 'INV{order_id}', 'INV12345' ],
			[ 99, 12345, '{payment_id}-{order_id}', '99-12345' ],
		];
	}

	/**
	 * Test get transaction service.
	 *
	 * @return void
	 * @dataProvider transaction_service_provider
	 */
	public function test_get_transaction_service( $service ) {
		// Build transaction.
		$services = [ (object) [] ];

		if ( null !== $service ) {
			$services[] = (object) [ 'Name' => $service ];
		}

		$transaction = (object) [
			'Services' => $services,
		];

		// Assertion.
		$this->assertEquals( $service, Util::get_transaction_service( $transaction ) );
	}

	/**
	 * Transaction service provider.
	 *
	 * @return array[]
	 */
	public function transaction_service_provider() {
		return [
			[ 'ideal' ],
			[ null ],
		];
	}
}
