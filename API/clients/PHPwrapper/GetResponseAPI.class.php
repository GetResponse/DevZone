<?php

/**
 * GetResponsePHP is a PHP5 implementation of the GetResponse API
 * @internal This wrapper is incomplete and subject to change.
 * @author Ben Tadiar <ben@bentadiar.co.uk>
 * @copyright Copyright (c) 2010 Assembly Studios
 * @link http://www.assemblystudios.co.uk
 * @package GetResponsePHP
 * @version 0.1
 */

/**
 * GetResponse Class
 * @package GetResponsePHP
 */
class GetResponse
{	
	/**
	 * GetResponse API key
	 * http://www.getresponse.com/my_api_key.html
	 * @var string
	 */
	public $apiKey = 'PASS_API_KEY_WHEN_INSTANTIATING_CLASS';
	
	/**
	 * GetResponse API URL
	 * @var string
	 * @access private
	 */
	private $apiURL = 'http://api2.getresponse.com';
	
	/**
	 * Text comparison operators used to filter results
	 * @var array
	 * @access private
	 */
	private $textOperators = array('EQUALS', 'NOT_EQUALS', 'CONTAINS', 'NOT_CONTAINS', 'MATCHES');
	
	/**
	 * Check cURL extension is loaded and that an API key has been passed
	 * @param string $apiKey GetResponse API key
	 * @return void
	 */
	public function __construct($apiKey = null)
	{
		if(!extension_loaded('curl')) trigger_error('GetResponsePHP requires PHP cURL', E_USER_ERROR);
		if(is_null($apiKey)) trigger_error('API key must be supplied', E_USER_ERROR);
		$this->apiKey = $apiKey;
	}
	
	/**
	 * Test connection to the API, returns "pong" on success
	 * @return string
	 */
	public function ping()
	{
		$request  = $this->prepRequest('ping');
		$response = $this->execute($request);
		return $response->ping;
	}
	
	/**
	 * Get basic user account information
	 * @return object
	 */
	public function getAccountInfo()
	{
		$request  = $this->prepRequest('get_account_info');
		$response = $this->execute($request);
		return $response;
	}
	
	/**
	 * Get a list of active campaigns, optionally filtered
	 * @param string $operator Comparison operator
	 * @param string $comparison Text/expression to compare against
	 * @return object 
	 */
	public function getCampaigns($operator = 'CONTAINS', $comparison = '%')
	{
		$params = null;
		if(in_array($operator, $this->textOperators)) $params = array('name' => array($operator => $comparison));
		$request  = $this->prepRequest('get_campaigns', $params);
		$response = $this->execute($request);
		return $response;
	}
	
	/**
	 * Return a campaign by ID
	 * @param string $id Campaign ID
	 * @return object
	 */
	public function getCampaignByID($id)
	{
		$request  = $this->prepRequest('get_campaign', array('campaign' => $id));
		$response = $this->execute($request);
		return $response;
	}
	
	/**
	 * Return a list of users, optionally filtered by multiple conditions
	 * @todo Implement all conditions, this is unfinished
	 * @param array|null $campaigns Optional argument to narrow results by campaign ID
	 * @param string $operator
	 * @param string $comparison
	 * @return object
	 */
	public function getContacts($campaigns = null, $operator = 'CONTAINS', $comparison = '%')
	{
		$params = null;
		if(is_array($campaigns)) $params['campaigns'] = $campaigns;
		$params['name'] = $this->prepTextOp($operator, $comparison);
		$request  = $this->prepRequest('get_contacts', $params);
		$response = $this->execute($request);
		return $response;
	}
	
	/**
	 * Return a contact by ID
	 * @param string $id User ID
	 * @return object
	 */
	public function getContactByID($id)
	{
		$request  = $this->prepRequest('get_contact', array('contact' => $id));
		$response = $this->execute($request);
		return $response;
	}
	
	/**
	 * Set a contact name
	 * @param string $id User ID
	 * @return object
	 */
	public function setContactName($id, $name)
	{
		$request  = $this->prepRequest('set_contact_name', array('contact' => $id, 'name' => $name));
		$response = $this->execute($request);
		return $response;
	}
	
	/**
	 * Return a contacts custom information
	 * @param string $id User ID
	 * @return object
	 */
	public function getContactCustoms($id)
	{
		$request  = $this->prepRequest('get_contact_customs', array('contact' => $id));
		$response = $this->execute($request);
		return $response;
	}
	
	/**
	 * Set custom contact information
	 * $customs is an associative array, the keys of which should correspond to the
	 * custom field name you wish to add/modify/remove.
	 * Actions: added if not present, updated if present, removed if value is null
	 * @todo Implement multivalue customs.
	 * @param string $id User ID
	 * @param array $customs
	 * @return object
	 */
	public function setContactCustoms($id, $customs)
	{
		if(!is_array($customs)) trigger_error('Second argument must be an array', E_USER_ERROR);
		foreach($customs as $key => $val) $params[] = array('name' => $key, 'content' => $val);
		$request  = $this->prepRequest('set_contact_customs', array('contact' => $id, 'customs' => $params));
		$response = $this->execute($request);
		return $response;
	}
	
	/**
	 * Return a contacts GeoIP
	 * @param string $id User ID
	 * @return object
	 */
	public function getContactGeoIP($id)
	{
		$request  = $this->prepRequest('get_contact_geoip', array('contact' => $id));
		$response = $this->execute($request);
		return $response;
	}
	
	/**
	 * List dates when the messages were opened by contacts
	 * @param string $id User ID
	 * @return object
	 */
	public function getContactOpens($id)
	{
		$request  = $this->prepRequest('get_contact_opens', array('contact' => $id));
		$response = $this->execute($request);
		return $response;
	}
	
	/**
	 * List dates when the links in messages were clicked by contacts
	 * @param string $id User ID
	 * @return object
	 */
	public function getContactClicks($id)
	{
		$request  = $this->prepRequest('get_contact_clicks', array('contact' => $id));
		$response = $this->execute($request);
		return $response;
	}
	
	/**
	 * Add contact to the specified list (Requires email verification by contact)
	 * The return value of this function will be "queued", and on subsequent
	 * submission of the same email address will be "duplicated".
	 * @param string $campaign Campaign ID
	 * @param string $name Name of contact
	 * @param string $email Email address of contact
	 * @param string $action Standard, insert or update
	 * @param int $cycle_day
	 * @param array $customs
	 * @return object
	 */
	public function addContact($campaign, $name, $email, $action = 'standard', $cycle_day = 0, $customs = array())
	{
		$params = array('campaign' => $campaign, 'action' => $action, 'name' => $name,
						'email' => $email, 'cycle_day' => $cycle_day, 'ip' => $_SERVER['REMOTE_ADDR']);
		if(!empty($customs)) {
			foreach($customs as $key => $val) $c[] = array('name' => $key, 'content' => $val);
			$params['customs'] = $c;
		}
		$request  = $this->prepRequest('add_contact', $params);
		$response = $this->execute($request);
		return $response;
	}
	
	/**
	 * Delete a contact
	 * @param string $id
	 * @return object
	 */
	public function deleteContact($id)
	{
		$request  = $this->prepRequest('delete_contact', array('contact' => $id));
		$response = $this->execute($request);
		return $response;
	}
	
	/**
	 * Get blacklist masks on account level
	 * Account is determined by API key
	 * @return object
	 */
	public function getAccountBlacklist()
	{
		$request  = $this->prepRequest('get_account_blacklist');
		$response = $this->execute($request);
		return $response;
	}
	
	/**
	 * Adds blacklist mask on account level
	 * @param string $mask
	 * @return object
	 */
	public function addAccountBlacklist($mask)
	{
		$request  = $this->prepRequest('add_account_blacklist', array('mask' => $mask));
		$response = $this->execute($request);
		return $response;
	}
	
	/**
	 * Delete blacklist mask on account level
	 * @param string $mask
	 * @return object
	 */
	public function deleteAccountBlacklist($mask)
	{
		$request  = $this->prepRequest('delete_account_blacklist', array('mask' => $mask));
		$response = $this->execute($request);
		return $response;
	}
	
	/**
	 * Return a key => value array for text comparison
	 * @param string $operator
	 * @param mixed $comparison
	 * @return array
	 * @access private
	 */
	private function prepTextOp($operator, $comparison)
	{
		if(!in_array($operator, $this->textOperators)) trigger_error('Invalid text operator', E_USER_ERROR);
		if($operator === 'CONTAINS') $comparison = '%'.$comparison.'%';
		return array($operator => $comparison);
	}
	
	/**
	 * Return array as a JSON encoded string
	 * @param string $method API method to call
	 * @param array  $params Array of parameters
	 * @return string JSON encoded string
	 * @access private
	 */
	private function prepRequest($method, $params = null)
	{
		$array = array($this->apiKey);
		if(!is_null($params)) $array[1] = $params;
		$request = json_encode(array('method' => $method, 'params' => $array));
		return $request;
	}
	
	/**
	 * Executes an API call
	 * @param string $request JSON encoded array
	 * @return object
	 * @access private
	 */
	private function execute($request)
	{
		$handle = curl_init($this->apiURL);
		curl_setopt($handle, CURLOPT_POST, 1);
		curl_setopt($handle, CURLOPT_POSTFIELDS, $request);
		curl_setopt($handle, CURLOPT_HEADER, 'Content-type: application/json');
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);	  			   
		$response = json_decode(curl_exec($handle));
		if(curl_error($handle)) trigger_error(curl_error($handle), E_USER_ERROR);
		$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
		if($httpCode != '200') trigger_error('API call failed. Server returned status code '.$httpCode, E_USER_ERROR);
		curl_close($handle);
		if(!$response->error) return $response->result;
	}
}

?>
