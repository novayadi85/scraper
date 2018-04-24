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
$productsToScrapes = getBrickData("productsToScrape",false,'*',false,$searchobject,$connection);			
$allproducts = getBrickData("scraper_product",false,"*",false,$searchobject,$connection);



function tag_update($product){
	global $searchobject, $connection , $customer , $customerId , $productLists , $productsToScrapes ,$allproducts;
	$storeKey = getAPIStore($customerId);
	$tags = $product["tags"];
	$tags = explode(",", $tags);
	$tags = array_filter($tags);
	$response = array();
	if(sizeof($tags)){
		
		
		
		if(!empty($product["vendor"]) && $product["vendor"] != "-" && $product["vendor"] != "heino-cykler"){
			if(!in_array("Mærke_".$product["vendor"] , $tags))
			$tags[] = "Mærke_".$product["vendor"];
		}
		
		foreach($tags as $t => $tag){
			if (!stristr($tag, "_")) {
				//if(!in_array("Collection_".$tag , $tags))
				//$tags[] = "Collection_".$tag;
			}
			
			if($tag == "Mærke_heino-cykler"){
				unset($tags[$t]);
			}
			
			if (stristr($tag, "Collection_")) {
				unset($tags[$t]);
			}
		}
		
		if(is_array($product["variants"]) && sizeof($product["variants"])){
			if(is_array($product["options"]) && sizeof($product["options"])){
				foreach($product["options"] as $option){
					if(isset($option["name"])){
						$label = $option["name"];
						if(isset($option["values"]) && sizeof($option["values"])){
							foreach($option["values"] as $value){
								if(!in_array($label . "_" . $value , $tags))
								$tags[] = $label . "_" . $value;
							}
						}
					}
				}
			}
		}
		
		
		if(sizeof($tags) && is_array($tags)){
			$tags = (array_map("trim",$tags));
			$tags = implode(",",$tags);
			$params = array(
				"tags" =>  (string) $tags,
				"handle" => $product["handle"],
				"title" => $product["title"],
				"id" => $product["id"]
			);
		
			
			$ch = curl_init($storeKey."/admin/products/".$product["id"].".json");
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
			$datajson = json_encode(array('product' => $params));
			curl_setopt($ch, CURLOPT_POSTFIELDS,$datajson );
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen($datajson))
			);
			$response = curl_exec ($ch);
			$response = json_decode($response , true);
			
		}
		
	}
	
	return $response;
}

function send2Shopify($id , $type = "product"){
	global $searchobject, $connection , $customer , $customerId , $productLists , $productsToScrapes ,$allproducts;
	file_put_contents($_SERVER["DOCUMENT_ROOT"].'/system/logs/upload-products.log', $id . " , uploaded , ". date("Y-m-d H:i:s"). '\r\n' . PHP_EOL, FILE_APPEND);
	$Helper_String = new Helper_String;
	$storeKey = getAPIStore($customerId);
	/**
	mode:send2Shopify
	type:product
	id:GOUH-YLPY-2796-KXMP-ORKR-4744-UD
	**/
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
		if($type == "product" || $type == "products"){
			$urls = array();
			$collections = array();
			//load tags
			if(sizeof($productLists)){
				foreach($productLists as $x => $productList){
					$results = json_decode($productList["productUrls"], true);
					$title = $productList["h1"];
					if(sizeof($results)){
						foreach($results as $result){
							if(isset($collections[$title])){
								$array1 = array_map("trim",$collections[$title]);
								$array2 = array_map("trim",$result);
								$collections[$title] = array_unique(array_merge_recursive($array1,$array2),SORT_REGULAR);
							}
							else{
								$collections[$title] = $result;
							}
							
							$urls = array_unique(array_merge_recursive($urls,$result),SORT_REGULAR);

						}
					}
				}
			}
			
			if(is_array($id)){
				if(sizeof($allproducts) && is_array($allproducts)){
					foreach($allproducts as $index => $allproduct){
						if(!in_array($allproduct["id"],$id)){
							unset($allproducts[$index]);
						}
					}
				}
			}
			else{
				$allproducts = getBrickData("scraper_product",$id,"*",false,false,$connection);
				//get product 
			}

			if(sizeof($allproducts)){
				foreach($allproducts as $product){
					if(is_numeric($product["onlineId"])){
						$ch = curl_init($storeKey."/admin/products/".$product["onlineId"].".json");
						curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						$response = curl_exec ($ch);
						$body = json_decode($response,true);
						if(isset($body["product"])){
							$out["response"] = tag_update($body["product"]);
							if(isset($body["product"]["handle"])){
								$updates = array();
								$updates["statusApi"] = "uploaded";
								$updates["onlineId"] = $body["product"]["id"];
								$updates["handle"] = $body["product"]["handle"];
								$updates["uploadProductToShopify"] = "false";
								saveBrickData("scraper_product",$updates,false,$product["id"],$connection);
							}
						}
					}
				}
				
			}
			
		}

	}
	else{
		$out["error"] = true;
	}
	return  json_encode($out);
	die();
}

$searchobject = false;
$searchobject[] = array(
	"fieldname" => "uploadProductToShopify",
	"searchtype" => "=",
	"value" => "true"
); 
$quoteProducts = getBrickData("scraper_product",false,"*",false,$searchobject,$connection);
$quotes = array_slice($quoteProducts, 0, 50);
$response = array();
/* if(sizeof($quotes)){
	foreach($quotes as $quote){
		$response[$quote["id"]] = send2Shopify($quote["id"]);
	}
} */ 

// $response = send2Shopify("RQHE-XVNC-4921-DRGK-ODHR-4362-OR");
$response = send2Shopify("PEUQ-UVXF-2638-BTFZ-OPGZ-8927-IU");
$response = send2Shopify("UOFM-ZKID-8226-LFCG-SIYB-3183-IE");
print "<pre>";
print_r($response);