<?php
/**
 * Class that manages all the features of Contestant Rating Wordpress plugin
 *
 */
class CR {
	var $_points = 0;
	var $_user;
	var $_momentLimit = 10;

	/**
	 * Create the database tables to support plugin behaviour.
	 *
	 * @param boolean $echo If true echoes messages to user
	 */
	function install($echo = false) {
		global $table_prefix, $wpdb;

		$table_name = $table_prefix . "cr_post";
		if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") !== $table_name) {
			$sql = "CREATE TABLE {$table_name} (
			  ID bigint(20) unsigned NOT NULL default '0',
			  votes int(10) unsigned NOT NULL default '0',
			  points int(10) unsigned NOT NULL default '0',
			  PRIMARY KEY (ID)
			);";

			require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
			dbDelta($sql);
			if ($echo) _e("Table has been created\n");
		} else {
			if ($echo) _e("The table has already been created\n");
		}

		$table_name = $table_prefix . "cr_user";
		if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") !== $table_name) {
			$sql = "CREATE TABLE {$table_name} (
			  user varchar(32) NOT NULL default '',
			  post bigint(20) unsigned NOT NULL default '0',
			  points int(10) unsigned NOT NULL default '0',
			  ip char(15) NOT NULL,
			  vote_date datetime NOT NULL,
			  PRIMARY KEY (`user`,post),
			  KEY vote_date (vote_date)
  		);";
			require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
			dbDelta($sql);
			if ($echo) _e("Scorecard created\n");
		} elseif (!$wpdb->get_row("SHOW COLUMNS FROM {$table_name} LIKE 'vote_date'")) {
			$wpdb->query("ALTER TABLE {$table_name} ADD ip CHAR( 15 ) NOT NULL, ADD vote_date DATETIME NOT NULL");
			$wpdb->query("ALTER TABLE {$table_name} ADD INDEX (vote_date)");
			if ($echo) _e("Scorecard has been updated\n");
		} else {
			if ($echo) _e("The scorecard was already created\n");
		}
	}

	/**
	 * Get the html that shows the stars for voting
	 * If the user has already vote then it shows stars with puntuation. No voting is allowed
	 *
	 * @return string
	 */
	function getVotingStars($mode=0) {
		global $id, $wpdb, $table_prefix;
		$rated = false;
		if (isset($this->_user)) {
			$user = $wpdb->escape($this->_user);
			$table_name = $table_prefix . "cr_user";
			$rated = (bool) $wpdb->get_var("SELECT COUNT(*) FROM {$table_name} WHERE user='{$user}' AND post={$id}");
		}
		if (($this->_points > 0) && !$rated) {
			$user = $wpdb->escape($this->_user);
			$table_name = $table_prefix . "cr_user";
			$ip = $_SERVER['REMOTE_ADDR'];
			$vote_date = date('Y-m-d H:i:s');
			$wpdb->query("INSERT INTO {$table_name} (user, post, points, ip, vote_date) VALUES ('{$user}', {$id}, {$this->_points}, '{$ip}', '{$vote_date}')");
			$table_name = $table_prefix . "cr_post";
			if ($wpdb->get_var("SELECT COUNT(*) FROM {$table_name} WHERE ID={$id}")) {
				$wpdb->query("UPDATE {$table_name} SET votes=votes+1, points=points+{$this->_points} WHERE ID={$id};");
			} else {
				$wpdb->query("INSERT INTO {$table_name} (ID, votes, points) VALUES ({$id}, 1, {$this->_points});");
			}
			$rated = true;
//			$this->_setBestsOfMoment();
		}
		$data = $this->_getPoints();
		if($mode==1)
		return $this->_drawStars($data->votes, $data->points,1);
		if ($rated || !isset($_COOKIE['wp_cr'])) {
			$html = $this->_drawStars($data->votes, $data->points);
		} else {
			$html = $this->_drawVotingStars($data->votes, $data->points);
		}
		return $html;
	}

	/**
	 * Get the html that shows the stars with puntuation.
	 *
	 * @return string
	 */
	function getStars() {
		$data = $this->_getPoints();
		return $this->_drawStars($data->votes, $data->points);
	}

	/**
	 * Get the points and votes of current post
	 *
	 * @return object
	 */
	function _getPoints() {
		global $id, $wpdb, $table_prefix;
		$table_name = $table_prefix . "cr_post";
		return $wpdb->get_row("SELECT votes, points FROM {$table_name} WHERE ID={$id}");
	}
	
	/**
	 * Draw the stars
	 *
	 * @param int $votes
	 * @param int $points
	 * @return string
	 */
	function _drawStars($votes, $points,$mode=0) {
		if ($votes > 0) {
			$rate = $points / $votes;
		} else {
			$rate = 0;
		}
		$grades=array("A+","A","A-","B+","B","B-","C+","C","C-","D+","D","D-","F");
		$gr_count=0;
		if($mode==0)
		{
		$html = '<div class="CR_container"><div class="CR_stars"> ';
		for ($i = 1; $i <= 5; ++$i) {
			if ($i <= $rate) {
				$class = 'CR_full_star';
				$char = '*';
			} elseif ($i <= ($rate + .5)) {
				$class = 'CR_half_star';
				$char = '&frac12;';
			} else {
				$class = 'CR_no_star';
				$char = '&nbsp;';
			}
			$html .= '<span class="' . $class . '">' . $char . '</span> ';
		}
		}
		for($r=5;$r>=0;$r-=0.36,$gr_count++)
		{
			if($rate>=$r)
			{
			$result_grade=$gr_count;
			break;				
			}
		}
		if(!($result_grade>=0 && $result_grade<=11))
		$result_grade=12;
		if($mode==0)
		$html .= '<span class="CR_votes">' . (int) $votes . '</span> <span class="CR_tvotes">' . __('vote') . '</span>';
		if(isset($result_grade))
		$html .= '<span class="CR_votes"><strong>Grade of Idea : <font color="#FF0000">' . $grades[$result_grade] . '</font></strong></span>';
		if($mode==0)
		$html .= '</div></div>';
		return $html;
	}

	/**
	 * Draw the voting stars
	 *
	 * @param int $votes
	 * @param int $points
	 * @return string
	 */
	function _drawVotingStars($votes, $points) {
		global $id;
		if ($votes > 0) {
			$rate = $points / $votes;
		} else {
			$rate = 0;
		}
		$html = '<div class="CR_container"><form id="CR_form_' . $id . '" action="' . $_SERVER['PHP_SELF'] . '" method="post" class="CR_stars" onmouseout="CR_star_out(this)"> ';
		for ($i = 1; $i <= 5; ++$i) {
			if ($i <= $rate) {
				$class = 'CR_full_voting_star';
				$char = '*';
			} elseif ($i <= ($rate + .5)) {
				$class = 'CR_half_voting_star';
				$char = '&frac12;';
			} else {
				$class = 'CR_no_voting_star';
				$char = '&nbsp;';
			}
			//$html .= sprintf('<input type="radio" id="cr_star_%1$d_%2$d" class="star" name="cr_stars" value="%2$d"/><label class="%3$s" for="cr_star_%1$d_%2$d" onmouseover="CR_star_over(this, %2$d)">%2$d</label> ', $id, $i, $class);
			$html .= sprintf('<input type="radio" id="cr_star_%1$d_%2$d" class="star" name="cr_stars" value="%2$d"/><label class="%3$s" for="cr_star_%1$d_%2$d">%2$d</label> ', $id, $i, $class);
		}
		$html .= '<span class="CR_votes">' . (int) $votes . '</span> <span class="CR_tvotes">' . __('votes') . '</span> <span class="CR_tvote important">' . __('Cast your vote now!') . '</span>';
		$html .= '<input type="hidden" name="p" value="' . $id . '" />';
		$html .= '<input type="submit" name="vote" value="' . __('Voting') . '" />';
		$html .= '</form></div>';
		return $html;
	}

	function getBestOfMonth($month = null, $limit = 10) {
		global $wpdb, $table_prefix;
		$month = is_null($month) ? date('m') : (int)$month;
		$limit = (int)$limit;
		$table_name = $table_prefix . "cr_user";
		$sql = "SELECT post, COUNT(*) AS votes, SUM(points) AS points, AVG(points)
			FROM {$table_name}
			WHERE MONTH(vote_date)={$month} AND YEAR(vote_date)=YEAR(NOW())
			GROUP BY 1
			ORDER BY 4 DESC, 2 DESC
			LIMIT {$limit}";
		$data = $wpdb->get_results($sql);
		if (is_array($data)) {
			$html = '<ul class="CR_month_scores">';
			foreach ($data AS $row) {
				$title = get_the_title($row->post);
				$html .= '<li><a class="post_title" href="' . get_permalink($row->post) . '" title="' . $title . '">' . $title . '</a> ' . $this->_drawStars($row->votes, $row->points) . '</li>';
			}
			$html .= '</ul>';
			return $html;
		}
	}

	/**
	 * Get the best post of the moment. The moment is the time between now and 30 days before
	 *
	 * @return string
	 */
	function getBestOfMoment($limit = 10) {
		global $wpdb, $table_prefix;
		$table_name = $table_prefix . "cr_user";
		$avg = (int)$wpdb->get_var("SELECT COUNT( * ) / COUNT( DISTINCT post ) AS votes FROM {$table_name} WHERE vote_date BETWEEN DATE_SUB(DATE_SUB(NOW(), INTERVAL 1 DAY), INTERVAL 1 MONTH) AND DATE_SUB(NOW(), INTERVAL 1 DAY)");
		$sql = "SELECT post, COUNT(*) AS votes, SUM(points) AS points, AVG(points)
			FROM {$table_name}
			WHERE vote_date BETWEEN DATE_SUB(DATE_SUB(NOW(), INTERVAL 1 DAY), INTERVAL 1 MONTH) AND DATE_SUB(NOW(), INTERVAL 1 DAY)
			GROUP BY 1
			HAVING votes >= {$avg}
			ORDER BY 4 DESC, 2 DESC
			LIMIT {$limit}";
		$data = $wpdb->get_results($sql);
		$oldScore = array();
		if (is_array($data)) {
			$i = 1;
			foreach ($data AS $row) {
				$oldScore[$row->post] = $i++;
			}
		}
		$avg = (int)$wpdb->get_var("SELECT COUNT( * ) / COUNT( DISTINCT post ) AS votes FROM {$table_name} WHERE vote_date BETWEEN DATE_SUB(NOW(), INTERVAL 1 MONTH) AND NOW()");
		$sql = "SELECT post, COUNT(*) AS votes, SUM(points) AS points, AVG(points)
			FROM {$table_name}
			WHERE vote_date BETWEEN DATE_SUB(NOW(), INTERVAL 1 MONTH) AND NOW()
			GROUP BY 1
			HAVING votes >= {$avg}
			ORDER BY 4 DESC, 2 DESC
			LIMIT {$limit}";
		return $this->_drawScoreBoard($wpdb->get_results($sql), $oldScore);
	}

	/**
	 * Draw a scoreboard from two arrays comparing positions to set trends
	 *
	 * @param array $score
	 * @param array $oldScore
	 * @return string
	 */
	function _drawScoreBoard($score, $oldScore = null) {
		if (is_array($score)) {
			$html = '<ol class="CR_moment_scores">';
			$position = 1;
			$trends = array(__('Low'), __('Upload'), __('Maintain'));
			foreach ($score AS $row) {
				$html .= '<li>';
				$html .= $this->_drawStars($row->votes, $row->points);
				if (is_array($oldScore)) {
					$trend = '<span class="trend_up" title="' . $trends[1] . '">(' . $trends[1] . ')</span>';
					if (isset($oldScore[$row->post])) {
						if ($position > $oldScore[$row->post]) {
							$trend = '<span class="trend_dw" title="' . $trends[0] . '">(' . $trends[0] . ')</span>';
						} elseif ($position == $oldScore[$row->post]) {
							$trend = '<span class="trend_eq" title="' . $trends[2] . '">(' . $trends[2] . ')</span>';
						}
					}
					$html .= $trend;
				}
//				$html .= ' <span class="position">' . $row->position . '</span>';
				$title = get_the_title($row->post);
				if (strlen($title) > 32) {
					$titleAbbr = substr($title, 0, 32) . '...';
				} else {
					$titleAbbr = $title;
				}
				$html .= ' <a class="post_title" href="' . get_permalink($row->post) . '" title="' . $title . '">' . $titleAbbr . '</a> ';
				$html .= '</li>';
				$position++;
			}
			$html .= '</ol>';
			return $html;
		}
	}	

	/**
	 * Initialize the values.
	 * Get the puntuation from url and the user from the cookies.
	 * If no cookie exists generate a new user.
	 * Refresh the cookie to hold the value of user for 1 year
	 *
	 */
	function init() {
		if (isset($_COOKIE['wp_cr'])) {
			$this->_user = $_COOKIE['wp_cr'];
		} else {
		  if (!isset($this->_user)) {
		    srand((double)microtime()*1234567);
  			$this->_user = md5(microtime() . rand(1000, 90000000));
		  }
		}
		setcookie('wp_cr', $this->_user, time()+60*60*24*365, '/');
		if (isset($_REQUEST['cr_stars'])) {
			$points = (int) $_REQUEST['cr_stars'];
			if (($points > 0) && ($points <= 5)) {
				$this->_points = $points;
			}
		}
	}
}
?>