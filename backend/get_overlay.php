<?php
	require('config.php');
	require('db_connect.php');
	require('db_utils.php');
	
	function get_images($project_id, $medium_id, $db_conn){
		$query_statement_2 = "SELECT name FROM mediums WHERE id='" . $medium_id . "'";
		$query_2 = mysql_query($query_statement_2, $db_conn);
		$row_2 = mysql_fetch_array($query_2);
		
		$global_category_name = $row_2['name'];
		
		$query_statement_2 = "SELECT * FROM mediscs WHERE medium_id='" . $medium_id . "'";
		$query_2 = mysql_query($query_statement_2, $db_conn);
		$query_array_2 = array();
		
		while ($row_2 = mysql_fetch_array($query_2)){
			array_push($query_array_2, "medisc_id='" . $row_2['id'] . "'");
		}
		
		$result = array();
		$result[0] = 0;
		$result['id'] = array();
		$result['name'] = array();
		
		$query_statement_2 = "SELECT * FROM images WHERE (project_id='" . $project_id ."' AND " . get_list($query_array_2, " OR ") . ")";
		$query_2 = mysql_query($query_statement_2, $db_conn);
		
		while ($row_2 = mysql_fetch_array($query_2)){
			$result[0]++;
			$result['class_attr'][$result[0]] = $global_category_name . "_" . $project_id . "_" . $row_2['id'];
			$result['id'][$result[0]] = $row_2['id'];
			$result['name'][$result[0]] = $row_2['name'];
		}
		
		return $result;
	}
	
	
	$mediums = array();
	$mediums[0] = 0;
	$mediums['name'] = array();
	$mediums['id'] = array();
	
	$query_statement = "SELECT * FROM mediums WHERE name='" . $_POST['category'] . "'";
	$query = mysql_query($query_statement, $db_conn);
	while($row = mysql_fetch_array($query)){
		$mediums[0]++;
		$mediums['name'][$mediums[0]] = $row['name'];
		$mediums['id'][$mediums[0]] = $row['id'];
	} 
	
	$query_statement = "SELECT * FROM mediums WHERE name!='" . $_POST['category'] . "'";
	$query = mysql_query($query_statement, $db_conn);
	while ($row = mysql_fetch_array($query)){
		$mediums[0]++;
		$mediums['name'][$mediums[0]] = $row['name'];
		$mediums['id'][$mediums[0]] = $row['id'];
	}
	
	
	$response .= '<div id="overlay-left">';
	$response .= '<div id="overlay-left-content">';
	$response .= '<div id="other-wrapper">';
	$response .= '<div class="overlay-block">';
	$response .= '<h1>other ';
	
	$query_statement_2 = "SELECT name FROM projects WHERE id='" . $_POST['project_id'] . "'";
	$query_2 = mysql_query($query_statement_2);
	$row_2 = mysql_fetch_array($query_2);
	
	$response .= $row_2['name'] . "</h1>";
	$GLOBAL_PROJECT = $row_2['name'];
	
	$response .= '<ul class="overlay-list clearfix">';
	
	$image_array = get_images($_POST['project_id'], $mediums['id'][1], $db_conn);
	for ($i = 1; $i <= $image_array[0]; $i++){
		if ($image_array['id'][$i] != $_POST['image_id']){
			$response .= '<li>';
			$response .= "<a href='#'>";
			$response .= "<div class='img-container'>";
			$response .= "<img class='" . $image_array['class_attr'][$i] . "' style='height:100px; width:70px' src='" . $PROJS_PATH . $image_array['name'][$i] . "' />"; 
			$response .= "<span class='tooltip'><h5>" . $GLOBAL_PROJECT . "-" . $image_array['name'][$i] . "</h5></span>";
			$response .= "</div>";
			$response .= "</a>";
			$response .= '</li>';
		}
	}	

	
	$response .= '</ul>';
	$response .= '</div>';
	$response .= '</div>';
	$response .= '<div id="tv-spots">';
	$response .= '<div class="overlay-block">';
	$response .= '<h1>' . $mediums['name'][2] . '</h1>';
	$response .= '<ul class="overlay-list clearfix">';
	
	
	$image_array = get_images($_POST['project_id'], $mediums['id'][2], $db_conn);
	for ($i = 1; $i <= $image_array[0]; $i++){
		if ($image_array['id'][$i] != $_POST['image_id']){
			$response .= '<li>';
			$response .= "<div class='img-container'>";
			$response .= "<img class='" . $image_array['class_attr'][$i] . "' style='height:100px; width:70px' src='" . $PROJS_PATH . $image_array['name'][$i] . "' />"; 
			$response .= "<span class='tooltip'><h5>" . $GLOBAL_PROJECT . "-" . $image_array['name'][$i] .  "</h5></span>";
			$response .= "</div>";
			$response .= '</li>';
		}
	}
	
	
	$response .= '</ul>';
	$response .= '</div>';
	$response .= '</div>';
	$response .= '<div id="banner">';
	$response .= '<div class="overlay-block">';
	$response .= '<h1>' . $mediums['name'][3] . '</h1>';
	$response .= '<ul class="overlay-list clearfix">';
	
	
	$image_array = get_images($_POST['project_id'], $mediums['id'][3], $db_conn);
	for ($i = 1; $i <= $image_array[0]; $i++){
		if ($image_array['id'][$i] != $_POST['image_id']){
			$response .= '<li>';
			$response .= "<div class='img-container'>";
			$response .= "<img class='" . $image_array['class_attr'][$i] . "' style='height:100px; width:70px' src='" . $PROJS_PATH . $image_array['name'][$i] . "' />"; 
			$response .= "<span class='tooltip'><h5>" . $GLOBAL_PROJECT . "-" . $image_array['name'][$i] .  "</h5></span>";
			$response .= "</div>";
			$response .= '</li>';
		}
	}
	
	$response .= '</ul>';
	$response .= '</div>';
	$response .= '</div>';
	$response .= '<div id="related work">';
	$response .= '<div class="overlay-block">';
	$response .= '<h1>related work</h1>';
	$response .= '<ul>';
	$response .= '<li>';
	$response .= '<img src="images/related.jpg" />';
	$response .= '<img src="images/related-2.jpg" />';
	$response .= '<img src="images/related-2.jpg" />';
	$response .= '</li>';
	$response .= '<li>';
	$response .= '<img src="images/related.jpg" />';
	$response .= '<img src="images/related-2.jpg" />';
	$response .= '<img src="images/related-2.jpg" />';
	$response .= '</li>';
	$response .= '</ul>';
	$response .= '</div>';
	$response .= '</div>';
	$response .= '<div id="share-wrapper">';
	$response .= '<div class="overlay-block">';
	$response .= '</div>';
	$response .= '</div>';
	$response .= '</div>';
	$response .= '</div>';
	$response .= '<div id="overlay-content">';
	$response .= '<div id="slider-wrapper">';
	$response .= '<ul id="main-slider">';
	
	$query_statement = "SELECT * FROM images WHERE id='" . $_POST['image_id'] . "'";
	$query = mysql_query($query_statement, $db_conn);
	$row = mysql_fetch_array($query);
	
	$response .= '<li><img src="' . $PROJS_PATH . $row['name'] . '" /></li>'; 
	//$response .= "<li>" . $_POST['image_id'] . "</li>";
	$image_array = get_images($_POST['project_id'], $mediums['id'][1], $db_conn); 
	for ($i = 1; $i <= $image_array[0]; $i++){
		if ($image_array['id'][$i] != $_POST['image_id']){
			$response .= "<li>";
			$response .= "<img src='" . $PROJS_PATH . $image_array['name'][$i] . "' />"; 
			$response .= "</li>";
		}
	}	
	
	$response .= '</ul>';
	$response .= '</div>';
	$response .= '</div>';
	
	echo $response; 
?>