#osCommerce plugin

version 0.5, 2012-12-20

##INFO

Expand your GetResponse contacts database whenever an order is placed via your osCommerce account.

##AUTHORS

Pawel Pabian, Sylwester Okroj, Grzegorz Struczynski

Implix (http://implix.com)
Dev Getresponse (http://dev.getresponse.com)

##REQUIRED

PHP cURL (http://php.net/manual/en/book.curl.php)

##INSTALLATION

Download module for your osCommerce version from: https://github.com/GetResponse/DevZone/downloads

1.  Copy content of the archive to osCommerce top level. (oscommerce_01.gif)
2.  Edit file: `/<your_admin_path>/modules.php` (default: `/admin/modules.php`):

	In first line, after `<?php` tag add:

        ob_start();


	In last line, before `?>` tag add:

        ob_end_flush();


3.	Edit file: `/<your_admin_path>/includes/template_top.php`

	Copy the whole code chunk below and paste in before `</head>` tag:

        <!- GetResponse Plugin ->
        <?php
        if (tep_session_is_registered('admin') AND $_GET['module']=='ot_getresponse' AND $_GET['action']=='edit') {
		    echo '<script type="text/javascript" src="' . tep_catalog_href_link('ext/modules/order_total/ot_getresponse_campaign.js') . '"></script>';
        }
        else if (tep_session_is_registered('admin') AND $_GET['module']=='ot_getresponse') {
            echo '<script type="text/javascript" src="' . 	tep_catalog_href_link('ext/modules/order_total/ot_getresponse_export.js') . '"></script>';
        }
        ?>
        <!- GetResponse Plugin ->


4.	In the administration panel go to "Modules"=>"Order total" setting. Instal the module. (oscommerce_02.gif)

5.	Now open the edit panel. (oscommerce_03.gif)

6.	Click "Edit" button and set required params:
	- API key (https//app.getresponse.com/my_api_key.html)
	- campaign name (oscommerce_04.gif)

7.	Save settings. Click "Export to campaign" to export your customers contacts.

Done!

From now on it's all automated: a REF number will be allocated to your osCommerce shop name for immediate
contact source tracking (http://www.getresponse.com/features/email-analytics.html). And every time a new customer places
an order, he will be added to your campaign contacts automatically. His contact details: city, country and phone number
will be added too as contact custom fields. He also will be placed at the beginning of the follow-up cycle
(http://www.getresponse.com/features/unlimited-follow-ups.html).

This is all available now with just one click.

##CHANGELOG

version 0.5, 2012-12-20

* Fixed issue with cycle_day. If subscriber is already added to campaign cycle_day isn't reset.

version 0.4, 2012-05-22

* Update jsonRPCClient.php API/lib.

version 0.3, 2012-03-29

* New distribution of GetResponse Integration Plugin for osCommerce 2.3.x

version 0.2, 2012-03-29

* Fixed exception on missing campaign
