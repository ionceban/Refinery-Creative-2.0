<?php
	require('config.php');
	require('db_connect.php');
	require('utils.php');
	
	$category = $_POST['category'];
	$image_id = $_POST['image_id'];
	
	$query_statement = "SELECT projects.id,projects.name FROM images,projects WHERE ";
	$query_statement .= "(images.id='" . $image_id . "' AND images.project_id=projects.id)";
	$query = mysql_query($query_statement, $db_conn);
	$row = mysql_fetch_row($query);
	
	$project_id = $row[0];
	$project_name = $row[1];
	
	function get_images($project_id, $medium_id, $db_conn){
		$query_statement = "SELECT images.id,images.name FROM images,mediscs WHERE ";
		$query_statement .= "(images.medisc_id=mediscs.id AND mediscs.medium_id='" . $medium_id . "'";
		$query_statement .= " AND images.project_id='" . $project_id . "')";
		$query = mysql_query($query_statement, $db_conn);
		
		$response = array();
		$response[0] = 0;
		$response['id'] = array();
		$response['name'] = array();
		while ($row = mysql_fetch_row($query)){
			$response[0]++;
			$response['id'][$response[0]] = $row[0];
			$response['name'][$response[0]] = $row[1];
		}
		
		return $response;
	}
	
	$mediums = array();
	$mediums[0] = 0;
	$mediums['name'] = array();
	$mediums['id'] = array();
	
	$query_statement = "SELECT id,name FROM mediums WHERE name='" . $category . "'";
	$query = mysql_query($query_statement, $db_conn);
	while($row = mysql_fetch_row($query)){
		$mediums[0]++;
		$mediums['name'][$mediums[0]] = $row[1];
		$mediums['id'][$mediums[0]] = $row[0];
	} 
	
	$query_statement = "SELECT id,name FROM mediums WHERE name!='" . $category . "'";
	$query = mysql_query($query_statement, $db_conn);
	while ($row = mysql_fetch_row($query)){
		$mediums[0]++;
		$mediums['name'][$mediums[0]] = $row[1];
		$mediums['id'][$mediums[0]] = $row[0];
	}
	
	
	$response .= '<div id="overlay-left">';
	$response .= '<div id="overlay-left-content">';
	
	$image_array = get_images($project_id, $mediums['id'][1], $db_conn);
	
	if ($image_array[0] > 1){
	
		$response .= '<div id="other-'. $mediums['name'][1] . '">';
		$response .= '<div class="overlay-block">';
		$response .= '<h1>other ' . $mediums['name'][1] . '</h1>';
		$response .= '<ul class="overlay-list clearfix">';
		
		for ($i = 1; $i <= $image_array[0]; $i++){
			if ($image_array['id'][$i] != $_POST['image_id']){
				$response .= '<li>';
				$response .= "<a href='#'>";
				$response .= "<div class='img-container'>";
				
				$class_attr = $mediums['name'][1] . "_" . $image_array['id'][$i];
				$file_attrs = preg_split('/\./', $image_array['name'][$i]);
				$thumber_body = $PROJS_PATH . $file_attrs[0] . "_t_thumber";
				$thumber_ext = extension_checker($thumber_body);
				$list_body = $file_attrs[0] . "_t_list";
				$src_attr = $PROJS_PATH . $list_body . "." . $thumber_ext;
				
				$response .= "<img class='" . $class_attr . "' src='" . $src_attr . "' />"; 
				$response .= "<span class='tooltip'><h5>" . $project_name . "</h5></span>";
				$response .= "</div>";
				$response .= "</a>";
				$response .= '</li>';
			}
		}	
	
		
		$response .= '</ul>';
		$response .= '</div>';
		$response .= '</div>';
	}
	
	$image_array = get_images($project_id, $mediums['id'][2], $db_conn);
	
	if ($image_array[0] > 0){
	
		$response .= '<div id="other-'. $mediums['name'][2] . '">';
		$response .= '<div class="overlay-block">';
		$response .= '<h1>other ' . $mediums['name'][2] . '</h1>';
		$response .= '<ul class="overlay-list clearfix">';
		
		for ($i = 1; $i <= $image_array[0]; $i++){
			if ($image_array['id'][$i] != $_POST['image_id']){
				$response .= '<li>';
				$response .= "<a href='#'>";
				$response .= "<div class='img-container'>";
				
				$class_attr = $mediums['name'][2] . "_" . $image_array['id'][$i];
				$file_attrs = preg_split('/\./', $image_array['name'][$i]);
				$thumber_body = $PROJS_PATH . $file_attrs[0] . "_t_thumber";
				$thumber_ext = extension_checker($thumber_body);
				$list_body = $file_attrs[0] . "_t_list";
				$src_attr = $PROJS_PATH . $list_body . "." . $thumber_ext;
				
				$response .= "<img class='" . $class_attr . "' src='" . $src_attr . "' />"; 
				$response .= "<span class='tooltip'><h5>" . $project_name . "</h5></span>";
				$response .= "</div>";
				$response .= "</a>";
				$response .= '</li>';
			}
		}	
	
		
		$response .= '</ul>';
		$response .= '</div>';
		$response .= '</div>';
	}
	
	$image_array = get_images($project_id, $mediums['id'][3], $db_conn);
	
	if ($image_array[0] > 0){
	
		$response .= '<div id="other-'. $mediums['name'][3] . '">';
		$response .= '<div class="overlay-block">';
		$response .= '<h1>other ' . $mediums['name'][3] . '</h1>';
		$response .= '<ul class="overlay-list clearfix">';
		
		for ($i = 1; $i <= $image_array[0]; $i++){
			if ($image_array['id'][$i] != $_POST['image_id']){
				$response .= '<li>';
				$response .= "<a href='#'>";
				$response .= "<div class='img-container'>";
				
				$class_attr = $mediums['name'][3] . "_" . $image_array['id'][$i];
				$file_attrs = preg_split('/\./', $image_array['name'][$i]);
				$thumber_body = $PROJS_PATH . $file_attrs[0] . "_t_thumber";
				$thumber_ext = extension_checker($thumber_body);
				$list_body = $file_attrs[0] . "_t_list";
				$src_attr = $PROJS_PATH . $list_body . "." . $thumber_ext;
				
				$response .= "<img class='" . $class_attr . "' src='" . $src_attr . "' />"; 
				$response .= "<span class='tooltip'><h5>" . $project_name . "</h5></span>";
				$response .= "</div>";
				$response .= "</a>";
				$response .= '</li>';
			}
		}	
	
		
		$response .= '</ul>';
		$response .= '</div>';
		$response .= '</div>';
	}
	
	$image_array = get_images($project_id, $mediums['id'][4], $db_conn);
	
	if ($image_array[0] > 0){
	
		$response .= '<div id="other-'. $mediums['name'][4] . '">';
		$response .= '<div class="overlay-block">';
		$response .= '<h1>other ' . $mediums['name'][4] . '</h1>';
		$response .= '<ul class="overlay-list clearfix">';
		
		for ($i = 1; $i <= $image_array[0]; $i++){
			if ($image_array['id'][$i] != $_POST['image_id']){
				$response .= '<li>';
				$response .= "<a href='#'>";
				$response .= "<div class='img-container'>";
				
				$class_attr = $mediums['name'][4] . "_" . $image_array['id'][$i];
				$file_attrs = preg_split('/\./', $image_array['name'][$i]);
				$thumber_body = $PROJS_PATH . $file_attrs[0] . "_t_thumber";
				$thumber_ext = extension_checker($thumber_body);
				$list_body = $file_attrs[0] . "_t_list";
				$src_attr = $PROJS_PATH . $list_body . "." . $thumber_ext;
				
				$response .= "<img class='" . $class_attr . "' src='" . $src_attr . "' />"; 
				$response .= "<span class='tooltip'><h5>" . $project_name . "</h5></span>";
				$response .= "</div>";
				$response .= "</a>";
				$response .= '</li>';
			}
		}	
	
		
		$response .= '</ul>';
		$response .= '</div>';
		$response .= '</div>';
	}
	
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
	$response .= '</div>';
	$response .= '</div>';
	$response .= '<div id="overlay-content">';
	$response .= '<div id="slider-wrapper">';
	$response .= '<ul id="main-slider">';
	
	$query_statement = "SELECT id,name FROM images WHERE id='" . $_POST['image_id'] . "'";
	$query = mysql_query($query_statement, $db_conn);
	$row = mysql_fetch_row($query);
	
	$response .= '<li><img src="' . $PROJS_PATH . $row[1] . '" /></li>'; 
	
	$image_array = get_images($project_id, $mediums['id'][1], $db_conn); 
	for ($i = 1; $i <= $image_array[0]; $i++){
		if ($image_array['id'][$i] != $image_id){
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