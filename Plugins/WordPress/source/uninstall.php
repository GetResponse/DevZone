<?php
// Plugin uninstall tasks
if ( !defined( 'ABSPATH') || !defined('WP_UNINSTALL_PLUGIN') )
{
    return;
}

$GrOptionDbPrefix = 'GrIntegrationOptions_';

delete_option($GrOptionDbPrefix . 'web_from_id');
delete_option($GrOptionDbPrefix . 'widget');
?>