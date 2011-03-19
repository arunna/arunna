<?
	function page_count($num_rows,$view){
		$kel=$num_rows/$view;
		if ($kel==floor($num_rows/$view)){
			return $kel;
		}else{
			return floor($num_rows/$view)+1;
		} 
	}
	
	function paging($url,$num_rows,$top_page=1,$view=10,$page_view=5){
		if ($num_rows == 0)
		return;
		$top_go=1;
		$page_go=1;
		
		$prev=$top_page-1;
		$next=$top_page+1;
		$page_range=floor($page_view/ 2);
		$html="<ul class=\"pagging\"><li><a href=\"$url"."1\">Newest</a></li>";
		
		if ($top_page!=1)
		{
			$html.="<li><a href=\"$url".$prev."\">Prev</a></li>";
		}else{
			$html.="<li><a href=\"#\" class=\"selected\">Prev</a></li>";
		}
		
		//melipat halaman berdasarkan range yang di inginkan
		if($top_page<$page_view && page_count($num_rows,$view) >= $page_view){
			$top_go=1;
			$page_go=$page_view;
		}elseif($top_page< $page_view && page_count($num_rows,$view) < $page_view){
			if($top_page - $page_range <=1){
				$top_go=1;
			}else{
				$top_go=$top_page - $page_range;
			}
			$page_go=page_count($num_rows,$view);
		
		}elseif($top_page >= $page_view && $top_page <= page_count($num_rows,$view)){
			if($top_page - $page_range<=1){
				$top_go=1;
			}else{
				$top_go=$top_page - $page_range;
			}
			if($top_page +$page_range > page_count($num_rows,$view)){
				$page_go=page_count($num_rows,$view);
			}else{
				$page_go=$top_page + $page_range;
			}
		}
		//stop lipatan
		for($i=$top_go;$i<=$page_go;$i++){
			if ($i==$top_page){
				$html.= "<li><a href=\"#\" class=\"selected\">$i</a></li>";
			}else{
				$html.= "<li><a href=\"$url".$i."\" >$i</a></li>";
			}
		}
		if ($top_page!=page_count($num_rows,$view)){
			$html.="<li><a href=\"$url".$next."\">Next</a></li>";
		}else{
			$html.= "<li><a href=\"#\" class=\"selected\">Next</a></li>";
		}
		
		$html.="<li><a href=\"$url".page_count($num_rows,$view)."\">Oldest</a></li></ul>";
		
		return $html;
	}

?>