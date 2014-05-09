<?php
/**
 * class-gr-widget-webform.php
 *
 * @author Grzeogrz Struczynski <grzegorz.struczynski@implix.com>
 * http://getresponse.com
 */
class GR_Widget extends WP_Widget {

	var $GrOptionDbPrefix = 'GrIntegrationOptions_';

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'getresponse-widget',
			__( 'GetResponse Web Form', 'Gr_Integration' ),
			array( 'description' => __( 'Dispaly a GetResponse Web Form on your site.', 'Gr_Integration' ), )
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$webform_id = $instance['select'];
		$style_id = $instance['style'];

		$api_key = get_option($this->GrOptionDbPrefix . 'api_key');
		if ( !empty($api_key)) {
			$api = new GetResponseIntegration($api_key);
			$webform = $api->getWebform($webform_id);
		}

		// css styles Webform/Wordpress
		$css = ($style_id == 1) ? '&css=1' : null;

		if ($webform)
		{
			$form = '<p>';
			$form .= '<script type="text/javascript" src="' . $webform->$webform_id->url . $css .'"></script>';
			$form .= '</p>';
		}

		if (!empty($form))
		{
			echo $args['before_widget'];
			echo __( $form, 'text_domain' );
			echo $args['after_widget'];
		}
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 * @return string|void
	 */
	public function form( $instance ) {

		$select = ($instance) ? esc_attr($instance['select']) : '';
		$style = ($instance) ? esc_attr($instance['style']) : '';
		$api_key = get_option($this->GrOptionDbPrefix . 'api_key');
		if ( !empty($api_key)) {
			$api = new GetResponseIntegration($api_key);
			$campaigns = $api->getCampaigns();

			if ( !empty($campaigns)) {
				$campaign_id = array();
				foreach($campaigns as $cid=>$campaign) {
					$campaign_id[$cid] = $campaign->name;
				}
				$webforms = $api->getWebforms();
				$webforms = Gr_Integration::SortByKeyValue($webforms, 'name');
			}
		}
		?>

		<?php if ($api_key) { ?>
		<p>
			<?php
			if ( !empty($webforms) and false === (is_array($webforms) and isset($webforms['type']) and $webforms['type'] == 'error')) { ?>
			<label for="<?php echo $this->get_field_id( 'select' ); ?>"><?php _e( 'Web Form:' ); ?></label>
			<select name="<?php echo $this->get_field_name('select'); ?>" id="<?php echo $this->get_field_id( 'select' ); ?>" class="widefat">
				<?php
					foreach ($webforms as $webform) {
						echo '<option value="' . $webform->id . '" id="' . $webform->id . '"', $select == $webform->id ? ' selected="selected"' : '', '>', $webform->name . ' (' . $campaign_id[$webform->campaign] . ')', '</option>';
					}
				?>
			</select>
			<?php }else {
				_e('No Webforms', 'Gr_Integration');
				}
			?>
		</p>
		<p>
			<input id="<?php echo $this->get_field_id('style'); ?>" name="<?php echo $this->get_field_name('style'); ?>" type="checkbox" value="1" <?php checked( '1', $style ); ?> />
			<label for="<?php echo $this->get_field_id('style'); ?>"><?php _e('Use Wordpress CSS styles', 'Gr_Integration'); ?></label>
		</p>
		<?php
		}
		else {
			?>
		<p><?php _e('API key is not set.', 'Gr_Integration'); ?></p>
		<?php
		}
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['select'] = strip_tags($new_instance['select']);
		$instance['style'] = strip_tags($new_instance['style']);

		return $instance;
	}

} // class GR_Widget