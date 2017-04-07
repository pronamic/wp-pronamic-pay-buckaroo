<?php

/**
 * Title: Buckaroo statuses constants
 * Description:
 * Copyright: Copyright (c) 2005 - 2017
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.2.0
 * @since 1.0.0
 */
class Pronamic_WP_Pay_Gateways_Buckaroo_Statuses {
	/**
	 * Payment success
	 *
	 * @var string
	 */
	const PAYMENT_SUCCESS = '190';

	/**
	 * Payment failure
	 *
	 * @var string
	 */
	const PAYMENT_FAILURE = '490';

	/**
	 * Validation error
	 *
	 * @var string
	 */
	const VALIDATION_FAILURE = '491';

	/**
	 * Technical error
	 *
	 * @var string
	 */
	const TECHNICAL_ERROR = '492';

	/**
	 * Payment rejected
	 *
	 * @var string
	 */
	const PAYMENT_REJECTED = '690';

	/**
	 * Waiting for user input
	 *
	 * @var string
	 */
	const WAITING_FOR_USER_INPUT = '790';

	/**
	 * Waiting for processor
	 *
	 * @var string
	 */
	const WAITING_FOR_PROCESSOR = '791';

	/**
	 * Waiting on consumer action (e.g.: initiate money transfer)
	 *
	 * @var string
	 */
	const WAITING_ON_CONSUMER_ACTION = '792';

	/**
	 * Payment on hold (e.g. waiting for sufficient balance)
	 *
	 * @var string
	 */
	const PAYMENT_ON_HOLD = '793';

	/**
	 * Cancelled by consumer
	 *
	 * @var string
	 */
	const CANCELLED_BY_CONSUMER = '890';

	/**
	 * Cancelled by merchant
	 *
	 * @var string
	 */
	const CANCELLED_BY_MERCHANT = '891';

	/////////////////////////////////////////////////

	/**
	 * Transform an Buckaroo state to an more global status
	 *
	 * @param string $status
	 */
	public static function transform( $status_code ) {
		switch ( $status_code ) {
			case self::PAYMENT_SUCCESS :
				return Pronamic_WP_Pay_Statuses::SUCCESS;
			case self::PAYMENT_FAILURE :
			case self::VALIDATION_FAILURE :
			case self::TECHNICAL_ERROR :
			case self::PAYMENT_REJECTED :
				return Pronamic_WP_Pay_Statuses::FAILURE;
			case self::WAITING_FOR_USER_INPUT :
			case self::WAITING_FOR_PROCESSOR :
			case self::WAITING_ON_CONSUMER_ACTION :
			case self::PAYMENT_ON_HOLD :
				return Pronamic_WP_Pay_Statuses::OPEN;
			case self::CANCELLED_BY_CONSUMER :
			case self::CANCELLED_BY_MERCHANT :
				return Pronamic_WP_Pay_Statuses::CANCELLED;
			default :
				return null;
		}
	}
}
