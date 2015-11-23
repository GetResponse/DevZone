#Wordpress plugin

version 2.1, 2014-05-09 [changelog](#changelog)

##INFO

This plug-in enables installation of a GetResponse fully customizable sign up form on your WordPress site or blog. Once a web form is created and added to the site the visitors are automatically added to your GetResponse contact list and sent a confirmation email. The plug-in additionally offers sign-up upon leaving a comment. 


##AUTHORS

GetResponse, Grzegorz Struczynski

[Implix](http://implix.com)
[Dev Getresponse](http://dev.getresponse.com)
##REQUIRED

[PHP cURL](http://php.net/manual/en/book.curl.php) for "Subscribe via Comment" option.

##INSTALLATION

Download module for your Wordpress version from [Downloads](http://wordpress.org/extend/plugins/getresponse-integration) section.

Method 1.

1. Go to your WordPress admin account.
2. Open Plug-Ins in the left-side bar menu, choose Add New, and search for GetResponse plug-in. Choose the available GetResponse Integration version.
3. Install the plug-in and activate it in your account.

Method 2.

1. Download the GetResponse plug-in for your WordPress version.
2. Unzip the downloaded file and extract the code to to your /wp-content/plugins/ folder.
3. To complete installation you should activate the module in the plug-ins section of your administration panel.

##CONFIGURATION

1. Create the web form in your GetResponse account.
2. Go to your plug-in settings in your WordPress account.
3. Enable the “Subscribe via Comment” option if you want to offer all commenting visitors to join your mailing list. Type in the invitation to subscribe e.g. “Subscribe to join the buzz”.
4. Enable the “Subscribe via Checkout Page” option if you want to offer your customers to join your mailing list at the checkout stage. (available only if WooCommerce is activated). Type in the invitation to subscribe e.g. “Subscribe to join the buzz”.
5. In the top menu bar inside the Wordpress WYSIWYG editor you will find a dropdown menu with all your GetResponse Web Forms. Click on the selected Web Form and it will be added into your Wordpress post.
6. On the Wordpress Widgets page you can drag the GetResponse Web Form module into desired page areas.

With GetResponse form builder you can fully adjust the form to your needs: add brand logo and image, custom fields, and confirmation URLs, or enable pop-up option. Note that to modify your WordPress form you need to do it from GetResponse account – the changes will be displayed automatically on your site.

##Where can I place my web form on my Wordpress page?

You can embed your web form in the sidebar or in a lightbox. In order to use a lighbox, choose this form type in the web form type section, made available in the form editor in your GetResponse account.

##SCREENSHOTS

1. Widget view ![Widget view](https://github.com/GetResponse/DevZone/raw/master/Plugins/WordPress/screenshot-1.png)
2. Plugin settings view ![How to find web form ID](https://github.com/GetResponse/DevZone/raw/master/Plugins/WordPress/screenshot-2.png)
3. WYSIWYG editor view ![Light box integration](https://github.com/GetResponse/DevZone/raw/master/Plugins/WordPress/screenshot-3.png)
4. Webform view ![Example form on page](https://github.com/GetResponse/DevZone/raw/master/Plugins/WordPress/screenshot-4.png)
5. Site view ![Leaving a comment view](https://github.com/GetResponse/DevZone/raw/master/Plugins/WordPress/screenshot-5.png)
6. Leave a comment view ![Leaving a comment view](https://github.com/GetResponse/DevZone/raw/master/Plugins/WordPress/screenshot-6.png)

##CHANGELOG<a name="changelog">

version v2.1, 2014-05-09

* Added subscribe via the registration page
* Campaign names and Web Forms are now sorted by name
* Added checking if curl extension is set and curl_init method is callable
* Removed typo and deprecated unused params
* Tested up to: 3.9.1

version v2.0, 2014-04-07

* Integration is based on API Key;
* Web form ID needs no longer to be copied; now web form is selected from the drop-down menu;
* Customer details can be updated at Checkout page;
* Checkout subscription checkbox now can be ticked by default;
* Comments subscription checkbox now can be ticked by default;
* Shortcode now contains webform url instad of webform id;
* Drop-down menu with webforms has been added to WYSIWYG editor;
* Web forms can now be instantly added into multiple places inside the WordPress page via Widgets;
* Custom fields can be easily mapped via the web form upon subscription;

version 1.3.2, 2013-06-18

* Fixed bugs in getting options. Thanks to @norcross.

version 1.3.1, 2013-02-08

* Added shortcode

version 1.3.0, 2012-11-29

* Added integration with WooCommerce to allow users to subscribe via the checkout page.

version 1.2.1, 2012-03-29

* Fixed code.

version 1.2.0

* Note that the web form installed via the old version of the plug-in will still be fully operational, so you do not need to replace it with the new one. If you want to add the new “Subscribe via comment” function, simply delete old plug-in and install new – and use the same web form ID.  

version 1.1.1

* Fixed integration with new WebForms.

version 1.1

* Added possiblity to use Wordpress styles,
* Added integration with new WebForms.

version 1.0

* Inital release.