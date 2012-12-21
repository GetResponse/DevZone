<?php
/**
 * Add contact to your GetResponse campaign when order is made.
 *
 * @author Pawel Pabian, Sylwester Okrój
 * http://implix.com
 * http://dev.getresponse.com
 */

// includowanie clasy json
require_once(DIR_FS_CATALOG.'includes/classes/jsonRPCClient.php');

// funkcja install - select
function gr_edit($id, $key = '') {
	$name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');
	$gr_campaig = array(array('id' => '0', 'text' => TEXT_NONE));
	$api_key = MODULE_ORDER_TOTAL_GETRESPONSE_API_KEY;
	if ( !empty($api_key) ) {
		try {
			$client = new jsonRPCClient('http://api2.getresponse.com');
			$result = $client->get_campaigns( $api_key );
			foreach ($result as $k) {
				$gr_campaig[] = array(
						'id' => $k['name'],
						'text' => $k['name'],
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
					$client = new jsonRPCClient('http://api2.getresponse.com');
					$result = $client->get_campaigns(MODULE_ORDER_TOTAL_GETRESPONSE_API_KEY,
							array ('name' => array ('EQUALS' => MODULE_ORDER_TOTAL_GETRESPONSE_CAMPAIGN))
					);
					if (empty($result)) {
						$json = array( 'status' => 2, 'response' =>'No campaign with the specified name' );
					} else {
						$duplicated = 0;
						$queued = 0;
						$contact = 0;
                        $campaign_id = array_pop(array_keys($result));

						while ($row = tep_db_fetch_array($query))
						{
                            $check_cycle_day = $client->get_contact(
                                MODULE_ORDER_TOTAL_GETRESPONSE_API_KEY,
                                array (
                                    'campaigns' => array($campaign_id),
                                    'email' => array (
                                        'EQUALS' => $row['email']
                                    )
                                )
                            );

                            $cycle_day = (!empty($check_cycle_day) and isset($check_cycle_day[$campaign_id]['cycle_day'])) ? "'cycle_day' => ".$check_cycle_day[$campaign_id]['cycle_day']."," : '0';

                            $r = $client->add_contact(MODULE_ORDER_TOTAL_GETRESPONSE_API_KEY,
									array (
											'campaign'  => $campaign_id,
											'name'      => $row['firstname'] . ' ' . $row['lastname'],
											'email'     => $row['email'],
											'cycle_day' => $cycle_day,
											'customs' => array(
													array(
															'name'       => 'ref',
															'content'    => STORE_NAME
													),
													array(
															'name'       => 'telephone',
															'content'    => $row['telephone']
													),
													array(
															'name'       => 'country',
															'content'    => $row['country']
													),
													array(
															'name'       => 'city',
															'content'    => $row['city']
													)
											)
									)
							);
							$contact++;
							if ($r['duplicated']==1)
							{
								$duplicated++;
							}
							else if ($r['queued']==1)
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
				$client = new jsonRPCClient('http://api2.getresponse.com');
				$result = $client->get_campaigns( $api_key );
				foreach ($result as $k) {
					$results[] = array(
							'id' => $k['name'],
							'text' => $k['name'],
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

		$client = new jsonRPCClient('http://api2.getresponse.com');
		$result = NULL;

		try {
			$result = $client->get_campaigns(
					MODULE_ORDER_TOTAL_GETRESPONSE_API_KEY,
					array (
							'name' => array (
									'EQUALS' => MODULE_ORDER_TOTAL_GETRESPONSE_CAMPAIGN
							)
					)
			);

			if (empty($result)) {
				throw new Exception(
						'Missing GetResponse campaign: ' .
						MODULE_ORDER_TOTAL_GETRESPONSE_CAMPAIGN
				);
			}

            $campaign_id = array_pop(array_keys($result));

            $check_cycle_day = $client->get_contact(
                MODULE_ORDER_TOTAL_GETRESPONSE_API_KEY,
                array (
                    'campaigns' => array($campaign_id),
                    'email' => array (
                        'EQUALS' => $order->customer['email_address']
                    )
                )
            );

            $cycle_day = (!empty($check_cycle_day) and isset($check_cycle_day[$campaign_id]['cycle_day'])) ? "'cycle_day' => ".$check_cycle_day[$campaign_id]['cycle_day']."," : '0';

            $client->add_contact(
					MODULE_ORDER_TOTAL_GETRESPONSE_API_KEY,
					array (
							'campaign'  => $campaign_id,
							'name'      => $order->customer['firstname'] . ' ' . $order->customer['lastname'],
							'email'     => $order->customer['email_address'],
							'cycle_day' => $cycle_day,
							'customs' => array(
									array(
											'name'       => 'ref',
											'content'    => STORE_NAME
									),
									array(
											'name'       => 'telephone',
											'content'    => $order->customer['telephone']
									),
									array(
											'name'       => 'country',
											'content'    => $order->customer['country']['title']
									),
									array(
											'name'       => 'city',
											'content'    => $order->customer['city']
									)
							)
					)
			);
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
				'Click <a href=\"http://www.getresponse.com/my_api_key.html\">HERE</a> to get your API key',
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
