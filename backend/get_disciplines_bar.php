<?php
	require('db_connect.php');	

	if (!$_POST['category']){
		die("Inconsistent data");
	}

	// detect medium | divison
	
	
	$category = $_POST['category'];
	$old_category = $category;

	if ($category == 'digital-motion') {
		$category = 'digital motion';
	}

	if ($category == 'home-entertainment'){
		$category = 'home entertainment';
	}

	$query_statement = "SELECT * FROM mediums WHERE name='" . $category . "'";
	$query = mysql_query($query_statement, $db_conn);

	if (mysql_fetch_row($query)){
		$table = "mediums";
		$table_id = "medium_id";

		$cross_table = "mediscs";
		$cross_table_id = "medisc_id";
	}

	$query_statement = "SELECT * FROM divisions WHERE name='" . $category . "'";
	$query = mysql_query($query_statement, $db_conn);

	if (mysql_fetch_row($query)){
		$table ="divisions";
		$table_id = "division_id";

		$cross_table = "didiscs";
		$cross_table_id = "didisc_id";
	}

	if (!$table){
		die("Inconsistent data");
	}

	// get da' disciplines
	
	$query_statement = "SELECT disciplines.name, COUNT(*) FROM images, " . $table . ", " . $cross_table . ", disciplines WHERE";
	$query_statement .= "(images." . $cross_table_id . "=" . $cross_table . ".id";
	$query_statement .= " AND " . $table . ".id=" . $cross_table . "." . $table_id;
	$query_statement .= " AND disciplines.id=" . $cross_table . ".discipline_id";
	$query_statement .= " AND " . $table . ".name='" . $category . "' AND images.queued=0 AND images.shadowbox=0) GROUP BY disciplines.name";
	$query_statement .= " ORDER BY disciplines.name"; 

	$query = mysql_query($query_statement, $db_conn);

	$response = "<li><a href='#!/" . $old_category . "' rel='show-all' class='sub-active'>show all</a></li>";

	while ($row = mysql_fetch_row($query)){
		$response .= "<li><a href='#!/" . $old_category . "' rel='" . $row[0] . "'>" . $row[0] . "</a></li>";
	}

	echo $response;
?>

