<?php
function paging_new($tot_count,$max_res,$pge,$nol)
{
		if($tot_count<1)
			return false;
		///////////////////////     PAGING   ///////////////////////////////////////
		$total=$tot_count;
		$limit=$max_res;
		$pagenew=$pge;
		$num_of_links=$nol;
		$from =(($pagenew * $limit) - $limit) ; 
		
		$total_pagenews = ceil($total / $limit); 
		
		//////////////////////////////  Query String  ///////////////////////////////////
		$f=explode("&pagenew",$_SERVER['QUERY_STRING']);
		$qs=$f[0];
		///////////////////////////////////////////////////////////////////////////////
		$nl=floor($num_of_links/2);
		if(($pagenew-$nl)>=2 && ($pagenew+$nl)<$total_pagenews)
		{
			$k=$pagenew-$nl;
		}
		elseif(($pagenew+$nl)>=$total_pagenews && $total_pagenews>$num_of_links)
		{
			$k=$total_pagenews-($num_of_links-1);
		}
		else
		{
			$k=1;
		}
		if($total_pagenews>=$num_of_links)
			$total_k=$num_of_links;
		else
			$total_k=$total_pagenews;
		
		if(($from+$limit)<=$total)
		$to_k=$from+$limit;
		else
		$to_k=$total;
		echo '<div class="tablenav"><div class="tablenav-pages">';

		echo '<span class="displaying-num">Displaying '.($from+1).'&#8211;'. $to_k .' of '. $total.'</span>';

		if($total>$limit)
		{
				// Build Previous Link 
				if($pagenew > 1){ 
					$prev = ($pagenew - 1); 
					echo " <a class=\"prev page-numbers\" href=\"".$_SERVER['PHP_SELF']."?".$qs."&pagenew=$prev\"> &laquo; </a>&nbsp;"; 
				} 
				
				for($i = 1; $i <= $total_k; $i++)
				{ 
					if(($pagenew) == $k)
					{ 
						echo " <span class=\"page-numbers current\">$k</span> "; 
					} 
					else 
					{ 
						echo " <a class=\"page-numbers\" href=\"".$_SERVER['PHP_SELF']."?".$qs."&pagenew=$k\">$k</a> ";
					} 
					$k++;
				} 
				// Build Next Link 
				if($pagenew < $total_pagenews)
				{ 
					$next = ($pagenew + 1); 
					echo " <a class=\"next page-numbers\" href=\"".$_SERVER['PHP_SELF']."?".$qs."&pagenew=$next\"> &raquo; </a> "; 
				} 	
		}
			
		echo '</div></div>';		
}

?>