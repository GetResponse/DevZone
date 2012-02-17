<?php
// Plugin uninstall tasks
if ( !defined( 'ABSPATH') || !defined('WP_UNINSTALL_PLUGIN') )
{
    return;
}

$GrOptionDbPrefix = 'GrIntegrationOptions_';
delete_option($GrOptionDbPrefix . 'new_web_from_id');
delete_option($GrOptionDbPrefix . 'style_id');
delete_option($GrOptionDbPrefix . 'widget');
delete_option($GrOptionDbPrefix . 'comment_on');
delete_option($GrOptionDbPrefix . 'comment_label');
?>