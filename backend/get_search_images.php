<?php
	require('db_connect.php');
	require('utils.php');
	require('config.php');
	
	if (!$_POST['query_string'] || $_POST['query_string'] == ''){
		die("No results.");
	}

	$query_string = $_POST['query_string'];

	$Q_state = "SELECT images.id, images.name, images.featured, images.thumb FROM images, projects, mediums, divisions, mediscs, didiscs WHERE ";
	$Q_state .= "(images.medisc_id=mediscs.id AND images.didisc_id=didiscs.id AND mediscs.medium_id=mediums.id AND images.queued=0 AND didiscs.division_id=divisions.id AND projects.id=images.project_id AND ";
	$Q_state .= "(projects.name LIKE '%" . addslashes(addslashes($query_string)) . "%'";
	$Q_state .= " OR mediums.name LIKE '%" . addslashes(addslashes($query_string)) . "%'";
	$Q_state .= " OR divisions.name LIKE '%" . addslashes(addslashes($query_string)) . "%'))";
	
	$Q_altern = mysql_query($Q_state, $db_conn);

	$map_altern = array();

	while ($row_altern = mysql_fetch_row($Q_altern)){
		$map_altern[$row_altern[0]]['image_id'] = $row_altern[0];
		$map_altern[$row_altern[0]]['featured'] = $row_altern[2];
		$map_altern[$row_altern[0]]['filename'] = $row_altern[1];
		$map_altern[$row_altern[0]]['thumb'] = $row_altern[3];
	}

	$Q_state = "SELECT images.id, images.name, images.featured, images.thumb FROM images, imgkeyws, keywords WHERE ";
	$Q_state .= "(images.id=imgkeyws.image_id AND keywords.id=imgkeyws.keyword_id AND keywords.name LIKE '%" . addslashes(addslashes($query_string)) . "%')";
	//die ($Q_state);
	$Q_altern = mysql_query($Q_state, $db_conn);

	while ($row_altern = mysql_fetch_row($Q_altern)){
		$map_altern[$row_altern[0]]['image_id'] = $row_altern[0];
		$map_altern[$row_altern[0]]['featured'] = $row_altern[2];
		$map_altern[$row_altern[0]]['filename'] = $row_altern[1];
		$map_altern[$row_altern[0]]['thumb'] = $row_altern[3];
	}

	/*$query_statement = "SELECT images.id, images.name, images.featured, images.thumb FROM images, projects WHERE";
	$query_statement .= " images.project_id=projects.id AND projects.name LIKE '%" . addslashes(addslashes($query_string)) . "%'";

	$query = mysql_query($query_statement, $db_conn); */
	
	$results = array();
	$results[0] = 0;
	
	$types_string = "";
	$sizes_string = "";

	foreach ($map_altern as $el_altern){
		if ($el_altern['featured'] == '1'){
			$sizes_string .= "1";
		} else {
			$sizes_string .= "0";
		}

		if ($el_altern['thumb'] == '1'){
			$types_string .= "0";
		} else {
			$types_string .= "1";
		}

		$results[0]++;
		$results[$results[0]]['filename'] = $el_altern['filename'];
		$results[$results[0]]['featured'] = $el_altern['featured'];
		$results[$results[0]]['image_id'] = $el_altern['image_id'];
	}

	if ($results[0] == 0){
		die("No results.");
	}
	
	/*while ($row = mysql_fetch_row($query)){
		if ($row[2] == '1'){
			$sizes_string .= "1";
		} else {
			$sizes_string .= "0";
		}

		if ($row[3] == '1'){
			$types_string .= "0";
		} else {
			$types_string .= "1";
		}

		$results[0]++;
		$results[$results[0]]['filename'] = $row[1];
		$results[$results[0]]['featured'] = $row[2];
		$results[$results[0]]['image_id'] = $row[0];
	}*/

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

		if ($results[$i]['featured'] == '0'){
			$replace_with .= "_t_normal.";
		} else {
			$replace_with .= "_t_featured.";
		}
		
		$replace_with .= $thumber_ext . "' class='" . $results[$i]['image_id'] . "'";

		$response = str_replace($to_replace, $replace_with, $response); 
	}

	echo $response;
?>
