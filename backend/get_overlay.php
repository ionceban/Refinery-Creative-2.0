<?php
	require('config.php');
	require('db_connect.php');
	require('utils.php');
	
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
	
	$mediums[0] = 0;
	
	$query_statement = "SELECT mediums.id, mediums.name FROM images, mediums, mediscs WHERE ";
	$query_statement .= "images.id='" . $image_id . "' AND images.medisc_id=mediscs.id";
	$query_statement .= " AND mediums.id=mediscs.medium_id";
	
	$query = mysql_query($query_statement, $db_conn);
	$row = mysql_fetch_row($query);
	
	$mediums[0]++;
	$mediums['name'][$mediums[0]] = $row[1];
	$mediums['id'][$mediums[0]] = $row[0];
	
	$current_medium_id = $row[0];
	
	$query_statement = "SELECT id,name FROM mediums WHERE id!='" . $current_medium_id . "'";
	$query = mysql_query($query_statement, $db_conn);
	
	while ($row = mysql_fetch_row($query)){
		$mediums[0]++;
		$mediums['name'][$mediums[0]] = $row[1];
		$mediums['id'][$mediums[0]] = $row[0];
	}
	
	$response .= '<div id="overlay-left">';
	$response .= '<div id="overlay-left-content">';
	
	for ($j = 1; $j <= 4; $j++){
		
		$image_array = get_images($project_id, $mediums['id'][$j], $db_conn);
		
		if ($j == 1){
			$limit = 1;
		} else {
			$limit = 0;
		}
		
		if ($image_array[0] > $limit){
		
			$response .= '<div id="other-'. $mediums['name'][$j] . '">';
			$response .= '<div class="overlay-block">';
			$response .= '<h1>other ' . $mediums['name'][$j] . '</h1>';
			$response .= '<ul class="overlay-list clearfix">';
			
			for ($i = 1; $i <= $image_array[0]; $i++){
				if ($image_array['id'][$i] != $_POST['image_id']){
					$response .= '<li>';
					$response .= "<a href='#'>";
					$response .= "<div class='img-container'>";
					
					$class_attr = $image_array['id'][$i];
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
	}
	
	$response .= '<div id="related work">';
	$response .= '<div class="overlay-block">';
	$response .= '<h1>related work</h1>';
	$response .= '<ul class="overlay-list clearfix">';

	// Limit the number of results
	$related_limit = 20;
	$related_counter = 0;
	
	// 3 or more keywords
	$related_arr = get_number_keywords($image_id, $db_conn, 3, 300, $related_limit - $related_counter);
	$related_counter += intval($related_arr[0]); 
	$response .= process_related_array($related_arr, $db_conn, $PROJS_PATH);

	// 2 keywords
	$related_arr = get_number_keywords($image_id, $db_conn, 2, 2, $related_limit - $related_counter);
	$related_counter += intval($related_arr[0]);
	$response .= process_related_array($related_arr, $db_conn, $PROJS_PATH);
	
	// medium and 1 keyword
	$related_arr = get_one_keyword_medium($image_id, $db_conn, $related_limit - $related_counter);
	$response .= process_related_array($related_arr, $db_conn, $PROJS_PATH);
	
	
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
	
	$response .= "<li>";
	if ($mediums['name'][1] == 'print' || $mediums['name'][1] == 'interactive'){
		list($temp_width, $temp_height, $temp_src, $temp_attr) = getimagesize($PROJS_PATH . $row[1]);
		
		if ($temp_width > 600){
			$old_width = $temp_width;
			$old_height = $temp_height;
			$temp_width = 600;
			$temp_height = intval(($temp_width * $old_height) / $old_width);
		}
		
		if ($temp_height > 550){
			$old_width = $temp_width;
			$old_height = $temp_height;
			$temp_height = 550;
			$temp_width = intval(($temp_height * $old_width) / $old_height);
		}

		$top_margin = intval((550 - $temp_height) / 2);
		
		$response .= "<img src='" . $PROJS_PATH . $row[1] . "' style='width: " . $temp_width . "px; height: ";
		$response .= $temp_height . "px; margin-top:" . $top_margin . "px' />";
	} else {
		$video_attrs = preg_split('/\./', $row[1]);
		$response .= "<div class='video-container' >";
		$response .= "<div class='video-js-box'>";
		$response .= "<video class='video-js' height='470' width='600' controls preload>";
		$response .= "<source src='" . $PROJS_PATH . $video_attrs[0] . ".mp4' type='video/mp4' />";
		$response .= "<source src='" . $PROJS_PATH . $video_attrs[0] . ".ogg' type='video/ogg' />";
		$response .= "</video>";
		$response .= "</div>";
		$response .= "</div>";
	}
	$response .= "</li>";
	
	$image_array = get_images($project_id, $mediums['id'][1], $db_conn); 
	for ($i = 1; $i <= $image_array[0]; $i++){
		if ($image_array['id'][$i] != $image_id){
			$response .= "<li>";
			if ($mediums['name'][1] == 'print' || $mediums['name'][1] == 'interactive'){
				list($temp_width, $temp_height, $temp_src, $temp_attr) = getimagesize($PROJS_PATH . $image_array['name'][$i]);
		
				if ($temp_width > 600){
					$old_width = $temp_width;
					$old_height = $temp_height;
					$temp_width = 600;
					$temp_height = intval(($temp_width * $old_height) / $old_width);
				}
				
				if ($temp_height > 550){
					$old_width = $temp_width;
					$old_height = $temp_height;
					$temp_height = 550;
					$temp_width = intval(($temp_height * $old_width) / $old_height);
				}
				
				$top_margin = intval((550 - $temp_height) / 2);
				
				$response .= "<img src='" . $PROJS_PATH . $image_array['name'][$i] . "' style='width: " . $temp_width . "px; height: ";
				$response .= $temp_height . "px; margin-top: " . $top_margin . "px' />";
			} else {
				$video_attrs = preg_split('/\./', $image_array['name'][$i]);
				$response .= "<div class='video-container'>";
				$response .= "<div class='video-js-box'>";
				$response .= "<video  height='470' width='600' class='video-js' controls preload>";
				$response .= "<source src='" . $PROJS_PATH . $video_attrs[0] . ".mp4' type='video/mp4' />";
			   	$response .= "<source src='" . $PROJS_PATH . $video_attrs[0] . ".ogg' type='video/ogg' />";	
				$response .= "</video>";
				$response .= "</div>";
				$response .= "</div>";
			}
			$response .= "</li>";
		}
	}	
	
	$response .= '</ul>';
	$response .= '</div>';
	$response .= '</div>';
	
	echo $response; 
?>
