<?php

/**
 * Uninstaller. Removes options and settings from the database.
 * @package ForceNonSSL
 * @author Ian Dunn <ian.dunn@mpangodev.com>
 */
 
if( defined('WP_UNINSTALL_PLUGIN') && WP_UNINSTALL_PLUGIN == 'force-non-ssl/force-non-ssl.php' )
{
	delete_option('FNSSL_exceptions');
	delete_option('FNSSL_options');
}
else
	die('Access denied.');

?>