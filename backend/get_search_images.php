<?php
	require('db_connect.php');
	require('utils.php');
	require('config.php');
	
	if (!$_POST['query_string'] || $_POST['query_string'] == ''){
		die("No results.");
	}

	$query_string = $_POST['query_string'];

	$query_statement = "SELECT images.id, images.name, images.featured, images.thumb FROM images, projects WHERE";
	$query_statement .= " images.project_id=projects.id AND projects.name LIKE '%" . addslashes(addslashes($query_string)) . "%'";

	$query = mysql_query($query_statement, $db_conn);
	
	$results = array();
	$results[0] = 0;
	
	$types_string = "";
	$sizes_string = "";
	
	while ($row = mysql_fetch_row($query)){
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
