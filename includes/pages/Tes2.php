<?
error_reporting(false);
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/inc/db/connection.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/system/bricks/adminFunctions.php");
include_once $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Scraper/import/helper/data.php";
include_once $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Shopify/Helper/Data.php";
include_once $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Scraper/import/helper/string.php";
include_once $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Shopify/Class/Tree.php";

$params = array(
	"title" => "Burton Custom Freestyle 151",
    "body_html" => "<strong>Good snowboard!</strong>",
    "vendor" => "Burton",
    "product_type"=> "Snowboard",
    "tags" => "Barnes",
	"images" => array(
		array(
			"src" => "https://image.heino-cykler.dk//media/catalog/product/cache/2/image/9df78eab33525d08d6e5fb8d27136e95/Webshop/Energi/High5ny/High5-energigel-energygel-citrussmag.jpg"
		),
		array(
			"src" => "https://image.heino-cykler.dk//media/catalog/product/cache/2/image/9df78eab33525d08d6e5fb8d27136e95/Webshop/Energi/High5ny/High5-energigel-energygel-banansmag.jpg"
		)
	)
);


$datajson = json_encode(array('product' => $params));
$ch = curl_init($storeKey."/admin/products/231408467977.json");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch, CURLOPT_POSTFIELDS,$datajson );
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	'Content-Type: application/json',
	'Content-Length: ' . strlen($datajson))
);
$response = curl_exec ($ch);
print "<pre>";
print_r($params);
print_r(json_decode($response,true));