<?php
	
	require('config.php');
	require('db_connect.php');
	require('utils.php');

	// detecting category (medium | divison)
	
	if (!$_POST['category']){
		die("Inconsistent data");
	}
	
	$_POST['category'] = str_replace('-', ' ', $_POST['category']);
	
	$query_statement = "SELECT id FROM mediums WHERE name='" . $_POST['category'] . "'";
	$query = mysql_query($query_statement, $db_conn);
	$row = mysql_fetch_row($query);
	
	if ($row){
		$category_id = $row[0];
		$table = 'mediums';
		$table_id = 'medium_id';
		$cross_table = 'mediscs';
		$cross_table_id = 'medisc_id';
	}
	
	if (!$category_id){
		$query_statement = "SELECT id FROM divisions WHERE name='" . $_POST['category'] . "'";
		$query = mysql_query($query_statement, $db_conn);
		$row = mysql_fetch_row($query);
		
		if ($row){
			$category_id = $row[0];
			$table = 'divisions';
			$table_id = 'division_id';
			$cross_table = 'didiscs';
			$cross_table_id = 'didisc_id';
		}
	}
	
	if (!$category_id){
		die("Inconsistent data");
	}
	
	// parsing "_"-separated strings. ALL if BLANK
	
	if ($_POST['discipline']){
		$disciplines_array = list_to_array($_POST['discipline']);
	} else {
		$disciplines_array = array();
	}
	
	if ($_POST['deliverable']){
		$deliverables_array = list_to_array($_POST['deliverable']);
	} else {
		$deliverables_array = array();
	}
	
	if ($_POST['keywords']){
		$keywords_array = list_to_array($_POST['keywords']);
	} else {
		$keywords_array = array();
	}
	
	if ($_POST['year']){
		$years_array = list_to_array($_POST['year']);
	} else {
		$years_array = array();
	}
	
	// build the medium+disciplines+years query
	
	$query_statement = "SELECT images.id, images.name, images.featured, mediums.name, images.thumb FROM images, years, disciplines";
	$query_statement .= ", " . $table . ", " . $cross_table;
	
	if ($table != 'mediums'){
		$query_statement .= ", mediums, mediscs";
	}
	
	$query_statement .= " WHERE ";

	// not shadowbox-only
	$query_statement .= "images.shadowbox=0";

	// not in queue
	$query_statement .= " AND images.queued=0";
	
	// category match
	
	$query_statement .= " AND images." . $cross_table_id . "=" . $cross_table . ".id";
	$query_statement .= " AND " . $table . ".id=" . $cross_table . "." . $table_id;
	$query_statement .= " AND " . $table . ".id=" . $category_id;
	
	if ($table != 'mediums'){
		$query_statement .= " AND images.medisc_id=mediscs.id AND mediums.id=mediscs.medium_id";
	}
	
	//discipline match
	
	$query_statement .= " AND " . $cross_table . ".discipline_id=disciplines.id";
	$query_statement .= " AND (1=";
	
	// show all thingy
	if (!$_POST['discipline']){
		$query_statement .= "1";
	} else {
		$query_statement .= "0";
	}
	
	foreach ($disciplines_array as $single_discipline){
		if ($single_discipline == 'show-all'){
			$query_statement .= " OR 1=1";
		} else {
			$query_statement .= " OR disciplines.name='" . $single_discipline . "'";
		}
	}
	
	$query_statement .= ")";
	
	// year match
	
	$query_statement .= " AND images.year_id=years.id";
	$query_statement .= " AND (1=";
	
	if (!$_POST['years']){
		$query_statement .= "1";
	} else {
		$query_statement .= "0";
	}
	
	foreach ($years_array as $single_year){
		if ($single_year == 'show-all'){
			$query_statement .= " OR 1=1";
		} else {
			$query_statement .= " OR years.value='" . $single_year . "'";
		}
	}
	
	$query_statement .= ")";
	
	// trigger it

	$query = mysql_query($query_statement, $db_conn);
	
	$image_map = array();
	
	while ($row = mysql_fetch_row($query)){
		$image_map[$row[0]]['image_id'] = $row[0];
		$image_map[$row[0]]['filename'] = $row[1];
		$image_map[$row[0]]['featured'] = $row[2];
		$image_map[$row[0]]['medium_name'] = $row[3];
		$image_map[$row[0]]['thumb'] = $row[4];
		$image_map[$row[0]]['occ'] = 1;
	}
	
	// build the deliverables query
	
	$query_statement = "SELECT DISTINCT imgdelivs.image_id FROM deliverables, imgdelivs WHERE ";
	$query_statement .= "deliverables.id=imgdelivs.deliverable_id";
	$query_statement .= " AND (1=";
	
	if (!$_POST['deliverable']){
		$query_statement .= "1";
	} else {
		$query_statement .= "0";
	}
	
	foreach ($deliverables_array as $single_deliverable){
		if ($single_deliverable == 'show-all'){
			$query_statement .= " OR 1=1";
		} else {
			$query_statement .= " OR deliverables.name='" . $single_deliverable . "'";
		}
	}
	
	$query_statement .= ")";
	
	// trigger it
	
	$query = mysql_query($query_statement, $db_conn);
	
	while ($row = mysql_fetch_row($query)){
		if ($image_map[$row[0]]['image_id']){
			$image_map[$row[0]]['occ']++;
		}
	}
	
	// build the keywords query
	
	$query_statement = "SELECT DISTINCT imgkeyws.image_id FROM imgkeyws, keywords WHERE ";
	$query_statement .= "keywords.id=imgkeyws.keyword_id";
	$query_statement .= " AND (1=";
	
	if (!$_POST['keywords']){
		$query_statement .= "1";
	} else {
		$query_statement .= "0";
	}
	
	foreach ($keywords_array as $single_keyword){
		if ($single_keyword == 'show-all'){
			$query_statement .= " OR 1=1";
		} else {
			$query_statement .= " OR keywords.name='" . $single_keyword . "'";
		}
	}
	
	$query_statement .= ")";
	
	// trigger it
	
	$query = mysql_query($query_statement, $db_conn);
	
	while ($row = mysql_fetch_row($query)){
		if ($image_map[$row[0]]['image_id']){
			$image_map[$row[0]]['occ']++;
		}
	}
	
	$results[0] = 0;
	$types_string = "";
	$sizes_string = "";
		
	foreach ($image_map as $single_image){
		if ($single_image['occ'] == 3){
				
			if ($single_image['thumb'] == '1'){
				$types_string .= "0";
			} else {
				$types_string .= "1";
			}
			
			if ($single_image['featured'] == '1'){
				$sizes_string .= "1";
			} else {
				$sizes_string .= "0";
			}
		
			$results[0]++;
			$results[$results[0]]['filename'] = $single_image['filename'];
			$results[$results[0]]['featured'] = $single_image['featured'];
			$results[$results[0]]['image_id'] = $single_image['image_id'];
		}
	}
	
	$command = "./refinery_grid " . $results[0] . " " . $types_string . " " . $sizes_string;
	
	exec($command, $response_array);
	
	$response = "";
	
	foreach ($response_array as $response_chunk){
		$response .= $response_chunk;
	}
	
	for ($i = $results[0]; $i >= 1; $i--){
		$to_replace = "dummy_source" . $i . "'";
		
		$file_attrs = preg_split('/\./', $results[$i]['filename']);
		$thumber_ext = extension_checker($PROJS_PATH . $file_attrs[0] . "_t_thumber");
		
		$replace_with = $PROJS_PATH . $file_attrs[0];
		
		if ($results[$i]['featured'] == '1'){
			$replace_with .= "_t_featured.";
		} else {
			$replace_with .= "_t_normal.";
		}
		
		$replace_with .= $thumber_ext . "' class='" . $results[$i]['image_id'] . "'";
		
		$response = str_replace($to_replace, $replace_with, $response);
	}
	
	echo $response;
?>
