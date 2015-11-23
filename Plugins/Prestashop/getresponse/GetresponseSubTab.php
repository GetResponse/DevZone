<?php

/*
 * This module integrate GetResponse and PrestaShop
 * Allows subscribe via checkout page and export your contacts to GetResponse's campaign.
 * @author	 Grzegorz Struczynski <gstruczynski@implix.com>
 * @copyright  GetResponse
 * @license	http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */


if (!class_exists('GetresponseDatabaseModule.php'))
	require_once(dirname(__FILE__).'/../../modules/getresponse/GetresponseDatabaseModule.php');

class GetresponseSubTab extends AdminTab
{
	public function __construct()
	{
		// structure
		$this->table		 = 'getresponse_settings';
		$this->class_name	 = 'GetresponseSubTabClass';
		$this->edit		  = false;
		$this->delete		= true;
		$this->identifier	= 'api_key';
		$this->webform_img   = '/img/t/Getresponse_webform.gif';

		$instance			= Db::getInstance();
		$this->db			= new DbConnection($instance);

		parent::__construct();
	}

	/*
	 * Main view
	 */
	public function displayList()
	{
		global $currentIndex;

		$url = $currentIndex.'&token='.$this->token;
		$msg = new GetresponseError();

		$updategetresponse_settings = Tools::getValue('updategetresponse_settings');
		$exportgetresponse_settings = Tools::getValue('exportgetresponse_settings');
		$ordergetresponse_settings  = Tools::getValue('ordergetresponse_settings');
		$webformgetresponse_settings  = Tools::getValue('webformgetresponse_settings');

		if (!empty($updategetresponse_settings))
		{
			$api_key = Tools::getValue('api_key');

			if (!empty($api_key))
			{
				$c = $this->db->getCampaigns($api_key);
				if (is_array($c))
				{
					$this->db->updateApikey($api_key);
					$this->apikey = $api_key;
					echo $msg->success('API Key update successful');
				}
				else
					echo $msg->errorMsg('Wrong API key');
			}
			else
				echo $msg->errorMsg('Api Key field can\'t be empty');
		}
		else if (!empty($exportgetresponse_settings))
		{
			$settings = $this->db->getSettings();

			if (!empty($settings))
				$this->apikey = $settings['api_key'];
			else
				echo $msg->errorMsg('Wrong API Key');

			$campaign = Tools::getValue('campaign');

			if ((!empty($campaign) && $campaign == '0'))
				echo $msg->errorMsg('No campaign selected');
			else
			{
				$newsletter_guests = false;

				$ng = Tools::getValue('newsletter_guests');

				if (!empty($ng))
					$newsletter_guests = true;

				$contacts = $this->db->getContacts(null, $newsletter_guests);

				if (empty($contacts))
					echo $msg->errorMsg('No contacts to export');
				else
				{
					$add = $this->db->exportSubscriber($this->apikey, $campaign, $contacts);

					if ($add['status'] == 1)
						echo $msg->success($add['message']);
					else
						echo $msg->errorMsg($add['message']);
				}
			}
		}
		else if (!empty($ordergetresponse_settings))
		{
			$settings = $this->db->getSettings();

			if (!empty($settings))
				$this->apikey = $settings['api_key'];
			else
				echo $msg->errorMsg('Wrong API Key');

			$order_campaign = Tools::getValue('order_campaign');
			$order_status   = Tools::getValue('order_status');
			$update_address = Tools::getValue('update_address');

			if (!empty($order_campaign) && $order_campaign != '0' && !empty($order_status))
			{
				$update_address = empty($update_address) ? 'no' : $update_address;

				$this->db->updateSettings($order_status, $order_campaign, $update_address);
				$this->db->updateCustoms($_POST);

				echo $msg->success('Settings update successful');
			}
			else if (!empty($order_campaign) && $order_campaign == '0')
				echo $msg->errorMsg('No campaign selected');

		}
		else if (!empty($webformgetresponse_settings))
		{
			$webform_id      = Tools::getValue('webform_id');
			$webform_status  = Tools::getValue('webform_status');
			$webform_sidebar = Tools::getValue('webform_sidebar');
			$webform_style   = Tools::getValue('webform_style');

			if ((!empty($webform_id) && $webform_id <= '0'))
				echo $msg->errorMsg('No Web Form ID or incorrect value');
			else
			{
				$this->db->updateWebformSettings($webform_id, $webform_status, $webform_sidebar, $webform_style);
				echo $msg->success('Settings update successful');
			}
		}

		// apikey settings
		$settings  = $this->db->getSettings();
		if (!empty($settings))
			$this->apikey = $settings['api_key'];

		$custom_fields = $this->db->getCustoms();
		if (!empty($custom_fields))
		{
			$new_inputs = '';
			$inputs = '';

			foreach ($custom_fields as $custom_field)
			{
				if ($settings['update_address'] == 'yes')
					$value = !empty($custom_field['custom_name']) ? $custom_field['custom_name'] : '';
				else
					$value = $custom_field['custom_field'];

				if ($custom_field['default'] == 'yes')
				{
					$inputs .= '<input style="margin-bottom:5px" id="'.$custom_field['custom_value'].
						'" name="export_'.$custom_field['custom_value'].'"value="'.$custom_field['custom_field'].
						'" disabled="disabled"></input><span style="color: #AAAAAA"> '.
						Tools::ucfirst($custom_field['custom_field']).'</span><br/>';
					$new_inputs .= '<input style="margin-bottom:5px" id="'.$custom_field['custom_value'].
						'" name="'.$custom_field['custom_value'].'"value="'.$custom_field['custom_field'].
						'" disabled="disabled"></input><span style="color: #AAAAAA"> '.
						Tools::ucfirst($custom_field['custom_field']).'</span><br/>';
				}
				else
				{
					$inputs .= '<input style="margin-bottom:5px" id="'.$custom_field['custom_value'].
						'" name="export_'.$custom_field['custom_value'].'"value="'.$custom_field['custom_field'].'"></input> '.
						Tools::ucfirst($custom_field['custom_field']).'<br/>';
					$new_inputs .= '<input style="margin-bottom:5px" id="'.$custom_field['custom_value'].
						'" name="'.$custom_field['custom_value'].'"value="'.$value.'"></input> '.
						Tools::ucfirst($custom_field['custom_field']).'<br/>';
				}
			}
		}

		echo <<<APIFORM
		<div class="toolbarBox toolbarHead">
		<span style="font-size: 2em; text-shadow:0 1px 0 white;line-height:52px;padding-left:10px">GetResponse Settings & Actions</span>
		</div>
		<form id="form-api" action="$url" method="post" class="width2">
			<fieldset>
				<legend>{$this->l('API Key Settings')}</legend>
					<label>{$this->l('Api Key: ')}</label>
						<div class="margin-form">
							<input id="api_key" name="api_key" value="{$this->apikey}" style="width: 150px"></input>
							<sup>*</sup>
					<br><br>
					<input type="submit" value="{$this->l('Save')}" name="update{$this->table}" class="button" />

					</div><div class="small">
						<sup>*</sup>{$this->l('API Key can be found here: https://app.getresponse.com/my_api_key.html')}
					</div>
			</fieldset>
		<script>
			window.onload = function()
			{
				setTimeout(function ()
				{
					$('.conf').remove();
					$('.error').remove();
				}, 7000);

				if ($('#update_address').is(':checked') == true)
				{
					$('#update_extra').html('<br/><span style="color:black;font-size: 12px">Name your custom fields:<sup>**</sup><br/>$new_inputs</span>');
					$('#update_sup_extra').html('<sup>**</sup>{$this->l('Empty input fields won\'t be updated.')}');
				}

			}
		</script>
APIFORM;

		// export data
		if ($this->apikey)
		{
			$campaigns  = $this->db->getCampaigns($this->apikey);
			$options = '';
			$options2 = '';
			$options3 = '';

			if (!empty($campaigns))
			{
				foreach ($campaigns as $campaign)
				{
					$options .= '<option value="'.$campaign['id'].'">'.$campaign['name'].'</option>';

					$seleted = '';
					$order_campaign = Tools::getValue('order_campaign');

					if ($campaign['id'] == $settings['campaign_id'] || !empty($order_campaign) && $order_campaign == $campaign['id'])
						$seleted = 'selected';

					$options2 .= '<option value="'.$campaign['id'].'"'.$seleted.'>'.$campaign['name'].'</option>';
					$options3 .= '<option value="'.$campaign['id'].'"'.$seleted.'>'.$campaign['name'].'</option>';
				}

				echo <<<EXPORTFORM
			<br/>
				<fieldset>
					<legend>{$this->l('Export Customers')}</legend>
						<label>{$this->l('Select target campaign: ')}</label>
							<div class="margin-form">
								<select id="campaign" name="campaign" style="width: 150px">
									$options
								</select>
						</div>
						<label>{$this->l('Guests who subscribed to newsletter: ')}</label>
							<div class="margin-form">
								<input form="form-api" type="checkbox" name="newsletter_guests" id="newsletter_guests" value="yes"/>
						<br/><br/>

						<div id="extra"></div>

						<input type="submit" value="{$this->l('Export')}" name="export{$this->table}" class="button" />
						</div>
						<div class="small">
							<div id="sup_extra"></div>
						</div>
				</fieldset>

			<br/>
			<script>
			$('#campaign').change(function()
			{
				if(($('#campaign').val() != '0'))
				{
					$('#extra').html('<span style="color:black;font-size: 12px">Name your custom fields:<sup>*</sup><br/>$inputs</span>');
					$('#sup_extra').html('<sup>*</sup>{$this->l('Empty input fields won\'t be added.')}');
				}
				else
				{
					$('#extra,#sup_extra').empty();
				}
			});
			</script>
EXPORTFORM;

				// order form
				$opt_yes = '';
				$opt_no = '';
				$opt_update = '';

				$order_status = Tools::getValue('order_status');
				$update_address = Tools::getValue('update_address');

				if ($settings['active_subscription'] == 'yes' || !empty($order_status) && $order_status == 'yes')
				{
					$opt_yes = 'selected';

					if ($settings['update_address'] == 'yes' || !empty($update_address) && $update_address == 'yes')
						$opt_update = 'checked';
				}
				else
					$opt_no = 'selected';

				echo <<<ORDERFORM
			<br/>
			<fieldset>
					<legend>{$this->l('Subscription via registration page')}</legend>
						<label>{$this->l('Select target campaign: ')}</label>
							<div class="margin-form">
								<select id="order_campaign" name="order_campaign" style="width: 150px">
									$options2
								</select>
						<br/>
						</div>
						<label>{$this->l('Subscription: ')}</label>
							<div class="margin-form">
								<select id="order_status" name="order_status" style="width: 150px">
									<option value="no" {$opt_no}>{$this->l('disabled')}</option>
									<option value="yes" {$opt_yes}>{$this->l('enabled')}</option>
								</select>
								<sup>*</sup>
						<br/>
						</div>
						<label>{$this->l('Update contact data on checkout page:')}</label>
							<div class="margin-form">
								<input form="form-api" type="checkbox" name="update_address" id="update_address" value="yes" {$opt_update}/>
								<div id="update_extra"></div>
						<br>
						<input type="submit" value="{$this->l('Save')}" name="order{$this->table}" class="button" />
						</div>
						<div class="small">
							<sup>*</sup>{$this->l('If update isn\'t selected, only the following data will be imported: firstname, lastname, email.')}
							<br/>
							<div id="update_sup_extra"></div>
						</div>
				</fieldset>

			<br/>
			<script>
			$('#update_address').change(function()
			{
				if($('#update_address').is(':checked') == true)
				{
					$('#update_extra').html('<br/><span style="color:black;font-size: 12px">Name your custom fields:<sup>**</sup><br/>$new_inputs</span>');
					$('#update_sup_extra').html('<sup>**</sup>{$this->l('Empty input fields won\'t be updated.')}');
				}
				else
				{
					$('#update_extra,#update_sup_extra').empty();
				}
			});

			$('#order_status').change(function()
			{
				if($('#order_status').val() == 'no')
				{
					$('#update_extra,update_sup_extra').empty();
					$('#update_address').removeAttr('checked');
				}
			});

			</script>
ORDERFORM;
			}
		}

		$webform_settings  = $this->db->getWebformSettings();

		// order form
		$webform_yes   = '';
		$webform_no    = '';
		$sidebar_left  = '';
		$sidebar_right = '';
		$webform_style = '';
		$presta_style  = '';

		$webform_status  = Tools::getValue('webform_status');
		$webform_stylee  = Tools::getValue('webform_style');
		$webform_sidebar = Tools::getValue('webform_sidebar');

		if ($webform_settings['active_subscription'] == 'yes' || !empty($webform_status) && $webform_status == 'yes')
		{
			$webform_yes = 'selected';

			if ($webform_settings['style'] == 'webform' || !empty($webform_stylee) && $webform_stylee == 'yes')
				$webform_style = 'selected';
			else
				$presta_style = 'selected';

			if ($webform_settings['sidebar'] == 'right' || !empty($webform_sidebar) && $webform_sidebar == 'yes')
				$sidebar_right = 'selected';
			else
				$sidebar_left = 'selected';
		}
		else
			$webform_no = 'selected';

		echo <<<ORDERFORM
			<br/>
			<fieldset>
					<legend>{$this->l('Subscription via Web Form')}</legend>
						<label>{$this->l('Web Form ID: ')}</label>
							<div class="margin-form">
								<input id="webform_id" name="webform_id" value="{$webform_settings['webform_id']}" style="width: 150px"></input>
								<sup>*</sup>
						<br/>
						</div>
						<label>{$this->l('Web Form position: ')}</label>
							<div class="margin-form">
								<select id="webform_sidebar" name="webform_sidebar" style="width: 150px">
									<option value="left" {$sidebar_left}>{$this->l('Left sidebar')}</option>
									<option value="right" {$sidebar_right}>{$this->l('Right sidebar')}</option>
								</select>
						<br/>
						</div>
						<label>{$this->l('Style: ')}</label>
							<div class="margin-form">
								<select id="webform_style" name="webform_style" style="width: 150px">
									<option value="webform" {$webform_style}>{$this->l('Web Form')}</option>
									<option value="prestashop" {$presta_style}>{$this->l('PrestaShop')}</option>
								</select>
						<br/>
						</div>
						<label>{$this->l('Subscription: ')}</label>
							<div class="margin-form">
								<select id="webform_status" name="webform_status" style="width: 150px">
									<option value="no" {$webform_no}>{$this->l('disabled')}</option>
									<option value="yes" {$webform_yes}>{$this->l('enabled')}</option>
								</select>
						<br/><br/>
						<input type="submit" value="{$this->l('Save')}" name="webform{$this->table}" class="button" />
						</div>
						<div class="small">
							<sup>*</sup>{$this->l('You will find your web form ID right in your GetResponse account...')}
							<a href="#webform_info" id="webform_info" style="color:#009DD4"><span id="webform_click">click here to see more</span></a>
							<span id="webform_info2"></span>
							<br/>
							<div id="webform_extra"></div>
						</div>
				</fieldset>
			</form>
			<br/>
			<script>
			$('#webform_info').click(function()
			{
				var info_part1 = '<br/>Go to Web Forms => Web forms list and click on the \"Source\" link in the selected web form.';
				var info_part2 = 'Your web form ID is the number you\'ll see right after the \"?wid=\" portion of the JavaScript code.';

				$('#webform_click').html('');
				$('#webform_info2').html(info_part1+info_part2);
				$('#webform_extra').html('<br/><span style="color:black;font-size: 12px"><img src="{$this->webform_img}"/></span>');
			});

			$('#order_status').change(function()
			{
				if($('#order_status').val() == 'no')
				{
					$('#update_extra,update_sup_extra').empty();
					$('#update_address').removeAttr('checked');
				}
			});

			</script>
ORDERFORM;

	}
}
