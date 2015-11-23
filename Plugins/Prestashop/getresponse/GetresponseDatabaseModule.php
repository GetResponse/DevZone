<?php
/*
 *This Module is used when a query to either the PrestaShop Database.
 * @author	 Grzegorz Struczynski <gstruczynski@implix.com>
 * @copyright  GetResponse
 * @license	http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

require_once(dirname(__FILE__).'/GetresponseErrorModule.php');
require_once(dirname(__FILE__).'/getresponse_api/jsonRPCClient.php');

/*
 * Class is used to calls to the PrestaShop Database
 *
 * Functions Create, Update, Insert
 * @uses Database instance [ie DB::getInstance()]
 */
class DbConnection
{

	public function __construct($database)
	{
		$this->db = $database;
		$this->obj = 1;
		$this->api_url = 'http://api2.getresponse.com';
	}

	/******************************************************************/
	/** Get Methods ***************************************************/
	/******************************************************************/

	public function getSettings()
	{
		$sql = 'SELECT
					api_key, active_subscription, update_address, campaign_id
				FROM
					'._DB_PREFIX_.'getresponse_settings
				';

		if ($results = $this->db->ExecuteS($sql))
			return $results[0];
	}

	public function getWebformSettings()
	{
		$sql = 'SELECT
					webform_id, active_subscription, sidebar, style
				FROM
					'._DB_PREFIX_.'getresponse_webform
				';

		if ($results = $this->db->ExecuteS($sql))
			return $results[0];
	}

	public function getCampaigns($api_key)
	{
		if (empty($api_key))
			return false;

		$campaigns[] = array(
			'id' => 0,
			'name' => 'Select campaign'
		);

		try
		{
			$client = new JsonRpcClient($this->api_url);
			$results = $client->get_campaigns( $api_key );
			foreach ($results as $id => $info)
			{
				$name = isset($info['name']) ? $info['name'] : $info['description'];
				$campaigns[] = array(
					'id' => $id,
					'name' => $name,
				);
			}

			return $campaigns;
		}
		catch (Exception $e)
		{
			return false;
		}
	}

	public function checkModuleStatus($module)
	{
		if (empty($module))
			return false;

		$sql = 'SELECT
					active
				FROM
					'._DB_PREFIX_.'module
				WHERE
					name = "'.pSQL($module).'"
				';

		if ($results = $this->db->ExecuteS($sql))
		{
			if (isset($results[0]['active']) && $results[0]['active'] == 1)
				return 'active';
		}

		return false;
	}

	public function getContacts($email = null, $newsletter_guests = null)
	{
		$where = !empty($email) ? " AND cu.email = '".$email."'" : null;

		if (!empty($newsletter_guests))
		{
			$blocknewsletter = $this->checkModuleStatus('blocknewsletter');

			if ($blocknewsletter == 'active')
			{
				$ng_where = 'UNION SELECT
						"Friend" as firstname,
						"" as lastname,
						n.email as email,
						"" as company,
						"" as birthday,
						"" as address1,
						"" as address2,
						"" as postcode,
						"" as city,
						"" as phone,
						"" as country
					FROM
						'._DB_PREFIX_.'newsletter n
					WHERE
						n.active = 1
				';
			}
		}

		$sql = 'SELECT
					cu.firstname as firstname,
					cu.lastname as lastname,
					cu.email as email,
					cu.company as company,
					cu.birthday as birthday,
					ad.address1 as address1,
					ad.address2 as address2,
					ad.postcode as postcode,
					ad.city as city,
					ad.phone as phone,
					co.iso_code as country
				FROM
					'._DB_PREFIX_.'customer as cu
				JOIN
					'._DB_PREFIX_.'address ad ON cu.id_customer = ad.id_customer
				JOIN
					'._DB_PREFIX_.'country co ON ad.id_country = co.id_country
				WHERE
					cu.newsletter = 1'.$where.'
				AND
					ad.active = 1
				'.$ng_where.'
				';

		if ($results = $this->db->ExecuteS($sql))
		{
			if (!empty($where))
				return $results[0];
			else
				return $results;
		}
	}

	public function getCustoms($default = null)
	{
		$where = !empty($default) ? " WHERE `default` = '".$default."'" : null;

		$sql = 'SELECT
					*
				FROM
					'._DB_PREFIX_.'getresponse_customs'.
				$where;

		if ($results = $this->db->ExecuteS($sql))
			return $results;
	}

	/******************************************************************/
	/** Update Methods ************************************************/
	/******************************************************************/

	public function updateApikey($apikey)
	{
		$data = array('api_key' => pSQL($apikey));

		if ($this->db->autoExecute(_DB_PREFIX_.'getresponse_settings', $data, 'UPDATE', 'id = 1'))
			return true;

		return false;
	}

	public function updateWebformSettings($webform_id, $active_subscription, $sidebar, $style)
	{
		$data = array('webform_id' => pSQL($webform_id),
			'active_subscription' => pSQL($active_subscription),
			'sidebar' => pSQL($sidebar),
			'style' => pSQL($style)
		);

		if ($this->db->autoExecute(_DB_PREFIX_.'getresponse_webform', $data, 'UPDATE', 'id = 1'))
			return true;

		return false;
	}

	public function updateSettings($active_subscription, $campaign_id, $update_address)
	{
		$data = array('active_subscription' => pSQL($active_subscription), 'campaign_id' => pSQL($campaign_id), 'update_address' => pSQL($update_address));

		if ($this->db->autoExecute(_DB_PREFIX_.'getresponse_settings', $data, 'UPDATE', 'id = 1'))
			return true;

		return false;
	}

	public function updateCustoms($customs)
	{
		$settings_customs = $this->getCustoms();

		foreach ($settings_customs as $sc)
			$allowed[] = $sc['custom_value'];

		foreach ($allowed as $a)
		{
			if (in_array($a, array_keys($customs)))
			{
				$sql = 'UPDATE
							'._DB_PREFIX_.'getresponse_customs
						SET
							custom_name = '.mysql_real_escape_string(pSQL($customs[$a])).'
						WHERE
							custom_value = '.mysql_real_escape_string(pSQL($a));

				$this->db->ExecuteS($sql);
			}
		}
	}

	/******************************************************************/
	/** API Methods *****************************&*********************/
	/******************************************************************/

	public function exportSubscriber($apikey, $campaign_id, $customers)
	{
		if (empty($_POST))
			return array('status'=>'0' , 'message' => 'Request error');

		$duplicated = 0;
		$queued = 0;
		$contact = 0;
		$notadded = 0;

		if (!empty($customers))
		{
			foreach ($customers as $customer)
			{
				$customs = $this->mapCustoms($customer, $_POST, 'export');

				if (!empty($customs['custom_error']) && $customs['custom_error'] == true)
				{
					return array(
						'status' => '0' ,
						'message' => 'Incorrect field name: "'.$customs['custom_message'].
						'". <br/>The field name contains invalid characters. Only alphanumeric characters and the underscore symbol are allowed.');
				}

				$r = $this->addContactToGR($apikey, $campaign_id, $customer['firstname'], $customer['lastname'], $customer['email'], $customs);

				$contact++;

				if ($r === false)
					$notadded++;
				else if (isset($r['duplicated']) && $r['duplicated'] == 1)
					$duplicated++;
				else if (isset($r['queued']) && $r['queued'] == 1)
					$queued++;
			}
		}

		return array(
			'status' => '1' ,
			'message' => 'Export completed. <br />
				Contacts: '.$contact.'.
				Queued:'.$queued.'.
				Updated: '.$duplicated.'.
				Not added: '.$notadded.'.'
			);
	}

	private function mapCustoms($customer, $customer_post, $type)
	{
		$fields = array();

		//get fields form db
		$custom_fields = $this->getCustoms('no');

		// make fields array
		if (!empty($custom_fields))
		{
			foreach ($custom_fields as $cf)
				$fields[$cf['custom_value']] = $cf['custom_name'];
		}

		// default reference custom
		$customs[] = array(
			'name'	   => 'ref',
			'content'	=> 'prestashop'
		);

		// for fields from DB
		if (!empty($fields))
		{
			foreach ($fields as $field_key => $field_vaule)
			{
				$fv = $field_vaule;
				//compose address custom field
				if ($field_key == 'address1')
					$address_name = $field_vaule;

				// for POST actions (export or update (order))
				if (!empty($customer_post))
				{
					if ($type != 'order')
					{
						$fv = $customer_post[$field_key];
						//update address custom field
						$address_name = !empty($customer_post['address1']) ? $customer_post['address1'] : null;
					}
				}

				$exportgetresponse_settings = Tools::getValue('exportgetresponse_settings');

				// for export change field value
				if (!empty($exportgetresponse_settings))
				{
					// add prefix for export fields
					$new_fk = 'export_'.$field_key;
					$fv = $customer_post[$new_fk];
					//update address custom field
					$address_name = !empty($customer_post['export_address1']) ? $customer_post['export_address1'] : null;

					$field_vaule = $fv;
				}

				// allowed custom and non empty
				if (in_array($field_key, array_keys($customer)) == true && (!empty($fv) && !empty($customer[$field_key])))
				{
					// validation for custom field name
					if (false == preg_match('/^[_a-zA-Z0-9]{2,32}$/m', Tools::stripslashes(($fv))))
						return array('custom_error'=>'true','custom_message'=>$fv);

					// compose address value address+address2
					if ($fv == 'address1')
					{
						$address2 = !empty($customer['address2']) ? ' '.$customer['address2'] : '';

						$customs[] = array(
							'name'	   => $address_name,
							'content'	=> $customer['address1'].$address2
						);
					}
					// others custom fields
					else
					{
						$customs[] = Array(
							'name'	   => $field_vaule,
							'content'	=> $customer[$field_key]
						);
					}
				}
			}
		}

		return $customs;
	}

	public function addSubscriber($params, $apikey, $campaign_id, $action)
	{
		$allowed = array('order', 'create');
		$prefix = 'customer';

		//add_contact
		if (!empty($action) && in_array($action, $allowed) == true && $action == 'create')
		{
			$prefix = 'newCustomer';
			$customs = $this->mapCustoms($params[$prefix], null, 'create');
		}
		//update_contact
		else
		{
			$contact = $this->getContacts($params[$prefix]->email, null);
			$customs = $this->mapCustoms($contact, $_POST, 'order');
		}

		if (isset($params[$prefix]->newsletter) && $params[$prefix]->newsletter == 1)
			$this->addContactToGR($apikey, $campaign_id, $params[$prefix]->firstname, $params[$prefix]->lastname, $params[$prefix]->email, $customs);

		return true;
	}

	public function addContactToGR($apikey, $campaign_id, $first_name = null, $last_name = null, $email, $customs)
	{
		// required params
		if (empty($apikey) || empty($campaign_id) || empty($email))
			return false;

		try
		{
			$client = new JsonRpcClient($this->api_url);

			$name = !empty($first_name) ? $first_name.' '.$last_name : 'Friend';
			$cycle_day = '0';

			$check_cycle_day = $client->get_contacts(
				$apikey,
				array (
					'campaigns' => array($campaign_id),
					'email' => array (
						'EQUALS' => $email)
				)
			);

			if (!empty($check_cycle_day) && isset($check_cycle_day[$campaign_id]['cycle_day']))
				$cycle_day = $check_cycle_day[$campaign_id]['cycle_day'];

			$params = array (
				'campaign'  => $campaign_id,
				'name'	  => $name,
				'email'	 => $email,
				'cycle_day' => $cycle_day,
				'customs'   => $customs
			);

			$result = $client->add_contact($apikey, $params);

			return $result;
		}
		catch (Exception $e)
		{
			return false;
		}
	}

}