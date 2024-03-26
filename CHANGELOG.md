# Change Log

All notable changes to this project will be documented in this file.

This projects adheres to [Semantic Versioning](http://semver.org/) and [Keep a CHANGELOG](http://keepachangelog.com/).

## [Unreleased][unreleased]
-

## [4.3.4] - 2024-03-26

### Commits

- ncu -u ([8163933](https://github.com/pronamic/wp-pronamic-pay-buckaroo/commit/816393344f8fd2cc21fdcf4689e68d8f08c4d19b))

Full set of changes: [`4.3.3...4.3.4`][4.3.4]

[4.3.4]: https://github.com/pronamic/wp-pronamic-pay-buckaroo/compare/v4.3.3...v4.3.4

## [4.3.3] - 2023-06-01

### Commits

- Switch from `pronamic/wp-deployer` to `pronamic/pronamic-cli`. ([b53a9fa](https://github.com/pronamic/wp-pronamic-pay-buckaroo/commit/b53a9fa0300d83a81754d0a40c24d964ff40235c))
- Updated .gitattributes ([60718b3](https://github.com/pronamic/wp-pronamic-pay-buckaroo/commit/60718b3785858d7ff99faa8400e8075fde9b2a80))

Full set of changes: [`4.3.2...4.3.3`][4.3.3]

[4.3.3]: https://github.com/pronamic/wp-pronamic-pay-buckaroo/compare/v4.3.2...v4.3.3

## [4.3.2] - 2023-03-29
### Commits

- Set Composer type to `wordpress-plugin`. ([5014877](https://github.com/pronamic/wp-pronamic-pay-buckaroo/commit/5014877fdcb7be2d285340292ade4040e47edd80))
- Change refund function signature. ([c2d2732](https://github.com/pronamic/wp-pronamic-pay-buckaroo/commit/c2d2732b1b95d65215711d30e948851c7345015d))
- Updated .gitattributes ([0e5fa4c](https://github.com/pronamic/wp-pronamic-pay-buckaroo/commit/0e5fa4c37c78dcfce675e1e340887dbe81812820))
- Requires PHP: 7.4. ([3bb5d4a](https://github.com/pronamic/wp-pronamic-pay-buckaroo/commit/3bb5d4ad9b38c46f00d9667ae7686cb829dcd0fb))

### Composer

- Changed `wp-pay/core` from `^4.6` to `v4.9.0`.
	Release notes: https://github.com/pronamic/wp-pay-core/releases/tag/v4.9.0
Full set of changes: [`4.3.1...4.3.2`][4.3.2]

[4.3.2]: https://github.com/pronamic/wp-pronamic-pay-buckaroo/compare/v4.3.1...v4.3.2

## [4.3.1] - 2023-01-31
### Added

- Added `Software` HTTP header to all remote Buckaroo requests for partnership.

### Composer

- Changed `php` from `>=8.0` to `>=7.4`.
Full set of changes: [`4.3.0...4.3.1`][4.3.1]

[4.3.1]: https://github.com/pronamic/wp-pronamic-pay-buckaroo/compare/v4.3.0...v4.3.1

## [4.3.0] - 2022-12-29

### Commits

- Added support for https://github.com/WordPress/wp-plugin-dependencies. ([cd40f86](https://github.com/pronamic/wp-pronamic-pay-buckaroo/commit/cd40f86abde9f1168a32ea8e8c2f882d2cd76424))
- No longer use deprecated `FILTER_SANITIZE_STRING`. ([1736713](https://github.com/pronamic/wp-pronamic-pay-buckaroo/commit/1736713b97f14954cc174967a1334224ae6991ba))

### Composer

- Changed `php` from `>=5.6.20` to `>=8.0`.
- Changed `wp-pay/core` from `^4.4` to `v4.6.0`.
	Release notes: https://github.com/pronamic/wp-pay-core/releases/tag/v4.6.0
Full set of changes: [`4.2.2...4.3.0`][4.3.0]

[4.3.0]: https://github.com/pronamic/wp-pronamic-pay-buckaroo/compare/v4.2.2...v4.3.0

## [4.2.2] - 2022-10-11
- Fixed possible "Warning: Invalid argument supplied for foreach()" when enriching payment methods (pronamic/wp-pronamic-pay-buckaroo#7).

## [4.2.1] - 2022-09-27
- Update to `wp-pay/core` version `^4.4`.

## [4.2.0] - 2022-09-26
- Updated payment methods registration.
- Updated for Sisow via Buckaroo integration (pronamic/wp-pronamic-pay-sisow#3).

## [4.1.0] - 2022-04-11
- No longer catch exception, should be handled downstream.
- No longer use core gateway mode.

## [4.0.0] - 2022-01-11
### Changed
- Updated to https://github.com/pronamic/wp-pay-core/releases/tag/4.0.0.

### Fixed
- Fix "Fatal error: Uncaught Exception: Could not JSON decode response, HTTP response: "400 Bad Request", HTTP body length: "67", JSON error: "Syntax error"." when getting issuers with invalid configuration.

## [3.0.2] - 2021-08-18
- Fix "Fatal error: Uncaught Error: Undefined class constant 'V_PAY'".

## [3.0.1] - 2021-08-16
- Added support for American Express, Maestro, Mastercard, V PAY and Visa.
- Save `CustomerIBAN` and `CustomerBIC` for Sofort payments.

## [3.0.0] - 2021-08-05
- Updated to `pronamic/wp-pay-core`  version `3.0.0`.
- Updated to `pronamic/wp-money`  version `2.0.0`.
- Switched to `pronamic/wp-coding-standards`.
- Fix setting BIC as consumer bank details IBAN in status update.
- Updated hooks documentation.

## [2.2.0] - 2021-06-18
- Added initial support for refunds.
- Added WP-CLI command to retrieve the transaction status by transaction key.
- Updated integration to JSON API.
- Switched to WordPress REST API for Push URL.

## [2.1.2] - 2021-04-26
- Started using `pronamic/wp-http`.

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

[unreleased]: https://github.com/wp-pay-gateways/buckaroo/compare/4.2.2...HEAD
[4.2.2]: https://github.com/pronamic/wp-pronamic-pay-buckaroo/compare/4.2.1...4.2.2
[4.2.1]: https://github.com/pronamic/wp-pronamic-pay-buckaroo/compare/4.2.0...4.2.1
[4.2.0]: https://github.com/pronamic/wp-pronamic-pay-buckaroo/compare/4.1.0...4.2.0
[4.1.0]: https://github.com/wp-pay-gateways/buckaroo/compare/4.0.0...4.1.0
[4.0.0]: https://github.com/wp-pay-gateways/buckaroo/compare/3.0.2...4.0.0
[3.0.2]: https://github.com/wp-pay-gateways/buckaroo/compare/3.0.1...3.0.2
[3.0.1]: https://github.com/wp-pay-gateways/buckaroo/compare/3.0.0...3.0.1
[3.0.0]: https://github.com/wp-pay-gateways/buckaroo/compare/2.2.0...3.0.0
[2.2.0]: https://github.com/wp-pay-gateways/buckaroo/compare/2.1.2...2.2.0
[2.1.2]: https://github.com/wp-pay-gateways/buckaroo/compare/2.1.1...2.1.2
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
