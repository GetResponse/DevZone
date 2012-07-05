<?php
class ControllerModuleGetresponse extends Controller {
	
	private $error = array(); 
	private $gr_apikey;
	private $gr_apikey_url = 'http://api2.getresponse.com';
	private $campaign;
	
	public function index() {   
		$this->load->language('module/getresponse');
		$this->document->setTitle($this->language->get('heading_title'));
		
		// module settings to read and/or write config 
		$this->load->model('setting/setting');					
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('getresponse', $this->request->post);				
			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}
				
		// language
		$this->data['heading_title'] = $this->language->get('heading_title');		
		$this->data['text_module'] = $this->language->get('text_module');
		$this->data['text_success'] = $this->language->get('text_success');
		$this->data['text_none'] = $this->language->get('text_none');
		$this->data['text_yes'] = $this->language->get('text_yes');
		$this->data['text_no'] = $this->language->get('text_no');
		$this->data['entry_title'] = $this->language->get('entry_title');
		$this->data['entry_export'] = $this->language->get('entry_export');
		$this->data['entry_apikey'] = $this->language->get('entry_apikey');	
		$this->data['entry_campaign'] = $this->language->get('entry_campaign');
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_export'] = $this->language->get('button_export');	
		
		$this->data['token'] = $this->session->data['token'];

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		// get settings from settins or post
		if (isset($this->request->post['config_apikey'])) {
			$this->apikey = $this->request->post['config_apikey'];
		} else {
			$this->apikey = $this->config->get('config_apikey');
		}
		
		if (isset($this->request->post['config_enable_module'])) {
			$this->enable_module = $this->request->post['config_enable_module'];
		} else {
			$this->enable_module = $this->config->get('config_enable_module');
		}
		
		if (isset($this->request->post['config_campaign'])) {
			$this->campaign = $this->request->post['config_campaign'];
		} else {
			$this->campaign = $this->config->get('config_campaign');
		}
		
		// set settings in view
		$this->data['config_apikey'] = $this->apikey;
		$this->data['config_campaign'] = $this->campaign;
		
		// breadcrumbs
  		$this->data['breadcrumbs'] = array();
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);		
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/getresponse', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
   		// actions
		$this->data['action'] = $this->url->link('module/getresponse', 'token=' . $this->session->data['token'], 'SSL');		
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['modules'] = array();
		
		if (isset($this->request->post['getresponse_module'])) {
			$this->data['modules'] = $this->request->post['getresponse_module'];
		} elseif ($this->config->get('getresponse_module')) { 
			$this->data['modules'] = $this->config->get('getresponse_module');
		}	

		// load lang
		$this->load->model('localisation/language');		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		// load template
		$this->template = 'module/getresponse.tpl';
		$this->children = array(
			'common/header',
			'common/footer',
		);
				
		$this->response->setOutput($this->render());
	}
	
	// validate permission
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/getresponse')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
	
	// get list of campaning
	public function campaning() {
		
		$this->gr_apikey = $this->request->post['api_key'];
		$results[] = array(
				'id' => 0,
				'text' => '-- none --',
		);
		
		try {
			$this->load->library('jsonRPCClient');
			$client = new jsonRPCClient($this->gr_apikey_url);
			$result = $client->get_campaigns($this->gr_apikey);
			foreach ($result as $k) {
				$results[] = array(
						'id' => $k['name'],
						'text' => $k['name'],
				);
			}
		}
		catch (Exception $e) {
			$this->data['error_warning'] = 'Error!' .$e;
		}
		$this->response->setOutput(json_encode($results));
	}
	
	// export contacts to campaign
	public function export() {
		
		$this->load->model('module/getresponse');
		$contacts = $this->model_module_getresponse->getContacts();
		
		$this->gr_apikey = $this->request->post['api_key'];
		
		$this->campaign = $this->request->post['campaign'];		

		$this->load->library('jsonRPCClient');
		try {
			$client = new jsonRPCClient($this->gr_apikey_url);
			$result = $client->get_campaigns($this->gr_apikey, array ('name' => array ('EQUALS' => $this->campaign)));
		}
		catch (Exception $e) {
			$this->data['error_warning'] = 'Error!' .$e;
		}		
		
		if (empty($result)) {
			$results = array( 'status' => 2, 'response' =>'  No campaign with the specified name.' );
		} else {
			$duplicated = 0;
			$queued = 0;
			$contact = 0;
			foreach ($contacts as $row)
			{
				$r = $client->add_contact($this->gr_apikey,
						array (
								'campaign'  => key($result),
								'name'      => $row['firstname'] . ' ' . $row['lastname'],
								'email'     => $row['email'],
								'cycle_day' => '0',
								'customs' => array(
										array(	'name'       => 'ref',
												'content'    => 'OpenCart'
										),
										array(	'name'       => 'telephone',
												'content'    => $row['telephone']
										),
										array(	'name'       => 'country',
												'content'    => $row['country']
										),
										array(	'name'       => 'city',
												'content'    => $row['city']
										)
								)
						)
				);
				$contact++;
				if  (array_key_exists('queued',$r)) {	$queued++;	}
				else if (array_key_exists('duplicated',$r))  { $duplicated++; }	
			}
			
			$results = array('status' => 1,	'response' =>'  Export completed. Contacts: ' .$contact. '. Queued:' .$queued. '. Updated: ' .$duplicated. '.');
		}
	$this->response->setOutput(json_encode($results));
	}	
}
?>