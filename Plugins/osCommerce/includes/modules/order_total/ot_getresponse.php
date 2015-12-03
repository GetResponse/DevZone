<?php
/**
 * Add contact to your GetResponse campaign when order is made.
 *
 * @author Pawel Pabian, Sylwester Okrój
 * http://implix.com
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
	return tep_draw_pull_down_menu($name, $gr_campaig, $id);
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

	function ot_getresponse() {
		$this->code = 'ot_getresponse';
		$this->title = MODULE_ORDER_TOTAL_GETRESPONSE_TITLE;
		$this->description = MODULE_ORDER_TOTAL_GETRESPONSE_DESCRIPTION;
		$this->sort_order = MODULE_ORDER_TOTAL_GETRESPONSE_SORT_ORDER;

		if (MODULE_ORDER_TOTAL_GETRESPONSE_STATUS == 'true') {
			$this->enabled = true;
		}
		else {
			$this->enabled = false;
		}

		/*
		 *	AJAX z GETa
		*/
		if ($_GET['gr']=='export') {				// dla export u
			ob_clean();
			// START

			$query = tep_db_query('
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
					FROM customers cu
					JOIN address_book bo ON cu.customers_id = bo.customers_id
					JOIN countries ca ON ca.countries_id = bo.entry_country_id
					WHERE cu.customers_newsletter = 1
					');

			if (tep_db_num_rows($query)>0) {

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
						if ($custom->name == 'oscommerce_phone') {
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
						$response = $client->addCustomField(array('name' => 'oscommerce_phone', 'type' => 'text', 'hidden' => 'false', 'values' => array()));
						$phoneCustomId = $response->customFieldId;
					}

					if (empty($result)) {
						$json = array( 'status' => 2, 'response' =>'No campaign with the specified name' );
					} else {
						$duplicated = 0;
						$queued = 0;
						$contact = 0;
                        $campaign_id = reset($result)->campaignId;
						while ($row = tep_db_fetch_array($query))
						{
							$params = array (
									'email'      => $row['email'],
									'name'       => $row['firstname'] . ' ' . $row['lastname'],
									'campaign'   => array('campaignId' => $campaign_id),
									'dayOfCycle' => '0',
									'customFieldValues' => array(
											array(
													'customFieldId' => $refCustomId,
													'value'         => array(STORE_NAME)
											),
											array(
													'customFieldId' => $phoneCustomId,
													'value'         => array($row['telephone'])
											),
											array(
													'customFieldId' => $countryCustomId,
													'value'         => array($row['country'])
											),
											array(
													'customFieldId' => $cityCustomId,
													'value'         => array($row['city'])
											)
									)
							);

							$r = $client->addContact($params);
							$contact++;

							if ($r->message=='Contact already added')
							{
								$currentUserParams = array('query' => array('email' => $row['email'], 'campaignId' => $campaign_id));
								$contactId = $client->getContacts($currentUserParams);
								$contactId = reset($contactId)->contactId;
								$client->updateContact($contactId, $params);
								$duplicated++;
							}
							else if ($r->message=='Contact in queue')
							{
								$queued++;
							}
						}
						$json = array('status' => 1,
								'response' =>'export completed. <br /> Contacts: ' .$contact. '. Queued:' .$queued. '. Updated: ' .$duplicated. '.');

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
		else if ($_GET['gr']=='get_campaign') {		// dla pobierania listy kampani
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
				if ($custom->name == 'oscommerce_phone') {
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
				$response = $client->addCustomField(array('name' => 'oscommerce_phone', 'type' => 'text', 'hidden' => 'false', 'values' => array()));
				$phoneCustomId = $response->customFieldId;
			}

			$campaign_id = reset($result)->campaignId;

			$params = array (
					'email'      => $order->customer['email_address'],
					'name'       => $order->customer['firstname'] . ' ' . $order->customer['lastname'],
					'campaign'   => array('campaignId' => $campaign_id),
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
		$check_query = tep_db_query(
				"SELECT configuration_value " .
				"FROM " . TABLE_CONFIGURATION . " " .
				"WHERE configuration_key = 'MODULE_ORDER_TOTAL_GETRESPONSE_STATUS'"
		);

		$this->_check = tep_db_num_rows($check_query);

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
		tep_db_query(
				"INSERT INTO " . TABLE_CONFIGURATION . " " .
				"(
				configuration_title,
				configuration_key,
				configuration_value,
				configuration_description,
				configuration_group_id,
				sort_order,
				set_function,
				date_added
		) " .
				"VALUES " .
				"(
				'Active',
				'MODULE_ORDER_TOTAL_GETRESPONSE_STATUS',
				'true',
				'Activate module?',
				'6',
				'1',
				'tep_cfg_select_option(array(\'true\', \'false\'), ', NOW()
		), " .
				"(
				'API key',
				'MODULE_ORDER_TOTAL_GETRESPONSE_API_KEY',
				'',
				'Click <a href=\"http://www.getresponse.com/manage_api.html\">HERE</a> to get your API key',
				'6',
				'2',
				NULL,
				NOW()
		);"
		);
		tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ORDER_TOTAL_GETRESPONSE_SORT_ORDER', '10', 'Sort order of display.', '6', '2', now())");
		tep_db_query("insert into " . TABLE_CONFIGURATION . "(configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Campaign', 'MODULE_ORDER_TOTAL_GETRESPONSE_CAMPAIGN', '0', 'Select the campaign to which customers will be added', '6', '0', 'gr_title', 'gr_edit(', now())");
	}

	function remove() {
		foreach ($this->keys() as $key) {
			tep_db_query(
					"DELETE FROM " . TABLE_CONFIGURATION . " " .
					"WHERE configuration_key = '$key'"
			);
		}
	}

}

?>
