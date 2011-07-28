<?php
	require('db_connect.php');
	require('db_utils.php');
	$medium = $_POST['medium'];
	if (!$medium) die("Please select a medium!");
	$medium_id = get_medium_by_name($medium, $db_conn);
	$query_statement = "SELECT * FROM mediscs WHERE medium_id='" . $medium_id . "'";
	$query = mysql_query($query_statement);
	
	$response = "";
	$response .= "<ul id='filter-header'>";
	$response .= "<li class='filter-title'>";
	$response .= "<strong>category:</strong>";
	$response .= "<span>" . $medium . "</span>";
	$response .= "</li>";
	$response .= "<li class='filter-title'>";
	$response .= "<strong>results:</strong>";
	
	$query_statement = "SELECT * FROM mediscs WHERE medium_id='" . $medium_id . "'";
	$query = mysql_query($query_statement);
	$mediscs_array = array();
	while ($row = mysql_fetch_array($query)){
		array_push($mediscs_array, "medisc_id='" . $row['id'] . "'");
	}
	$query_statement = select_query('images', get_list($mediscs_array, " OR "));
	$query = mysql_query($query_statement);
	$num_res = mysql_num_rows($query);
	
	$response .= "<span>" . $num_res . "</span>";
	$response .= "</li>";
	$response .= "<li class='header-right'>";
	$response .= "clear refinements";
	$response .= "</li>";
	$response .= "</ul>";
	$response .= "<div class='filter-types'>";
	$response .= "<dl class='first-col'>";
	$response .= "<dt>";
	$response .= "discipline:";
	$response .= "</dt>";
	$response .= "<dd>";
	$response .= "<a href='#' rel='show-all'>show all results</a>";
	$response .= "</dd>";
	
	$query_statement = "SELECT * FROM mediscs WHERE medium_id='" . $medium_id ."'";
	$query = mysql_query($query_statement, $db_conn);
	while ($row = mysql_fetch_array($query)){
		$query_statement_2 = "SELECT name FROM disciplines WHERE id='" . $row['discipline_id'] . "' ORDER BY name";
		$query_2 = mysql_query($query_statement_2, $db_conn);
		$row_2 = mysql_fetch_array($query_2);
		$discipline = $row_2['name'];
		$response .= "<dd>";
		$response .= "<a href='#' rel='" . encode_dragos($discipline) . "'>" . $discipline . "</a>";
		$response .= "</dd>";
	}
	
	$response .= "</dl>";
	$response .= "<dl>";
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
		$response .= "<a href='#' rel='" . encode_dragos($row['name']) . "'>" . $row['name'] . "</a>";
		$response .= "</dd>";
	}
	
	$response .= "</dl>";
	$response .= "<dl>";
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
		$response .= "<a href='#' rel='" . encode_dragos($row['name']) . "'>" . $row['name'] . "</a>";
		$response .= "</dd>";
	}
	
	$response .= "</dl>";
	$response .= "<dl class='last-col'>";
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
		$response .= "<a href='#' rel='" . encode_dragos($row['value']) . "'>" . $row['value'] . "</a>";
		$response .= "</dd>";
	}
	
	$response .= "</dl>";
	$response .= "</div>";
	
	echo $response;
?>