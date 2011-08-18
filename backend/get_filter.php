<?php
	require('db_connect.php');
	require('db_utils.php');
	
	$category = decode_cat_name($_POST['category']);
	
	$query_statement = "SELECT id FROM mediums WHERE name='" . $category . "'";
	$query = mysql_query($query_statement, $db_conn);
	$row = mysql_fetch_row($query);
	
	if ($row){
		$table = "mediums";
		$table_id = "medium_id";
		$cross_table = "mediscs";
		$cross_table_id = "medisc_id";
		$category_id = $row[0];
	}
	
	if (!$table){
		$query_statement = "SELECT id FROM divisions WHERE name='" . $category . "'";
		$query = mysql_query($query_statement, $db_conn);
		$row = mysql_fetch_row($query);
		
		if ($row){
			$table = "divisions";
			$table_id = "division_id";
			$cross_table = "didiscs";
			$cross_table_id = "didisc_id";
			$category_id = $row[0];
		}
	}
	
	if (!$table){
		die("Invalid category");
	}
	
	$response = "";
	$response .= "<ul id='filter-header'>";
	$response .= "<li class='filter-title'>";
	$response .= "<strong>category:</strong>";
	$response .= "<span>" . $category . "</span>";
	$response .= "</li>";
	
	$query_statement = "SELECT COUNT(*) FROM images, " . $cross_table . " WHERE ";
	$query_statement .= "(images." . $cross_table_id . "=" . $cross_table . ".id";
	$query_statement .= " AND " . $cross_table . "." . $table_id . "='" . $category_id . "')";
	$query = mysql_query($query_statement, $db_conn);
	$row = mysql_fetch_row($query);
	
	$response .= "<li class='filter-title'>";
	$response .= "<strong>results:</strong>";
	$response .= "<span>" . $row[0] . "</span>";
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
	
	$query_statement = "SELECT disciplines.name, COUNT(*) FROM images, " . $table . ", " . $cross_table;
	$query_statement .= ", disciplines WHERE (images." . $cross_table_id . "=" . $cross_table . ".id";
	$query_statement .= " AND " . $table . ".id=" . $cross_table . "." . $table_id;
	$query_statement .= " AND disciplines.id=" . $cross_table . ".discipline_id";
	$query_statement .= " AND " . $table . ".name='" . $category . "') GROUP BY disciplines.name";
	$query_statement .= " ORDER BY disciplines.name";
	$query = mysql_query($query_statement, $db_conn);
	
	while ($row = mysql_fetch_row($query)){
		$response .= "<dd>";
		$response .= "<a href='#' rel='" . $row[0] . "'>" . $row[0] . " (" . $row[1] . ")</a>";
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
	
	$query_statement = "SELECT deliverables.name, COUNT(*) FROM images, deliverables, imgdelivs";
	$query_statement .= ", " . $table . ", " . $cross_table . " ";
	$query_statement .= " WHERE (images.id=imgdelivs.image_id AND deliverables.id=imgdelivs.deliverable_id";
	$query_statement .= " AND images." . $cross_table_id . "=" . $cross_table . ".id";
	$query_statement .= " AND " . $table . ".id=" . $cross_table . "." . $table_id;
	$query_statement .= " AND " . $table . ".name='" . $category . "')";
	$query_statement .= " GROUP BY deliverables.name ORDER BY deliverables.name";
	$query= mysql_query($query_statement, $db_conn);
	
	while ($row = mysql_fetch_row($query)){
		$response .= "<dd>";
		$response .= "<a href='#' rel='" . $row[0] . "'>" . $row[0] . " (" . $row[1] . ")</a>";
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
	
	$query_statement = "SELECT keywords.name, COUNT(*) FROM images, keywords, imgkeyws";
	$query_statement .= ", " . $table . ", " . $cross_table . " ";
	$query_statement .= "WHERE (images.id=imgkeyws.image_id AND keywords.id=imgkeyws.keyword_id";
	$query_statement .= " AND images." . $cross_table_id . "=" . $cross_table . ".id";
	$query_statement .= " AND " . $table . ".id=" . $cross_table . "." . $table_id;
	$query_statement .= " AND " . $table . ".name='" . $category . "')";
	$query_statement .= " GROUP BY keywords.name ORDER BY keywords.name";
	$query = mysql_query($query_statement, $db_conn);
	
	while ($row = mysql_fetch_row($query)){
		$response .= "<dd>";
		$response .= "<a href='#' rel='" . $row[0] . "'>" . $row[0] . " (" . $row[1] . ")</a>";
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
	
	$query_statement = "SELECT years.value, COUNT(*) FROM images, years";
	$query_statement .= ", " . $table . ", " . $cross_table . " ";
	$query_statement .= " WHERE (years.id=images.year_id";
	$query_statement .= " AND images." . $cross_table_id . "=" . $cross_table . ".id";
	$query_statement .= " AND " . $table . ".id=" . $cross_table . "." . $table_id;
	$query_statement .= " AND " . $table . ".name='" . $category . "')";
	$query_statement .= " GROUP BY years.value ORDER BY years.value";
	$query = mysql_query($query_statement, $db_conn);
	
	while ($row = mysql_fetch_row($query)){
		$response .= "<dd>";
		$response .= "<a href='#' rel='" . $row[0] . "'>" . $row[0] . " (" . $row[1] . ")</a>";
		$response .= "</dd>";
	}
	$response .= "</dl>";
	
	echo $response;
?>
