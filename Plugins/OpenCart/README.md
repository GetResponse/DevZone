#OpenCart plugin

version 1.2, 2012-07-05 [changelog](#changelog)

##INFO

Let all your customers be automatically added to your email campaigns and follow up with perfectly customized offers effortlessly.

Simply install the plugin and automatically grow your email audience with Getresponse. From now on every new customer of your OpenCart shop will be automatically added to your list upon placing an order.

##AUTHORS

Sylwester Okrój

[Implix](http://implix.com)
[Dev Getresponse](http://dev.getresponse.com)
##REQUIRED

[PHP cURL](http://php.net/manual/en/book.curl.php)

##INSTALLATION

Download module for your OpenCart version from [Downloads](https://github.com/GetResponse/DevZone/downloads) section.

1. Copy content of this archive to OpenCart top level.<br/><br/>![Screenshot 1](https://github.com/GetResponse/DevZone/raw/master/Plugins/OpenCart/opencart_01.gif)
2. In the administration panel go to “Extensions”=>”Modules” setting.<br/><br/>![Screenshot 2](https://github.com/GetResponse/DevZone/raw/master/Plugins/OpenCart/opencart_02.gif)
3. Click “Edit” button in “GetResponse” row and insert API key (copy your secret API key from [here](https://app.getresponse.com/my_api_key.html)), and choose the campaign name.<br/><br/>![Screenshot 3](https://github.com/GetResponse/DevZone/raw/master/Plugins/OpenCart/opencart_03.gif)
4. Click “Export to campaign” to export your customers contacts.
5. Save your API key and selected campaign for future exports.

Done!

Next time only click “Export to campaign” button and all the new customers will be automatically added to your campaign with their contact details! 
The limit is 10,000 a day, so if you’ve go more contacts in your OpenCart account you’ll have to repeat the export operation for as long as it takes. 
You’ll also be able to export all the new contacts whenever you feel like. 
Note that all subsequent exports will be updates only as contacts don’t get duplicated. Due to the fact that they are imported contact they will all be sent a confirmation email.

##CHANGELOG<a name="changelog">

version 1.2, 2012-07-05

* Removed unnecessary comments and fixed ajax request for IE7.

version 1.1, 2012-05-22

* Update jsonRPCClient.php API/lib.

version 1.0, 2012-03-29

* New distribution of GetResponse Integration Plugin for OpenCart 1.5.x