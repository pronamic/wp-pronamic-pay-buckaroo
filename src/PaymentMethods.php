<?php

namespace Pronamic\WordPress\Pay\Gateways\Buckaroo;

use Pronamic\WordPress\Pay\Core\PaymentMethods as Core_PaymentMethods;

/**
 * Title: Buckaroo payment methods constants
 * Description:
 * Copyright: 2005-2023 Pronamic
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 3.0.2
 * @since 1.0.0
 */
class PaymentMethods {
	/**
	 * Indicator for the 'Achteraf Betalen' payment method
	 *
	 * @var string
	 */
	const PAYMENT_GUARANTEE = 'paymentguarantee';

	/**
	 * Indicator for the 'American Express' payment method
	 *
	 * @var string
	 */
	const AMERICAN_EXPRESS = 'amex';

	/**
	 * Indicator for the 'Bancontact / Mr Cash' payment method
	 *
	 * @var string
	 */
	const BANCONTACT_MISTER_CASH = 'bancontactmrcash';

	/**
	 * Indicator for the 'èM! Payment' payment method
	 *
	 * @var string
	 */
	const EM_PAYMENT = 'empayment';

	/**
	 * Indicator for the 'Giropay' payment method
	 *
	 * @var string
	 */
	const GIROPAY = 'giropay';

	/**
	 * Indicator for the 'iDEAL' payment method
	 *
	 * @var string
	 */
	const IDEAL = 'ideal';

	/**
	 * Indicator for the 'Maestro' payment method
	 *
	 * @var string
	 */
	const MAESTRO = 'maestro';

	/**
	 * Indicator for the 'MasterCard' payment method
	 *
	 * @var string
	 */
	const MASTERCARD = 'mastercard';

	/**
	 * Indicator for the 'Overschrijving' payment method
	 *
	 * @var string
	 */
	const TRANSFER = 'transfer';

	/**
	 * Indicator for the 'PayPal' payment method
	 *
	 * @var string
	 */
	const PAYPAL = 'paypal';

	/**
	 * Indicator for the 'paysafecard' payment method
	 *
	 * @var string
	 */
	const PAYSAFECARD = 'paysafecard';

	/**
	 * Indicator for the 'Sofortüberweisung' payment method
	 *
	 * @var string
	 */
	const SOFORTUEBERWEISING = 'sofortueberweisung';

	/**
	 * Indicator for the 'Ukash' payment method
	 *
	 * @var string
	 */
	const UKASH = 'Ukash';

	/**
	 * Indicator for the 'Visa' payment method
	 *
	 * @var string
	 */
	const VISA = 'visa';

	/**
	 * Indicator for the 'V PAY' payment method.
	 *
	 * @var string
	 */
	const V_PAY = 'vpay';

	/**
	 * Payments methods map.
	 *
	 * @var array<string, string>
	 */
	private static $map = [
		Core_PaymentMethods::AMERICAN_EXPRESS => self::AMERICAN_EXPRESS,
		Core_PaymentMethods::BANK_TRANSFER    => self::TRANSFER,
		Core_PaymentMethods::BANCONTACT       => self::BANCONTACT_MISTER_CASH,
		Core_PaymentMethods::MISTER_CASH      => self::BANCONTACT_MISTER_CASH,
		Core_PaymentMethods::GIROPAY          => self::GIROPAY,
		Core_PaymentMethods::IDEAL            => self::IDEAL,
		Core_PaymentMethods::MAESTRO          => self::MAESTRO,
		Core_PaymentMethods::MASTERCARD       => self::MASTERCARD,
		Core_PaymentMethods::PAYPAL           => self::PAYPAL,
		Core_PaymentMethods::SOFORT           => self::SOFORTUEBERWEISING,
		Core_PaymentMethods::V_PAY            => self::V_PAY,
		Core_PaymentMethods::VISA             => self::VISA,
	];

	/**
	 * Transform WordPress payment method to Buckaroo method.
	 *
	 * @since 1.1.6
	 *
	 * @param string      $payment_method WordPress payment method to transform to Buckaroo method.
	 * @param null|string $default        Default payment method.
	 *
	 * @return string|null
	 */
	public static function transform( $payment_method, $default = null ) {
		if ( ! is_scalar( $payment_method ) ) {
			return null;
		}

		if ( isset( self::$map[ $payment_method ] ) ) {
			return self::$map[ $payment_method ];
		}

		return $default;
	}

	/**
	 * Convert method from Buckaroo indicator to a Pronamic indicator.
	 *
	 * @param string $method Method.
	 * @return string|null
	 */
	public static function from_buckaroo_to_pronamic( $method ) {
		$key = \array_search( $method, self::$map );

		if ( false === $key ) {
			return null;
		}

		return $key;
	}
}
