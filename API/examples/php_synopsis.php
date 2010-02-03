<?php

/**
 *
 * Implementation of sample scenario using GetResponse API:
 *
 * Add new contact to campaign 'sample_marketing'.
 * Start his follow-up cycle and set custom field
 * 'last_purchased_product' to 'netbook'.
 *
 * @author Pawel Pabian
 * http://implix.com
 * http://dev.getresponse.com
 *
 */

# JSON-RPC module is required
# available at http://github.com/GetResponse/DevZone/blob/master/API/lib/jsonRPCClient.php
# alternate version available at http://jsonrpcphp.org/
require_once 'jsonRPCClient.php';

# your API key
# available at http://www.getresponse.com/my_api_key.html
$api_key = 'ENTER_YOUR_API_KEY_HERE';

# API 2.x URL
$api_url = 'http://api2.getresponse.com';

# initialize JSON-RPC client
$client = new jsonRPCClient($api_url);

$result = NULL;

# get CAMPAIGN_ID of 'sample_marketing' campaign
try {
    $result = $client->get_campaigns(
        $api_key,
        array (
            # find by name literally
            'name' => array ( 'EQUALS' => 'sample_marketing' )
        )
    );
}
catch (Exception $e) {
    # check for communication and response errors
    # implement handling if needed
    die($e->getMessage());
}

# uncomment this line to preview data structure
# print_r($result);

# since there can be only one campaign of this name
# first key is the CAMPAIGN_ID you need
$CAMPAIGN_ID = array_pop(array_keys($result));

# add contact to 'sample_marketing' campaign
try {
    $result = $client->add_contact(
        $api_key,
        array (
            'campaign'  => $CAMPAIGN_ID,
            'name'      => 'Sample Name',
            'email'     => 'sample@email.com',
            'cycle_day' => '0',
            'customs' => array(
                array(
                    'name'       => 'last_purchased_product',
                    'content'    => 'netbook'
                )
            )
        )
    );
}
catch (Exception $e) {
    # check for communication and response errors
    # implement handling if needed
    die($e->getMessage());
}

# uncomment this line to preview data structure
# print_r($result);

print("Contact added\n");

?>