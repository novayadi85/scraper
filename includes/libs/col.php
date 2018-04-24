<?
error_reporting(true);
ini_set('max_execution_time', '0');
ini_set('max_input_time', '-1');
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/inc/db/connection.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/system/bricks/adminFunctions.php");
include_once $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Scraper/import/helper/data.php";
include_once $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Shopify/Helper/Data.php";
include_once $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Scraper/import/helper/string.php";
include_once $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Shopify/Class/Tree.php";


$searchobject = false;
$customer = false;
$customerId = "OOBH-JFOT-2144-HJFO-DQPO-1432-KL";
$productLists = getBrickData("scraper_productlist",false,"*",$customer,$searchobject,$connection);

function send2Shopify($id , $type = "product"){
	global $searchobject, $connection , $customer , $customerId , $productLists ;
	file_put_contents($_SERVER["DOCUMENT_ROOT"].'/system/logs/upload-lists.log', $id . " , uploaded , ". date("Y-m-d H:i:s"). '\r\n' . PHP_EOL, FILE_APPEND);
	$Helper_String = new Helper_String;
	$storeKey = getAPIStore($customerId);
	$out["error"] = false;
	$out["data"] = array();
	$array = array();
	$table = array();
	$variantsData = variantsData();
	$array["header"] = stdHeaders();
	$server_output = array(
		"updated",
		"created",
		"imported",
		"errors"
	);
	
	if(!empty($id)){
		if($type == "lists" || $type == "list"){
			$searchobject = false;
			if(is_array($id)){
				if(sizeof($productLists) && is_array($productLists)){
					foreach($productLists as $index => $productList){
						if(!in_array($productList["id"],$id)){
							unset($productLists[$index]);
						}
					}
				}
			}
			else{
				$productLists = getBrickData("scraper_productlist",$id,"*",$customer,$searchobject,$connection);
			}
			
			$collections = getCollections();
			if(sizeof($collections) && is_array($collections)){
				foreach($collections as $i => $collection){
					$title = strtolower($collection["title"]);
					$collections[$title] = $collection["id"];
					unset($collections[$i]);
				}
			}
			
			
			if(sizeof($productLists) && is_array($productLists)){
				foreach($productLists as $index => $productList){
					
					$params = array();
					if(empty($productList["h1"])){
						$productList["h1"] = $productList["title"];
					}
					$title = strtolower($productList["h1"]);
					$params["colection_id"] = false;
					if(isset($collections[$title])){
						$params["colection_id"] = $collections[$title];
						if(empty($params["colection_id"])){
							$params["colection_id"] = false;
						}
					}
					getMetafield("shortText","collection");
					
					$others = array();
					$others["body_html"] = utf8_decode($productList["textArea_1"]);
					$others["meta"] = array("shortText" => $productList["meta_field_2"]);
					$params["title"] = convertUtf8($productList["h1"]);
					$params["column"] = "tag";
					$params["relation"] = "equals";
					$params["condition"] = convertUtf8($productList["h1"]);
					$response = create_collection($params,$params["colection_id"],$others);	
					print_r($response);
					$updates = array();
					$updates["statusApi"] = "uploaded";
					$updates["uploadCollectionToShopify"] = "false";
					if(isset($response["smart_collection"]["handle"])){
						$updates["handle"] = $response["smart_collection"]["handle"];
						$server_output["created"][] = $response;
						//print "First";
					}
					else{
						$others["body_html"] = $productList["textArea_1"];
						$response = create_collection($params,$params["colection_id"],$others);	
						print_r($response);
						if(isset($response["smart_collection"]["handle"])){
							$updates["handle"] = $response["smart_collection"]["handle"];
							$server_output["created"][] = $response;
							
						}
						else{
							$server_output["errors"][] = $response;
						}
						
					}
					
					$out["id"][] = saveBrickData("scraper_productlist",$updates,false,$productList["id"],$connection);
					
				}	
			}
			
		}

		$updated = count($server_output["updated"]);
		$created = count($server_output["created"]);
		
		
		$out = array(
			"meta" => $server_output["meta"],
			"updated" => count($server_output["updated"]),
			"created" =>  count($server_output["created"]),
			"imported" => ($updated + $created),
			"errors" => $server_output["errors"]
		);
		
		if(count($server_output["errors"])){
			if(!$msg){
				$msg = "Error..";
			}
			$out["msg"] = $msg;
		}
	}
	else{
		$out["error"] = true;
	}
	return  json_encode($out);
	die();
}
print "<pre>";
$searchobject = false;
/* $searchobject[] = array(
	"fieldname" => "uploadCollectionToShopify",
	"searchtype" => "=",
	"value" => "true"
);  */


include_once $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Shopify/Class/Tree.php";
$quoteProducts = getBrickData("scraper_productlist",false,"*",false,$searchobject,$connection);
$Tree = new ElmTree();
$quoteProducts = $Tree->buildTree($quoteProducts,$customerId);

foreach($quoteProducts as $quoteProduct){
	$data[] = $quoteProduct["id"];
	if(isset($quoteProduct["children"])){
		$new =  $Tree->combineData($quoteProduct["children"],$quoteProduct["id"]);
		$array1 = array_map("trim",$data);
		$array2 = array_map("trim",$new);
		$data = array_unique(array_merge_recursive($array1,$array2),SORT_REGULAR);
	}
}



/* $quotes = array_slice($quoteProducts, 0, 50);
$response = array();
if(sizeof($quotes)){
	foreach($quotes as $quote){
		$response[$quote["id"]] = send2Shopify($quote["id"],"lists");
	}
} 
 */
print count($quoteProducts);
print_r($data);