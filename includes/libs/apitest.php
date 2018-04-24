<?
error_reporting(true);
ini_set('max_execution_time', '0');
ini_set('max_input_time', '-1');
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/inc/db/connection.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/system/bricks/adminFunctions.php");
//$storeKey = "https://d40d09bd137b3f5cce8e0bb7ef9fc883:69934a6ff9bb504ea711da74b7ebbfed@shopadjust.myshopify.com";
$storeKey = "https://7bfff0cd775a3252fd7e666b3c8c75c0:7a3c839287613bff5c571dd9d9bd3386@heino-cykler.myshopify.com";

/* function getProductsByPage($page = 1){
	global $storeKey ;
	$ch = curl_init($storeKey."/admin/products.json?limit=250&page=".$page);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json')
	);
	$products = curl_exec ($ch);
	curl_close ($ch); 
	$products = json_decode($products,true);
	
	if(is_array($products)){
		foreach($products["products"] as $product){
			$products[$product["handle"]] = $product;
		}
		unset($products["products"]);
	}
	return $products;
}

function countProducts(){
	global $storeKey ;
	$ch = curl_init($storeKey."/admin/products/count.json");
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json')
	);
	$response = curl_exec ($ch);
	curl_close ($ch); 
	$response = json_decode($response,true);
	$body = json_decode($response,true);
	return $body;
}


function tag_update($product){
	global $customerId , $storeKey;
	//$storeKey = getAPIStore($customerId);
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
} */

$allproducts = getBrickData("scraper_product",false,"*",false,false,$connection);
if(sizeof($allproducts) && is_array($allproducts)){
	foreach($allproducts as $index => $allproduct){
		$productDb[$allproduct["onlineId"]] = $allproduct["images"];
	}
}

$out = array();
$ch = curl_init($storeKey."/admin/products/count.json");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec ($ch);
$body = json_decode($response,true); 
$count = $body["count"];
$pagination = ceil($count / 250);
$_SESSION["download"] = array();
for($ii=1;$ii<=$pagination;$ii++){
	$ch = curl_init($storeKey."/admin/products.json?limit=250&page=$ii");
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec ($ch);
	$body = json_decode($response,true);
	foreach($body["products"] as $product){
		// $out[] = tag_update($product);
		if(empty($product["images"])){
			if(isset($productDb[$product["id"]])){
				if(json_decode($productDb[$product["id"]]) == true){
					$_SESSION["download"][] = array(
						"id" => $product["id"],
						"images" => implode(",",json_decode($productDb[$product["id"]]))
					);
					
				}
				else{
					$_SESSION["download"][] = array(
						"id" => $product["id"],
						"images" => $productDb[$product["id"]]
					);
				}
			}
			else{
				$_SESSION["download"][] = array(
					"id" => $product["id"],
					"images" => ""
				);
			}
		}
	}
	curl_close ($ch);
} 

print "<pre>";
print_r($_SESSION["download"]);
print "</pre>";

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

if($_GET["download"]){
	$file = array_to_csv($_SESSION["download"] , "missingimages.csv");
	print $file;
	exit(); 
}
