<?
error_reporting(true);
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/inc/db/connection.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/system/bricks/adminFunctions.php");
include_once $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Scraper/import/helper/data.php";
include_once $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Shopify/Helper/Data.php";
include_once $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Scraper/import/helper/string.php";
include_once $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Shopify/Class/Tree.php";
$searchobject = false;
$searchobject[] = array(
	"fieldname" => "uploadProductToShopify",
	"searchtype" => "=",
	"value" => "true"
); 
$quoteProducts = getBrickData("scraper_product",false,"*",false,$searchobject,$connection);
print "Product Quotes : " . count($quoteProducts);
$quotes = array_slice($quoteProducts, 0, 50);
$response = array();
if(sizeof($quotes)){
	foreach($quotes as $quote){
		$response[$quote["id"]] = array("id" => $quote["id"]  , "title" =>$quote["h1"]);
	}
} 

$searchobject = false;
$searchobject[] = array(
	"fieldname" => "uploadCollectionToShopify",
	"searchtype" => "=",
	"value" => "true"
); 
$quoteLists = getBrickData("scraper_productlist",false,"*",false,$searchobject,$connection);
$responseList = array();
if(sizeof($quoteLists)){
	foreach($quoteLists as $quote){
		$responseList[$quote["id"]] = array("id" => $quote["id"]  , "title" => utf8_decode($quote["h1"]));
	}
} 

print " / &nbsp;List Quotes : " . count($quoteLists);


print "<pre>";
print_r($response);
print "</pre>";
print "=============";
print "=============";
print "=============";
print "<pre>";
print_r($responseList);
print "</pre>";