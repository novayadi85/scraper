<?
session_start();
ini_set('max_execution_time', '0');
ini_set('max_input_time', '-1');
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

$request_body = file_get_contents('php://input');
$ajax = json_decode($request_body,true);

if($ajax["action"] == "getCustomers"){
	$out = array();
	$searchobject = false;
	$searchobject[] = array(
		"fieldname" => "deleted",
		"searchtype" => "=",
		"value" => "false"
	);
	$lists = getBrickData("customer",false,"*",false,$searchobject,$connection);
	$out["list"] = $lists;
	print json_encode($out);
}

if($ajax["action"] == "getLists"){
	$products = array();
	$customer = $ajax["params"];
	/* $productsToScrapes = getBrickData("scraper_product",false,'*',$customer,false,$connection);			
	foreach($productsToScrapes as $k => $productsToScrape){
		$productsToScrapes[$productsToScrape["handle"]] = $productsToScrape["href"];
		unset($productsToScrapes[$k]);
		if(!empty($productsToScrape["handle"])){
			$products[] = array(
				"type" => "products",
				"path" => $productsToScrape["href"],
				"target" => "/products/".$productsToScrape["handle"]
			);
		}
	} */

	$listToScrapes = getBrickData("scraper_productlist",false,'*',false,false,$connection);			
	foreach($listToScrapes as $k => $listToScrape){
		$listToScrapes[$listToScrape["handle"]] = $listToScrape["href"];
		unset($listToScrapes[$k]);
		if(!empty($listToScrape["handle"])){
			$products[] = array(
				"type" => "collections",
				"path" => $listToScrape["href"],
				"target" => "/collections/".$listToScrape["handle"]
			);
		}
		
	}

	/* $pageToScrapes = getBrickData("scraper_page",false,'*',$customer,false,$connection);			
	foreach($pageToScrapes as $k => $pageToScrape){
		if(!empty($pageToScrape["handle"])){
			$pageToScrapes[$pageToScrape["handle"]] = $pageToScrape["href"];
			$products[] = array(
				"type" => "pages",
				"path" => $pageToScrape["href"],
				"target" => "/pages/".$pageToScrape["handle"]
			);
		}
		unset($pageToScrapes[$k]);
			
	} */
	
	$_SESSION["redirects"] = $products;
	echo json_encode(array("list" => $products));
}

if($ajax["action"] == "send"){ 
	$out = array();
	$out["error"] = true;
	$redirects = $ajax["params"];
	$customer = $ajax["customer"];
	$_SESSION["cId"] = $customer;
	$histories = getBrickData("redirectUrls",false,'*',$customer,false,$connection);			
	if(sizeof($histories)){
		foreach($histories as $k => $history){
			if(!empty($history["path"])){
				$histories[$history["path"]] = $history;
			}
			unset($histories[$k]);
		}
	}
	
	if(sizeof($redirects)){
		foreach($redirects as $redirect){
			
			///unset($redirect["type"]);
			$id = false;
			$external_id = "";
			if(isset($histories[$redirect["path"]])){
				$external_id = $histories[$redirect["path"]]["external_id"];
				$id = $histories[$redirect["path"]]["id"];
				/* if($external_id){
					$ch = curl_init($storeKey."/admin/redirects/".$external_id.".json");
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
				} */
				if($external_id == "false"){
					$external_id = "";
				}
			}
			else{
				/* $ch = curl_init($storeKey."/admin/redirects.json");
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); */
				
			}
			
			
			$insert = false;
			$insert["path"] = $redirect["path"];
			$insert["target"] = $redirect["target"];
			$insert["external_id"] = $external_id;
			$ids = saveBrickData("redirectUrls",$insert,$customer,$id,$connection);
			$out["saved"][$ids] = $insert;
			/**
			$params = array("redirect" => $redirect);
			$datajson = json_encode($params);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$datajson );
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen($datajson))
			); 
			$response = curl_exec ($ch);
			$body = json_decode($response,true);
			curl_close ($ch); 
			$out["response"][] = $body;
			if(isset($body["redirect"])){
				
			} */
			
			$out["error"] = false;
			
		}
	}
	
	$_SESSION["redirects"] = $redirects;
	echo json_encode($out);
	exit(); 
}
/* $ch = curl_init($storeKey."/admin/products/count.json");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec ($ch);
$body = json_decode($response,true);
$count = $body["count"];
$pagination = ceil($count / 250);
for($ii=1;$ii<=$pagination;$ii++){
	$ch = curl_init($storeKey."/admin/products.json?limit=250&page=$ii");
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec ($ch);
	$body = json_decode($response,true);
	foreach($body["products"] as $product){
		if(isset($productsToScrapes[$product["handle"]])){
			$products[] = array(
				"path" => $productsToScrapes[$product["handle"]],
				"target" => "/products/".$product["handle"]
			);
	
		}
	}
	curl_close ($ch);
} */