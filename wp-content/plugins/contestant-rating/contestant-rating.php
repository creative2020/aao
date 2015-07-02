<?php
/*
Plugin Name: Contestant Rating
Plugin URI: http://dingobytes.com/
Description: Plugin to rate post, modified from script from O Doutor
Version: 0.4
Author: Andrew Alba
Author URI: http://dingobytes.com/
*/

/*  Copyright 2009  Andrew Alba  (email : andrew.alba@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


require_once('cr.class.php');

/**
 * Tag to use in templates that shows stars for voting
 *
 */
function CR_show_voting_stars() {
	global $CR;
	echo $CR->getVotingStars();
}

/**
 * Tag to use in templates that shows stars with puntuation
 *
 */
function CR_show_stars() {
	global $CR;
	echo $CR->getStars();
}

function CR_bests_of_month($month = null, $limit = 10) {
	global $CR;
	echo $CR->getBestOfMonth($month, $limit);
}

function CR_bests_of_moment($limit = 10) {
	global $CR;
	echo $CR->getBestOfMoment($limit);
}
function js() {
	echo "<script type=\"text/javascript\">
			/*global post_ajax_star */
			post_ajax_star = \"" . WP_PLUGIN_URL . "/contestant-rating/cr-ajax-stars.php\";
		 </script>";
}
add_action('wp_head', 'js');
wp_register_style('contestantRatingCSS', WP_PLUGIN_URL . '/contestant-rating/css/contestantRating.min.css');
wp_enqueue_style('contestantRatingCSS');
wp_enqueue_script('contestantRatingJS', WP_PLUGIN_URL . '/contestant-rating/js/contestantRating.min.js', array('jquery'), '0.1');
/* Assigning hooks to actions */
$CR =& new CR();
add_action('activate_contestant-rating/contestant-rating.php', array(&$CR, 'install')); /* only works on WP 2.x*/
add_action('init', array(&$CR, 'init'));
?>