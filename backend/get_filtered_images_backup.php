<?php
	require('config.php');
	require('db_connect.php');
	require('db_utils.php');

	$table = array('mediums', 'divisions');
	$table_id = array('medium_id', 'division_id');
	$cross_table = array('mediscs', 'didiscs');
	$cross_table_id = array('medisc_id', 'didisc_id');

	if (!$_POST['category']) die('Invalid URL');
	$category = decode_cat_name($_POST['category']);

	$query_statement = "SELECT id FROM mediums WHERE name='" . $category . "'";
	$query = mysql_query($query_statement, $db_conn);
	$row = mysql_fetch_array($query);

	if ($row){
		$category_id = $row['id'];
		$current_index = 0;
	}

	$query_statement = "SELECT id FROM divisions WHERE name='" . $category . "'";
	$query = mysql_query($query_statement, $db_conn);
	$row = mysql_fetch_array($query);

	if ($row){
		$category_id = $row['id'];
		$current_index = 1;
	}

	if (!$category_id) die('Invalid URL');

	if (!$_POST['discipline']) $discipline_array = array('show-all'); else
		$discipline_array = parse_string($_POST['discipline']);
	$query_statement = "SELECT * FROM " . $cross_table[$current_index] . " WHERE (" . $table_id[$current_index] . "='" . $category_id . "' AND ";
	$query_array = array();

	foreach ($discipline_array as $discipline){
		if ($discipline == 'show-all') array_push($query_array, '1=1'); else {
			$query_statement_2 = "SELECT id FROM disciplines WHERE name='" . $discipline . "'";
			$query_2 = mysql_query($query_statement_2, $db_conn);
			$row_2 = mysql_fetch_array($query_2);
			if (!$row_2) die("Invalid URL");
			
			$copy_row_2 = $row_2['id'];
			$query_statement_2 = "SELECT id FROM " . $cross_table[$current_index] . " WHERE (" . $table_id[$current_index] . "='" . $category_id;
			$query_statement_2 .= "' AND discipline_id='" . $row_2['id'] . "')";
			$query_2 = mysql_query($query_statement_2, $db_conn);
			$row_2 = mysql_fetch_array($query_2);
			if (!$row_2) die("Invalid URL");
			array_push($query_array, "discipline_id='" . $copy_row_2 . "'");
		}
	}

	$query_statement .= get_list($query_array, " OR ") . ")";
	$query = mysql_query($query_statement, $db_conn);

	$query_array = array();
	while ($row = mysql_fetch_array($query)){
		array_push($query_array, $cross_table_id[$current_index] . "='" . $row['id' ] . "'");
	}

	$query_statement = "SELECT * FROM images WHERE " . get_list($query_array, " OR ") . " ORDER BY date DESC, id DESC";
	$query = mysql_query($query_statement, $db_conn);
	
	$result = array();
	while ($row = mysql_fetch_array($query)){
		$result[$row['id']]['length'] = '1';
		$result[$row['id']]['filename'] = $row['name'];
		$result[$row['id']]['featured'] = $row['featured'];
		$result[$row['id']]['image_id'] = $row['id'];
		$query_statement_2 = "SELECT medium_id FROM mediscs WHERE id='" . $row['medisc_id'] . "'";
		$query_2 = mysql_query($query_statement_2, $db_conn);
		$row_2 = mysql_fetch_array($query_2);
		$query_statement_2 = "SELECT name FROM mediums WHERE id='" . $row_2['medium_id'] . "'";
		$query_2 = mysql_query($query_statement_2, $db_conn);
		$row_2 = mysql_fetch_array($query_2);
		$result[$row['id']]['medium_name'] = $row_2['name'];
		$result[$row['id']]['project_id'] = $row['project_id'];
	}

	if (!$_POST['deliverable']) $deliverable_array = array('show-all'); else
		$deliverable_array = parse_string($_POST['deliverable']);

	$query_array = array();

	foreach ($deliverable_array as $deliverable){
		if ($deliverable == 'show-all') array_push($query_array, '1=1'); else {
			$query_statement_2 = "SELECT id FROM deliverables WHERE name='" . $deliverable . "'";
			$query_2 = mysql_query($query_statement_2, $db_conn);
			$row_2 = mysql_fetch_array($query_2);
			if (!$row_2) die("Invalid URL");
			array_push($query_array, "deliverable_id='" . $row_2['id'] . "'");
		}
	}

	$query_statement = "SELECT DISTINCT image_id FROM imgdelivs WHERE " . get_list($query_array, " OR ");
	$query = mysql_query($query_statement, $db_conn);
	while ($row = mysql_fetch_array($query))
		if ($result[$row['image_id']]['length'] == '1') $result[$row['image_id']]['length'] = '2';

	if (!$_POST['keywords']) $keywords_array = array('show-all'); else
		$keywords_array = parse_string($_POST['keywords']);

	$query_array = array();

	foreach ($keywords_array as $keyword){
		if ($keyword == 'show-all') array_push($query_array, '1=1'); else {
			$query_statement_2 = "SELECT id FROM keywords WHERE name='" . $keyword . "'";
			$query_2 = mysql_query($query_statement_2, $db_conn);
			$row_2 = mysql_fetch_array($query_2);
			if (!$row_2) die("Invalid URL");
			array_push($query_array, "keyword_id='" . $row_2['id'] . "'");
		}
	}

	$query_statement = "SELECT DISTINCT image_id FROM imgkeyws WHERE " . get_list($query_array, " OR ");
	$query = mysql_query($query_statement, $db_conn);
	while ($row = mysql_fetch_array($query))
		if ($result[$row['image_id']]['length'] == '2') $result[$row['image_id']]['length'] = '3';

	if (!$_POST['year']) $year_array = array('show-all'); else
		$year_array = parse_string($_POST['year']);

	$query_array = array();

	foreach ($year_array as $year){
		if ($year == 'show-all') array_push($query_array, '1=1'); else {
			$query_statement_2 = "SELECT id FROM years WHERE value='" . $year . "'";
			$query_2 = mysql_query($query_statement_2, $db_conn);
			$row_2 = mysql_fetch_array($query_2);
			if (!$row_2) die("Invalid URL");
			array_push($query_array, "year_id='" . $row_2['id'] . "'");
		}
	}

	$query_statement = "SELECT id FROM images WHERE " . get_list($query_array, " OR ");
	$query = mysql_query($query_statement, $db_conn);
	while ($row = mysql_fetch_array($query)){
		if ($result[$row['id']]['length'] == '3') $result[$row['id']]['length'] = '4';
	}
	
	$query_statement = "SELECT id FROM images WHERE queued='0'";
	$query = mysql_query($query_statement, $db_conn);
	
	$number = 0;
	$types_string = "";
	$sizes_string = "";
	
	$files_arr = array();
	$featured_arr = array();
	$appended_class_arr = array();
	
	foreach ($result as $elem)
		if ($elem['length'] == '4'){
			$number++;
			if ($elem['medium_name'] == 'print' || $elem['medium_name'] == 'interactive'){
				$types_string .= "0";
			} else {
				$types_string .= "1";
			}
			
			if ($elem['featured'] == '1'){
				$sizes_string .= "1";
			} else {
				$sizes_string .= "0";
			}
			$files_arr[$number] = $elem['filename'];
			$featured_arr[$number] = $elem['featured'];
			$appended_class_arr[$number] = $elem['medium_name'] . "_" . $elem['project_id'] . "_" . $elem['image_id'];
		}

	$response_final = "";
	$cmd_string = "refinery_grid.exe " . $number . " " . $types_string . " " . $sizes_string;
	exec($cmd_string, $response_arr);
	
	foreach ($response_arr as $resp_chunk){
		$response_final .= $resp_chunk;
	}
	
	for ($i = $number; $i >= 1; $i--){
		$dummy_src = "dummy_source" . $i . "'";
		$file_attrs = preg_split('/\./', $files_arr[$i]);
		$core_aux = $file_attrs[0];
		$core_name = $file_attrs[0] . "_t_thumber.";
		$core_jpeg = /*'../../refinery-backend/projs/'*/ $FILES_PATH . $core_name . "jpg";
		$core_png = '../../refinery-backend/projs/' . $core_name . "png";
		$core_gif = '../../refinery-backend/projs/' . $core_name . "gif";
		
		$core_ext = "";
		if (file_exists($core_jpeg)){
			$core_ext = 'jpg';
		} else if (file_exists($core_png)){
			$core_ext = 'png';
		} else {
			$core_ext = 'gif';
		}
		
		$fname = "";
		if ($featured_arr[$i] == '1'){
			$fname = $PROJS_PATH . $core_aux . "_t_featured." . $core_ext;
		} else {
			$fname = $PROJS_PATH . $core_aux . "_t_normal." . $core_ext;
		}
		
		$fname .= "' class='" . $appended_class_arr[$i] . "'";
		$response_final = str_replace($dummy_src, $fname, $response_final);
	}
	echo $response_final;
?>
