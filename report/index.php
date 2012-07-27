<!-- 
	Welcome to crash report system of Tbsfactory
 -->
<?php

function parse_date($date)
{
    if (preg_match('/([0-9]{2,4})-([0-9][0-9])-([0-9][0-9])T([0-9][0-9]):([0-9][0-9]):([0-9][0-9])(\.[0-9][0-9][0-9])?(\+|-)([0-9][0-9]):([0-9][0-9])/i', $date, $matches))
    {
        return strtotime("$matches[1]-$matches[2]-$matches[3] $matches[4]:$matches[5]:$matches[6] $matches[8]$matches[9]$matches[10]");
    }
}

function mysql_insert($table, $inserts) 
{
    $values = array_map('mysql_real_escape_string', array_values($inserts));
    $keys = array_keys($inserts);
    return mysql_query('INSERT INTO `'.$table.'` (`'.implode('`,`', $keys).'`) VALUES (\''.implode('\',\'', $values).'\')');
}

function saveToDatabase($object)
{
	include ("../source/php/db.php");
	mysql_connect($dbhost,$dbuser,$dbpass);
	mysql_select_db($dbname);

	$result = mysql_insert($table, $object);
	if (!$result) {
    	die('Invalid query: ' . mysql_error());
	}
	mysql_close();
}

//Check if HTTP POST
if(strtolower($_SERVER['REQUEST_METHOD']) == 'post') {       
	foreach($_POST as $key => $value) {
	
		//Check if json key
		if (strtolower($key) == 'json') {
		
			//Get JSON from POST
			$json = json_decode($value, true);

			//Convert time from RFC 3339 to MySQL Timestamp
			date_default_timezone_set('Europe/Berlin'); 
			$json['USER_CRASH_DATE'] = date("Y-m-d H:i:s", parse_date($json['USER_CRASH_DATE']));
			
			//Save report to database
			saveToDatabase($json);
		}
	} 
}  

?>
