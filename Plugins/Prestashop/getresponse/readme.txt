=== GetResponse Integration ===
Contributors: GetResponse
Requires at least: 1.5.0.0
Tested up to: 1.5.4.1
Stable tag: 2.1

== Description ==

The GetResponse Integration module allows you to quickly and easily export your contacts to your GetResponse campaign.
New subscribers can also be added via the newsletter subscription on your checkout page. Additionally, you can place
a GetResponse Web Form in your Online Shop.

== Installation ==

1. Download the GetResponse Integration module (zip file) 
2. Login to your Prestashop Administration panel and select Modules, then click on "Add new module".
3. Select the .zip file as the module to install.
3. To complete installation you have to activate (enable) the module in the Modules section of your administration panel.

= Configuration: =

1. Select GetResponse/Settings & Actions
2. Use the API Key from GetReponse site. You can find it at: https://app.getresponse.com/my_api_key.html
3. Follow the instructions on the site. You can choose to either export all the contacts you have in Prestahop or add
   subscribers every time they create an account at your shop and enable the "Newsletter Subscription" checkbox.

= Subscription via Web Form: =

1. The Web Form ID can be found in GetResponse in the "?wid=" param of a created Web Form.
2. In the "Web Form position" settings you can choose where to place it.
3. With the Style option you can use either the GetResponse style of the Web Form or the PrestaShop format for the style.
4. Enable the subscription feature in the "Subscription" pulldown.

Please note:
If you want to use the GetResponse web form within Prestashop instead of the default web form, please disable
the "Newsletter block" module first and export the contacts. Deleting this module will also delete all the
contacts subscribed up to that point from the Prestashop data base.

= v2.1 =

* Fixed issue with export contacts. "API request error" removed. "Not added" status added.
* PrestaShop Coding standards.

= v2.0 =

* Subscribtion via Web Form added.
* Export guests who subscribed to newsletter.
* Fixed issue with cycle_day. If subscriber is already added to campaign cycle_day isn't reset to 0.
* Updated jsonRPCClient.php API/lib.

When you update from v1.0 to v2.0 please make sure you first uninstall the v1.0 module and then delete it.
Then install the new v2.0 module.

= v1.0 =

* Inital release.