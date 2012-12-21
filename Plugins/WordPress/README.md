#Wordpress plugin

version 1.3.0, 2012-03-29 [changelog](#changelog)

##INFO

This plug-in enables installation of a GetResponse fully customizable sign up form on your WordPress site or blog. Once a web form is created and added to the site the visitors are automatically added to your GetResponse contact list and sent a confirmation email. The plug-in additionally offers sign-up upon leaving a comment. 


##AUTHORS

GetResponse

[Implix](http://implix.com)
[Dev Getresponse](http://dev.getresponse.com)
##REQUIRED

[PHP cURL](http://php.net/manual/en/book.curl.php) for "Subscribe via Comment" option.

##INSTALLATION

Download module for your Wordpress version from [Downloads](http://wordpress.org/extend/plugins/getresponse-integration) section.

Method 1.

1.	Download the GetResponse plug-in for your WordPress version.
2.	Unzip the downloaded file and extract the code to to your ```/wp-content/plugins/``` folder.
3.	To complete installation you should activate the module in the plug-ins section of your administration panel.

Method 2.

1.	Go to your WordPress admin account.
2.	Open Plug-Ins in the left-side bar menu, choose Add New, and search for GetResponse plug-in. Choose the available GetResponse Integration 1.2.1 version.
3.	Install the plug-in and activate it in your account. 

##CONFIGURATION

1. Create the web form in your GetResponse account.
2. Go to you’re the plug-in settings in your WordPress account.
3. Get your GetResponse form ID (you’ll learn where from the plug-in configuration window) and type it in the “Subscribe via Web Form” field. Note that leaving the field empty will disable any previously added web form.
4. Enable the “Subscribe via Comment” option if you want to offer all commenting visitors to join your mailing list. Type in the invitation to subscribe e.g. “Subscribe to join the buzz”.
5. Enable the “Subscribe via Checkout Page” option if you want to offer to join your mailing list after checkout (if WooCommerce is activated). Type in the invitation to subscribe e.g. “Subscribe to join the buzz”.
6. Modify the position of your web form from your WordPress settings.


With GetResponse form builder you can fully adjust the form to your needs: add custom fields, confirmation URLs, enable pop-up option, image, logo etc. Note that to modify your WordPress form site you need to do it from GetResponse account – the changes will be displayed automatically on your site.

##WEB FORM ID

Your web form id can be found on you account Webforms. There if you move cursor over "preview" link the last number after the ?id= code id you web from id.


##SCREENSHOTS

1.	Widget view ![Widget view](https://github.com/GetResponse/DevZone/raw/master/Plugins/WordPress/screenshot-1.png)
2.	How to find web form ID ![How to find web form ID](https://github.com/GetResponse/DevZone/raw/master/Plugins/WordPress/screenshot-2.png)
3.	Light box integration ![Light box integration](https://github.com/GetResponse/DevZone/raw/master/Plugins/WordPress/screenshot-3.png)
4.	Example form on page ![Example form on page](https://github.com/GetResponse/DevZone/raw/master/Plugins/WordPress/screenshot-4.png)
5.	Leaving a comment view ![Leaving a comment view](https://github.com/GetResponse/DevZone/raw/master/Plugins/WordPress/screenshot-5.png)

##CHANGELOG<a name="changelog">

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