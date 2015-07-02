<?php 
if ( in_category('investment_ideas')) { 
	if (!is_user_logged_in()) {
	auth_redirect();
	}
	}
?>
<?php get_header();?>
<div id="content">
<div id="content-main">
<?php if (have_posts()) : ?>
		
		<?php while (have_posts()) : the_post(); ?>
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
					<?php the_content(); ?>
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
				<p class="postmetadata">Posted in <?php the_category(', ') ?> | <?php comments_number('No Comments', '1 Comment','% Comments')?></p>
                <?php if ( in_category('investment_ideas')) { ?>
                <p><?php CR_show_voting_stars(); ?></p> 
                <?php } ?>
				<?php comments_template(); ?>
			</div>
	
		<?php endwhile; ?>

		<p align="center"><?php posts_nav_link(' - ','&#171; Prev','Next &#187;') ?></p>
		
	<?php else : ?>

		<h2 class="center">Not Found</h2>
		<p class="center">Sorry, but you are looking for something that isn't here.</p>		

	<?php endif; ?>
</div><!-- end id:content-main -->
<?php get_sidebar();?>
<?php get_footer();?>