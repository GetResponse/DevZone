#Prestashop plugin

version 2.1, 2013-08-13 [changelog](#changelog)

##INFO

The GetResponse Integration module allows you to quickly and easily export your contacts to your GetResponse campaign.
New subscribers can also be added via the newsletter subscription on your checkout page.

##AUTHORS

 Grzegorz Struczynski

[Implix](http://implix.com)
[Dev Getresponse](http://dev.getresponse.com)

##REQUIRED

[Prestashop](http://www.prestashop.com/en/download) (at least ver. 1.5.x)

##INSTALLATION

Download module [plugin_prestashop-2.1.zip](https://github.com/GetResponse/DevZone/raw/master/Plugins/Prestashop/plugin_prestashop-2.1.zip).

1.  Add plugi-in to your PrestaShop store in the modules tab. <br/><br/>
	![Screenshot 1](https://github.com/GetResponse/DevZone/raw/master/Plugins/Prestashop/prestashop_01.jpg)
2.  The plug-in then appears in the modules list. To activate it, click the “Install” button.<br/><br/>

	![Screenshot 2](https://github.com/GetResponse/DevZone/raw/master/Plugins/Prestashop/prestashop_02.jpg)  
3.  In the GetResponse tab, choose your options for “GetResponse Settings and Actions.”

	![Screenshot 3](https://github.com/GetResponse/DevZone/raw/master/Plugins/Prestashop/prestashop_03.jpg) 
4. Enter and Save your GetResponse API, which you can retrieve [here](https://app.getresponse.com/my_api_key.html)


	Done!

##Export Customers

If you’d like to export all your existing customers, go to the Export Customers section. Use the dropdown menu to select a target campaign from your GetResponse account. Then click the Export button.

##Subscription via registration page

If you want to import new PrestaShop customers when they register with your store, go to the Subscription via registration page section. Use the dropdown menu to Select a target campaign from your GetResponse account then choose “enabled” in the dropdown menu of the Subscription field.

Note: To add all new customers, even those who have not yet made a purchase, uncheck the box labeled Update address data via checkout. If you prefer to capture only customers who make a purchase, check the box.

Enter a Name for custom fields that you want to include with your contacts. No matter which export method you choose, all contacts imported from PrestaShop to GetResponse have a unique custom field “ref: prestashop” to enable you to track the contact source for reporting and segmentation.

Click the Save button.

That’s it. From that point forward, new customers are added to your chosen GetResponse campaign automatically.

##Subscription via Web Form
1. The Web Form ID can be found in GetResponse in the "?wid=" param of a created Web Form.
2. In the "Web Form position" settings you can choose where to place it. 
3. With the Style option you can use either the GetResponse style of the Web Form or the PrestaShop format for the style.
4. Enable the subscription feature in the "Subscription" pulldown.

Please note:

If you want to use the GetResponse web form within Prestashop instead of the default web form, please disable
the "Newsletter block" module first and export the contacts. Deleting this module will also delete all the
contacts subscribed up to that point from the Prestashop data base.

##Update to the latest version

When you update from v1.0 to v2.1 please make sure you first uninstall the v1.0 module and then delete it.
Then install the new v2.1 module.

##CHANGELOG<a name="changelog">

version 2.1, 2013-08-13

* Fixed issue with export contacts. "API request error" removed. "Not added" status added.
* PrestaShop Coding standards.

version 2.0, 2013-02-01

* Subscribtion via Web Form added.
* Export guests who subscribed to newsletter.
* Fixed issue with cycle_day. If subscriber is already added to campaign cycle_day isn't reset to 0.
* Updated jsonRPCClient.php API/lib.

version 1.0, 2012-11-30

* Inital release of GetResponse Integration Plugin for Prestashop 1.5.x
