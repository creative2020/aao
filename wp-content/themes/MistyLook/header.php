<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php bloginfo('name'); ?> <?php if ( is_single() ) { ?> &raquo; Blog Archive <?php } ?> <?php wp_title(); ?></title>
<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats -->
<meta name="keywords" content="<?php bloginfo('description'); ?>" />
<meta name="description" content="<?php bloginfo('description'); ?>" />
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php
global $page_sort;	
	if(get_settings('mistylook_sortpages')!='')
	{ 
		$page_sort = 'sort_column='. get_settings('mistylook_sortpages');
	}	
	global $pages_to_exclude;
	
	if(get_settings('mistylook_excludepages')!='')
	{ 
		$pages_to_exclude = 'exclude='. get_settings('mistylook_excludepages');
	}
	
	$current_user = wp_get_current_user();
	if($current_user->ID=='')
	{
	$chk_page=get_page_by_title('Login');
	$pages_to_exclude = 'exclude='.$chk_page->ID;
	}
?>
<?php wp_head(); ?>
</head>
<script defer="defer" id="snap_preview_anywhere" type="text/javascript" src="http://spa.snap.com/snap_preview_anywhere.js?ap=0&amp;si=1&amp;sb=1&amp;key=e5a3c1e6dff5de35b1c6e7c470411886&amp;domain=&amp;fl=wordpress&amp;pub=pub-2311827-www.wordpress.com&amp;es=all&amp;lang=en"></script>
<body id="section-index">
<div id="navigation">
<ul>
	<li <?php if(is_home()){echo 'class="current_page_item"';}?>><a href="<?php bloginfo('siteurl'); ?>/" title="Home">Home</a></li>
		<?php wp_list_pages('title_li=&depth=1&'.$page_sort.'&'.$pages_to_exclude)?>
<!--	<li class="search"><form method="get" id="searchform" action="<?php bloginfo('home'); ?>"><input type="text" class="textbox" value="<?php echo wp_specialchars($s, 1); ?>" name="s" id="s" /><input type="submit" id="searchsubmit" value="Search" /></form></li>
--></ul>
</div><!-- end id:navigation -->


<div id="container">


<div id="header">
<h1><a href="<?php bloginfo('siteurl');?>/" title="<?php bloginfo('name');?>"><?php bloginfo('name');?></a></h1>
<h2><?php bloginfo('description');?></h2>
</div><!-- end id:header -->


<div id="feedarea">
<dl>
	<dt><strong>Feeds:</strong></dt>
	<dd><a href="http://feeds.feedburner.com/aboveaverageodds">Posts</a></dd>
	<dd><a href="<?php bloginfo('comments_rss2_url'); ?>">Comments</a></dd>		
</dl>
</div><!-- end id:feedarea -->

  
  <div id="headerimage">
</div><!-- end id:headerimage -->