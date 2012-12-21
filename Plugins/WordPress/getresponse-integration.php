<?php
/*
Plugin Name: GetResponse Integration Plugin
Plugin URI: http://wordpress.org/extend/plugins/getresponse-integration/
Description: This plug-in enables installation of a GetResponse fully customizable sign up form on your WordPress site or blog. Once a web form is created and added to the site the visitors are automatically added to your GetResponse contact list and sent a confirmation email. The plug-in additionally offers sign-up upon leaving a comment.
Version: 1.3.0
Author: GetResponse, Grzegorz Struczynski
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

class Gr_Integration
{	
	/**
	 * URL
	 **/
	var $GETRESPONSE_URL = 'http://getresponse.com/view_webform.js';
	var $GETRESPONSE_URL_CURL = 'http://app.getresponse.com/add_contact_webform.html';		
	var $GETRESPONSE_URL_FEED = 'http://blog.getresponse.com/feed';
    var $GrOptionDbPrefix = 'GrIntegrationOptions_'; 	// plugin db prefix

	/**
	 * Constructor
	 */
    function Gr_Integration()
    {
        // settings site
        add_action('admin_menu',array(&$this, 'Init'));
        
        // settings link in plugin page
        if (is_admin()) 
        {			
            add_filter( 'plugin_action_links', array(&$this, 'AddPluginActionLink'), 10, 2 );
		}

        if (is_numeric(get_option($this->GrOptionDbPrefix . 'new_web_from_id')))
        {
            // on/off comment
            if ( get_option($this->GrOptionDbPrefix . 'comment_on'))
            {
                add_action('comment_form',array(&$this,'AddCheckboxToComment'));
                add_action('comment_post',array(&$this,'GrabEmailFromComment'));
            }

            // on/off checkout for WooCommerce
            if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
            {
                if ( get_option($this->GrOptionDbPrefix . 'checkout_on'))
                {
                    add_action('woocommerce_after_checkout_billing_form', array(&$this, 'AddCheckboxToCheckoutPage'), 5);
                    add_action('woocommerce_ppe_checkout_order_review', array(&$this, 'AddCheckboxToCheckoutPage'), 5);
                    add_action('woocommerce_checkout_order_processed', array(&$this, 'GrabEmailFromCheckoutPage'), 5, 2);
                    add_action('woocommerce_ppe_do_payaction', array(&$this, 'GrabEmailFromCheckoutPagePE'), 5, 1);
                }
            }
        }
        // registe widget and css file
        add_action('init', array(&$this, 'WidgetRegister'));
    }
    
	/**
	 * Add admin page
	 */
    function Init()
    {
    	// settings menu
        add_options_page(
	     				 __('GetResponse', 'Gr_Integration'),  
	     				 __('GetResponse', 'Gr_Integration'), 
	     		 		'manage_options', 
	     				 __FILE__,
	     				array(&$this, 'AdminOptionsPage'
	     				)
	    );
        
        // enqueue CSS
        wp_enqueue_style( 'GrStyle' );
    }
    
	/**
	 * Add settings change button on plugin page
	 */
	function AddPluginActionLink( $links, $file )
	{
		if ( $file == $this->PluginName() ) 
		{
			$settings_link = '<a href="' . admin_url( 'options-general.php?page=' . $this->PluginName() ) . '">' . __('Settings', 'Gr_Integration') . '</a>';
			array_unshift( $links, $settings_link );
		}
		return $links;
	}

	/**
	 * Get plugin name
	 * @return string plugin name
	 */
	function PluginName()
	{
        static $this_plugin;
		if( empty($this_plugin) )
		{
		    $this_plugin = plugin_basename(__FILE__);
		}  
		return $this_plugin;
	}	
	
	/**
	 * Admin page settings
	 */
	function AdminOptionsPage() 
	{
        $woocommerce = 'off';

        if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
        {
            $woocommerce = 'on';
        }

	    if ( isset($_POST['new_web_from_id']) and
	    	 isset($_POST['style_id']) and 
	    	 isset($_POST['comment_on']) and 
	    	 isset($_POST['comment_label']) )
	    {
	    	if ( is_numeric($_POST['new_web_from_id']) OR empty($_POST['new_web_from_id']) ) 
	    	{	
                update_option($this->GrOptionDbPrefix . 'new_web_from_id', $_POST['new_web_from_id']);
                update_option($this->GrOptionDbPrefix . 'style_id', $_POST['style_id']);
                update_option($this->GrOptionDbPrefix . 'comment_on', $_POST['comment_on']);
                update_option($this->GrOptionDbPrefix . 'comment_label', $_POST['comment_label']);

                if ( $woocommerce == 'on' and isset($_POST['checkout_on']))
                {
                    update_option($this->GrOptionDbPrefix . 'checkout_on', $_POST['checkout_on']);
                    update_option($this->GrOptionDbPrefix . 'checkout_label', $_POST['checkout_label']);
                }

				?>
					<div id="message" class="updated fade">
						<p><strong><?php _e('Settings saved', 'Gr_Integration'); ?></strong></p>
					</div>
				<?php
	    	}
	    	else {
	    		?>
		    		<div id="message" class="error fade">
		    			<p><strong><?php _e('Settings error', 'Gr_Integration'); ?></strong> - You must enter an integer to <i>Web from id</i> input or leave empty to disable.</p>
		    		</div>
	    		<?php
	    	}
	    }

		?>
		<!-- CONFIG BOX -->
			<div class="GR_config_box">
				<table class="wp-list-table widefat">
					<thead>
						<tr>
							<th>GetResponse Config</th>
						</tr>
					</thead>
					<tbody id="the-list">
						<tr class="active" id="">
							<td class="desc">
								<form method="post"
									action="<?php echo admin_url( 'options-general.php?page=' . $this->PluginName() ); ?>">
											
									<h3>
										<?php _e('Subscribe via Web Form', 'Gr_Integration'); ?>
									</h3>
									<label class="GR_label" for="new_web_from_id"><?php _e('Web form ID:', 'Gr_Integration'); ?>
									</label> <input class="GR_input" type="text"
										name="new_web_from_id"
										value="<?php echo get_option($this->GrOptionDbPrefix . 'new_web_from_id') ?>" />
									(leave empty to disable)
			
									<?php
									if (get_option($this->GrOptionDbPrefix . 'style_id') == 1)
									{
										$webform = "selected";
									}
									else
									{
										$wordpress = "selected";
									}
									?>
									<br /> <label class="GR_label" for="style_id">Style:</label> <select
										class="GR_select" name="style_id">
										<option value="1" <?php echo $webform;?>>Web Form</option>
										<option value="0" <?php echo $wordpress;?>>Wordpress</option>
									</select>
			
									<h3>
										<?php _e('Subscribe via Comment', 'Gr_Integration'); ?>
									</h3>
									<?php
									if (get_option($this->GrOptionDbPrefix . 'comment_on') == 1)
									{
										$on = "selected";
									}
									else
									{
										$off = "selected";
									}
									?>
									<label class="GR_label" for="comment_on"><?php _e('Comment integration:', 'Gr_Integration'); ?>
									</label> <select class="GR_select2" name="comment_on">
										<option value="1" <?php echo $on;?>>On</option>
										<option value="0" <?php echo $off;?>>Off</option>
									</select> (allow subscriptions when visitors comment) <br /> <label
										class="GR_label" for="comment_label"><?php _e('Additional text:', 'Gr_Integration'); ?>
									</label> <input class="GR_input2" type="text" name="comment_label"
										value="<?php echo get_option($this->GrOptionDbPrefix . 'comment_label', 'Sign up to our newsletter!') ?>" />

                                    <?php if ( $woocommerce == 'on' )
                                    {
                                        if (get_option($this->GrOptionDbPrefix . 'checkout_on') == 1)
                                        {
                                            $ch_on = "selected";
                                        }
                                        else
                                        {
                                            $ch_off = "selected";
                                        }
                                        ?>
                                        <h3>Subscribe via Checkout Page</h3>
                                        <label class="GR_label" for="checkout_on"><?php _e('Checkout integration:', 'Gr_Integration'); ?>
                                        </label>
                                        <select class="GR_select2" name="checkout_on">
                                            <option value="1" <?php echo $ch_on;?>>On</option>
                                            <option value="0" <?php echo $ch_off;?>>Off</option>
                                        </select> (allow subscriptions visitors via the checkout page) <br /> <label
                                            class="GR_label" for="comment_label"><?php _e('Additional text:', 'Gr_Integration'); ?>
                                        </label>
                                        <input class="GR_input2" type="text" name="checkout_label"
                                               value="<?php echo get_option($this->GrOptionDbPrefix . 'checkout_label', 'Sign up to our newsletter!') ?>" />
                                        <?php
                                    }
                                    ?>
									<h3>Where is my web form ID?</h3>
									<p>You'll find your web form ID right in your GetResponse account. Go to Web Forms => Web forms list and click on the "Source" link in a selected web form. Your web form ID is the number you'll see right after the "?wid=" portion of the JavaScript code. </p>
									<div class="GR_img_webform_id"></div>	
			
									<p class="submit">
										<input type="submit" name="Submit"
											value="<?php _e('Save', 'Gr_Integration'); ?>"
											class="button-primary" />
									</p>
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
	 * Register the Getresponse widget on wp
	 */
	function WidgetRegister()
	{	    
		$check = get_option($this->GrOptionDbPrefix . 'new_web_from_id');
		if (!empty($check))
		{
			$display = 'DisplaySidebarWidget';
		}
		else {
			$display = 'DisplaySidebarWidgetEmpty';
		}
        
		// register webform widget
		wp_register_sidebar_widget( 'getresponse-widget',
									__( 'GetResponse WebForm', 'Gr_Integration' ),
									array( &$this, $display ),
									array( 'description' => ' ' )
									);  
		// register CSS file    
		wp_register_style( 'GrStyle', plugins_url('getresponse-integration.css', __FILE__) );
	}

	/**
	 * Display webform
	 */
	function DisplaySidebarWidget()
	{
        $form = '<p>';
        $new_id = get_option($this->GrOptionDbPrefix . 'new_web_from_id');
        $style_id = get_option($this->GrOptionDbPrefix . 'style_id');

        if($style_id == 0)
        {
        	$form .= '<script type="text/javascript" src="' .$this->GETRESPONSE_URL. '?wid='. $new_id .'&css=1"></script>';
        }
        elseif($style_id == 1)
        {
        	$form .= '<script type="text/javascript" src="' .$this->GETRESPONSE_URL. '?wid='. $new_id .'"></script>';
        }
        $form .= '</p>';
        echo $form;
	}
	
	/**
	 * Display warrning where no webform_id
	 */
	function DisplaySidebarWidgetEmpty()
	{
		echo 'No Webform.';
	}
	
	/**
	 * Add Checkbox to comment form
	 */
	function AddCheckboxToComment()
	{
		if (!is_user_logged_in() ) {
		?>
		<p>
        <input class="GR_checkbox" value="1" id="comment_checkbox" type="checkbox" name="comment_checkbox"/>
            <label for="comment_checkbox">
            <?php echo get_option($this->GrOptionDbPrefix . 'comment_label');?>
            </label>
        </p>
        <br />		
		<?php
		}
	}

    /**
	 * Add Checkbox to checkout form
	 */
	function AddCheckboxToCheckoutPage()
	{
		?>
        <p class="form-row form-row-wide">
            <input class="input-checkbox" value="1" id="checkout_checkbox" type="checkbox" name="checkout_checkbox">
            <label for="checkout_checkbox" class="checkbox">
                <?php echo get_option($this->GrOptionDbPrefix . 'checkout_label');?>
            </label>
        </p>
		<?php
	}

    /**
     * Grab email from checkout form
     */
    function GrabEmailFromCheckoutPage()
    {
        if ( $_POST['checkout_checkbox'] == 1 )
        {
            $name = $_POST['billing_first_name'] . " " . $_POST['billing_last_name'];
            $webform_id = get_option($this->GrOptionDbPrefix . 'new_web_from_id');
            $this->getresponse_curl_contact( $name, $_POST['billing_email'], $webform_id, 'GET');
            $this->getresponse_curl_contact( $name, $_POST['billing_email'], $webform_id, 'POST');
        }
    }

    /**
     * Grab email from checkout form - paypal express
     */
    function GrabEmailFromCheckoutPagePE()
    {
        if ( $_POST['checkout_checkbox'] == 1 )
        {
            $name = 'Friend';
            $webform_id = get_option($this->GrOptionDbPrefix . 'new_web_from_id');
            $this->getresponse_curl_contact( $name, $_POST['billing_email'], $webform_id, 'GET');
            $this->getresponse_curl_contact( $name, $_POST['billing_email'], $webform_id, 'POST');
        }
    }

	/**
	 * Grab email from comment form
	 */
	function GrabEmailFromComment()
	{
		if ( $_POST['comment_checkbox'] == 1 AND isset($_POST['email']) )
		{	
			$webform_id = get_option($this->GrOptionDbPrefix . 'new_web_from_id');			
			$this->getresponse_curl_contact( $_POST['author'], $_POST['email'], $webform_id, 'GET');
			$this->getresponse_curl_contact( $_POST['author'], $_POST['email'], $webform_id, 'POST');
		}
	}
	
	/**
	 * Display GetResponse blog 10 RSS links
	 */
	function GrRss() {

		$num = 10;	// numbers of feeds:
		include_once(ABSPATH . WPINC . '/feed.php');
		$rss = fetch_feed( $this->GETRESPONSE_URL_FEED );

		if ( is_wp_error($rss) ) {
			echo 'No rss items, feed might be broken.';
		}
		else
		{
			$rss_items = $rss->get_items( 0, $rss->get_item_quantity( $num ) );

			// If the feed was erroneously
			if ( !$rss_items ) {
				$md5 = md5( $this->GETRESPONSE_URL_FEED );
				delete_transient( 'feed_' . $md5 );
				delete_transient( 'feed_mod_' . $md5 );
				$rss = fetch_feed( $this->GETRESPONSE_URL_FEED );
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
	 * Curl function to send a contact via comment
	 */
	function getresponse_curl_contact( $name, $email, $webform_id, $method='GET')
	{
		if ( function_exists( 'curl_init' ) )
		{
			$str_url = 'type=ajax';
			$str_url .= '&name=' . urlencode($name);
			$str_url .= '&email=' . urlencode($email);
			$str_url .= '&webform_id=' . $webform_id;
			$str_url .= '&submit=Sign%20Up!';
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
			curl_setopt($ch, CURLOPT_TIMEOUT, 60);
			curl_setopt($ch, CURLOPT_HEADER, 0 );
			if ($method == 'POST')
			{
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $str_url);
				curl_setopt($ch, CURLOPT_URL, $this->GETRESPONSE_URL_CURL);
			}
			else {
				curl_setopt($ch, CURLOPT_POST, 0);
				curl_setopt($ch, CURLOPT_URL, $this->GETRESPONSE_URL_CURL . '?' . $str_url );
			}
			$output = curl_exec($ch);
			curl_close($ch);
			return true;
		}
		else {
			return false;
		}
	}
}

	/**
	 * Init plugin
	 */
if( defined('ABSPATH') and defined('WPINC') )
{
    if ( empty($GLOBALS['Gr_Integration']) )
    {
        $GLOBALS['Gr_Integration'] = new Gr_Integration();
    }
}
?>