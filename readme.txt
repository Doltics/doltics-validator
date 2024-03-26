=== Doltics Validator ===
Contributors: doltics
Tags: antispam, anti-spam, anti spam, validator, validation
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 7.0
Stable tag: 1.1.1
License: GPLv2 or later

Simple validation for your website.

== Description ==

Simple validation for your website.

Our validation API checks and ensures the content processes on your website is valid.
We validate email records against the domain to ensure that you are receiving and sending valid content from your site using out third party solution.
We check and verify the email domain MX records are valid. Each new unique request is maintained for 24 hours before a fresh request is done.

1. [Documentation](https://doltics.com/docs/email-validation-api/)
2. [Terms of service](https://doltics.com/terms-of-service/)

Future plugin updates will include [Deep learning](https://en.wikipedia.org/wiki/Deep_learning) to better understand spam emails and email content with compatibility with popular form plugins.
For now we validate the `is_email` function provided by WordPress core.

== Installation ==

1. Upload the Doltics Validator plugin to your site, activate it.
2. Navigate to Settings > "Doltics Validator" and enable or disable the integration.

== Frequently Asked Questions ==

= Do you save emails sent to the API? =
No we do not save or store any emails sent to the API. We only process the emails for validation.

= I found a bug in the plugin. =
Please post it in the [GitHub issues page](https://github.com/Doltics/doltics-validator/issues/new/choose) and we'll fix it right away. Thanks for helping.


== Changelog ==

= 1.1.1 =

* Fix form validation error.

= 1.1.0 =

* SPAM API integration.
* Better SPAM and email validation.

= 1.0.0 =

* Initial plugin release.
