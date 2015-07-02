<?php get_header();?>
<div id="content">
<div id="content-main">
<?php
// get all category ids
$category_ids = get_all_category_ids();
foreach($category_ids as $cat_id) {
  $cat_name = get_cat_name($cat_id);
  if($cat_name!='investment_ideas')
  $cateids .= $cat_id.",";
}
$cateids=substr($cateids,0,strlen($cateids)-1);

//detect the set number of posts per page
$numofpage = get_option('posts_per_page');

// first page 14 posts
if (!is_paged()) {
	$posts = get_posts('numberposts='.$numofpage.'&category='.$cateids);
// second page with offset
} elseif($paged == 2) {
    $posts = get_posts('numberposts='.$numofpage.'&offset='.$numofpage.'&category='.$cateids);
// all other pages with settings from backend
} else {
    $offset = $numofpage*($paged-2)+14;
    $posts = get_posts('numberposts='.$numofpage.'&offset='.$offset.'&category='.$cateids);
}


?>
		<?php if ($posts) {
				$AsideId = get_settings('mistylook_asideid');
				function ml_hack($str)
				{
					return preg_replace('|</ul>\s*<ul class="asides">|', '', $str);
				}
				ob_start('ml_hack');
				foreach($posts as $post)
				{
					start_wp();
				?>
				<?php if ( in_category($AsideId) && !is_single() ) : ?>
					<ul class="asides">
						<li id="p<?php the_ID(); ?>">
							<?php echo wptexturize($post->post_content); ?>							
							<br/>
							<?php comments_popup_link('(0)', '(1)','(%)')?>  | <a href="<?php the_permalink(); ?>" title="Permalink: <?php echo wptexturize(strip_tags(stripslashes($post->post_title), '')); ?>" rel="bookmark">#</a> <?php edit_post_link('(edit)'); ?>
						</li>						
					</ul>
				<?php else: // If it's a regular post or a permalink page ?>	
				<div class="post" id="post-<?php the_ID(); ?>">
				<div class="posttitle">
					<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
					<p class="post-info"><?php the_time('M jS, Y') ?> by <?php the_author_posts_link() ?> <?php edit_post_link('Edit', '', ' | '); ?> </p>
				</div>
				
				<div class="entry">
					<?php the_content('Continue Reading &raquo;'); ?>
					<?php wp_link_pages(); ?>
				</div>
		
				<p class="postmetadata">Posted in <?php the_category(', ') ?> | <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></p>
				<?php comments_template(); ?>
			</div>
			<?php endif; // end if in category ?>
			<?php
				}
			}
			else
			{ ?>
				<h2 class="center">Not Found</h2>
				<p class="center">Sorry, but you are looking for something that isn't here.</p>
			<?php }
		?>
		<p align="center"><?php posts_nav_link(' - ','&#171; Newer Posts','Older Posts &#187;') ?></p>
</div><!-- end id:content-main -->
<?php get_sidebar();?>
<?php get_footer();?>