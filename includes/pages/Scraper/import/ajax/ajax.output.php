<?php
header('Access-Control-Allow-Origin: *');
error_reporting(true);
set_time_limit(0);
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/inc/db/connection.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/system/bricks/adminFunctions.php");
include_once $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Shopify/Class/Tree.php";

function childs($items, $parent = 0){
	$out = array();
	foreach($items as $item){
		$urls = array();
		if($item["parentid"] == $parent){
			$urls = childs($items,$item["id"]);
			if(is_array($urls)){
				$array1 = array_map("trim",$urls);
				$array2 = array_map("trim",$out);
				$out = array_unique(array_merge_recursive($array1,$array2),SORT_REGULAR);
			}
			
			$surls = json_decode($item["productUrls"],true);
			if(is_array($surls) && sizeof($surls)){
				foreach($surls as $url){
					if(is_array($url)){
						$array1 = array_map("trim",$url);
						$array2 = array_map("trim",$out);
						$out = array_unique(array_merge_recursive($array1,$array2),SORT_REGULAR);
					}
					else{
						$out[] = $url;
					}
				}
			}
			
		}
	}
	return $out;
}	

	$urls = $_REQUEST["urls"];
	
	if(!empty($_REQUEST["list"]) && $_REQUEST["list"] && $_REQUEST["type"] == "shopify_product"){
		if($_REQUEST["child"] == "true"){
			$lists = getBrickData("scraper_productlist",false,"*",false,false,$connection);
			$urls = childs($lists,$_REQUEST["list"]);
			if(sizeof($urls) <= 0){
				foreach($lists as $item){
					if($item["id"] == $_REQUEST["list"]){
						$surls = json_decode($item["productUrls"],true);
						if(is_array($surls) && sizeof($surls)){
							foreach($surls as $url){
								if(is_array($url)){
									$array1 = array_map("trim",$url);
									$array2 = array_map("trim",$urls);
									$urls = array_unique(array_merge_recursive($array1,$array2),SORT_REGULAR);
								}
								else{
									$urls[] = $url;
								}
							}
						}
					}
				}
			}
		}
		else{
			list($lists) = getBrickData("scraper_productlist",$_REQUEST["list"],"*",false,false,$connection);
			if(!empty($lists["productUrls"])){
				$surls = json_decode($lists["productUrls"],true);
				$urls = array();
				foreach($surls as $url){
					if(is_array($url)){
						$array1 = array_map("trim",$url);
						$array2 = array_map("trim",$urls);
						$urls = array_unique(array_merge_recursive($array1,$array2),SORT_REGULAR);
					}
					else{
						$urls[] = $url;
					}
				}
			}
		}	
	}
	
	
	$data = array();
	$paginations = array();
	if(isset($_POST["pagination"]["params"]) && !empty($_POST["pagination"]["params"])){
		$param = $_POST["pagination"]["params"];
		$min = $_POST["pagination"]["min"];
		$max = $_POST["pagination"]["max"];
		$extension = $_POST["pagination"]["extension"];
		foreach ($urls as $key => $url) {
			$i = "";
			$urls[$key] = $url;
			$path = parse_url($url, PHP_URL_PATH);
			$url = str_replace($path,"",$url);
			if(isset($extension)  && !empty($extension)){
				$path = str_replace(".".$extension,$i,$path);
			}
			
			for($i = $min; $i <= $max; $i++ ){
				$page = str_replace("%%number%%",$i,$param);
				$page = str_replace("%%path%%",$path,$page);
				$urls[] = $url . $page ;
				$paginations[$key][] =  $url . $page;				
			}
			
		}
	}

	
	//print_r($urls); exit();

	// CURLS multi-handle
	$mh = curl_multi_init();
	
	// Hold CURLS requests for each file
	$requests = array();
	
	$options = array(
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_AUTOREFERER    => true, 
		CURLOPT_USERAGENT      => "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:56.0) Gecko/20100101 Firefox/56.0",
		CURLOPT_HEADER         => false,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_RETURNTRANSFER => true
	);
	
	/* 
	$xurls = array();
	foreach ($urls as $key => $url) {
		$xurls[$key] = "http://82.165.22.219/phantomjs/index.php?url=".$url;
	} 
	*/
	
	
	
	foreach ($urls as $key => $url) {
		
		// Add initialized CURL object to array
		$requests[$key] = curl_init($url);
	
		// Set CURL object options
		curl_setopt_array($requests[$key], $options);
		
		// Add CURL object to multi-handle
		curl_multi_add_handle($mh, $requests[$key]);
	}
	
	// Do while all request have been completed
	do {
	   curl_multi_exec($mh, $active);
	} while ($active > 0);
	
	// Collect all data here and clean up
	foreach ($requests as $key => $request) {
	
		$data[$key] = curl_multi_getcontent($request);
		curl_multi_remove_handle($mh, $request);
		curl_close($request);
	}
	
	curl_multi_close($mh);
	
	
	// Parsing relative url
	foreach ($urls as $key => $url) {
		$parseUrl = parse_url($url);
	
		if (!isset($parseUrl["scheme"])) {
			$parseUrl = parse_url("https://".$url);
		}
		
		$host = $parseUrl["scheme"]."://".$parseUrl["host"];
		$outHtml = str_replace(array("src=\"/","href=\"/"),array("src=\"".$host."/","href=\"".$host."/"), $data[$key]);
		//$outHtml = utf8_encode($outHtml);
		$data[$key] = $outHtml;
	}
	$out["data"] =  $data;
	$out["urls"] = $urls;
	$out["paginations"] = $paginations;
	$print = json_encode($out);
	
	if(empty($print)){
		$out["data"] =  array_map("utf8_encode", $data);
		$print = json_encode($out);
	}
	
	print $print ;
?>