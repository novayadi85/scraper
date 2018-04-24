<?
session_start();
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/inc/db/connection.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/system/bricks/adminFunctions.php");
include_once $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Scraper/import/helper/data.php";


function array_to_csv($array, $download = "") {
	ob_start();
	$f = fopen('php://output', 'w') or die("Can't open php://output");
	$counter = count($array);
	$headerDisplayed = false;
	for ($i = 0; $i < $counter; $i++) { 
		if ( !$headerDisplayed ) {
			fputcsv($f, array_keys(array_map(function($value) {  return $value;  }, $array[$i])),",", "\"" );
			$headerDisplayed = true;
		} 
		
		if(!fputcsv($f, array_map(function($value) {  return $value;  }, $array[$i]), ",", "\"")) {
			die("Can't write line $i: ".print_r($array[$i], true));
		}
	}
	fclose($f) or die("Can't close php://output");
	$str = str_replace('"""', '"', ob_get_contents());
	ob_end_clean();
	if(strlen($download) > 0) {
		header('Content-Type: application/csv');
		header('Content-Disposition: attachment; filename="'.$download.'"');
	   echo $str; 
	} else {
		return $str;
	}
}
$array2csv = $_SESSION["redirects"];
$file = array_to_csv($array2csv , "redirect.csv");
print $file;
exit(); 
	
	