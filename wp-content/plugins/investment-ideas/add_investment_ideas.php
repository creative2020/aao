
<div class="wrap">
<div id="icon-edit" class="icon32"><br /></div>

<h2>Add New Investment ideas</h2>
<form name="post_investment_ideas" action="" method="post" id="post">
<input type="hidden" value="" name="uploadfiles" >
<div id="poststuff" class="metabox-holder has-right-sidebar">

<div id="side-info-column" class="inner-sidebar">

<div id="side-sortables" class="meta-box-sortables"><div id="submitdiv" class="postbox " >
<div class="handlediv" title="Click to toggle"><br /></div><h3 class='hndle'><span>Publish</span></h3>

<div class="inside">
<div class="submitbox" id="submitpost">

<div id="minor-publishing">


<div id="minor-publishing-actions">

<div id="save-action">
<input  type="submit" name="save" id="save-post" value="<?php if($post_status!='draft') { ?>Revert to draft<?php } else {?>Save Draft<?php } ?>" tabindex="4" class="button button-highlighted" />
</div>

<?php if($post_id!='') { ?>
<div id="preview-action">
<?php if($post_status=='draft') { ?>
<a class="preview button" href="<?php bloginfo("siteurl"); ?>/?p=<?php echo $post_id;?>&preview=true" target="wp-preview" id="post-preview" tabindex="4">Preview</a>
<?php } else if($post_status=='publish') { ?>
<a class="preview button" href="<?php bloginfo("siteurl"); ?>/?p=<?php echo $post_id;?>" target="wp-preview" id="post-preview" tabindex="4">Preview</a>
<?php } ?>
</div>
<?php } ?>

<div class="clear"></div>
</div>
<div id="misc-publishing-actions">

<div style="padding-left: 5px;"><label for="post_status">Status:</label>
<span id="post-status-display"><?php echo $post_status;?></span>
</div>

</div>
</div>
<script type="text/javascript" language="javascript">
function pass_visible(e)
{
	if(e=="public")
	{
		document.getElementById("password-div").style.visibility='hidden';
	}
	else if(e=="password protected")
	{
		document.getElementById("password-div").style.visibility='visible';
	}
	
}
</script>
<div class="misc-pub-section " id="visibility">
Visibility: <span id="post-visibility-display"><?php echo $visibility_status;?></span>
<div id="post-visibility-select">
<input type="radio" name="visibility" id="visibility-radio-public" value="public" <?php if( $visibility_status=="public") {?>checked="checked"<?php } ?>  onClick="return pass_visible(this.value);"/> <label for="visibility-radio-public" class="selectit">Public</label><br />
<input type="radio" name="visibility" id="visibility-radio-password" value="password protected" <?php if( $visibility_status=="password protected") {?>checked="checked"<?php } ?> onClick="return pass_visible(this.value);"/> <label for="visibility-radio-password" class="selectit">Password protected</label><br />
<div id="password-div" style="visibility: hidden;"><label for="post_password">Password:</label> <input type="text" name="post_password" id="post_password" value="<?php echo $post_password;?>" /><br /></div>
</div>
</div>
<script type="text/javascript" language="javascript">
pass_visible("<?php echo $visibility_status;?>");
</script>
<div id="major-publishing-actions">
  <div id="publishing-action">
		<?php if($action=="edit") { ?>
		<input type="hidden" name="editid" value="<?php echo $post_id;?>" />
		<input name="update" type="submit" accesskey="p" tabindex="5" id="publish" class="button-primary" value="<?php if($post_status=="draft") {echo "Publish";} else {echo "Update";}?>" />
		<?php } else { ?>
		<input name="publish" type="submit" class="button-primary" id="publish" tabindex="5" accesskey="p" value="Publish" />
		<?php } ?>
</div>
<div class="clear"></div>
</div>
</div>

</div>
</div>
</div>
</div>


<div id="post-body">
<div id="post-body-content">

<div id="titlediv">
<div id="titlewrap">
Title
<input type="text" name="post_title" size="30" tabindex="1" value="<?php echo stripslashes($post_title);?>" id="title" autocomplete="off" />
</div>
</div>

<div class="postarea">Description
<?php the_editor(stripslashes($post_content), $id = 'content', $prev_id = 'title', $media_buttons = false, $tab_index = 2); ?>
<table id="post-status-info" cellspacing="0"><tbody><tr>
	<td id="wp-word-count"></td>
	<td class="autosave-info">
	<span id="autosave">&nbsp;</span>
	</td>
</tr></tbody></table>
</div>

<div id="titlediv">
<div id="titlewrap">
Ticker
<input type="text" name="post_ticker" size="30" tabindex="1" value="<?php echo stripslashes($post_ticker);?>" id="post_ticker" autocomplete="off" />
</div>
</div>

<div id="titlediv">
<div id="titlewrap">
Attach Files
		<?php
		$uploader=new PhpUploader();
		
		$uploader->MultipleFilesUpload=true;
		$uploader->InsertText="Select Files";
		
		$uploader->MaxSizeKB=1024000;
			
		//$uploader->AllowedFileExtensions="jpeg,jpg,gif,png,pdf";
		
		$uploader->SaveDirectory=WP_CONTENT_DIR."/uploads/multiupload/".$folder_name;
		
		$uploader->Render();
		?>
	<script type="text/javascript">
	function CuteWebUI_AjaxUploader_OnTaskComplete(task) {
	document.post_investment_ideas.uploadfiles.value=document.post_investment_ideas.uploadfiles.value+','+task.FileName;
	}
	</script>
</div>
</div>

<div id="titlediv">
<div id="titlewrap">
<?php
if(count($NewImagesArray)>0)
{
foreach ( $NewImagesArray as $NewImage ) 
{
echo "<a href='".$NewImage->guid."' target='_blank'>".$NewImage->post_title."</a>";
echo "<br />";
}
}
?>
</div>
</div>

</div>
</div>
<br class="clear" />
</div><!-- /poststuff -->
</form>
</div>
