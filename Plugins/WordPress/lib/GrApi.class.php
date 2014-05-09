<?php
/**
 * GrApi.class.php
 *
 * @author Grzeogrz Struczynski <grzegorz.struczynski@implix.com>
 * http://getresponse.com
 */
class GetResponseIntegration
{
	public $apiKey;
	private $apiUrl = 'http://api2.getresponse.com';

	public function __construct($apiKey = null) {

		if (is_null($apiKey)) {
			return array('type' => 'error', 'msg' => 'API key must be supplied');
		}

		$this->apiKey = $apiKey;
	}

	public function ping() {
		$request  = $this->request('ping');
		$response = $this->execute($request);
		return $response;
	}

	public function getCampaigns() {
		$request  = $this->request('get_campaigns');
		$response = $this->execute($request);
		if ( !is_array($response) && !$response->error) {
			return $response->result;
		}
	}

	public function getWebforms($campaigns_id = array()) {
		$request  = $this->request('get_webforms', array('campaigns' => $campaigns_id));
		$response = $this->execute($request);
		if ( !is_array($response) && !$response->error) {
			return $response->result;
		}
	}

	public function getWebform($webform_id) {
		$request  = $this->request('get_webform', array('webform' => $webform_id));
		$response = $this->execute($request);
		if ( !is_array($response) && !$response->error) {
			return $response->result;
		}
	}

	public function getContact($email_address, $campaign_id) {
		$request  = $this->request('get_contacts', array ( 'email' => array( 'EQUALS' => $email_address), 'campaigns' => array($campaign_id) ));
		$response = $this->execute($request);
		if ( !is_array($response) && !$response->error) {
			return $response->result;
		}
	}

	public function setContactCustoms($contact_id, $customs) {
		$request  = $this->request('set_contact_customs', array('contact' => $contact_id, 'customs' => $customs));
		$response = $this->execute($request);
		if ( !is_array($response) && !$response->error) {
			return $response->result;
		}
	}

	public function addContact($campaign, $name, $email, $cycle_day = 0, $customs = array()) {
		$c = array();
		$params = array(
			'campaign' => $campaign,
			'name' => $name,
			'email' => $email, 
			'cycle_day' => $cycle_day, 
			'ip' => $_SERVER['REMOTE_ADDR']
		);

		// default ref
		$c[] = array('name' => 'ref', 'content' => 'wordpress');
		if ( !empty($customs))  {
			foreach($customs as $key => $val)  {
				if (!empty($key) && !empty($val))
					$c[] = array('name' => $key, 'content' => $val);
			}
		}
		$params['customs'] = $c;

		$request  = $this->request('add_contact', $params);
		$response = $this->execute($request);

		// contact already added to campaign
		if ( !empty($customs) && !is_array($response) && isset($response->error) && preg_match('[Contact already added to target campaign]', $response->error->message)) {
			$contact_id = $this->getContact($email, $campaign);
			$contact_id = array_pop(array_keys((array)$contact_id));
			if ($contact_id && !empty($params['customs'])) {
				$this->setContactCustoms($contact_id, $params['customs']);
			}
		}
		else {
			return $response->result;
		}
	}

	private function request($method, $params = null, $id = null) {
		$array = array($this->apiKey);
		if ( !is_null($params)) {
			$array[1] = $params;
		}
		$request = json_encode(array('method' => $method, 'params' => $array, 'id' => $id));
		return $request;
	}

	private function execute($request) {
		$ch = curl_init($this->apiUrl);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
		curl_setopt($ch, CURLOPT_HEADER, 'Content-type: application/json');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = json_decode(curl_exec($ch));
		if (curl_error($ch)) {
			return array('type' => 'error', 'msg' => curl_error($ch));
		}
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ( !(($httpCode == '200') || ($httpCode == '204'))) {
			return array('type' => 'error', 'msg' => 'API call failed. Server returned status code ' . $httpCode);
		}

		curl_close($ch);
		return $response;
	}
} // class GetResponseIntegration