<?php
/**
 * Config test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2021 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Buckaroo
 */

namespace Pronamic\WordPress\Pay\Gateways\Buckaroo;

use Pronamic\WordPress\Pay\Core\Gateway as Core_Gateway;

/**
 * Config test
 *
 * @author  Reüel van der Steege
 * @version unreleased
 * @since   unreleased
 */
class ConfigTest extends \WP_UnitTestCase {
	/**
	 * Test config.
	 */
	public function test_config() {
		$config = new Config();

		$config->website_key       = 'jpERWpuvUK';
		$config->secret_key        = 'FA82C3F2EE964729377A172F7564F372';
		$config->excluded_services = '';
		$config->invoice_number    = '{payment_id}';

		$this->assertEquals( 'jpERWpuvUK', $config->get_website_key() );
		$this->assertEquals( 'FA82C3F2EE964729377A172F7564F372', $config->get_secret_key() );
		$this->assertEquals( '', $config->get_excluded_services() );
		$this->assertEquals( '{payment_id}', $config->get_invoice_number() );
	}
}
