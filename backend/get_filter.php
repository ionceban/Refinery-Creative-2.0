<?php
	require('db_connect.php');
	require('db_utils.php');

	$available = array('print', 'av', 'interactive', 'digital motion', 'home entertainment', 'broadcast', 'theatrical', 'gaming');	

	$get_index = array();
	$get_index['print'] = 0;
	$get_index['av'] = 0;
	$get_index['digital motion'] = 0;
	$get_index['interactive'] = 0;
	$get_index['home entertainment'] = 1;
	$get_index['theatrical'] = 1;
	$get_index['broadcast'] = 1;
	$get_index['gaming'] = 1;
	
	$cross_table = array('mediscs', 'didiscs');

	$column_name = array('medium_id', 'division_id');

	$cross_column_name = array('medisc_id', 'didisc_id');
	
	$category = $_POST['category'];
	if (!$category) die("Please select a category");

	$category = decode_cat_name($category);

	$exists = 0;

	for ($i = 0; $i < 8; $i++)
		if ($available[$i] == $category) $exists = 1;

	if ($exists == 0) die("Invalid category");
	
	$current_index = $get_index[$category];
	
	if ($current_index == 0) $category_id = get_medium_by_name($category, $db_conn);
	else $category_id = get_division_by_name($category, $db_conn);
	
	$response = "";
	$response .= "<ul id='filter-header'>";
	$response .= "<li class='filter-title'>";
	$response .= "<strong>category:</strong>";
	$response .= "<span>" . $category . "</span>";
	$response .= "</li>";
	$response .= "<li class='filter-title'>";
	$response .= "<strong>results:</strong>";
	
	$query_statement = "SELECT * FROM " . $cross_table[$current_index] . " WHERE " . $column_name[$current_index] . "='" . $category_id . "'";
	$query = mysql_query($query_statement, $db_conn);
	$cross_table_array = array();
	while ($row = mysql_fetch_array($query)){
		array_push($cross_table_array, $cross_column_name[$current_index] . "='" . $row['id'] . "'");
	}
	$query_statement = select_query('images', get_list($cross_table_array, " OR "));
	$query = mysql_query($query_statement, $db_conn);
	$num_res = mysql_num_rows($query);
	
	$response .= "<span>" . $num_res . "</span>";
	$response .= "</li>";
	$response .= "<li class='header-right'>";
	$response .= "clear refinements";
	$response .= "</li>";
	$response .= "</ul>";
	$response .= "<div class='filter-types'>";
	$response .= "<dl class='first-col discipline'>";
	$response .= "<dt>";
	$response .= "discipline:";
	$response .= "</dt>";
	$response .= "<dd>";
	$response .= "<a href='#' rel='show-all'>show all results</a>";
	$response .= "</dd>";
	
	$query_statement = "SELECT * FROM " . $cross_table[$current_index] . " WHERE " . $column_name[$current_index] . "='" . $category_id ."'";
	$query = mysql_query($query_statement, $db_conn);
	while ($row = mysql_fetch_array($query)){
		$query_statement_2 = "SELECT name FROM disciplines WHERE id='" . $row['discipline_id'] . "' ORDER BY name";
		$query_2 = mysql_query($query_statement_2, $db_conn);
		$row_2 = mysql_fetch_array($query_2);
		$discipline = $row_2['name'];
		$response .= "<dd>";
		$response .= "<a href='#' rel='" . $discipline . "'>" . $discipline . "</a>";
		$response .= "</dd>";
	}
	
	$response .= "</dl>";
	$response .= "<dl class='deliverable'>";
	$response .= "<dt>";
	$response .= "deliverable:";
	$response .= "</dt>";
	$response .= "<dd>";
	$response .= "<a href='#' rel='show-all'>show all results</a>";
	$response .= "</dd>";
	
	$query_statement = "SELECT name FROM deliverables ORDER BY name";
	$query = mysql_query($query_statement, $db_conn);
	while ($row = mysql_fetch_array($query)){
		$response .= "<dd>";
		$response .= "<a href='#' rel='" . $row['name'] . "'>" . $row['name'] . "</a>";
		$response .= "</dd>";
	}
	
	$response .= "</dl>";
	$response .= "<dl class='keywords'>";
	$response .= "<dt>";
	$response .= "key words:";
	$response .= "</dt>";
	$response .= "<dd>";
	$response .= "<a href='#' rel='show-all'>show all results</a>";
	$response .= "</dd>";
	
	$query_statement = "SELECT name FROM keywords ORDER BY name";
	$query = mysql_query($query_statement, $db_conn);
	while ($row = mysql_fetch_array($query)){
		$response .= "<dd>";
		$response .= "<a href='#' rel='" . $row['name'] . "'>" . $row['name'] . "</a>";
		$response .= "</dd>";
	}
	
	$response .= "</dl>";
	$response .= "<dl class='last-col year'>";
	$response .= "<dt>";
	$response .= "year:";
	$response .= "</dt>";
	$response .= "<dd>";
	$response .= "<a href='#' rel='show-all'>show all results</a>";
	$response .= "</dd>";
	
	$query_statement = "SELECT value FROM years ORDER BY value";
	$query = mysql_query($query_statement, $db_conn);
	while ($row = mysql_fetch_array($query)){
		$response .= "<dd>";
		$response .= "<a href='#' rel='" . $row['value'] . "'>" . $row['value'] . "</a>";
		$response .= "</dd>";
	}
	
	$response .= "</dl>";
	$response .= "</div>";
	
	echo $response;
?>
