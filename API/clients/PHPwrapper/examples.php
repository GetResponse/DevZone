<?php

require_once('GetResponseAPI.class.php');
$api = new GetResponse('YOUR_API_KEY');

// Connection Testing
$ping = $api->ping();
var_dump($ping);

// Account
$details = $api->getAccountInfo();
var_dump($details);

// Campaigns
$campaigns 	 = (array)$api->getCampaigns();
$campaignIDs = array_keys($campaigns);
$campaign 	 = $api->getCampaignByID($campaignIDs[0]);
var_dump($campaigns, $campaign);

// Contacts
$contacts 	= (array)$api->getContacts(null);
$contactIDs	= array_keys($contacts);
$setName 	= $api->setContactName($contactIDs[0], 'John Smith');
$setCustoms	= $api->setContactCustoms($contactIDs[0], array('title' => 'Mr', 'middle_name' => 'Fred'));
$customs 	= $api->getContactCustoms($contactIDs[0]);
$contact 	= $api->getContactByID($contactIDs[0]);
$geoIP 		= $api->getContactGeoIP($contactIDs[0]);
$opens 		= $api->getContactOpens($contactIDs[0]);
$clicks 	= $api->getContactClicks($contactIDs[0]);
var_dump($contacts, $setName, $setCustoms, $customs, $contact, $geoIP, $opens, $clicks);

// Blacklists
$addBlacklist = $api->addAccountBlacklist('someone@domain.co.uk');
$getBlacklist = $api->getAccountBlacklist();
$delBlacklist = $api->deleteAccountBlacklist('someone@domain.co.uk');
var_dump($addBlacklist, $getBlacklist, $delBlacklist);

?>
