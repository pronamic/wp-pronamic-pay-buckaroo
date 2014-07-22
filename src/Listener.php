<?php

/**
 * Title: Buckaroo listener
 * Description:
 * Copyright: Copyright (c) 2005 - 2014
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0.0
 */
class Pronamic_WP_Pay_Buckaroo_Listener implements Pronamic_Pay_Gateways_ListenerInterface {
	public static function listen() {
		if ( filter_has_var( INPUT_GET, 'buckaroo_push' ) ) {
			$method = filter_input( INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING );

			$data = array();

			switch ( $method ) {
				case 'GET':
					$data = $_GET;

					break;
				case 'POST':
					$data = $_POST;

					break;
			}

			$data = array_change_key_case( $data, CASE_LOWER );

			if ( isset(
				$data[ Pronamic_WP_Pay_Buckaroo_Parameters::INVOICE_NUMBER ],
				$data[ Pronamic_WP_Pay_Buckaroo_Parameters::STATUS_CODE ]
			) ) {
				$payment_id = $data[ Pronamic_WP_Pay_Buckaroo_Parameters::INVOICE_NUMBER ];

				$payment = get_pronamic_payment( $payment_id );

				Pronamic_WP_Pay_Plugin::update_payment( $payment );
			}
		}
	}
}
