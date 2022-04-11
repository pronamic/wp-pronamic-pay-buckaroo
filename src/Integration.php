<?php

namespace Pronamic\WordPress\Pay\Gateways\Buckaroo;

use Pronamic\WordPress\Pay\AbstractGatewayIntegration;

/**
 * Title: Buckaroo integration
 * Description:
 * Copyright: 2005-2022 Pronamic
 * Company: Pronamic
 *
 * @author Reüel van der Steege
 * @version 2.0.4
 * @since 1.0.0
 */
class Integration extends AbstractGatewayIntegration {
	/**
	 * REST route namespace.
	 *
	 * @var string
	 */
	const REST_ROUTE_NAMESPACE = 'pronamic-pay/buckaroo/v1';

	/**
	 * Host.
	 * 
	 * @var string
	 */
	private $host;

	/**
	 * Construct Buckaroo integration.
	 *
	 * @param array<string, array<string>> $args Arguments.
	 */
	public function __construct( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'id'            => 'buckaroo',
				'name'          => 'Buckaroo',
				'host'          => 'checkout.buckaroo.nl',
				'url'           => 'https://plaza.buckaroo.nl/',
				'product_url'   => \__( 'http://www.buckaroo-payments.com', 'pronamic_ideal' ),
				'dashboard_url' => 'https://plaza.buckaroo.nl/',
				'provider'      => 'buckaroo',
				'supports'      => array(
					'payment_status_request',
					'refunds',
					'webhook',
					'webhook_log',
					'webhook_no_config',
				),
				'manual_url'    => \__( 'https://www.pronamic.eu/support/how-to-connect-buckaroo-with-wordpress-via-pronamic-pay/', 'pronamic_ideal' ),
			)
		);

		parent::__construct( $args );

		$this->host = $args['host'];

		/**
		 * CLI.
		 *
		 * @link https://github.com/woocommerce/woocommerce/blob/3.9.0/includes/class-woocommerce.php#L453-L455
		 */
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			new CLI( $this );
		}
	}

	/**
	 * Setup.
	 */
	public function setup() {
		\add_filter(
			'pronamic_gateway_configuration_display_value_' . $this->get_id(),
			array( $this, 'gateway_configuration_display_value' ),
			10,
			2
		);

		// Push controller.
		$push_controller = new PushController();

		$push_controller->setup();
	}

	/**
	 * Gateway configuration display value.
	 *
	 * @param string $display_value Display value.
	 * @param int    $post_id       Gateway configuration post ID.
	 * @return string
	 */
	public function gateway_configuration_display_value( $display_value, $post_id ) {
		$config = $this->get_config( $post_id );

		return (string) $config->website_key;
	}

	/**
	 * Get settings fields.
	 *
	 * @return array<int, array<string, callable|int|string|bool|array<int|string,int|string>>>
	 */
	public function get_settings_fields() {
		$fields = array();

		// Website Key.
		$fields[] = array(
			'section'  => 'general',
			'filter'   => FILTER_SANITIZE_STRING,
			'meta_key' => '_pronamic_gateway_buckaroo_website_key',
			'title'    => __( 'Website Key', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => array( 'code' ),
			'tooltip'  => __( 'Website key as mentioned in the Buckaroo dashboard on the page "Profile » Website".', 'pronamic_ideal' ),
		);

		// Secret Key.
		$fields[] = array(
			'section'  => 'general',
			'filter'   => FILTER_SANITIZE_STRING,
			'meta_key' => '_pronamic_gateway_buckaroo_secret_key',
			'title'    => __( 'Secret Key', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => array( 'regular-text', 'code' ),
			'tooltip'  => __( 'Secret key as mentioned in the Buckaroo dashboard on the page "Configuration » Secret Key for Digital Signature".', 'pronamic_ideal' ),
		);

		// Excluded services.
		$fields[] = array(
			'section'  => 'advanced',
			'filter'   => FILTER_SANITIZE_STRING,
			'meta_key' => '_pronamic_gateway_buckaroo_excluded_services',
			'title'    => __( 'Excluded services', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => array( 'regular-text', 'code' ),
			'tooltip'  => sprintf(
				/* translators: %s: <code>brq_parameter</code> */
				__( 'This controls the Buckaroo %s parameter.', 'pronamic_ideal' ),
				sprintf( '<code>%s</code>', 'brq_exludedservices' )
			),
		);

		// Invoice number.
		$fields[] = array(
			'section'     => 'advanced',
			'filter'      => FILTER_SANITIZE_STRING,
			'meta_key'    => '_pronamic_gateway_buckaroo_invoice_number',
			'title'       => __( 'Invoice number', 'pronamic_ideal' ),
			'type'        => 'text',
			'classes'     => array( 'regular-text', 'code' ),
			'tooltip'     => sprintf(
				/* translators: %s: <code>brq_parameter</code> */
				__( 'This controls the Buckaroo %s parameter.', 'pronamic_ideal' ),
				sprintf( '<code>%s</code>', 'brq_invoicenumber' )
			),
			'description' => sprintf(
				'%s<br />%s',
				/* translators: %s: <code>{tag}</code> */
				sprintf( __( 'Available tags: %s', 'pronamic_ideal' ), sprintf( '<code>%s</code> <code>%s</code>', '{order_id}', '{payment_id}' ) ),
				/* translators: %s: default code */
				sprintf( __( 'Default: <code>%s</code>', 'pronamic_ideal' ), '{payment_id}' )
			),
		);

		// Push URL.
		$fields[] = array(
			'section'  => 'feedback',
			'title'    => __( 'Push URL', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => array( 'large-text', 'code' ),
			'value'    => \rest_url( self::REST_ROUTE_NAMESPACE . '/push' ),
			'readonly' => true,
			'tooltip'  => __( 'The Push URL as sent with each transaction to receive automatic payment status updates on.', 'pronamic_ideal' ),
		);

		return $fields;
	}

	/**
	 * Get config.
	 *
	 * @param int $post_id Gateway config post ID.
	 *
	 * @return Config
	 */
	public function get_config( $post_id ) {
		$config = new Config();

		$config->set_host( $this->host );

		$config->website_key       = get_post_meta( $post_id, '_pronamic_gateway_buckaroo_website_key', true );
		$config->secret_key        = get_post_meta( $post_id, '_pronamic_gateway_buckaroo_secret_key', true );
		$config->excluded_services = get_post_meta( $post_id, '_pronamic_gateway_buckaroo_excluded_services', true );
		$config->invoice_number    = get_post_meta( $post_id, '_pronamic_gateway_buckaroo_invoice_number', true );

		return $config;
	}

	/**
	 * Get gateway.
	 *
	 * @param int $post_id Post ID.
	 * @return Gateway
	 */
	public function get_gateway( $post_id ) {
		$gateway = new Gateway( $this->get_config( $post_id ) );

		$gateway->set_mode( $this->get_mode() );

		return $gateway;
	}
}
