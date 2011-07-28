<?php
	
	function get_list($arr, $separator){
		$i = 0;
		$result = "(";
		foreach($arr as $elem){
			$i++;
			if ($i > 1) $result .= $separator;
			$result .= $elem;
		}
		$result .= ")";
		return $result;
	}

	function select_query($target_table, $condition){
		$result = "SELECT * FROM " . $target_table;
		$result .= " WHERE " . $condition;
		return $result;
	}
	
	function parse_string($str){
		$result = array();	
		$cs = "";
		for ($i = 0 ; $i < strlen($str) ; $i++){
			if ($str[$i] == "_"){
				array_push($result, $cs);
				$cs = "";
			} else {
				$cs .= $str[$i];
			}
		}
		if (strlen($cs) > 0){
			array_push($result, $cs);
		}
		return $result;
	}
	
	function get_medium_by_name($medium, $db_conn){
		$query_statement = select_query('mediums', get_list(array("name='" . $medium . "'"), " AND "));
		$query = mysql_query($query_statement, $db_conn);
		$row = mysql_fetch_array($query);
		return $row['id'];
	}
	
	function get_division_by_name($division, $db_conn){
		$query_statement = select_query('divisions', get_list(array("name='" . $division . "'"), " AND "));
		$query = mysql_query($query_statement, $db_conn);
		$row = mysql_fetch_array($query);
		return $row['id'];
	}
	
	function get_discipline_by_name($discipline, $db_conn){
		$query_statement = select_query('disciplines', get_list(array("name='" . $discipline ."'"), " AND "));
		$query = mysql_query($query_statement, $db_conn);
		$row = mysql_fetch_array($query);
		return $row['id'];
	}
	
	function get_deliverable_by_name($deliverable, $db_conn){
		$query_statement = select_query('deliverables', get_list(array("name='" . $deliverable ."'"), " AND "));
		$query = mysql_query($query_statement, $db_conn);
		$row = mysql_fetch_array($query);
		return $row['id'];
	}
	
	function get_keyword_by_name($keyword, $db_conn){
		$query_statement = select_query('keywords', get_list(array("name='" . $keyword ."'"), " AND "));
		$query = mysql_query($query_statement, $db_conn);
		$row = mysql_fetch_array($query);
		return $row['id'];
	}
	
	function get_year_by_value($year, $db_conn){
		$query_statement = select_query('years', get_list(array("value='" . $year ."'"), " AND "));
		$query = mysql_query($query_statement, $db_conn);
		$row = mysql_fetch_array($query);
		return $row['id'];
	}

	function encode_dragos($str){
		$str2 = $str;
		for ($i = 0; $i < strlen($str2); $i++){
			if ($str2[$i] == ' ') $str2[$i] = '%';
			if ($str2[$i] == '/') $str2[$i] = ';';
		}
		return $str2;
	}
	
	function decode_dragos($str){
		$str2 = $str;
		for ($i = 0; $i < strlen($str2); $i++){
			if ($str2[$i] == '%') $str2[$i] = ' ';
			if ($str2[$i] == ';') $str2[$i] = '/';
		}
		return $str2;
	}

	function decode_cat_name($str){
		$str2 = $str;
		for ($i = 0; $i < strlen($str2); $i++)
			if ($str2[$i] == '-') $str2[$i] = ' ';
		return $str2;
	}
?>
