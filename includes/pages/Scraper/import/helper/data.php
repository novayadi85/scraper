<?php 
session_start();
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/inc/db/connection.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/system/bricks/adminFunctions.php");
//$storeKey = 'https://d5d7c79520eb2a4277e032517ffaa266:1852d0f701efccde2ba91e6ce3da6a01@onlyforapptest.myshopify.com';
$storeKey = "";

if(isset($_SESSION["cId"])){
	$storeKey = getAPIStore($_SESSION["cId"]);
}

function getAPIStore($cId){
	global $connection;
	$searchobject = false;
	$customer = getBrickData("customer",$cId,"*",false,$searchobject,$connection);
	if(sizeof($customer)){
		list($customer) = $customer;
		$apikey = $customer["api_key"];
		$api_password = $customer["api_password"];
		$storeUrl = $customer["shop"];
		$parseUrl = parse_url($storeUrl);
		$storeUrl = $parseUrl["host"];
		$storeKey = "https://$apikey:$api_password@$storeUrl";
		return $storeKey;
	}
	else{
		return false;
	}	
}

function updateMetafieldCollection($params,$id){
	global $storeKey;
	/**
	POST /admin/products/#{id}/metafields.json
	{
	  "metafield": {
		"namespace": "inventory",
		"key": "warehouse",
		"value": 25,
		"value_type": "integer"
	  }
	}
	**/
	if(is_numeric($id)){
		$datajson = json_encode($params);
		$ch = curl_init($storeKey."/admin/collections/".$id."/metafields.json");
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS,$datajson );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($datajson))
		);
		$response = curl_exec ($ch);
		curl_close ($ch);
		return $response;
	}
	
	return false;
	
	
}

function updateMetafieldProduct($params,$id){
	global $storeKey;
	/**
	POST /admin/products/#{id}/metafields.json
	{
	  "metafield": {
		"namespace": "inventory",
		"key": "warehouse",
		"value": 25,
		"value_type": "integer"
	  }
	}
	**/
	if(is_numeric($id)){
		$datajson = json_encode($params);
		$ch = curl_init($storeKey."/admin/products/".$id."/metafields.json");
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS,$datajson );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($datajson))
		);
		$response = curl_exec ($ch);
		curl_close ($ch);
		return $response;
	}
	
	return false;
	
	
}

function getMetafield($key,$type = "product"){
	global $storeKey;
	$params = array('metafield' => 
		array(
			'namespace' => "Extra", 
			'key' => $key,
			'value' => 0,
			'value_type' => "string",
			'owner_resource' => $type
		)
	);	
	$datajson = json_encode($params);
	$ch = curl_init($storeKey."/admin/metafields.json");
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

	curl_setopt($ch, CURLOPT_POSTFIELDS,$datajson );
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		'Content-Length: ' . strlen($datajson))
	);
	$response = curl_exec ($ch);
	curl_close ($ch);
	return $response;
}

function getProducts(){
	global $storeKey ;
	$ch = curl_init($storeKey."/admin/products.json?limit=250&fields=id,title,handle,tags");
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

function getPages(){
	global $storeKey ;
	$ch = curl_init($storeKey."/admin/pages.json?fields=id,title,handle,tags");
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json')
	);
	$pages = curl_exec ($ch);
	curl_close ($ch); 
	$pages = json_decode($pages,true);
	
	if(is_array($pages)){
		foreach($pages["pages"] as $page){
			$pages[$page["handle"]] = $page;
		}
		unset($pages["pages"]);
	}
	return $pages;
}

function getCollections($id = false){
	global $storeKey;
	if($id){
		$ch = curl_init($storeKey."/admin/smart_collections/".$id.".json");
	}
	else{
		$ch = curl_init($storeKey."/admin/smart_collections.json?limit=250");
	}
	
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json')
	);
	$collections = curl_exec ($ch);
	
	curl_close ($ch); 
	$collections = json_decode($collections,true);
	if($id){
		$collections["smart_collections"]  = array($collections["smart_collection"]);
	}

	if(is_array($collections)){
		foreach($collections["smart_collections"] as $collection){
			$collections[$collection["id"]] = $collection;
		}
		unset($collections["smart_collections"]);
	}
	
	
	return $collections; 
}

function delete_collection($id){
	global $storeKey;
	$ch = curl_init($storeKey."/admin/smart_collections/".$id.".json");
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json')
	);
	$response = curl_exec ($ch);
	curl_close ($ch);
	return $response;
}

function saveCSV($request,$parent = false){
	global $connection;
	$fields["name"] = $request["title"];
	$fields["path"] = $request["path"];
	$fields["status"] = "true";
	$id = saveBrickData("csvfile",$fields,$parent,false,$connection);
	return $id;
}

function updateProductData($request,$parent = false){
	global $connection;
	if(isset($request["product"])){
		
		$searchobject = false;
		$searchobject[] = array(
			"fieldname" => "product_id",
			"searchtype" => "=",
			"value" => $request["product"]["id"]
		);
		
		$data = getBrickData("product",false,"*",false,$searchobject,$connection);
		$isRefanCreated = false;
		$fields["product_id"] = $request["product"]["id"];
		$fields["name"] = $request["product"]["title"];
		$fields["handle"] = $request["product"]["handle"];
		$fields["url"] = "/products/".$request["product"]["handle"];
		$fields["status"] = "true";
		$updates["statusApi"] = "uploaded";
		$updates["onlineId"] = $request["product"]["id"];
		
		if(sizeof($data) > 0 AND is_array($data)){
			list($data) = $data;
			$id = saveBrickData("product",$fields,$parent,$data["id"],$connection);
		}
		else{
			$id = saveBrickData("product",$fields,$parent,false,$connection);
		}
		
		return $id;
	}
	return false;
}

function create_page($request,$id = false){
	global $storeKey;
	if(is_array($request)){
		$params = array(
			"page" => $request
		);
	}
	
	$datajson = json_encode($params);
	
	if($id){
		$ch = curl_init($storeKey."/admin/pages/".$id.".json");
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
	}
	else{
		$ch = curl_init($storeKey."/admin/pages.json");
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	}

	curl_setopt($ch, CURLOPT_POSTFIELDS,$datajson );
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		'Content-Length: ' . strlen($datajson))
	);
	$response = curl_exec ($ch);
	$body = json_decode($response,true);
	return $body;
}

function create_collection($var = false , $id = false , $other = false){
	global $storeKey;
	$exists = array();
	if(is_numeric($id)){
		//check exist
		$colls = getCollections($id);
		if(isset($colls[$id])){
			if(is_array($colls[$id]["rules"])){
				foreach($colls[$id]["rules"] as $rule){
					$exists[] = $rule["condition"];
				}
			}
		}
	}
	
	
	$_conditions = array();
	$conditions = explode("," ,$var["condition"]);
	$conditions = array_filter($conditions);
	if(is_array($exists) && sizeof($exists)){
		$array1 = array_map("trim",$exists);
		$array2 = array_map("trim",$conditions);
		$conditions = array_unique(array_merge_recursive($array1,$array2),SORT_REGULAR);
	}
	
	if(is_array($conditions) && sizeof($conditions)){
		foreach($conditions as $condition){
			$condition = trim($condition);
			$_conditions[] = array(
				"column" => $var["column"],
				"relation" => $var["relation"],
				"condition" => $condition
			);	
		}
	}
	if(!empty($_conditions)){
		$params = array(
			"smart_collection" => array(
				"title" => $var["title"],
				"rules" => $_conditions
			)
		);
	}
	else{
		$params = array(
			"smart_collection" => array(
				"title" => $var["title"],
				"rules" => 
					array(
						array(
							"column" => "title",
							"relation" => "equals",
							"condition" => $var["title"]
						)
					)
			)
		);
	}
	
	if(isset($other["body_html"])){
		$params["smart_collection"]["body_html"] = ($other["body_html"]);
	}
	
	$datajson = json_encode($params);
	if(is_numeric($id)){
		$ch = curl_init($storeKey."/admin/smart_collections/".$id.".json");
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
	}
	else{
		$ch = curl_init($storeKey."/admin/smart_collections.json");
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	}

	curl_setopt($ch, CURLOPT_POSTFIELDS,$datajson );
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		'Content-Length: ' . strlen($datajson))
	);
	$response = curl_exec ($ch);
	$body = json_decode($response,true);
		
	
	if(isset($body["smart_collection"]["id"]) || is_numeric($id)){
		if(is_numeric($body["smart_collection"]["id"]) || is_numeric($id)){
			if(isset($other["meta"]) && !empty($other["meta"])){

				if(!is_numeric($id) && is_numeric($id = $body["smart_collection"]["id"])){
					$id = $body["smart_collection"]["id"];
				}
				
				foreach($other["meta"] as $key => $meta){
					$metafields['metafield'] = array(
						'namespace' => "Extra", 
						'key' => $key,
						'value' => utf8_decode($meta),
						'value_type' => 'string'
					);
					$res = updateMetafieldCollection($metafields,$id);
					$resbody = json_decode($response,true);
					if($resbody["errors"]){
						$metafields['metafield']['value'] = $meta;
						$res = updateMetafieldCollection($metafields,$id);
						
					}
					
				}
				
				
				//update 
				
			}
			
		}
		
	}
	curl_close ($ch);
	return $body;
}

function ExploreProduct($products){
	if(is_array($products)){
		foreach($products as $key => $product){
			$products[$product["handle"]] = $product["onlineId"];
			$products[$product["title"]] = $product["onlineId"];
		}
		unset($products[$key]);
	}
	return $products;
}

function matchProduct(){
	global $storeKey;
	$ch = curl_init($storeKey."/admin/products.json?limit=1000&fields=id,title,handle");
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
			$products[$product["handle"]] = $product["id"];
			$products[$product["title"]] = $product["id"];
		}
		unset($products["products"]);
	}
	
	return $products;
}


function testShop($storeData = false){
	global $storeKey;
	if($storeData){
		$storeKey = $storeData;
	}
	$ch = curl_init($storeKey."/admin/shop.json");
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json')
	);
	$shop = curl_exec ($ch);
	curl_close ($ch); 
	$shop = json_decode($shop,true);
	if(empty($shop) || !isset($shop["shop"])){
		return false;
	}
	
	return $shop;
}


/**
* @return array
* @param array $src
* @param array $in
* @param int|string $pos
*/
function array_push_before($src,$in,$pos){
    if(is_int($pos)) $R=array_merge(array_slice($src,0,$pos), $in, array_slice($src,$pos));
    else{
        foreach($src as $k=>$v){
            if($k==$pos)$R=array_merge($R,$in);
            $R[$k]=$v;
        }
    }return $R;
}

/**
* @return array
* @param array $src
* @param array $in
* @param int|string $pos
*/
function array_push_after($src,$in,$pos){
    if(is_int($pos)) $R=array_merge(array_slice($src,0,$pos+1), $in, array_slice($src,$pos+1));
    else{
        foreach($src as $k=>$v){
            $R[$k]=$v;
            if($k==$pos)$R=array_merge($R,$in);
        }
    }return $R;
}