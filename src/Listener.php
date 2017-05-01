<?php

/**
 * Title: Buckaroo listener
 * Description:
 * Copyright: Copyright (c) 2005 - 2017
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.2.8
 * @since 1.0.0
 */
class Pronamic_WP_Pay_Gateways_Buckaroo_Listener implements Pronamic_Pay_Gateways_ListenerInterface {
	public static function listen() {
		if ( filter_has_var( INPUT_GET, 'buckaroo_push' ) ) {
			$method = Pronamic_WP_Pay_Server::get( 'REQUEST_METHOD', FILTER_SANITIZE_STRING );

			$data = array();

			switch ( $method ) {
				case 'GET':
					$data = $_GET;

					break;
				case 'POST':
					$data = $_POST; // WPCS: CSRF OK

					break;
			}

			$data = array_change_key_case( $data, CASE_LOWER );

			$payment_id = null;

			if ( isset(
				$data[ Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::ADD_PRONAMIC_PAYMENT_ID ],
				$data[ Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::STATUS_CODE ]
			) ) {
				$payment_id = $data[ Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::ADD_PRONAMIC_PAYMENT_ID ];
			} elseif ( isset(
				$data[ Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::INVOICE_NUMBER ],
				$data[ Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::STATUS_CODE ]
			) ) {
				// Fallback for payments started with plugin version <= 4.5.5
				$payment_id = $data[ Pronamic_WP_Pay_Gateways_Buckaroo_Parameters::INVOICE_NUMBER ];
			}

			if ( $payment_id ) {
				$payment = get_pronamic_payment( $payment_id );

				Pronamic_WP_Pay_Plugin::update_payment( $payment );
			}
		}
	}
}
