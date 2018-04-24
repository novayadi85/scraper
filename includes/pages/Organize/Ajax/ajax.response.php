<?
session_start();
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/inc/db/connection.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/system/bricks/adminFunctions.php");
include_once $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/User/Helper/Data.php";
include_once $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Shopify/Class/Tree.php";
$searchobject = false;
$request_body = file_get_contents('php://input');
$ajax = json_decode($request_body,true);
if($ajax["action"] == "getLists"){
	$out = array();
	$searchobject = false;
	$searchobject[] = array(
		"fieldname" => "deleted",
		"searchtype" => "=",
		"value" => "false"
	);
	$lists = getBrickData("scraper_productlist",false,"*",false,$searchobject,$connection);
	
	$searchobject = false;
	$searchobject[] = array(
		"fieldname" => "deleted",
		"searchtype" => "=",
		"value" => "false"
	); 
	
	$productsToScrapes = getBrickData("productsToScrape",false,'*',false,$searchobject,$connection);
	$products = getBrickData("scraper_product",false,'*',false,$searchobject,$connection);
	
	$productsToScrapeTags = array();
	foreach($productsToScrapes as $b => $productsToScrape){
		if(!empty($productsToScrape["tags"])){
			$productsToScrapeTags[$productsToScrape["parentid"]][] = $productsToScrape["link"];
		}
		$productsToList[$productsToScrape["link"]] = $productsToScrape["parentid"];
	}
	
	$productsListingPending = array();
	$productsListingUploaded = array();
	foreach($products as $a => $product){
		if(isset($productsToList[$product["href"]])){
			if($product["uploadProductToShopify"] == "true"){
				$productsListingPending[$productsToList[$product["href"]]][] =  $product["href"];
			}
			else{
				$productsListingUploaded[$productsToList[$product["href"]]][] =  $product["href"];
			}
		}
	}
	
	
	$ElmTree = new ElmTree;
	$ElmTree->compareList = $productsToScrapeTags;
	$ElmTree->productsListingUploaded = $productsListingUploaded;
	$ElmTree->productsListingPending = $productsListingPending;
	$lists = $ElmTree->buildTree($lists,$_SESSION["cId"]);
/* 	
	
	print_r($ElmTree);
	exit();
	 */
	if(empty($lists)){
		$lists = false;
	}	
	$out["list"] = $lists;
	print json_encode($out);
}

if($ajax["action"] == "save"){
	$out = array();
	$sourceId = $ajax["details"]["sourceId"];
	$destId = $ajax["details"]["destId"];
	
	if(is_null($destId) || empty($destId)){
		$destId = $_SESSION["cId"];
	}

	if(!empty($sourceId)){
		$insert = false;
		$insert["parentid"] = $destId;
		$out["id"] = saveBrickData("scraper_productlist",$insert,$destId,$sourceId,$connection);
		
	}
	else{
		$out["error"] = true;
	}
	print json_encode($out);
}