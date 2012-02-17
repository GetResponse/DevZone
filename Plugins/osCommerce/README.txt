Add contact to your GetResponse campaign when order is made.

Version: 0.2

Author:  Pawel Pabian
         http://implix.com
         http://dev.getresponse.com

Installation:

    1. Copy content of this archive to osCommerce top level.
    2. In the administration panel
       (located under catalog/admin/index.php by default)
       go to "Modules"=>"Order total" setting.
    3. Click on "GetResponse" plugin and click "Install" button.
    4. Click "Edit" button and set required params:
       - API key
       - Campaign name

    Done!

Effect:

    When a customer makes an order he/she is added to GetResponse campaign.
    REF is set to osCommerce shop name.
    City, country and telephone values are added as contact custom fields.
    Contact is also placed at the beginning of the follow-up cycle.

Changelog:

    0.2
    Fixed exception on missing campaign.