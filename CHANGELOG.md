# Change Log

All notable changes to this project will be documented in this file.

This projects adheres to [Semantic Versioning](http://semver.org/) and [Keep a CHANGELOG](http://keepachangelog.com/).

## [Unreleased][unreleased]
- 

## [2.1.1] - 2020-04-20
- Fixed HTML entities in payment description resulting in invalid signature error.

## [2.1.0] - 2020-03-19
- Update setting consumer bank details.

## [2.0.4] - 2019-12-22
- Added URL to manual in gateway settings.
- Improved error handling with exceptions.
- Updated output fields to use payment.
- Updated payment status class name.

## [2.0.3] - 2019-08-27
- Updated packages.

## [2.0.2] - 2019-03-29
- Improved Buckaroo push response handling when payment was not found.
- Added missing PHP namespace alias usage `Pronamic\WordPress\Pay\Core\Server`.

## [2.0.1] - 2018-12-12
- Use issuer field from core gateway.
- Updated deprecated function calls.

## [2.0.0] - 2018-05-09
- Switched to PHP namespaces.

## [1.2.9] - 2017-12-12
- Added support for PayPal payment method.

## [1.2.8] - 2017-05-01
- Use custom payment ID field in transaction request/response instead of invoice number.

## [1.2.7] - 2017-04-10
- Use `brq_push` parameter for the Buckaroo Push URL.

## [1.2.6] - 2016-10-20
- Fixed unable to use payment method 'All available methods'.
- Added new Bancontact constant.
- Fixed `Fatal error: Call to undefined method Pronamic_WP_Pay_Gateways_Buckaroo_Client::get_error()`.

## [1.2.5] - 2016-06-14
- Simplified the gateway payment start function.

## [1.2.4] - 2016-04-12
- Added support for iDEAL issuer.

## [1.2.3] - 2016-03-23
- Added product and dashboard URLs.
- Updated gateway settings and add support for 'brq_excludedservices' parameter.
- Added advanced setting for 'brq_invoicenumber' parameter.

## [1.2.2] - 2016-03-02
- Added get settings function.

## [1.2.1] - 2016-02-01
- Added an gateway settings class.

## [1.2.0] - 2015-11-02
- Renamed namespace prefix from 'class Pronamic_WP_Pay_Buckaroo_' to 'Pronamic_WP_Pay_Gateways_Buckaroo_'.

## [1.1.2] - 2015-10-14
- Fix incorrect signature due to slashes in data.

## [1.1.1] - 2015-03-26
- Updated WordPress pay core library to version 1.2.0.
- Return array with output fields instead of HTML.

## [1.1.0] - 2015-02-27
- Updated WordPress pay core library to version 1.1.0.
- Fixed issues with filter_input INPUT_SERVER (https://bugs.php.net/bug.php?id=49184).

## [1.0.0] - 2015-01-19
- First release.

[unreleased]: https://github.com/wp-pay-gateways/buckaroo/compare/2.1.1...HEAD
[2.1.1]: https://github.com/wp-pay-gateways/buckaroo/compare/2.1.0...2.1.1
[2.1.0]: https://github.com/wp-pay-gateways/buckaroo/compare/2.0.4...2.1.0
[2.0.4]: https://github.com/wp-pay-gateways/buckaroo/compare/2.0.3...2.0.4
[2.0.3]: https://github.com/wp-pay-gateways/buckaroo/compare/2.0.2...2.0.3
[2.0.2]: https://github.com/wp-pay-gateways/buckaroo/compare/2.0.1...2.0.2
[2.0.1]: https://github.com/wp-pay-gateways/buckaroo/compare/2.0.0...2.0.1
[2.0.0]: https://github.com/wp-pay-gateways/buckaroo/compare/1.2.9...2.0.0
[1.2.9]: https://github.com/wp-pay-gateways/buckaroo/compare/1.2.8...1.2.9
[1.2.8]: https://github.com/wp-pay-gateways/buckaroo/compare/1.2.7...1.2.8
[1.2.7]: https://github.com/wp-pay-gateways/buckaroo/compare/1.2.6...1.2.7
[1.2.6]: https://github.com/wp-pay-gateways/buckaroo/compare/1.2.5...1.2.6
[1.2.5]: https://github.com/wp-pay-gateways/buckaroo/compare/1.2.4...1.2.5
[1.2.4]: https://github.com/wp-pay-gateways/buckaroo/compare/1.2.3...1.2.4
[1.2.3]: https://github.com/wp-pay-gateways/buckaroo/compare/1.2.2...1.2.3
[1.2.2]: https://github.com/wp-pay-gateways/buckaroo/compare/1.2.1...1.2.2
[1.2.1]: https://github.com/wp-pay-gateways/buckaroo/compare/1.2.0...1.2.1
[1.2.0]: https://github.com/wp-pay-gateways/buckaroo/compare/1.1.2...1.2.0
[1.1.2]: https://github.com/wp-pay-gateways/buckaroo/compare/1.1.1...1.1.2
[1.1.1]: https://github.com/wp-pay-gateways/buckaroo/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/wp-pay-gateways/buckaroo/compare/1.0.0...1.1.0
