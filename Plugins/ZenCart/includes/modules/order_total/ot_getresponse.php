<?php
/**
 * Add contact to your GetResponse campaign when order is made.
 *
 * @author Sylwester Okrój
 * http://dev.getresponse.com
 */
// includowanie clasy json
require_once(DIR_FS_CATALOG.'includes/classes/GetResponseAPI3.class.php');

// funkcja install - select
function gr_edit($id, $key = '') {
	$name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');
	$gr_campaig = array(array('id' => '0', 'text' => TEXT_NONE));
	$api_key = MODULE_ORDER_TOTAL_GETRESPONSE_API_KEY;
	if ( !empty($api_key) ) {
		try {
			$client = new GetResponseAPI3($api_key);
			$result = $client->getCampaigns();
			foreach ($result as $k) {
				$gr_campaig[] = array(
						'id' => $k->name,
						'text' => $k->name,
				);
			}
		}
		catch (Exception $e) {
			error_log($e->getMessage());
		}
	}
	return zen_draw_pull_down_menu($name, $gr_campaig, $id);
}
// funkcja install - tytul selecta
function gr_title($id) {
	if ($id == '0') {
		return TEXT_NONE;
	} else { return $id;
	}
}

// clasa modulu
class ot_getresponse {
	var $title, $output;

	function ot_getresponse() {
		$this->code = 'ot_getresponse';
		$this->title = 'GetResponse Integration';
		$this->description = 'Add customer data to GetResponse campaign whenever order is placed';
		$this->sort_order = MODULE_ORDER_TOTAL_GETRESPONSE_STATUS;
		if (MODULE_ORDER_TOTAL_GETRESPONSE_STATUS == 'true') {
			$this->enabled = true;
		}
		else {
			$this->enabled = false;
		}
		/*
		 *	AJAX z GETa
		*/
		if ($_GET['gr']=='export') // dla export
		{
			ob_clean();
			// START
			global $db;
			$query = $db->Execute('
					SELECT
					cu.customers_firstname AS firstname ,
					cu.customers_lastname AS lastname,
					cu.customers_email_address AS email,
					cu.customers_telephone AS telephone,
					bo.entry_street_address AS street_address,
					bo.entry_suburb AS suburb,
					bo.entry_postcode AS postcode,
					bo.entry_city AS city,
					bo.entry_state AS state,
					ca.countries_name AS country
					FROM ' . DB_PREFIX . 'customers cu
					JOIN ' . DB_PREFIX . 'address_book bo ON cu.customers_id = bo.customers_id
					JOIN ' . DB_PREFIX . 'countries ca ON ca.countries_id = bo.entry_country_id
					WHERE cu.customers_newsletter = 1
					');

			if ($query->RecordCount() > 0)
			{
				// sprawdzam polaczenie do api i kampanie
				try {
					$client = new GetResponseAPI3(MODULE_ORDER_TOTAL_GETRESPONSE_API_KEY);
					$result = $client->getCampaigns(array ('query' => array ('name' => MODULE_ORDER_TOTAL_GETRESPONSE_CAMPAIGN))
					);

					$customs = $client->getCustomFields();

					foreach($customs as $custom) {
						if ($custom->name == 'ref') {
							$refCustomId = $custom->customFieldId;
						}
						if ($custom->name == 'zencart_phone') {
							$phoneCustomId = $custom->customFieldId;
						}
						if ($custom->name == 'country') {
							$countryCustomId = $custom->customFieldId;
						}
						if ($custom->name == 'city') {
							$cityCustomId = $custom->customFieldId;
						}
					}

					if (empty($phoneCustomId)) {
						$response = $client->addCustomField(array('name' => 'zencart_phone', 'type' => 'text', 'hidden' => 'false', 'values' => array()));
						$phoneCustomId = $response->customFieldId;
					}

					if (empty($result))
					{
						echo json_encode(array( 'status' => 2, 'response' =>'No campaign with the specified name' )); die;
					}
					else {
						$duplicated = 0;
						$queued = 0;
						$contact = 0;
						$campaignId = reset($result)->campaignId;
						while (!$query->EOF) {
							$params = array (
									'email'      => $query->fields['email'],
									'name'       => $query->fields['firstname'] . ' ' . $query->fields['lastname'],
									'campaign'   => array('campaignId' => $campaignId),
									'dayOfCycle' => '0',
									'customFieldValues' => array(
											array(
													'customFieldId' => $refCustomId,
													'value'         => array(STORE_NAME)
											),
											array(
													'customFieldId' => $phoneCustomId,
													'value'         => array($query->fields['telephone'])
											),
											array(
													'customFieldId' => $countryCustomId,
													'value'         => array($query->fields['country'])
											),
											array(
													'customFieldId' => $cityCustomId,
													'value'         => array($query->fields['city'])
											)
									)
							);

							$r = $client->addContact($params);
							$contact++;

							if ($r->message=='Contact already added')
							{
								$currentUserParams = array('query' => array('email' => $query->fields['email'], 'campaignId' => $campaignId));
								$contactId = $client->getContacts($currentUserParams);
								$contactId = reset($contactId)->contactId;
								$client->updateContact($contactId, $params);
								$duplicated++;
							}
							else if ($r->message=='Contact in queue')
							{
								$queued++;
							}

							$query->MoveNext();
						}
						$json = array('status' => 1,'response' =>'Export completed. <br /> Contacts: ' .$contact. '. Queued:' .$queued. '. Updated: ' .$duplicated. '.');
					}
				}
				catch (Exception $e) {
					$json = array( 'status' => 3, 'response' =>'No or incorrect API key.' );
				}
			}
			else {
				$json = array( 'status' => 0, 'response' =>'No contacts to export' );
			}

			// KONIEC
			header('Content-type: application/json');	echo json_encode($json);	die();
		}
		else if ($_GET['gr']=='get_campaign')
		{		// dla pobierania listy kampani
			ob_clean();
			// START
			$api_key = $_POST['api_key'];
			$results[] = array(
					'id' => 0,
					'text' => TEXT_NONE,
			);
			try {
				$client = new GetResponseAPI3($api_key);
				$result = $client->getCampaigns();
				foreach ($result as $k) {
					$results[] = array(
							'id' => $k->name,
							'text' => $k->name,
					);
				}
			}
			catch (Exception $e) {
				error_log($e->getMessage());
			}
			//KONIEC
			header('Content-type: application/json');	echo json_encode($results);		die();
		}
	}

	function process() {
		global $order;

		if(!$this->enabled) {
			return;
		}

		try {
			$client = new GetResponseAPI3(MODULE_ORDER_TOTAL_GETRESPONSE_API_KEY);
			$result = $client->getCampaigns(array('query' => array('name' => MODULE_ORDER_TOTAL_GETRESPONSE_CAMPAIGN))
			);

			if (empty($result)) {
				throw new Exception(
						'Missing GetResponse campaign: ' .
						MODULE_ORDER_TOTAL_GETRESPONSE_CAMPAIGN
				);
			}

			$customs = $client->getCustomFields();

			foreach($customs as $custom) {
				if ($custom->name == 'ref') {
					$refCustomId = $custom->customFieldId;
				}
				if ($custom->name == 'zencart_phone') {
					$phoneCustomId = $custom->customFieldId;
				}
				if ($custom->name == 'country') {
					$countryCustomId = $custom->customFieldId;
				}
				if ($custom->name == 'city') {
					$cityCustomId = $custom->customFieldId;
				}
			}

			if (empty($phoneCustomId)) {
				$response = $client->addCustomField(array('name' => 'zencart_phone', 'type' => 'text', 'hidden' => 'false', 'values' => array()));
				$phoneCustomId = $response->customFieldId;
			}

			$campaignId = reset($result)->campaignId;

			$params = array (
					'email'      => $order->customer['email_address'],
					'name'       => $order->customer['firstname'] . ' ' . $order->customer['lastname'],
					'campaign'   => array('campaignId' => $campaignId),
					'dayOfCycle' => '0',
					'customFieldValues' => array(
							array(
									'customFieldId' => $refCustomId,
									'value'         => array(STORE_NAME)
							),
							array(
									'customFieldId' => $phoneCustomId,
									'value'         => array($order->customer['telephone'])
							),
							array(
									'customFieldId' => $countryCustomId,
									'value'         => array($order->customer['country']['title'])
							),
							array(
									'customFieldId' => $cityCustomId,
									'value'         => array($order->customer['city'])
							)
					)
			);

			$result = $client->addContact($params);
		}
		catch (Exception $e) {
			error_log($e->getMessage());
			return;
		}
	}

	function check() {
		global $db;
		if (!isset($this->_check)) {
			$check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_GETRESPONSE_STATUS'");
			$this->_check = $check_query->RecordCount();
		}
		return $this->_check;
	}

	function keys() {
		return array(
				'MODULE_ORDER_TOTAL_GETRESPONSE_STATUS',
				'MODULE_ORDER_TOTAL_GETRESPONSE_SORT_ORDER',
				'MODULE_ORDER_TOTAL_GETRESPONSE_API_KEY',
				'MODULE_ORDER_TOTAL_GETRESPONSE_CAMPAIGN',
		);
	}

	function install() {
		global $db;
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('This module is active?', 'MODULE_ORDER_TOTAL_GETRESPONSE_STATUS', 'true', '', '6', '1','zen_cfg_select_option(array(\'true\', \'false\'), ', now())");

		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ORDER_TOTAL_GETRESPONSE_SORT_ORDER', '1000', 'Sort order of display.', '6', '2', now())");

		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('API key', 'MODULE_ORDER_TOTAL_GETRESPONSE_API_KEY', '', 'Click <a href=\https://app.getresponse.com/manage_api.html\>HERE</a> to get your API key',  '6', '2', now())");

		$db->Execute("insert into " . TABLE_CONFIGURATION . "(configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Campaign', 'MODULE_ORDER_TOTAL_GETRESPONSE_CAMPAIGN', '0', 'Select the campaign to which customers will be added', '6', '0', 'gr_title', 'gr_edit(', now())");

	}

	function remove() {
		global $gr_enable;
		$gr_enable = 0;

		global $db;
		$db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
	}

}
?>
