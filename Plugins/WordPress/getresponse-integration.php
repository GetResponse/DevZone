<?php
/*
Plugin Name: GetResponse Integration Plugin
Plugin URI: http://wordpress.org/extend/plugins/getresponse-integration/
Description: This plug-in enables installation of a GetResponse fully customizable sign up form on your WordPress site or blog. Once a web form is created and added to the site the visitors are automatically added to your GetResponse contact list and sent a confirmation email. The plug-in additionally offers sign-up upon leaving a comment.
Version: 2.1
Author: GetResponse
Author: Grzegorz Struczynski
Author URI: http://getresponse.com/
License: GPL2

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class Gr_Integration {
	/**
	 * Db Prefix
	 **/
	var $GrOptionDbPrefix = 'GrIntegrationOptions_';

	/**
	 * Billing fields - custom fields map
	 **/
	var $biling_fields = array(
			'firstname'	=> array( 'value' => 'billing_first_name','name' => 'firstname','default' => 'yes'),
			'lastname'	=> array( 'value' => 'billing_last_name','name' => 'lastname','default' => 'yes'),
			'email'		=> array( 'value' => 'billing_email','name' => 'email','default' => 'yes'),
			'address'	=> array( 'value' => 'billing_address_1','name' => 'address','default' => 'no'),
			'city'		=> array( 'value' => 'billing_city','name' => 'city','default' => 'no'),
			'state'		=> array( 'value' => 'billing_state','name' => 'state','default' => 'no'),
			'phone'		=> array( 'value' => 'billing_phone','name' => 'phone', 'default' => 'no'),
			'country'	=> array( 'value' => 'billing_country', 'name' => 'country', 'default' => 'no' ),
			'company'	=> array( 'value' => 'billing_company', 'name' => 'company', 'default' => 'no' ),
			'postcode'	=> array( 'value' => 'billing_postcode', 'name' => 'postcode', 'default' => 'no' )
		);

	/**
	 * Constructor
	 */
	function Gr_Integration() {
		require_once('lib/GrApi.class.php');

		// settings site
		add_action('admin_menu', array(&$this, 'Init'));
		
		// settings link in plugin page
		if (is_admin()) {
			add_filter( 'plugin_action_links', array(&$this, 'AddPluginActionLink'), 10, 2 );
		}

		if (get_option($this->GrOptionDbPrefix . 'api_key')) {
			// on/off comment
			if ( get_option($this->GrOptionDbPrefix . 'comment_on')) {
				add_action('comment_form',array(&$this,'AddCheckboxToComment'));
				add_action('comment_post',array(&$this,'GrabEmailFromComment'));
			}
			// on/off registration form
			if ( get_option($this->GrOptionDbPrefix . 'registration_on')) {
				add_action('register_form',array(&$this,'AddCheckboxToRegistrationForm'));
				add_action('register_post',array(&$this,'GrabEmailFromRegistrationForm'));
			}

			// on/off checkout for WooCommerce
			if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				if ( get_option($this->GrOptionDbPrefix . 'checkout_on')) {
					add_action('woocommerce_after_checkout_billing_form', array(&$this, 'AddCheckboxToCheckoutPage'), 5);
					add_action('woocommerce_ppe_checkout_order_review', array(&$this, 'AddCheckboxToCheckoutPage'), 5);
					add_action('woocommerce_checkout_order_processed', array(&$this, 'GrabEmailFromCheckoutPage'), 5, 2);
					add_action('woocommerce_ppe_do_payaction', array(&$this, 'GrabEmailFromCheckoutPagePE'), 5, 1);
				}
			}
		}
		// register widget and css file
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
		add_action( 'plugins_loaded', array( $this, 'GrLangs') );
		add_action( 'init', array( $this, 'GrButtons') );
		add_action( 'admin_head', array( $this, 'GrJsShortcodes'));

		// register shortcode
		add_shortcode( 'grwebform', array( $this, 'showWebformShortCode') );
	}

	/**
	 * Add admin page
	 */
	function Init() {
		// settings menu
		add_options_page(
			 __('GetResponse', 'Gr_Integration'),
			 __('GetResponse', 'Gr_Integration'),
			'manage_options',
			 __FILE__,
			array(&$this, 'AdminOptionsPage')
		);

		// enqueue CSS
		wp_enqueue_style( 'GrStyle' );
		wp_enqueue_style( 'GrCustomsStyle' );

		// enqueue JS
		wp_enqueue_script( 'GrCustomsJs' );
	}

	/**
	 * Add settings change button on plugin page
	 */
	function AddPluginActionLink( $links, $file ) {
		if ( $file == $this->PluginName() ) {
			$settings_link = '<a href="' . admin_url( 'options-general.php?page=' . $this->PluginName() ) . '">' . __('Settings', 'Gr_Integration') . '</a>';
			array_unshift( $links, $settings_link );
		}
		return $links;
	}

	/**
	 * Get plugin name
	 * @return string plugin name
	 */
	function PluginName() {
		static $this_plugin;
		if( empty($this_plugin) ) {
			$this_plugin = plugin_basename(__FILE__);
		}  
		return $this_plugin;
	}

	/**
	 * Sort method
	 * @param $data
	 * @param $sortKey
	 * @param int $sort_flags
	 * @return array
	 */
	static function SortByKeyValue($data, $sortKey, $sort_flags=SORT_ASC) {
		if (empty($data) or !is_object($data) or empty($sortKey)) return $data;

		$ordered = array();
		foreach ($data as $key => $value) {
			$ordered[$value->$sortKey] = $value;
			$ordered[$value->$sortKey]->id = $key;
		}

		ksort($ordered, $sort_flags);

		return array_values($ordered);
	}

	/**
	 * Admin page settings
	 */
	function AdminOptionsPage() {

		//Check if curl extension is set and curl_init method is callable
		$this->checkCurlExtension();

		$woocommerce = 'off';
		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			$woocommerce = 'on';
		}

		$apikey = isset($_POST['api_key']) ? $_POST['api_key'] : get_option($this->GrOptionDbPrefix . 'api_key');

		if ( !empty($apikey)) {
			$api = new GetResponseIntegration($apikey);

			// api errors
			$ping = $api->ping();
			if (is_array($ping) && isset($ping['type']) && $ping['type'] == 'error')
			{
				echo $ping['msg'];
				return;
			}

			if (isset($ping->result->ping)) {
				update_option($this->GrOptionDbPrefix . 'api_key', $apikey);

				// admin page settings
				if ( isset($_POST['comment_campaign']) || isset($_POST['checkout_campaign']) )
				{
					$post_fields = array(
						'comment_campaign', 'checkout_campaign', 'comment_on', 'comment_label', 'comment_checked', 'checkout_checked', 'sync_order_data', 'fields_prefix',
						'registration_campaign', 'registration_campaign', 'registration_on', 'registration_label', 'registration_checked'
					);

					foreach ($post_fields as $field) {
						$val = isset($_POST[$field]) ? $_POST[$field] : null;
						update_option($this->GrOptionDbPrefix . $field, $val);
					}

					// woocommerce settings
					if ( $woocommerce == 'on' and isset($_POST['checkout_on'])) {
						update_option($this->GrOptionDbPrefix . 'checkout_on', $_POST['checkout_on']);
						update_option($this->GrOptionDbPrefix . 'checkout_label', $_POST['checkout_label']);
					}
					?>
					<div id="message" class="updated fade" style="margin: 2px; 0px; 0px;">
						<p><strong><?php _e('Settings saved', 'Gr_Integration'); ?></strong></p>
					</div>
					<?php
					// sync order data - custom fields
					if ( isset($_POST['custom_field']) ) {
						foreach ($this->biling_fields as $value => $bf) {
							if (in_array($value, array_keys($_POST['custom_field'])) == true && preg_match('/^[_a-zA-Z0-9]{2,32}$/m', stripslashes($_POST['custom_field'][$value])) == true) {
								update_option($this->GrOptionDbPrefix . $value, $_POST['custom_field'][$value]);
							}
							else {
								delete_option($this->GrOptionDbPrefix . $value);
							}
						}
					}
					else {
						foreach (array_keys($this->biling_fields) as $value) {
							delete_option($this->GrOptionDbPrefix . $value);
						}
					}
				}
			}
			else {
				?>
				<div id="message" class="error " style="margin: 2px; 0px; 0px;">
					<p><strong><?php _e('Settings error', 'Gr_Integration'); ?></strong> <?php _e(' - Invalid API Key', 'Gr_Integration') ?></p>
				</div>
			<?php
			}
		}

		if (isset($_POST['api_key']) and $_POST['api_key'] == '')
		{
			?>
			<div id="message" class="error " style="margin: 2px; 0px; 0px;">
				<p><strong><?php _e('Settings error', 'Gr_Integration'); ?></strong> <?php _e(' - API Key can\'t be empty.', 'Gr_Integration') ?></p>
			</div>
		<?php
			update_option($this->GrOptionDbPrefix . 'api_key', $apikey);
		}

		?>
		<!-- CONFIG BOX -->
			<div class="GR_config_box">
				<table class="wp-list-table widefat">
					<thead>
						<tr>
							<th><span class="GR_header"><?php _e('GetResponse Plugin Settings', 'Gr_Integration'); ?></span></th>
						</tr>
					</thead>
					<tbody id="the-list">
						<tr class="active" id="">
							<td class="desc">
								<form method="post" action="<?php echo admin_url( 'options-general.php?page=' . $this->PluginName() ); ?>">

									<!-- API KEY -->
									<p>
										<label class="GR_label" for="api_key"><?php _e('API Key:', 'Gr_Integration'); ?></label>
										<input class="GR_api" type="text" name="api_key" value="<?php echo get_option($this->GrOptionDbPrefix . 'api_key') ?>" />

										<a class="gr-tooltip">
											<span class="gr-tip" style="width:178px">
												<span>
													<?php _e('Enter your API key. You can find it on your GetResponse profile in Account Details -> GetResponse API', 'Gr_Integration'); ?>
												</span>
											</span>
										</a>
									</p>

									<!-- SUBMIT -->
									<br/>
									<input type="submit" name="Submit" value="<?php _e('Save', 'Gr_Integration'); ?>"  class="button-primary" />

									<!-- WEBFORM SETTINGS -->
									<div id="settings" <?php if (get_option($this->GrOptionDbPrefix . 'api_key') == '') {?>style="display: none;"<?php }?>>
										<!-- SUBSCRIBE VIA WEB FORM -->
										<h3>
											<?php _e('Subscribe via Web Form', 'Gr_Integration'); ?>
										</h3>

										<p>
											<?php _e('To activate a GetResponse Web Form widget drag it to a sidebar or click on it.', 'Gr_Integration'); ?>
											<?php echo '<a href="' . admin_url( 'widgets.php') . '"><strong>' . __('Go to Widgets site', 'Gr_Integration') . '</strong></a>';?>
										</p>

										<!-- SUBSCRIPTION VIA COMMENT -->
										<h3>
											<?php _e('Subscribe via Comment', 'Gr_Integration'); ?>
										</h3>

										<!-- COMMENT INTEGRATION SWITCH ON/OFF -->
										<?php
										$comment_type = get_option($this->GrOptionDbPrefix . 'comment_on');
										$registration_type = get_option($this->GrOptionDbPrefix . 'registration_on');
										?>
										<p>
											<label class="GR_label" for="comment_on"><?php _e('Comment integration:', 'Gr_Integration'); ?></label>
											<select class="GR_select2" name="comment_on" id="comment_integration">
												<option value="0" <?php selected($comment_type, 0); ?>><?php _e('Off', 'Gr_Integration'); ?></option>
												<option value="1" <?php selected($comment_type, 1); ?>><?php _e('On', 'Gr_Integration'); ?></option>
											</select> <?php _e('(allow subscriptions when visitors comment)', 'Gr_Integration'); ?>
										</p>

										<?php
											$comment_campaign = get_option($this->GrOptionDbPrefix . 'comment_campaign');
											$checkout_campaign = get_option($this->GrOptionDbPrefix . 'checkout_campaign');
											$registration_campaign = get_option($this->GrOptionDbPrefix . 'registration_campaign');
											// API Instance
											$api = $this->GetApiInstance();
											if ($api) {
												$campaigns = $api->getCampaigns();
											}
										?>

										<div id="comment_show" <?php if (get_option($this->GrOptionDbPrefix . 'comment_on') != 1) {?>style="display: none;"<?php }?>>
											<!-- CAMPAIGN TARGET -->
											<p>
												<label class="GR_label"for="comment_campaign"><?php _e( 'Target Campaign:', 'Gr_Integration'); ?></label>
												<?php
												// sort campaigns by name
												if ( !empty($campaigns)) {
													$campaigns = $this->SortByKeyValue($campaigns, 'name');
												}
												// check if no errors
												if ( !empty($campaigns) and false === (is_array($campaigns) and isset($campaigns['type']) and $campaigns['type'] == 'error')) {
												?>
												<select name="comment_campaign" id="comment_campaign" class="GR_select">
													<?php
														foreach ($campaigns as $campaign) {
															if (is_object($campaign)) {
																echo '<option value="' . $campaign->id . '" id="' . $campaign->id . '"', $comment_campaign == $campaign->id ? ' selected="selected"' : '', '>', $campaign->name, '</option>';
															}
														}
													?>
												</select>
												<?php }
												else {
													_e('No Campaigns.', 'Gr_Integration');
												}
												?>
											</p>

											<!-- ADDITIONAL TEXT - COMMENT SUBSCRIPTION-->
											<p>
												<label class="GR_label" for="comment_label"><?php _e('Additional text:', 'Gr_Integration'); ?></label>
												<input class="GR_input2" type="text" name="comment_label" value="<?php echo get_option($this->GrOptionDbPrefix . 'comment_label', __( 'Sign up to our newsletter!', 'Gr_Integration')) ?>" />
											</p>

											<!-- DEFAULT CHECKED - COMMENT SUBSCRIPTION -->
											<p>
												<label class="GR_label" for="comment_checked"><?php _e('Subscribe checkbox checked by default', 'Gr_Integration'); ?></label>
												<input class="GR_checkbox" type="checkbox" name="comment_checked" value="1" <?php if (get_option($this->GrOptionDbPrefix . 'comment_checked', '') == 1) { ?>checked="checked"<?php }?>/>
											</p>
										</div>

										<script>
											jQuery('#comment_integration').change(function() {
												var value = jQuery(this).val();
												if (value == '1') {
													jQuery('#comment_show').show('slow');
												}
												else {
													jQuery('#comment_show').hide('slow');
												}
											});
										</script>

										<!-- SUBSCRIBE VIA REGISTRATION PAGE-->
										<h3>
											<?php _e('Subscribe via Registration Page', 'Gr_Integration'); ?>
										</h3>

										<p>
											<label class="GR_label" for="registration_on"><?php _e('Registration integration:', 'Gr_Integration'); ?></label>
											<select class="GR_select2" name="registration_on" id="registration_integration">
												<option value="0" <?php selected($registration_type, 0); ?>><?php _e('Off', 'Gr_Integration'); ?></option>
												<option value="1" <?php selected($registration_type, 1); ?>><?php _e('On', 'Gr_Integration'); ?></option>
											</select> <?php _e('(allow subscriptions at the registration page)', 'Gr_Integration'); ?>
										</p>

										<div id="registration_show" <?php if (get_option($this->GrOptionDbPrefix . 'registration_on') != 1) {?>style="display: none;"<?php }?>>
											<!-- CAMPAIGN TARGET -->
											<p>
												<label class="GR_label"for="registration_campaign"><?php _e( 'Target Campaign:', 'Gr_Integration'); ?></label>
												<?php
												// check if no errors
												if ( !empty($campaigns) and false === (is_array($campaigns) and isset($campaigns['type']) and $campaigns['type'] == 'error')) {
													?>
													<select name="registration_campaign" id="registration_campaign" class="GR_select">
														<?php
														foreach ($campaigns as $campaign) {
															echo '<option value="' . $campaign->id . '" id="' . $campaign->id . '"', $registration_campaign == $campaign->id ? ' selected="selected"' : '', '>', $campaign->name, '</option>';
														} ?>
													</select>
												<?php }
												else {
													_e('No Campaigns.', 'Gr_Integration');
												}
												?>
											</p>

											<!-- ADDITIONAL TEXT - REGISTRATION SUBSCRIPTION-->
											<p>
												<label class="GR_label" for="registration_label"><?php _e('Additional text:', 'Gr_Integration'); ?></label>
												<input class="GR_input2" type="text" name="registration_label" value="<?php echo get_option($this->GrOptionDbPrefix . 'registration_label', __( 'Sign up to our newsletter!', 'Gr_Integration')) ?>" />
											</p>

											<!-- DEFAULT CHECKED - REGISTRATION SUBSCRIPTION -->
											<p>
												<label class="GR_label" for="registration_checked"><?php _e('Subscribe checkbox checked by default', 'Gr_Integration'); ?></label>
												<input class="GR_checkbox" type="checkbox" name="registration_checked" value="1" <?php if (get_option($this->GrOptionDbPrefix . 'registration_checked', '') == 1) { ?>checked="checked"<?php }?>/>
											</p>
										</div>

										<script>
											jQuery('#registration_integration').change(function() {
												var value = jQuery(this).val();
												if (value == '1') {
													jQuery('#registration_show').show('slow');
												}
												else {
													jQuery('#registration_show').hide('slow');
												}
											});
										</script>

										<!-- SUBSCRIPTION VIA CHECKOUT PAGE -->
										<?php if ( $woocommerce == 'on' ) {
											$checkout_type = get_option($this->GrOptionDbPrefix . 'checkout_on');
										?>
											<h3><?php _e('Subscribe via Checkout Page', 'Gr_Integration'); ?></h3>

											<!-- CHECKOUT INTEGRATION SWITCH ON/OFF -->
											<p>
												<label class="GR_label" for="checkout_on"><?php _e('Checkout integration:', 'Gr_Integration'); ?></label>
												<select class="GR_select2" name="checkout_on" id="checkout_integration">
													<option value="0" <?php selected($checkout_type, 0); ?>><?php _e('Off', 'Gr_Integration'); ?></option>
													<option value="1" <?php selected($checkout_type, 1); ?>><?php _e('On', 'Gr_Integration'); ?></option>
												</select> <?php _e('(allow subscriptions at the checkout stage)', 'Gr_Integration'); ?><br />
											</p>

											<div id="checkout_show" <?php if (get_option($this->GrOptionDbPrefix . 'checkout_on') == 0) {?>style="display: none;"<?php }?>>
												<!-- CAMPAIGN TARGET -->
												<p>
													<label class="GR_label" for="checkout_campaign"><?php _e( 'Target campaign:', 'Gr_Integration' ); ?></label>

													<?php
													// check if no errors
													if ( !empty($campaigns) and false === (is_array($campaigns) and isset($campaigns['type']) and $campaigns['type'] == 'error')) {
														?>
														<select name="checkout_campaign" id="checkout_campaign" class="GR_select">
															<?php
															foreach ($campaigns as $campaign) {
																echo '<option value="' . $campaign->id . '" id="' . $campaign->id . '"', $checkout_campaign == $campaign->id ? ' selected="selected"' : '', '>', $campaign->name, '</option>';
															} ?>
														</select>
													<?php }
													else {
														_e('No Campaigns.', 'Gr_Integration');
													}
													?>
												</p>

												<!-- ADDITIONAL TEXT - CHECKOUT SUBSCRIPTION -->
												<p>
													<label class="GR_label" for="comment_label"><?php _e('Additional text:', 'Gr_Integration'); ?></label>
													<input class="GR_input2" type="text" name="checkout_label" value="<?php echo get_option($this->GrOptionDbPrefix . 'checkout_label', __( 'Sign up to our newsletter!', 'Gr_Integration')) ?>" />
												</p>

												<!-- DEFAULT CHECKED - CHECKOUT SUBSCRIPTION -->
												<p>
													<label class="GR_label" for="checkout_checked"><?php _e('Sign up box checked by default', 'Gr_Integration'); ?></label>
													<input class="GR_checkbox" type="checkbox" name="checkout_checked" value="1" <?php if (get_option($this->GrOptionDbPrefix . 'checkout_checked', '') == 1) { ?>checked="checked"<?php }?>/>
												</p>

												<!-- SYNC ORDER DATA - CHECKOUT SUBSCRIPTION -->
												<p>
													<label class="GR_label" for="sync_order_data"><?php _e('Map custom fields:', 'Gr_Integration'); ?></label>
													<input class="GR_checkbox" type="checkbox" name="sync_order_data" id="sync_order_data" value="1" <?php if (get_option($this->GrOptionDbPrefix . 'sync_order_data', '') == 1) { ?>checked="checked"<?php }?>/>

													<a class="gr-tooltip">
														<span class="gr-tip" style="width:170px">
															<span>
																<?php _e('Check to update customer details. Each input can be max. 32 characters and include lowercase, a-z letters, digits or underscores. Incorrect or empty entries won’t be added.', 'Gr_Integration'); ?>
															</span>
														</span>
													</a>
												</p>

												<!-- CUSTOM FIELDS PREFIX - CHECKOUT SUBSCRIPTION -->
												<div id="customNameFields" style="display: none;">
													<div class="gr-custom-field" style="padding-left: 150px;">
														<select class="jsNarrowSelect" name="custom_field" multiple="multiple">
															<?php
															foreach ($this->biling_fields as $value => $filed) {
																$custom = get_option($this->GrOptionDbPrefix . $value);
																$field_name = ($custom) ? $custom : $filed['name'];
																echo '<option data-inputvalue="' . $field_name . '" value="' . $value . '" id="' . $filed['value'] . '"', ($filed['default'] == 'yes' || $custom)? ' selected="selected"' : '', $filed['default'] == 'yes' ? ' disabled="disabled"' : '','>', $filed['name'], '</option>';
															} ?>
														</select>
													</div>
												</div>
											</div>

											<script>
												jQuery('#checkout_integration').change(function() {
													var value = jQuery(this).val();
													if (value == '1') {
														jQuery('#checkout_show').show();
													}
													else {
														jQuery('#checkout_show').hide();
													}
												});

												var sod = jQuery('#sync_order_data'), cfp = jQuery('#customNameFields');
												if (sod.prop('checked') == true) {
													cfp.show();
												}
												sod.change(function() {
													cfp.toggle('slow');
												});

												jQuery('.jsNarrowSelect').selectNarrowDown();
											</script>

											<?php
										}
										?>

										<!-- SUBMIT -->
										<input type="submit" name="Submit" value="<?php _e('Save', 'Gr_Integration'); ?>" class="button-primary" />

										<!-- WEB FORM SHORTCODE -->
										<h3><?php _e('Web Form Shortcode', 'Gr_Integration'); ?></h3>
										<p><?php _e('With the GetResponse Wordpress plugin, you can use shortcodes to place web forms in blog posts. Simply place the following tag in your post wherever you want the web form to appear:', 'Gr_Integration'); ?>
											<br/>
											<code>[grwebform url="PUT_WEBFORM_URL_HERE" css="on/off"].</code>
											<br/>
											<?php _e('Set the CSS parameter to ON, and the web form will be displayed in GetResponse format; set it to OFF, and the web form will be displayed in the standard Wordpress format.', 'Gr_Integration'); ?>
										</p>
										<div class="GR_img_webform_shortcode"></div>
									</div>
								</form>
							</td>
						</tr>
					</tbody>
				</table>
			</div>			
		<!-- RSS BOX -->	
			<div class="GR_rss_box">
				<table class="wp-list-table widefat">
					<thead>
						<tr>
							<th>GetResponse RSS</th>
						</tr>
					</thead>
					<tbody id="the-list2">
						<tr class="active" id="">
							<td class="desc">
						<?php $this->GrRss(); ?>
							</td>
						</tr>
					</tbody>
				</table>
		<!-- SOCIAL BOX -->	
			<br />				
				<table class="wp-list-table widefat">
					<thead>
						<tr>
							<th>GetResponse Social</th>
						</tr>
					</thead>
					<tbody id="the-list2">
						<tr class="active" id="">
							<td class="desc">
							  <ul>
								<li>
								  <a class="GR_ico sprite facebook-ico" href="http://www.facebook.com/getresponse" target="_blank" title="Facebook">Facebook</a>
								</li>
								<li>
								  <a class="GR_ico sprite twitter-ico" href="http://twitter.com/getresponse" target="_blank" title="Twitter">Twitter</a>
								</li>
								<li>
								  <a class="GR_ico sprite linkedin-ico" href="http://www.linkedin.com/company/implix" target="_blank" title="LinkedIn">LinkedIn</a>
								</li>
								<li>
								  <a class="GR_ico sprite blog-ico" href="http://blog.getresponse.com/" target="_blank" title="Blog">Blog</a>
								</li>
							  </ul>
							</td>
						</tr>
					</tbody>
				</table>
			</div>		
		<?php
	}

	/**
	 * Register widgets
	 */
	function register_widgets() {
		wp_register_style( 'GrStyle', plugins_url('css/getresponse-integration.css', __FILE__) );
		wp_register_style( 'GrCustomsStyle', plugins_url('css/getresponse-custom-field.css', __FILE__) );
		wp_register_script( 'GrCustomsJs', plugins_url('js/getresponse-custom-field.src-verified.js', __FILE__) );
		include_once('lib/class-gr-widget-webform.php');
		register_widget( 'GR_Widget' );
	}

	/**
	 * Display shortcode for webform
	 *
	 * @param $atts
	 * @return string
	 */
	public static function showWebformShortCode($atts) {
		$params = shortcode_atts( array(
			'url' => 'null',
			'css' => 'on',
		), $atts );

		return '<script type="text/javascript" src="' . $params['url'] . ($params['css'] == "off" ? "&css=1" : "" ) . '"></script>';
	}

	/**
	 * Add Checkbox to comment form
	 */
	function AddCheckboxToComment() {
		if (!is_user_logged_in() ) {
			$checked = get_option($this->GrOptionDbPrefix . 'comment_checked');
			?>
			<p>
			<input class="GR_checkbox" value="1" id="gr_comment_checkbox" type="checkbox" name="gr_comment_checkbox" <?php if ($checked) {?>checked="checked"<?php }?>/>
				<?php echo get_option($this->GrOptionDbPrefix . 'comment_label');?>
			</p><br />
		<?php
		}
	}

	/**
	 * Add Checkbox to checkout form
	 */
	function AddCheckboxToCheckoutPage() {
		$checked = get_option($this->GrOptionDbPrefix . 'checkout_checked');
		?>
		<p class="form-row form-row-wide">
			<input class="input-checkbox GR_checkoutbox" value="1" id="gr_checkout_checkbox" type="checkbox" name="gr_checkout_checkbox" <?php if ($checked) {?>checked="checked"<?php }?> />
			<label for="gr_checkout_checkbox" class="checkbox">
				<?php echo get_option($this->GrOptionDbPrefix . 'checkout_label');?>
			</label>
		</p>
		<?php
	}

	/**
	 * Add Checkbox to registration form
	 */
	function AddCheckboxToRegistrationForm() {
		if (!is_user_logged_in() ) {
			$checked = get_option($this->GrOptionDbPrefix . 'registration_checked');
			?>
			<p class="form-row form-row-wide">
				<input class="input-checkbox GR_registrationbox" value="1" id="gr_registration_checkbox" type="checkbox" name="gr_registration_checkbox" <?php if ($checked) {?>checked="checked"<?php }?> />
				<label for="gr_registration_checkbox" class="checkbox">
					<?php echo get_option($this->GrOptionDbPrefix . 'registration_label');?>
				</label>
			</p><br/>
			<?php
		}
	}

	/**
	 * Grab email from checkout form
	 */
	function GrabEmailFromCheckoutPage() {
		if ($_POST['gr_checkout_checkbox'] == 1 ) {
			$name = $_POST['billing_first_name'] . " " . $_POST['billing_last_name'];
			$api = $this->GetApiInstance();
			if ($api) {
				$customs = array();
				$campaign = get_option($this->GrOptionDbPrefix . 'checkout_campaign');
				if (get_option($this->GrOptionDbPrefix . 'sync_order_data') == true) {
					foreach ($this->biling_fields as $custom_name => $custom_field) {
						$custom = get_option($this->GrOptionDbPrefix . $custom_name);
						if ($custom && !empty($_POST[$custom_field['value']])) {
							$customs[$custom] = $_POST[$custom_field['value']];
						}
					}
				}
				$api->addContact($campaign, $name, $_POST['billing_email'], 0, $customs);
			}
		}
	}

	/**
	 * Grab email from checkout form - paypal express
	 */
	function GrabEmailFromCheckoutPagePE() {
		if ($_POST['gr_checkout_checkbox'] == 1 ) {
			$api = $this->GetApiInstance();
			if ($api) {
				$campaign = get_option($this->GrOptionDbPrefix . 'checkout_campaign');
				$api->addContact($campaign, 'Friend', $_POST['billing_email']);
			}
		}
	}

	/**
	 * Grab email from comment form
	 */
	function GrabEmailFromComment() {
		if ( $_POST['gr_comment_checkbox'] == 1 AND isset($_POST['email']) ) {
			$api = $this->GetApiInstance();
			if ($api) {
				$campaign = get_option($this->GrOptionDbPrefix . 'comment_campaign');
				$api->addContact($campaign, $_POST['author'], $_POST['email']);
			}
		}
	}

	/**
	 * Grab email from registration form
	 */
	function GrabEmailFromRegistrationForm() {
		if ( $_POST['gr_registration_checkbox'] == 1 AND isset($_POST['user_email']) ) {
			$api = $this->GetApiInstance();
			if ($api) {
				$campaign = get_option($this->GrOptionDbPrefix . 'registration_campaign');
				$api->addContact($campaign, $_POST['user_login'], $_POST['user_email']);
			}
		}
	}

	/**
	 * Display GetResponse blog 10 RSS links
	 */
	function GrRss() {

		$lang = get_bloginfo("language") == 'pl-PL' ? 'pl' : 'com';
		$feed_url = 'http://blog.getresponse.' . $lang . '/feed';

		$num = 12; // numbers of feeds:
		include_once(ABSPATH . WPINC . '/feed.php');
		$rss = fetch_feed( $feed_url );

		if ( is_wp_error($rss) ) {
			echo 'No rss items, feed might be broken.';
		}
		else
		{
			$rss_items = $rss->get_items( 0, $rss->get_item_quantity( $num ) );

			// If the feed was erroneously
			if ( !$rss_items ) {
				$md5 = md5( $feed_url );
				delete_transient( 'feed_' . $md5 );
				delete_transient( 'feed_mod_' . $md5 );
				$rss = fetch_feed( $feed_url );
				$rss_items = $rss->get_items( 0, $rss->get_item_quantity( $num ) );
			}

			$content = '<ul class="GR_rss_ul">';
			if ( !$rss_items ) {
				$content .= '<li class="GR_rss_li">No rss items, feed might be broken.</li>';
			} else {
				foreach ( $rss_items as $item ) {
					$url = preg_replace( '/#.*/', '', esc_url( $item->get_permalink(), $protocolls=null, 'display' ) );
					$content .= '<li class="GR_rss_li">';
					$content .= '<a class="GR_rss_a" href="'.$url.'" target="_blank">'. esc_html( $item->get_title() ) .'</a> ';
					$content .= '</li>';
				}
			}
			$content .= '</ul>';
			echo $content;
		}
	}

	/**
	 * GetResponse MCE buttons
	 */
	function GrButtons() {
		add_filter( 'mce_buttons', array(&$this, 'GrRegisterButtons') );
		add_filter( "mce_external_plugins", array(&$this, 'GrAddButtons') );
	}

	/**
	 * GetResponse MCE plugin
	 */
	function GrAddButtons( $plugin_array ) {
		global $wp_version;

		if ($wp_version >= 3.9)
			$plugin_array['GrShortcodes'] = untrailingslashit( plugins_url( '/', __FILE__ ) ) . '/js/gr-plugin.js';
		else
			$plugin_array['GrShortcodes'] = untrailingslashit( plugins_url( '/', __FILE__ ) ) . '/js/gr-plugin_3_8.js';

		return $plugin_array;
	}

	/**
	 * Display GetResponse MCE buttons
	 */
	function GrRegisterButtons( $buttons ) {
		array_push(
			$buttons,
			'separator',
			'GrShortcodes'
		);
		return $buttons;
	}

	/**
	 * Display GetResponse MCE buttons
	 */
	function GrJsShortcodes() {
		$GrOptionDbPrefix = 'GrIntegrationOptions_';
		$api_key = get_option($GrOptionDbPrefix . 'api_key');

		if (!empty($api_key)) {
			$api = new GetResponseIntegration($api_key);
			$campaigns = $api->getCampaigns();
			$webforms = $api->getWebforms();
			// check if no errors
			if ( !empty($webforms) and false === (is_array($webforms) and isset($webforms['type']) and $webforms['type'] == 'error')) {
				$webforms = $this->SortByKeyValue($webforms, 'name');
			}
			else {
				$campaigns = null;
				$webforms = null;
			}
			$my_campaigns = json_encode($campaigns);
			$my_webforms = json_encode($webforms);
			?>
			<script type="text/javascript">
				var my_webforms = <?php echo $my_webforms; ?>;
				var my_campaigns = <?php echo $my_campaigns; ?>;
			</script>
	<?php }
	}

	/**
	 * API Instance
	 */
	function GetApiInstance() {
		$api_key = get_option($this->GrOptionDbPrefix . 'api_key');
		if ( !empty($api_key)) {
			$apiInstance = new GetResponseIntegration($api_key);
			return $apiInstance;
		}
		else {
			return false;
		}
	}

	/**
	 * Languages
	 */
	function GrLangs() {
		load_plugin_textdomain( 'Gr_Integration', false, plugin_basename( dirname( __FILE__ ) ) . "/langs" );
	}

	/**
	 * Check if curl extension is set and curl_init method is callable
	 */
	function checkCurlExtension() {
		if ( !extension_loaded('curl') or !is_callable('curl_init')) {
			echo "<h3>cURL Error !</h3>";
			echo '<h4>GetResponse Integration Plugin requires PHP cURL extension</h4>';
			return;
		}
	}
} //Gr_Integration

/**
 * Init plugin
 */
if ( defined('ABSPATH') and defined('WPINC') ) {
	if ( empty($GLOBALS['Gr_Integration']) ) {
		$GLOBALS['Gr_Integration'] = new Gr_Integration();
	}
}
?>