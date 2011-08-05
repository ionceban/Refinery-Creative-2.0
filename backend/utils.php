<?php

	function list_to_array($list){
		$response_arr = array();
		
		$current_item = "";
		for ($i = 0; $i < strlen($list); $i++){
			if ($list[$i] == '_'){
				array_push($response_arr, $current_item);
				$current_item = "";
			} else {
				$current_item .= $list[$i];
			}
		}
		
		if (strlen($current_item) > 0){
			array_push($response_arr, $current_item);
		}
		
		return $response_arr;
	}

	function extension_checker($file_path){
		$path_jpeg = $file_path . ".jpg";
		$path_gif = $file_path . ".gif";
		$path_png = $file_path . ".png";
		
		if (file_exists($path_gif)){
			return "gif";
		} else if (file_exists($path_jpeg)){
			return "jpg";
		} else if (file_exists($path_png)){
			return "png";
		}
		
		return "no_ext";
	}
	
	function delete_single_image($single_image_id, $db_conn){
		$query_statement = "SELECT name FROM images WHERE id='" . $single_image_id . "'";
		$query = mysql_query($query_statement, $db_conn);
		$row = mysql_fetch_array($query);
		
		$file_attrs = preg_split('/\./', $row['name']);
		$core_name = $file_attrs[0];
		$original_ext = $file_attrs[1];
		$thumber_ext = extension_checker('projs/' . $core_name . "_t_thumber");
		
		$original_file = 'projs/' . $core_name . "." . $original_ext;
		$thumber_file = 'projs/' . $core_name . "_t_thumber." . $thumber_ext;
		$normal_file = 'projs/' . $core_name . "_t_normal." . $thumber_ext;
		$featured_file = 'projs/' . $core_name . "_t_featured." . $thumber_ext;
		$grid_file = 'projs/' . $core_name . "_t_grid." . $thumber_ext;
		$list_file = 'projs/' . $core_name . "_t_list." . $thumber_ext;
		
		if (!unlink($original_file)) return "failed delete ";
		if (!unlink($thumber_file)) return "failed delete";
		if (!unlink($normal_file)) return "failed delete";
		if (!unlink($featured_file)) return "failed delete";
		if (!unlink($grid_file)) return "failed delete";
		if (!unlink($list_file)) return "failed delete";
		
		$query_statement = "DELETE FROM imgdelivs WHERE image_id='" . $single_image_id . "'";
		if (!mysql_query($query_statement, $db_conn)) return "failed DB";
		
		$query_statement = "DELETE FROM imgkeyws WHERE image_id='" . $single_image_id . "'";
		if (!mysql_query($query_statement, $db_conn)) return "failed DB";
		
		$query_statement = "DELETE FROM images WHERE id='" . $single_image_id . "'";
		if (!mysql_query($query_statement, $db_conn)) return "failed DB";
		
		return "success";
	}

	function get_filter_query($Mediums, $Divisions, $Deliverables, $Keywords, $order){
	
		$query_statement = "SELECT DISTINCT images.id, images.featured, images.name, images.date, projects.name, mediums.name, divisions.name";
		$query_statement .= " FROM images, projects, mediscs, didiscs, mediums, divisions, disciplines";
		$query_statement .= ", imgdelivs, imgkeyws, deliverables, keywords WHERE(images.queued=0 AND images.project_id=projects.id AND ";
		$query_statement .= "images.id=imgdelivs.image_id AND deliverables.id=imgdelivs.deliverable_id AND ";
		$query_statement .= "images.id=imgkeyws.image_id AND keywords.id=imgkeyws.keyword_id AND ";
		$query_statement .= "images.medisc_id=mediscs.id AND mediscs.medium_id=mediums.id AND ";
		$query_statement .= "images.didisc_id=didiscs.id AND didiscs.division_id=divisions.id";
		
		
		if ($Mediums){
			$query_statement .= " AND (1=0";
			$mediums_arr = list_to_array($Mediums);
			foreach ($mediums_arr as $single_medium){
				$query_statement .= " OR mediums.name='" . $single_medium . "'";
			}
			$query_statement .= ")";
		}
		
		if ($Divisions){
			$query_statement .= " AND (1=0";
			$divisions_arr = list_to_array($Divisions);
			foreach ($divisions_arr as $single_division){
				$query_statement .= " OR divisions.name='" . $single_division . "'";
			}
			$query_statement .= ")";
		}
		
		if ($Keywords){
			$query_statement .= " AND (1=0";
			$keywords_arr = list_to_array($Keywords);
			foreach ($keywords_arr as $single_keyword){
				$query_statement .= " OR keywords.name='" . $single_keyword . "'";
			}
			$query_statement .= ")";
		}
		
		if ($Deliverables){
			$query_statement .= " AND (1=0";
			$deliverables_arr = list_to_array($Deliverables);
			foreach ($deliverables_arr as $single_deliverable){
				$query_statement .= " OR deliverables.name='" . $single_deliverable . "'";
			}
			$query_statement .= ")";
		}
		
		$query_statement .= ")";
		
		$query_statement .= " ORDER BY " . $order;
		
		return $query_statement;
	}

	function get_number_keywords($image_id, $db_conn, $low_limit, $up_limit){
		$query_statement = "SELECT keyword_id FROM imgkeyws WHERE image_id='" . $image_id . "'";
		$query = mysql_query($query_statement, $db_conn);
		
		$keyw_arr = array();
		$keyw_arr[0] = 0;
		while ($row = mysql_fetch_row($query)){
			++$keyw_arr[0];
			$keyw_arr[$keyw_arr[0]] = $row[0];
		}
		
		$response == array();
		$response[0] = 0;
		
		if ($keyw_arr[0] < $low_limit){
			return $response;
		}
		
		$partial = array();
		
		for ($i = 1; $i <= $keyw_arr[0]; $i++){
			$query_statement = "SELECT image_id FROM imgkeyws WHERE (keyword_id='" . $keyw_arr[$i] . "' AND image_id";
			$query_statement .= "!='" . $image_id . "')";
			$query = mysql_query($query_statement, $db_conn);
			
			while ($row = mysql_fetch_row($query)){
				if ($partial[$row[0]]['image_id']){
					$partial[$row[0]]['occ']++;
				} else {
					$partial[$row[0]]['image_id'] = $row[0];
					$partial[$row[0]]['occ'] = 1;
				}
			}
		}
		
		foreach ($partial as $elem){
			if (intval($elem['occ']) >= $low_limit && intval($elem['occ']) <= $up_limit){
				$response[0]++;
				$response[$response[0]] = $elem['image_id'];
			}
		}
		
		return $response;
	}

	function get_one_keyword_medium($image_id, $db_conn){
		$query_statement = "SELECT mediums.id FROM images,mediscs,mediums WHERE (images.medisc_id=mediscs.id";
		$query_statement .= " AND mediums.id=mediscs.medium_id AND images.id='" . $image_id . "')";
		$query = mysql_query($query_statement, $db_conn);
		$row = mysql_fetch_row($query);
		
		$medium_id = $row[0];
		
		$partial = array();
		
		$query_statement = "SELECT images.id FROM images,mediscs,mediums WHERE (images.medisc_id=mediscs.id";
		$query_statement .= " AND mediums.id=mediscs.medium_id AND mediums.id='" . $medium_id . "'";
		$query_statement .= " AND images.id!='" . $image_id . "')";
		$query = mysql_query($query_statement, $db_conn);
		
		while ($row = mysql_fetch_row($query)){
			if ($partial[$row[0]]['image_id']){
				$partial[$row[0]]['occ']++;
			} else {
				$partial[$row[0]]['image_id'] = $row[0];
				$partial[$row[0]]['occ'] = 1;
			}
		}
		
		$one_keyw_arr = get_number_keywords($image_id, $db_conn, 1, 1);
		
		for ($i = 1; $i <= $one_keyw_arr[0]; $i++){
			if ($partial[$one_keyw_arr[$i]]['image_id']){
				$partial[$one_keyw_arr[$i]]['occ']++;
			} else {
				$partial[$one_keyw_arr[$i]]['image_id'] = $one_keyw_arr[$i];
				$partial[$one_keyw_arr[$i]]['occ'] = 1;
			}
		} 
		
		$response == array();
		$response[0] = 0;
		
		foreach ($partial as $elem){
			if (intval($elem['occ']) == 2){
				$response[0]++;
				$response[$response[0]] = $elem['image_id'];
			}
		}
		
		return $response;
	}

	function process_related_array($related_arr, $db_conn, $PROJS_PATH){
			
		$response = "";
		
		for ($i = 1; $i <= $related_arr[0]; $i++){
			$query_statement = "SELECT images.name,mediums.name,projects.name FROM images,projects,mediums,mediscs WHERE";
			$query_statement .= "(images.id='" . $related_arr[$i] . "' AND images.medisc_id=mediscs.id AND ";
			$query_statement .= "mediscs.medium_id=mediums.id AND images.project_id=projects.id)";
			
			$query = mysql_query($query_statement, $db_conn);
			$row = mysql_fetch_row($query);
			
			$class_attr = $row[1] . "_" . $related_arr[$i];
			$file_attrs = preg_split('/\./', $row[0]);
			$thumber_ext = extension_checker($PROJS_PATH . $file_attrs[0] . "_t_thumber");
			$list_body = $file_attrs[0] . "_t_grid";
			$src_attr = $PROJS_PATH . $list_body . "." . $thumber_ext;
			
			list($grid_width, $grid_height, $grid_src, $grid_attr) = getimagesize($src_attr);
			
			$height_constraint = 70;
			$width_constraint = intval(($height_constraint * $grid_width) / $grid_height);
			
			$response .= "<li>";
			$response .= "<a href='#'>";
			$response .= "<div class='img-container'>";
			$response .= "<img class='" . $class_attr . "' src='" . $src_attr . "' style='width:" . $width_constraint . "px; height:" . $height_constraint . "px' />"; 
			$response .= "<span class='tooltip'><h5>" . $row[2] . "</h5></span>";
			$response .= "</div>";
			$response .= "</a>";
			$response .= "</li>";
			
		}
		
		return $response;
	}
?>
