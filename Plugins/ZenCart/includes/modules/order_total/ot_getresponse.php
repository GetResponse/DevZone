<?php
/**
 * Add contact to your GetResponse campaign when order is made.
 *
 * @author Sylwester Okrój
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
					FROM customers cu
					JOIN address_book bo ON cu.customers_id = bo.customers_id
					JOIN countries ca ON ca.countries_id = bo.entry_country_id
					WHERE cu.customers_newsletter = 1
					');

			if ($query->RecordCount() > 0)
			{
				// sprawdzam polaczenie do api i kampanie
				try {
					$client = new jsonRPCClient('http://api2.getresponse.com');
					$result = $client->get_campaigns(MODULE_ORDER_TOTAL_GETRESPONSE_API_KEY,
							array ('name' => array ('EQUALS' => MODULE_ORDER_TOTAL_GETRESPONSE_CAMPAIGN))
					);

					if (empty($result))
					{
						$json = array( 'status' => 2, 'response' =>'No campaign with the specified name' );
					}
					else {
						$duplicated = 0;
						$queued = 0;
						$contact = 0;
						while (!$query->EOF) {
							$r = $client->add_contact(MODULE_ORDER_TOTAL_GETRESPONSE_API_KEY,
									array (
											'campaign'  => array_pop(array_keys($result)),
											'name'      => $query->fields['firstname'] . ' ' . $query->fields['lastname'],
											'email'     => $query->fields['email'],
											'cycle_day' => '0',
											'customs' => array(
													array(
															'name'       => 'ref',
															'content'    => STORE_NAME
													),
													array(
															'name'       => 'telephone',
															'content'    => $query->fields['telephone']
													),
													array(
															'name'       => 'country',
															'content'    => $query->fields['country']
													),
													array(
															'name'       => 'city',
															'content'    => $query->fields['city']
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

			$client->add_contact(
					MODULE_ORDER_TOTAL_GETRESPONSE_API_KEY,
					array (
							'campaign'  => array_pop(array_keys($result)),
							'name'      => $order->customer['firstname'] . ' ' . $order->customer['lastname'],
							'email'     => $order->customer['email_address'],
							'cycle_day' => '0',
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

		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('API key', 'MODULE_ORDER_TOTAL_GETRESPONSE_API_KEY', '', 'Click <a href=\http://www.getresponse.com/my_api_key.html\>HERE</a> to get your API key',  '6', '2', now())");

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
