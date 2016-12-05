=== Romit Checkout Page ===
Contributors:
Tags: romit, woocommerce, payment-gateway, checkout, commerce, credit-card
Requires at least: 3.0.1
Tested up to: 4.6.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A Payment Gateway for WooCommerce allowing you to take credit card payments using Romit.

== Description ==

Welcome to Romit Checkout. Romit Checkout is an implementation of Romit Connect API to provide users with a
streamlined, mobile-ready payment experience that is constantly improving without you doing any work.

= Get Started =

Sign up for a merchant account by opening your Romit account settings and changing Individual Account to Business
Account and following the wizard that shows. Then, visit http://docs.romit.io/docs/woocommerce for installation
instructions.

== Installation ==

= Minimum Requirements =
* WooCommerce 2.1.0 or later

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don't need to
leave your web browser. To do an automatic install of WooCommerce, log in to your WordPress dashboard, navigate to the
Plugins menu and click Add New.

In the search field type “Romit Checkout Page” and click Search Plugins. Once you've found our plugin you can view
details about it such as the the point release, rating and description. Most importantly of course, you can install it
by simply clicking “Install Now”.

= Manual Installation =
1. Unzip the files and upload the folder into your plugins folder (/wp-content/plugins/) overwriting older versions if they exist
1. Activate the plugin in your WordPress admin area.

= Updating =
The plugin should automatically update with new features, but you could always download the new version of the plugin
and manually update the same way you would manually install.

== Frequently Asked Questions ==

= How do I create sandbox accounts for testing? =
Simply go to our [Sandbox](https://login.sandbox.romit.io/?i=wallet&r=/#/app/register) and register. Our sandbox
environment will automatically allow you to start testing your integration.

= Where do I get my API credentials? =
From your account settings page, change the type from Individual Account to Business Account. Click Tools in the menu.
Under Romit Checkout, click Edit Checkout configuration. There you'll find your Checkout ID and, once you set your
Callback URL, your Callback Secret.

= Do I need to have an SSL Certificate? =
Yes. The transactions complete asynchronously. This means that Romit servers will contact your servers to let them know
the status of a transaction (e.g. executed, error, etc.). This communication happens over SSL.

== Screenshots ==

1. The Romit button in action
2. Here's the checkout form your customers will see

== Changelog ==

= 1.0.0 =
* Initial revision

= 1.1.0 =
* Cart empties on callback, allowing for backwards navigation
* Adding order note recording the Romit transfer ID
* Including the latest code in a zip file in the repository

= 1.1.1 =
* Formatting phone numbers passed into payment form

= 1.2.1 =
* Important fix for handling mismatched payment amounts