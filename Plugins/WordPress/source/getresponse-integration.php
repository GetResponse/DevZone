<?php
/*
Plugin Name: GetResponse Integration Plugin
Description: This plugin will add configurable GetResponse form to add contacts from your site. 
Version: 1.1
Author: Kacper Rowiński, Grzegorz Struczyński
License: GPL2
*/

/*  Copyright 2010 Kacper Rowiński  (email : krowinski@implix.com)

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

/*  Changelog:

1.1 - Added possiblity to use Wordpress styles,
    - Added integration with new WebForms.
*/
class Gr_Integration
{
    // plugin db prefix
    var $GrOptionDbPrefix = 'GrIntegrationOptions_';
    
    // default values for widget
    var $GrDefaultWidgetOptions = array
    (
        'title' => 'Subscribe Form',
    	'buttonText' => 'Subscribe',
        'subscriberEmailText' => 'Email',
    	'subscriberNameText' => 'Name',
        'confirmation_url' => '',
        'error_url'	=> '',
        'campaign_name' => '',
        'custom_ref' => '',
        'show_counter' => 'no',
        'hide_name' => 'no',
        'customs' => array(), 
    );
    
	/**
	 * Constructor
	 */
    function Gr_Integration()
    {
        add_action('admin_menu',array(&$this, 'Init'));
        
        if (is_admin()) 
        {			
            add_filter( 'plugin_action_links', array(&$this, 'AddPluginActionLink'), 10, 2 );
		} 
		else
		{
		    add_action('wp_footer', array(&$this, 'ShowPopup'));
		}
		
        load_plugin_textdomain( 'getresponse-integration-i18n', str_replace(ABSPATH , '' , dirname(__FILE__) . '/mo' ) );
	    
        // add js
	    wp_enqueue_script( 
	    	'gr_script', 
	        get_bloginfo('wpurl') . '/wp-content/plugins/getresponse-integration/js/getresponse-integration.js', 
	        array('jquery', 'jquery-form')
	    ); 
	    
	    // add cs
	    wp_enqueue_style(
	    	'gr_style', 
	    	get_bloginfo('wpurl') . '/wp-content/plugins/getresponse-integration/css/getresponse-integration.css'
	    );
	    
		// init widgets
		add_action('init', array(&$this, 'WidgetRegister'));
    }
    
	/**
	 * Add admin page
	 */
    function Init()
    {
	     add_options_page(
            __('GetResponse', 'Gr_Integration'),
            __('GetResponse', 'Gr_Integration'), 
    		'manage_options',
            __FILE__,
            array(&$this, 'AdminOptionsPage')
	    );
    }
    
	/**
	 * Add settings change button on plugin page
	 * @return array links
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
	    if ( isset($_POST['web_from_id']) or isset($_POST['new_web_from_id']) and isset($_POST['style_id']))
	    {
	        update_option($this->GrOptionDbPrefix . 'web_from_id', $_POST['web_from_id']);
                update_option($this->GrOptionDbPrefix . 'new_web_from_id', $_POST['new_web_from_id']);
                update_option($this->GrOptionDbPrefix . 'style_id', $_POST['style_id']);
			?>
				<div id="message" class="updated fade">
					<p><strong><?php _e('Settings saved', 'Gr_Integration'); ?></strong></p>
				</div>
			<?php
	    }

	    ?>
		<div class="wrap">
		
    		<h2><?php _e('GetResponse Options', 'Gr_Integration'); ?></h2>
    		
    		<h3><?php _e('Sidebar Widget', 'Gr_Integration'); ?></h3>
    		<p>
    	        <?php printf(__('The GetResponse widget(s) are available on <a href="%s">widgets management page</a>.', 'Gr_Integration'), admin_url('widgets.php')); ?>
    		</p>
    		
    		<h3><?php _e('LightBox options*', 'Gr_Integration'); ?></h3>
        	<p>
        		<form method="post" action="<?php echo admin_url( 'options-general.php?page=' . $this->PluginName() ); ?>">
                	<table class="form-table">
        				<tr valign="top">
                			<th scope="row">
                				<label for="web_from_id"><?php _e('Web from id:', 'Gr_Integration'); ?>&#160</label>
                			</th>
                			<td>
                				<input type="text" name="web_from_id" value="<?php echo get_option($this->GrOptionDbPrefix . 'web_from_id') ?>"/> 
                				<?php _e('(leave empty to disable)', 'Gr_Integration'); ?>
                			</td>
                		</tr>
                		<tr>
                			<td colspan="2">
        						<p>
        							<?php _e('Web form id and more lightbox options can be configured on your GetResponse', 'Gr_Integration'); ?>
        							<a href="http://www.getresponse.com/create_webform.html"><?php _e('account', 'Gr_Integration'); ?></a> 
        							<?php _e('( login requierd )', 'Gr_Integration'); ?> 
        						</p>
                                                        <h5><?php _e('*(Option is only available for webforms created before September 1st)', 'Gr_Integration'); ?></h5>
        				</td>
                                </tr>  
                	</table>
                        <p>
                           <h3><?php _e('New WebForm', 'Gr_Integration'); ?></h3>
                           <label for="new_web_from_id"><?php _e('Web from id:', 'Gr_Integration'); ?>&#160</label>
                           <input type="text" name="new_web_from_id" value="<?php echo get_option($this->GrOptionDbPrefix . 'new_web_from_id') ?>"/>

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

                            <select name="style_id">
        			<option value="1" name="webform" <?php echo $webform;?> >Use Webform styles </option>
        			<option value="0" name="wordpress" <?php echo $wordpress;?> >Use Wordpress styles </option>
                           </select>
        		</p>
                        
                	<p class="submit">
                		<input type="submit" name="Submit" value="<?php _e('Save', 'Gr_Integration'); ?>" class="button-primary" />
                	</p>
        		</form>
    		</p>

		</div>
		<?php
	}
	
    /**
     * Gr light box popup
     */
	function ShowPopup() 
	{
		$id = get_option($this->GrOptionDbPrefix . 'web_from_id');
		if ( empty($id) ) return;
		echo '<script type="text/javascript" src="http://www.getresponse.com/display_webform.js?wid='. $id .'"><!--empty--></script>';
	}
        
	/**
	 * Register the Getresponse widget on wp
	 */
	function WidgetRegister() 
	{	    
		$widget_options = get_option( $this->GrOptionDbPrefix . 'widget' );

		// if there is no options load defaults
		if (empty($widget_options))
		{
		    $widget_options[1] = $this->GrDefaultWidgetOptions;
		}
		
		$name = __( 'GetResponse Subscription Form', 'Gr_Integration' );
		$prefix = 'getresponse-widget';
		
		foreach ( array_keys($widget_options) as $widget_number ) 
		{
			wp_register_sidebar_widget(
			    $prefix . '-' . $widget_number, 
			    $name, 
			    array( &$this, 'DisplaySidebarWidget' ), 
			    array( 
			    	'classname' => 'widget_getresponse', 
			    	'description' => __( 'Add GetResponse widget subscription form to your site.', 'Gr_Integration' ),
			    ),
			    array( 'number' => $widget_number )
			);
			
			wp_register_widget_control(
			    $prefix . '-' . $widget_number, 
			    $name,
			    array( &$this, 'WidgetControl' ),
			    array( 'width' => 200, 'height' => 400, 'id_base' => $prefix ),
			    array( 'number' => $widget_number )
            );
		}
	}
	
	/**
	 * Display the widget on page
	 */
	function DisplaySidebarWidget( $args, $widget_args = null ) 
	{	    	
	    $number = isset($widget_args['number']) ? $widget_args['number'] : null;
	    
		$options = get_option( $this->GrOptionDbPrefix . 'widget' );
		// no options don't show form
		if ( empty($options[$number]) )
		{
			return;
		}
		
		// GR Form, generate form from given options
		
		$form .= '<div id="GRform">';
       	$form .= '<form accept-charset="utf-8" action="http://www.getresponse.com/cgi-bin/add.cgi">';
        $form .= '<input type="hidden" name="custom_http_referer" id="custom_http_referer" value="'. $_SERVER['REQUEST_URI'] .'"/>';
        
        if ( strlen($options[$number]['confirmation_url']) > 0 )
        {
            $form .= '<input type="hidden" name="confirmation_url" id="confirmation_url" value="' . $options[$number]['confirmation_url'] . '" />';
        }

        if ( strlen($options[$number]['error_url']) > 0 )
        {
            $form .= '<input type="hidden" name="error_url" id="error_url" value="'. $options[$number]['error_url'] .'" />';
        }
        
        if ( strlen($options[$number]['campaign_name']) > 0 )
        {
            $form .= '<input type="hidden" name="campaign_name" id="campaign_name" value="'. $options[$number]['campaign_name'] .'" />';
        }

        if ( strlen($options[$number]['custom_ref']) > 0 )
        {
            $form .= '<input type="hidden" name="custom_ref" id="custom_ref" value="' . $options[$number]['custom_ref'] . '" />';
        }       
        
        if ( strlen($options[$number]['title']) > 0 )
        {
            $form .= '<h2 class="widgettitle GRf-title">' . $options[$number]['title'] . '</h2>';
        }
				
        
        if ( 'yes' === $options[$number]['hide_name'] )
        {
            $form .= '<div style="display:none;">';
            $form .= '<label for="subscriber_name">'. $options[$number]['subscriberNameText'] .'</label>';
            $form .= '<input id="subscriber_name" name="subscriber_name" type="text" value="Friend"/>';
            $form .= '</div>';
        }
        else
        {
            $form .= '<div class="GRf-row">';
            $form .= '<label for="subscriber_name">'. $options[$number]['subscriberNameText'] .'</label>';
            $form .= '<input id="subscriber_name" name="subscriber_name" type="text" value=""/>';
            $form .= '</div>';
        }
	    
        $form .= '<div class="GRf-row">';
        $form .= '<label for="subscriber_email">'. $options[$number]['subscriberEmailText'] .'</label>';
        $form .= '<input id="subscriber_email" name="subscriber_email" type="text" value=""/>';
        $form .= '</div>';		

        // customs form hadnler
            if ( isset($options[$number]['customs']) and is_array($options[$number]['customs']) and count($options[$number]['customs']) > 0 )
	    {
	        foreach ( $options[$number]['customs'] as $values )
	        {
	            if ( isset($values['hidden']) )
	            {
                    $form .= '<input type="hidden" name="custom_'. $values['name'] .'" id="'. $values['name'] . '" value="' . (empty($values['value']) ? '' : $values['value']) . '" />';
	            }
	            else
	            {
                    $form .= '<div class="GRf-row">';
                    $form .= '<label for="'. $values['name'] .'">'. $values['name'] . '</label>';
                    $form .= '<input id="'. $values['name'] .'" name="custom_'. $values['name'] .'" type="text" value="' . (empty($values['value']) ? '' : $values['value']) . '"/>';
                    $form .= '</div>';
	            }
	        }
	    }    		    
        
        $form .= '<div class="GRf-hCnt">';
        $form .= '<input type="submit" class="GRfh-In" value="'. $options[$number]['buttonText'] .'" />';
        $form .= '</div>';

        $form .= '<div class="GRf-info">';
        $form .= 'GetResponse <a href="http://www.getresponse.com/" title="Email Marketing">Email Marketing</a>';
        $form .= '</div>';

        $form .= '</form>';        		      
 
	if ( 'yes' === $options[$number]['show_counter'] )
        {
            $form .= '<div>';
            $form .= '<script type="text/javascript" src="http://www.getresponse.com/display_subscribers_count.js?campaign_name='. $options[$number]['campaign_name'] .'"><!--empty--></script>';           
            $form .= '</div>';

        }

        $form .= '<P>';
        $new_id = get_option($this->GrOptionDbPrefix . 'new_web_from_id');
        $style_id = get_option($this->GrOptionDbPrefix . 'style_id');

        if($style_id == 0)
        {
        $form .= '<script type="text/javascript" src=http://www.getresponse.com/view_webform.js?wid='. $new_id .'&css=1"></script>';
        }
        elseif($style_id == 1)
        {
        $form .= '<script type="text/javascript" src="http://www.getresponse.com/view_webform.js?wid='. $new_id .'"></script>';
        }
        $form .= '</P>';

        echo $form;
	}
	
	/**
	 * Widget configuration panel
	 */
	function WidgetControl( $args = null )
	{    
		$number = 0;
		if ( isset($args['number']) and is_numeric($args['number']) )
		{
		    $number = $args['number'];
		}
		
		// get widget options
        $widget_options = get_option( $this->GrOptionDbPrefix . 'widget' );
                
        // post data handling
		if ( isset($_POST['sidebar']) and isset($_POST['widget_getresponse']) and is_array($_POST['widget_getresponse']) ) 
		{
		    foreach ( $_POST['widget_getresponse'] as $widget_number => $values ) 
			{
			    foreach ( $values as $name => $value )
			    {
			        // check if post name is a correct one
			        if ( isset($this->GrDefaultWidgetOptions[$name]) )
			        {
                        $widget_options[$widget_number][$name] = $value;
			        }
			        // customs handler
			        elseif( true == preg_match('/custom_/',$name) and is_array($value)  ) 
			        {
			            // remove unused customs
			            unset($widget_options[$widget_number]['customs'][$name]);
			            
			            // only add custom if name have added rest is optional
			            if ( '' != $value['name'] )
			            {
			                $widget_options[$widget_number]['customs'][$name] = $value;  
			            }
			        }
			    }
			}

			// update widget options in db
			update_option($this->GrOptionDbPrefix . 'widget', $widget_options);
		}
				
	    // $number - is dynamic number for multi widget, gived by WP
    	// by default $number = -1 (if no widgets activated). In this case we should use %i% for inputs
    	// to allow WP generate number automatically
    	$number = $args['number'] == -1 ? '%i%' : $args['number'];
		
    	// load def values if don't exists
	    if (empty($widget_options[$number]))
		{
		    $widget_options[$number] = $this->GrDefaultWidgetOptions;
		}
		
		// gr form options
    	?>
    		<p>
    		    <?php _e('This widget allows you to add an <a href="http://www.getresponse.com/" target="_blank">GetResponse</a> Web Subscription Form to your site.', 'Gr_Integration'); ?>
    		</p>
    		    		
    		<h3>
    		    <?php _e('GetResponse Options', 'Gr_Integration'); ?>
    		</h3>

    		<?php _e('<b>Title</b> (Optional)', 'Gr_Integration'); ?>
    		<br />
    		<?php _e('(eg. Subscribe)', 'Gr_Integration'); ?>
    		<br />
				<input name="widget_getresponse[<?php echo $number; ?>][title]" type="text" value="<?php echo attribute_escape($widget_options[$number]['title']); ?>" />
			<br />		
    		<br />
    		
    		<?php _e('<b>Button Text</b> (Optional)', 'Gr_Integration'); ?>
    		<br />
    		<?php _e('(eg. Subscribe)', 'Gr_Integration'); ?>
    		<br />
				<input name="widget_getresponse[<?php echo $number; ?>][buttonText]" type="text" value="<?php echo attribute_escape($widget_options[$number]['buttonText']); ?>" />
			<br />		
    		<br />    		
    		
    		<?php _e('<b>Email Text</b> (Optional)', 'Gr_Integration'); ?>
    		<br />
    		<?php _e('(eg. Email)', 'Gr_Integration'); ?>
    		<br />
				<input name="widget_getresponse[<?php echo $number; ?>][subscriberEmailText]" type="text" value="<?php echo attribute_escape($widget_options[$number]['subscriberEmailText']); ?>" />
			<br />		
    		<br />     		
    		
    		<?php _e('<b>Name Text</b> (Optional)', 'Gr_Integration'); ?>
    		<br />
    		<?php _e('(eg. Your Name)', 'Gr_Integration'); ?>
    		<br />
				<input name="widget_getresponse[<?php echo $number; ?>][subscriberNameText]" type="text" value="<?php echo attribute_escape($widget_options[$number]['subscriberNameText']); ?>" />
			<br />		
    		<br />  
    		
    		<?php _e('<b>Confirmation URL</b> (Optional)', 'Gr_Integration'); ?>
    		<br />
    		<?php _e('(eg. http://example.com/greetings.html)', 'Gr_Integration'); ?>
    		<br />
				<input name="widget_getresponse[<?php echo $number; ?>][confirmation_url]" type="text" value="<?php echo attribute_escape($widget_options[$number]['confirmation_url']); ?>" />
			<br />		
    		<br /> 

    		<?php _e('<b>Error URL</b> (Optional)', 'Gr_Integration'); ?>
    		<br />
    		<?php _e('(eg. http://example.com/error.html)', 'Gr_Integration'); ?>
    		<br />
				<input name="widget_getresponse[<?php echo $number; ?>][error_url]" type="text" value="<?php echo attribute_escape($widget_options[$number]['error_url']); ?>" />
			<br />		
    		<br /> 

    		<?php _e('<b>Your Getresponse Campaign Name</b> (Required)', 'Gr_Integration'); ?>
    		<br />
    		<?php _e('(eg. best_campaign)', 'Gr_Integration'); ?>
    		<br />
				<input name="widget_getresponse[<?php echo $number; ?>][campaign_name]" type="text" value="<?php echo attribute_escape($widget_options[$number]['campaign_name']); ?>" />
			<br />		
    		<br /> 
    		
    		<?php _e('<b>Ref Custom</b> (Optional)', 'Gr_Integration'); ?>
    		<br />
    		<?php _e('(eg. 555, ref)', 'Gr_Integration'); ?>
    		<br />
				<input name="widget_getresponse[<?php echo $number; ?>][custom_ref]" type="text" value="<?php echo attribute_escape($widget_options[$number]['custom_ref']); ?>" />
			<br />		
    		<br /> 

    		<h3>
    		    <?php _e('Customs', 'Gr_Integration'); ?>
    		</h3>

    		<?php
    		    // handle existing customs
    		    if ( isset($widget_options[$number]['customs']) and is_array($widget_options[$number]['customs']) and count($widget_options[$number]['customs']) > 0 )
    		    {
    		        foreach ( $widget_options[$number]['customs'] as $custom_id => $values )
    		        {
    		            ?>
                		<div>
                    		<br />
                				<?php _e('Name:', 'Gr_Integration'); ?><input style="width: 60%;" name="widget_getresponse[<?php echo $number; ?>][<?php echo $custom_id; ?>][name]" id="name" type="text" value="<?php echo $values['name']; ?>" />
                			<br />
                				<?php _e('Value:', 'Gr_Integration'); ?><input style="width: 60%;" name="widget_getresponse[<?php echo $number; ?>][<?php echo $custom_id; ?>][value]" id="value" type="text" value="<?php echo $values['value']; ?>" />	
                    		<br />
                    			<?php _e('Hide:', 'Gr_Integration'); ?><input style="width: 60%;" name="widget_getresponse[<?php echo $number; ?>][<?php echo $custom_id; ?>][hidden]" id="hidden" type="checkbox" value="on" <?php echo !empty($values['hidden']) ? 'checked=checked' : null; ?>"/>	
                			<span style="cursor:pointer;" onclick="RemoveExistingCustom(this);"><u><?php _e('<b>Remove</b>', 'Gr_Integration'); ?></u></span>	
                			<br />
                			<br />
                		</div> 
                		<?php 
    		        }
    		        ?>
                		<p><?php _e('(Remember to save!)', 'Gr_Integration'); ?></p>
            		<?php
    		    }
    		?>

    		<span style="cursor:pointer;" onclick="ShowHide('add_customs_<?php echo $number; ?>');"><u><?php _e('<b>Add custom(s)</b>', 'Gr_Integration'); ?></u></span>
    		<br />
    		<br />
    		<div>    		
    			<?php $id = time();  ?>
        		<div class="to_clone_<?php echo $number; ?>" id="add_customs_<?php echo $number; ?>" style="display: none;">
            		<br />
        				<?php _e('Name:', 'Gr_Integration'); ?><input style="width: 60%;" name="widget_getresponse[<?php echo $number; ?>][custom_<?php echo $id; ?>][name]" id="name" type="text" value="" />
        			<br />
        				<?php _e('Value:', 'Gr_Integration'); ?><input style="width: 60%;" name="widget_getresponse[<?php echo $number; ?>][custom_<?php echo $id; ?>][value]" id="value" type="text" value="" />	
            		<br />
            			<?php _e('Hide:', 'Gr_Integration'); ?><input style="width: 60%;" name="widget_getresponse[<?php echo $number; ?>][custom_<?php echo $id; ?>][hidden]" id="hidden" type="checkbox" value="on" />	
        				<a style="cursor:pointer;" onclick="AddCustom('to_clone_<?php echo $number; ?>');">[+]</a>
        				<a id="remove_custom" style="display: none; cursor:pointer;" onclick="RemoveCustom('to_clone_<?php echo $number; ?>');">[-]</a>
        			<br />
        			<br />
        		</div> 
    		</div>
    		
    		<?php _e('<b>Show Contacts Counter</b>', 'Gr_Integration'); ?>
    		<br />
    		<?php _e('(Yes, No)', 'Gr_Integration'); ?>
    		<br />
    			<select name="widget_getresponse[<?php echo $number; ?>][show_counter]">
        			<option value="yes" <?php echo $widget_options[$number]['show_counter'] == 'yes' ? 'selected=selected' : null; ?> >yes</option>
        			<option value="no" <?php echo $widget_options[$number]['show_counter'] == 'no' ? 'selected=selected' : null; ?> >no</option>
    			</select>
			<br />		
    		<br />

    		<?php _e('<b>Hide Name Input</b> All contacts will be added with name Friend', 'Gr_Integration'); ?>
    		<br />
    		<?php _e('(Yes, No)', 'Gr_Integration'); ?>
    		<br />
    			<select name="widget_getresponse[<?php echo $number; ?>][hide_name]">
        			<option value="yes" <?php echo $widget_options[$number]['hide_name'] == 'yes' ? 'selected=selected' : null; ?> >yes</option>
        			<option value="no" <?php echo $widget_options[$number]['hide_name'] == 'no' ? 'selected=selected' : null; ?> >no</option>
    			</select>
			<br />		
    		<br />
    	<?php
	}
}

//init plugin
if( defined('ABSPATH') and defined('WPINC') )
{
    if ( empty($GLOBALS['Gr_Integration']) )
    {
        $GLOBALS['Gr_Integration'] = new Gr_Integration();
    }
}
?>