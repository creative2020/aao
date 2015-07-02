<?php
require_once('../../../wp-config.php');
require_once('cr.class.php');
$id = $_REQUEST['p'];
$CR =& new CR();
$CR->init();
echo $CR->getVotingStars();
?>
