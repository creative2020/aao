<?php
/*
Plugin Name: Investment Ideas
Plugin URI: http://www.racytech.com/
Description: investment_ideas Plugin
Author: Racytech
Author http://www.racytech.com/
Version: 0.1
*/

global $category_array;
$category_array = array();

function inv_add_menu() {
	
	global $category_array;
	$icon_url = WP_PLUGIN_URL . '/' . plugin_basename(dirname(__FILE__)) . '/generic.png';
	add_menu_page('investment_ideas', 'Investment', 0, __FILE__, 'investment_ideas_mgt_list',$icon_url);
	add_submenu_page(__FILE__, 'Edit investment idea', 'Edit', 0, __FILE__, 'investment_ideas_mgt_list');
	add_submenu_page(__FILE__, 'Post investment idea', 'Post investment idea', 0, 'investment_ideas_mgt_edit', 'investment_ideas_mgt_edit');
	
	if(!$category_id=category_exists("investment_ideas"))
		wp_create_category("investment_ideas");
	else
		$category_array['investment_ideas']=$category_id;
		
}

add_action('admin_menu', 'inv_add_menu');

add_filter('admin_head','zd_multilang_tinymce');

function zd_multilang_tinymce() {
	wp_admin_css('thickbox');
	wp_print_scripts('jquery-ui-core');
	wp_print_scripts('jquery-ui-tabs');
	wp_print_scripts('post');
	wp_print_scripts('editor');
	add_thickbox();
	wp_print_scripts('media-upload');
	if (function_exists('wp_tiny_mce')) wp_tiny_mce();
}

function investment_ideas_mgt_list() {
global $wpdb,$category_array,$current_user;

$current_user = wp_get_current_user();
$user_id = $current_user->ID;

include("investment_ideas_function.php");

$max_res=5;
if($_REQUEST['pagenew'])
{
$pge=$_REQUEST['pagenew'];
}
else
{
$pge=1;
}

$nol=4;

$from =(($pge * $max_res) - $max_res) ; 

$action = $_REQUEST['action'];
$post_id = $_REQUEST['post'];
$post_status = $_REQUEST['post_status'];

switch($action) {
case 'trash':

	if ( ! wp_trash_post($post_id) )
		wp_die( __('Error in moving to Trash.') );
	
	break;
case 'untrash':

	if ( ! wp_untrash_post($post_id) )
		wp_die( __('Error in restoring from Trash.') );

	break;
case 'delete':
	$force = !EMPTY_TRASH_DAYS;
	if ( $post->post_type == 'attachment' ) 
	{
		$force = ( $force || !MEDIA_TRASH );
		if ( ! wp_delete_attachment($post_id, $force) )
			wp_die( __('Error in deleting.') );
	} 
	else 
	{
		if ( !wp_delete_post($post_id, $force) )
			wp_die( __('Error in deleting.') );
	}

	break;
} // end switch


	foreach($category_array as $cat_id)
	{
		$list_cat .= "'$cat_id',";
	}
	
	$list_cat = substr($list_cat,0,strlen($list_cat)-1);
	
	if($post_status=='publish')
		$status_condition = "$wpdb->posts.post_status = 'publish'";
	elseif($post_status=='draft')
		$status_condition = "$wpdb->posts.post_status = 'draft'";
	elseif($post_status=='trash')
		$status_condition = "$wpdb->posts.post_status = 'trash'";
	else
		$status_condition = "$wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'future' OR $wpdb->posts.post_status = 'draft' OR $wpdb->posts.post_status = 'pending' OR $wpdb->posts.post_status = 'private'";
		
	$querystr = "SELECT SQL_CALC_FOUND_ROWS $wpdb->posts . *,$wpdb->term_taxonomy.term_id
	FROM $wpdb->posts
	INNER JOIN $wpdb->term_relationships ON ( $wpdb->posts.ID = $wpdb->term_relationships.object_id )
	INNER JOIN $wpdb->term_taxonomy ON ( $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id )
	WHERE 1 =1
	AND $wpdb->term_taxonomy.taxonomy = 'category'
	AND $wpdb->term_taxonomy.term_id IN (".$list_cat.")
	AND $wpdb->posts.post_type = 'post'
	AND ( ".$status_condition." )
	AND post_author = '".$user_id."'
	GROUP BY $wpdb->posts.ID
	ORDER BY $wpdb->posts.post_date DESC
	";
	
	$tot_count=$wpdb->query($querystr);
	
	$querystr.=" limit ". $from .",".$max_res;
	
	$pageposts = $wpdb->get_results($querystr, OBJECT);
	$cnt=count($pageposts)-1;
	if($cnt==0)
	{
		 $page=$pge-1;
	}
	else
	{
		$page=$pge;
	}
	
	$queryall_post = "SELECT SQL_CALC_FOUND_ROWS $wpdb->posts . *
	FROM $wpdb->posts
	INNER JOIN $wpdb->term_relationships ON ( $wpdb->posts.ID = $wpdb->term_relationships.object_id )
	INNER JOIN $wpdb->term_taxonomy ON ( $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id )
	WHERE 1 =1
	AND $wpdb->term_taxonomy.taxonomy = 'category'
	AND $wpdb->term_taxonomy.term_id IN (".$list_cat.")
	AND (
	$wpdb->posts.post_status = 'publish'
	OR $wpdb->posts.post_status = 'future'
	OR $wpdb->posts.post_status = 'draft'
	OR $wpdb->posts.post_status = 'pending'
	OR $wpdb->posts.post_status = 'private'
	OR $wpdb->posts.post_status = 'trash'
	)
	AND post_author = '".$user_id."'
	GROUP BY $wpdb->posts.ID
	ORDER BY $wpdb->posts.post_date DESC
	";
	
	$all_post = $wpdb->get_results($queryall_post, OBJECT);
	$all_postes = count($all_post);
	
	foreach ( $all_post as $row ) { 
	if( $row->post_status=='publish' )
	$publish_count += 1;
	elseif( $row->post_status=='draft' )
	$draft_count += 1;
	elseif( $row->post_status=='trash' )
	$trash_count += 1;
	}
?>

<div class="wrap">
<?php if($action=='trash' || $action=='untrash' || $action=='delete') { ?>
<p>&nbsp;</p>
<div id="message" class="updated"><p>
<?php
if ( $action=='trash' ) {
	printf( 'Item moved to the trash.' );
	echo ' <a href="?page=investment-ideas/investment_ideas.php&doaction=undo&action=untrash&post='.$post_id.'&post_status='.$post_status.'">' . __('Undo') . '</a><br />';
}

if ( $action=='untrash' ) {
	printf( 'Item restored from the Trash.' );
}

if ( $action=='delete' ) {
	printf( 'Item permanently deleted.' );
}

?>
</p></div>
<?php } ?>

<ul class="subsubsub">
<li><a href="?page=investment-ideas/investment_ideas.php" <?php if($post_status=='') { ?> class="current" <?php }?>>All <?php if($all_postes-$trash_count>0) { ?><span class="count">(<?php echo $all_postes-$trash_count;?>)</span><?php } ?></a></li>

<?php if( $publish_count!=0 ) { ?>
<li> | <a href='?page=investment-ideas/investment_ideas.php&post_status=publish' <?php if($post_status=='publish') { ?> class="current" <?php } ?>>Published <span class="count">(<?php echo $publish_count;?>)</span></a></li>
<?php } ?>

<?php if( $draft_count!=0 ) { ?>
<li> | <a href='?page=investment-ideas/investment_ideas.php&post_status=draft' <?php if($post_status=='draft') { ?> class="current" <?php } ?>>Drafts <span class="count">(<?php echo $draft_count;?>)</span></a></li>
<?php } ?>

<?php if( $trash_count!=0 ) { ?>
<li> | <a href='?page=investment-ideas/investment_ideas.php&post_status=trash' <?php if($post_status=='trash') { ?> class="current" <?php } ?>>Trash <span class="count">(<?php echo $trash_count;?>)</span></a></li>
<?php } ?>
</ul>
<?php echo paging_new($tot_count,$max_res,$pge,$nol);?>
<table class="widefat post fixed" cellspacing="0">
	<?php if($pageposts){ ?>
	<thead>
	<tr>
	<th scope="col" id="title" class="manage-column column-title" style="">Post</th>
	<th scope="col" id="author" class="manage-column column-author" style="">Author</th>
	<th scope="col" id="categories" class="manage-column column-categories" style="">Type</th>
	<th scope="col" id="date" class="manage-column column-date" style="">Date</th>
	</tr>
	</thead>
	<tfoot>
	<tr>
	<th scope="col" id="title" class="manage-column column-title" style="">Post</th>
	<th scope="col" id="author" class="manage-column column-author" style="">Author</th>
	<th scope="col" id="categories" class="manage-column column-categories" style="">Type</th>
	<th scope="col" id="date" class="manage-column column-date" style="">Date</th>
	</tr>
	</tfoot>
	<tbody>
	<?php foreach ($pageposts as $row) { ?>
	<tr id="post-<?php echo $row->ID;?>" class='alternate author-self status-publish iedit' valign="top">
		<td class="post-title column-title">
		<strong><a class="row-title" href="?page=<?php if(get_cat_name($row->term_id)=="investment_ideas"){echo "investment_ideas_mgt_edit";} else if(get_cat_name($row->term_id)=="Page"){echo "page_mgt_edit";}?>&action=edit&post=<?php echo $row->ID;?>" title="Edit <?php echo $row->post_title;?>"><?php echo $row->post_title;?></a> 
		<?php 
		if($row->post_password!='' || $row->post_status=='draft') { 
		if($row->post_password!='' && $row->post_status=='draft' && $post_status!='draft') { echo " - <span class=\"post-state\">Password protected</span>,<span class=\"post-state\">Draft</span>"; }
		else if($row->post_password!='') { echo " - <span class=\"post-state\">Password protected</span>"; }
		else if($row->post_status=='draft' && $post_status!='draft') { echo " - <span class=\"post-state\">Draft</span>"; }
		}?>
		</strong>
		<?php if($post_status=='trash') { ?>
		<div class="row-actions"><span class='untrash'><a title='Restore this post from the Trash' href='?page=investment-ideas/investment_ideas.php&action=untrash&amp;post=<?php echo $row->ID;?>&post_status=<?php echo $post_status;?>'>Restore</a> | </span><span class='delete'><a class='submitdelete' title='Delete this post permanently' href='?page=investment-ideas/investment_ideas.php&action=delete&amp;post=<?php echo $row->ID;?>&post_status=<?php echo $post_status;?>'>Delete Permanently</a></span></div>
		<?php } else { ?>
		<div class="row-actions"><span class='edit'><a href="?page=<?php if(get_cat_name($row->term_id)=="investment_ideas"){echo "investment_ideas_mgt_edit";} else if(get_cat_name($row->term_id)=="Page"){echo "page_mgt_edit";}?>&action=edit&post=<?php echo $row->ID;?>" title="Edit this post">Edit</a> | </span> <span class='trash'><a class='submitdelete' title='Move this post to the Trash' href='?page=investment-ideas/investment_ideas.php&action=trash&amp;post=<?php echo $row->ID;?>&post_status=<?php echo $post_status;?>'>Trash</a> | </span><span class='view'><a href="<?php echo $row->guid;?>" title="View <?php echo $row->post_title;?>" rel="permalink">View</a></span></div>
		<?php } ?>
		</td>
		<td class="author column-author"><a href="profile.php"><?php the_author_meta('display_name',$row->post_author)?></a></td>
		<td class="categories column-categories"><?php echo get_cat_name($row->term_id);?></td>
		<td class="date column-date">
		<?php if( $row->post_status=='draft' ) { ?>
		<abbr title="<?php echo get_post_modified_time('Y/m/d H:i:s A', false, $row->ID, true);?>"><?php echo get_post_modified_time('Y/m/d', false, $row->ID, true);?></abbr>
		<br />Last Modified
		<?php } else { ?>
		<abbr title="<?php echo get_post_time('Y/m/d H:i:s A', false, $row->ID, true);?>"><?php echo get_post_time('Y/m/d', false, $row->ID, true);?></abbr>
		<br />Published
		<?php } ?>
		</td>	
	</tr>
	<?php } ?>
	<div class="clear"></div>
	<?php } else { ?>
		<?php if($post_status=='trash') { ?>
		<p>No investment ideas found in Trash</p>
		<?php } else { ?>
		<p>No investment ideas found</p>
		<?php } ?>
	<?php } ?>
	</tbody>
</table>
<?php paging_new($tot_count,$max_res,$pge,$nol);?>
</div>
<?php
}

//////////////////////////////////////////////////////////

function investment_ideas_mgt_edit() 
{
global $current_user,$category_array,$wpdb;

$current_user = wp_get_current_user();
include("investment_ideas_function.php");

$post_status = "draft";
$visibility_status = "public";

if(is_dir(WP_CONTENT_DIR."/uploads/multiupload/".$current_user->ID)==false)
mkdir(WP_CONTENT_DIR."/uploads/multiupload/".$current_user->ID,0777);

$folder_name = $current_user->ID;
$http_url = get_bloginfo('wpurl');

if($_REQUEST['save'])
{
	$post_title = $_REQUEST['post_title'];
	$post_content = $_REQUEST['content'];
	$post_ticker = $_REQUEST['post_ticker'];
	$post_status = "draft";
	
	$my_post['post_title'] = $post_title;
	$my_post['post_content'] = $post_content;
	$my_post['post_author'] = $current_user->ID;
	$my_post['post_category'] = array($category_array['investment_ideas']);
	$my_post['post_status'] = $post_status;
	$my_post['comment_status'] = "open";
	
	if($_REQUEST['visibility']=="password protected" && $_REQUEST['post_password']!='')
	{
	$post_password = $_REQUEST['post_password'];
	$my_post['post_password'] = $post_password;
	$visibility_status = "password protected";
	}
	else
	{
	$post_password = "";
	$my_post['post_password'] = $post_password;
	$visibility_status = "public";
	}
	
	if($_REQUEST['editid']!='')
	{
		$editid = $_REQUEST['editid'];
		$my_post['ID'] = $editid;
		$post_id = $editid;
		
		// Update the post into the database
		wp_update_post($my_post);
		update_post_meta($post_id, 'post_ticker', $post_ticker);
	}
	else
	{
		// Insert the post into the database
		$post_id = wp_insert_post($my_post);
		add_post_meta($post_id, 'post_ticker', $post_ticker);
	}
	
	$upload_files_array = explode(",",$_REQUEST['uploadfiles']);

	for($j=0;$j<count($upload_files_array);$j++)
	{
		if($upload_files_array[$j]!='' && $post_id!='')
		{
			$time = time();
			$source_file = WP_CONTENT_DIR.'/uploads/multiupload/'.$folder_name.'/'.$upload_files_array[$j];
			$dest_file = WP_CONTENT_DIR.'/uploads/'.$time.$upload_files_array[$j];
			copy($source_file,$dest_file);
			
			$filename = $http_url.'/wp-content/uploads/'.$time.$upload_files_array[$j];
			$wp_filetype = wp_check_filetype(basename($filename), null );
			
			$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title' => basename($time.$upload_files_array[$j]),
			'guid' => $filename,
			'post_content' => '',
			'post_status' => 'inherit'
			);
			
			$attach_id = wp_insert_attachment( $attachment, $filename, $post_id );
			require_once(ABSPATH . "wp-admin" . '/includes/image.php');
			$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
			wp_update_attachment_metadata( $attach_id,  $attach_data );
			unlink($source_file);
		}
	}

	printf( '<div id="message" class="updated"><p>');
	printf( 'Investment idea draft updated.' );
	?>
	<a href="<?php bloginfo("siteurl"); ?>/?p=<?php echo $post_id;?>&preview=true" target="_blank">Preview investment idea</a><br />
	<?php
	printf( '</p></div>' );	
	
	$action = "edit";
}


if($_REQUEST['publish'])
{
	$post_title = $_REQUEST['post_title'];
	$post_content = $_REQUEST['content'];
	$post_ticker = $_REQUEST['post_ticker'];
	$post_status = "publish";
	
	$my_post['post_title'] = $post_title;
	$my_post['post_content'] = $post_content;
	$my_post['post_author'] = $current_user->ID;
	$my_post['post_category'] = array($category_array['investment_ideas']);
	$my_post['post_status'] = $post_status;
	$my_post['comment_status'] = "open";

	if($_REQUEST['visibility']=="password protected" && $_REQUEST['post_password']!='')
	{
	$post_password = $_REQUEST['post_password'];
	$my_post['post_password'] = $post_password;
	$visibility_status = "password protected";
	}
	else
	{
	$post_password = "";
	$my_post['post_password'] = $post_password;
	$visibility_status = "public";
	}

	// Insert the post into the database
	$post_id = wp_insert_post($my_post);
	add_post_meta($post_id, 'post_ticker', $post_ticker);
	
	$upload_files_array = explode(",",$_REQUEST['uploadfiles']);

	for($j=0;$j<count($upload_files_array);$j++)
	{
		if($upload_files_array[$j]!='' && $post_id!='')
		{
			$time = time();
			$source_file = WP_CONTENT_DIR.'/uploads/multiupload/'.$folder_name.'/'.$upload_files_array[$j];
			$dest_file = WP_CONTENT_DIR.'/uploads/'.$time.$upload_files_array[$j];
			copy($source_file,$dest_file);
			
			$filename = $http_url.'/wp-content/uploads/'.$time.$upload_files_array[$j];
			$wp_filetype = wp_check_filetype(basename($filename), null );
			
			$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title' => basename($time.$upload_files_array[$j]),
			'guid' => $filename,
			'post_content' => '',
			'post_status' => 'inherit'
			);
			
			$attach_id = wp_insert_attachment( $attachment, $filename, $post_id );
			require_once(ABSPATH . "wp-admin" . '/includes/image.php');
			$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
			wp_update_attachment_metadata( $attach_id,  $attach_data );
			unlink($source_file);
		}
	}
	
	printf( '<div id="message" class="updated"><p>');
	printf( 'Investment idea published.' );
	?>
	<a href="<?php bloginfo("siteurl"); ?>/?p=<?php echo $post_id;?>" target="_blank">View investment idea</a><br />
	<?php
	printf( '</p></div>' );	
	
	$action = "edit";
}

if($_REQUEST['update'])
{
	$post_title = $_REQUEST['post_title'];
	$post_content = $_REQUEST['content'];
	$editid = $_REQUEST['editid'];
	$post_ticker = $_REQUEST['post_ticker'];
	$post_status = "publish";
	
	$my_post['ID'] = $editid;
	$my_post['post_title'] = $post_title;
	$my_post['post_content'] = $post_content;
	$my_post['post_author'] = $current_user->ID;
	$my_post['post_category'] = array($category_array['investment_ideas']);
	$my_post['post_status'] = $post_status;
	$my_post['comment_status'] = "open";

	if($_REQUEST['visibility']=="password protected" && $_REQUEST['post_password']!='')
	{
	$post_password = $_REQUEST['post_password'];
	$my_post['post_password'] = $post_password;
	$visibility_status = "password protected";
	}
	else
	{
	$post_password = "";
	$my_post['post_password'] = $post_password;
	$visibility_status = "public";
	}
	
	// Update the post into the database
	wp_update_post($my_post);
	update_post_meta($editid, 'post_ticker', $post_ticker);
	
	$upload_files_array = explode(",",$_REQUEST['uploadfiles']);

	for($j=0;$j<count($upload_files_array);$j++)
	{
		if($upload_files_array[$j]!='' && $editid!='')
		{
			$time = time();
			$source_file = WP_CONTENT_DIR.'/uploads/multiupload/'.$folder_name.'/'.$upload_files_array[$j];
			$dest_file = WP_CONTENT_DIR.'/uploads/'.$time.$upload_files_array[$j];
			copy($source_file,$dest_file);
			
			$filename = $http_url.'/wp-content/uploads/'.$time.$upload_files_array[$j];
			$wp_filetype = wp_check_filetype(basename($filename), null );
			
			$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title' => basename($time.$upload_files_array[$j]),
			'guid' => $filename,
			'post_content' => '',
			'post_status' => 'inherit'
			);
			
			$attach_id = wp_insert_attachment( $attachment, $filename, $editid );
			require_once(ABSPATH . "wp-admin" . '/includes/image.php');
			$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
			wp_update_attachment_metadata( $attach_id,  $attach_data );
			unlink($source_file);
		}
	}

	printf( '<div id="message" class="updated"><p>');
	printf( 'Investment idea updated.' );
	?>
	<a href="<?php bloginfo("siteurl"); ?>/?p=<?php echo $editid;?>" target="_blank">View investment idea</a><br />
	<?php
	printf( '</p></div>' );	
	
	$post_id = $editid;
	$action = "edit";
}

if($_REQUEST['post'] || $post_id!='')
{
	if($_REQUEST['post']!='')
	$post = $_REQUEST['post'];
	else
	$post = $post_id;
	
	$sql = "SELECT * FROM $wpdb->posts WHERE ID='$post'";
	$getpost = $wpdb->get_row($sql);
	
	$post_title = $getpost->post_title;
	$post_content = $getpost->post_content;
	$editid = $getpost->ID;
	$post_status = $getpost->post_status;
	
	if($getpost->post_password!='')
	{
	$post_password = $getpost->post_password;
	$visibility_status = "password protected";
	}
	$action = "edit";
	$post_id = $post;
	
	$get_post_ticker = get_post_custom_values('post_ticker', $post);
	if($get_post_ticker)
	$post_ticker = $get_post_ticker[0];
	
	$Images = & get_children( 'post_type=attachment&post_parent=' . $post );

    foreach ($Images as $Image) {
    $NewImagesArray [] = $Image;
    }
}

require_once "include_phpuploader.php";
include('add_investment_ideas.php');
}

?>