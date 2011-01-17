<?php

    defined('_JEXEC') or die('Direct Access to this location is not allowed.');

    $wid = $params->get('webform_id');
    $css = $params->get('display_css') == 'yes' ? '' : '&css=1';
	$domain = $params->get('notify_language');
	$domain = !empty($domain) ? $domain : 'com';

    echo '<script type="text/javascript" src="http://www.getresponse.'.$domain.'/view_webform.js?wid='.(int)$wid.$css.'"></script>';
?>
