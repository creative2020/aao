<?php
/*
Plugin Name: Login Message
Plugin URI: http://www.racytech.com/
Description: login_message Plugin
Author: Racytech
Author http://www.racytech.com/
Version: 0.1
*/

function logmsg_add_menu() {
	global $category_array;
	$icon_url = WP_PLUGIN_URL . '/' . plugin_basename(dirname(__FILE__)) . '/generic.png';
	add_menu_page('login_message', 'Login Message', 8, __FILE__, 'login_message_mgt_edit',$icon_url);
}

function login_message_install () 
{
		global $table_prefix, $wpdb;

		$table_name = $table_prefix . "login_message";
		if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") !== $table_name) 
		{
			$sql = "CREATE TABLE {$table_name} (
			  login_message_id int(1) unsigned NOT NULL AUTO_INCREMENT,
			  login_message text NOT NULL,
			  PRIMARY KEY (login_message_id)
			);";

			require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
			dbDelta($sql);
			if ($echo) _e("Table has been created\n");
		} 
		else 
		{
			if ($echo) _e("The table has already been created\n");
		}

}

add_action('admin_menu', 'logmsg_add_menu');
add_action('init', 'login_message_install');
//////////////////////////////////////////////////////////
function login_message_mgt_edit() 
{
	global $wpdb,$table_prefix;
	$table_name = $table_prefix . "login_message";
	
	if($_REQUEST['submit_login_message'])
	{
		$querystr = "SELECT * FROM {$table_name}";
		$pageposts = $wpdb->get_results($querystr, OBJECT);
		
		if(count($pageposts)>0)
		{
		$login_message = $_REQUEST['login_message'];
		$wpdb->query("UPDATE {$table_name} SET login_message='{$login_message}';");
		$errmsg = "Login message updated successfully";
		}
		else
		{
		$login_message = $_REQUEST['login_message'];
		$wpdb->query("INSERT INTO {$table_name} (login_message) VALUES ('{$login_message}')");
		$errmsg = "Login message inserted successfully";
		}
	}
	$meta_info = $wpdb->get_row("SELECT * FROM {$table_name} WHERE login_message_id!=''",ARRAY_A);
	$login_message = stripslashes($meta_info['login_message']);
?>
<div class="wrap">
<h2>Update Login Message</h2>
<table width="100%" cellpadding="0" cellspacing="2" border="0">
<form name="post_login_message" action="" method="post" id="post">
<tr>
  <td width="7%">&nbsp;</td>
  <td width="93%"><font color="#FF0000"><?php if($errmsg) { echo $errmsg; }?></font></td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td><textarea name="login_message" style="width: 400px; height: 250px;"><?php echo $login_message;?></textarea></td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td><input type="submit" name="submit_login_message" value="Submit" /></td>
</tr>
</form>
</table>
</div>
<?php
}
?>