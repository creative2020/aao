=== Plugin Name ===
Contributors: dingobytes
Donate link: http://www.redcross.org/go/donateall/?s_src=RSG000000000&s_subsrc=RCO_BigRedButton
Tags: rating, voting, post, stars
Requires at least: 2.0.2
Tested up to: 3.0.1
Stable tag: 0.4

Contestant Rating is a plugin that allows wordpress users to rate post(s) in a single post using a classic five star method.

== Description ==

Contestant Rating is a plugin that will allow a wordpress user to rate a post or in our version a Karaoke contestant using the classic method of five stars. 

This script is a modified version of post-star-rating plug in by O Doutor [post-star-rating](http://wordpress.org/extend/plugins/post-star-rating/). The script has been cleaned up to work with all browsers and was inteded to be used to rate video of karaoke contestants in wordpress post(s).

A few notes about the plugin:

*   The plugin requires jQuery. If jQuery is not "detected" when it is loaded, the plugin will load jQuery.
*   The javascript has been minified and placed in the /js/ directory. A full version is available and can be modified as you see necessary.
*   The cascading stylesheet has been minified and placed in the /css/ directory. A full version of the .css file is also there and can be modified as you see necessary.
*   The plugin uses an IP based cookie to disable the form to deter users from inflating the vote.
== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin folder `contestant-rating` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php CR_show_voting_stars(); ?>` in your template.

= THIS PLUGIN REQUIRES YOU TO ADD THE CODE INTO YOUR TEMPLATE AND NOT YOUR POST. =

== Frequently Asked Questions ==

= This plugin uses jQuery. Do I need to load jQuery in my template? =

Although you can load jQuery in the head section of the template, the plug in will try to detect if jQuery is loaded. If it does not detect jQuery, it will use the default jQuery that wordpress is using.

= Where do you place the code `<?php CR_show_voting_stars(); ?>` ? =

It is important that you place the code in your sites template and not just add to a post. It can be setup many different ways but the easiest way is to simply add it to  Main Index Template (index.php) where you want it to appear. If you only want it to appear when a certain tag is used (like 'contestant-rating'), surround it with an if statement such as 
`&lt;?php
	$posttags = get_the_tags();
		if ($posttags) {
			foreach($posttags as $tag) {
				$showStars=$tag->name;
				if ($showStars == 'contestant-rating') { 
					CR_show_voting_stars();
				}
			}
		}
?&gt;`

= Is there a demo somewhere of this plugin in action? =

Why yes, yes there is. You can see a demo at [Dingobytes.com](http://www.dingobytes.com/wordpress/contestant-rating-wordpress-plugin "Dingobytes.com")

== Screenshots ==

1. Five stars in the post when the post has not been voted on yet.
2. Mouse over of stars turns them yellow.
3. Another screen shot of mouse over with stars turning yellow.
4. After star has been clicked, the voting is disabled and vote is displayed.

== Changelog ==

= 0.1 =
* Ported over original plugin post-star-rating to work on all browsers.
* Busted up javascript and stylesheet to load in head (XHTML COMPLIANCE).

= 0.2 =
* Corrected the jQuery to use the proper selector for onclick and mouseover events.

= 0.3 =
* Updated CSS to better display scoreboards.
* Updated BestOfMoment query.
* Updated install directions with emphasis on placing code into theme/template and not into post.

= 0.4 =
* Updated plugin to resolve properly to plugin directory when WP is not installed in root directory. A big thanks to Max at [MaxisNOW.com](http://maxisnow.com/ "Max is NOW")

== Upgrade Notice ==

= 0.4 =
This version corrects AJAX call when WP is not installed in root directory.

= 0.3 =
This version cleans up some CSS issues, updates BestOfMoment query and updates install instructions.

= 0.1 =
This version fixes an issue with wrong selector in js being used to display mouseover effect when the post has already been voted on. Upgrade immediately.

== Arbitrary section ==

If you want to show scoreboards on your blog you can use the following tags:

- CR_bests_of_month(): Shows a list with the 10 best post of the current month
- CR_bests_of_month(month): Shows a list with the 10 best post of the "month" specified being an integer
- CR_bests_of_month(month, limit): Shows a list with the "limit" best post of the "month" (as an integer) specified
- CR_bests_of_moment(): Shows a list with the 10 best post of the moment. It shows trends too.
- CR_bests_of_moment(limit): Shows a list with the "limit" best post of the moment. It shows trends too.