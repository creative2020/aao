<?php
/**
 Template Name: Beta
*/
if (!is_user_logged_in()) {
auth_redirect();
}
$posts = get_posts('category_name=investment_ideas');
?>

<?php get_header();?>
<div id="content">
<div id="content-main">
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
				<?php
				$get_post_ticker = get_post_custom_values('post_ticker', $post->ID);
				if($get_post_ticker)
				$post_ticker = $get_post_ticker[0];
				?>
				<div class="posttitle">
					<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
					<p class="post-info"><?php the_time('M jS, Y') ?> by <?php the_author_posts_link() ?> <?php edit_post_link('Edit', '', ' | '); ?> <?php if($post_ticker!='') { echo "Ticker: $post_ticker"; }?></p>
				</div>
				
				<div class="entry">
					<?php the_content('Continue Reading &raquo;'); ?>
					<?php wp_link_pages(); ?>
				</div>
				<p>
                <?php
				$Images = & get_children( 'post_type=attachment&post_parent=' . $post->ID );
				
				foreach ($Images as $Image) {
				$NewImagesArray [] = $Image;
				}
				if(count($NewImagesArray)>0)
				{
				foreach ( $NewImagesArray as $NewImage ) 
				{
				echo "<a href='".$NewImage->guid."' target='_blank'>".$NewImage->post_title."</a>";
				echo "<br />";
				}
				}
				?>
				</p>
				<p class="postmetadata">Posted in <?php the_category(', ') ?> | <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></p>
				<p><?php 	
				     global $CR;
					 echo $CR->getVotingStars(1);
				   ?></p> 
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