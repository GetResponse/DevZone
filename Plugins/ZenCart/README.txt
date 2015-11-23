#ZenCart plugin

version 1.1, 2012-05-22

##INFO

Seamlessly enter email communication with all your new customers by adding them to your email list once they place an order in your ZenCart shop.

##AUTHORS

Sylwester Okrój

Implix (http://implix.com)
Dev Getresponse (http://dev.getresponse.com)

##REQUIRED

PHP cURL (http://php.net/manual/en/book.curl.php)

##INSTALLATION

Download module for your osCommerce version from: https://github.com/GetResponse/DevZone/downloads

1.  Copy content of the archive to ZenCart top level.

	- copy `/your_admin_path/` to your administration folder (zencart_01.gif)
	
2.  Edit file: `/<your_admin_path>/modules.php` (default: `/admin/modules.php`):

	In first line, after `<?php` tag add:

        ob_start();


	In last line, before `?>` tag add:

        ob_end_flush();


	Copy the entire code chunk below and paste before `</head>` tag:

        <!– GetResponse Plugin –>
        <?php
        if (MODULE_ORDER_TOTAL_GETRESPONSE_STATUS == 'true') {		
            echo '<script type="text/javascript" src="includes/getresponse/jquery-1.7.1.min.js"></script>';
            if ($_GET['module']=='ot_getresponse' AND $_GET['action']=='edit') {
                echo '<script type="text/javascript" src="includes/getresponse/ot_getresponse_campaign.js"></script>';
            }
            else if ($_GET['module']=='ot_getresponse' AND $_GET['action']!='remove') {
                echo '<script type="text/javascript" src="includes/getresponse/ot_getresponse_export.js"></script>';
            } 
        }
        ?>
        <!- GetResponse Plugin –>


3.	In the administration panel go to “Modules”=>”Order total” setting.(zencart_02.gif)

4.	Click on “GetResponse” plugin and click “Install” button. (zencart_03.gif)

5.	Click “Edit” button and set required params: 
	- API key (https://app.getresponse.com/my_api_key.html)
	- campaign name. (zencart_04.gif)
	
6.	Save settings. Click “Export to campaign” you’ll add all your existing ZenCart contacts to campaign.

Done!

From now on when a customer makes an order he/she is automatically added to GetResponse campaign together with their contact details (city, country and telephone values) that will be added as contact custom fields.
 
##CHANGELOG

version 1.1, 2012-05-22

* Update jsonRPCClient.php API/lib.

version 1.0, 2012-03-29

* New distribution of GetResponse Integration Plugin for ZenCart 1.5.x

