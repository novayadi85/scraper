<?php 

function getCollections($id = false){
	if($id){
		$ch = curl_init("https://d5d7c79520eb2a4277e032517ffaa266:1852d0f701efccde2ba91e6ce3da6a01@onlyforapptest.myshopify.com/admin/smart_collections/".$id.".json");
	}
	else{
		$ch = curl_init("https://d5d7c79520eb2a4277e032517ffaa266:1852d0f701efccde2ba91e6ce3da6a01@onlyforapptest.myshopify.com/admin/smart_collections.json");
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
			if(sizeof($collection["rules"]) && is_array($collection["rules"])){
				$tags = array();
				foreach($collection["rules"] as $rule){
					if($rule["column"] == "tag"){
						$tags[] = $rule["condition"];
					}
				}
				$collections[$collection["id"]]["tags"] = implode(",",$tags);
			}
		}
		unset($collections["smart_collections"]);
	}
	
	
	return $collections; 
}

function getProducts(){
	$ch = curl_init("https://d5d7c79520eb2a4277e032517ffaa266:1852d0f701efccde2ba91e6ce3da6a01@onlyforapptest.myshopify.com/admin/products.json?fields=id,title,handle,tags");
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

function updateProduct($id,$params){
	$ch = curl_init("https://d5d7c79520eb2a4277e032517ffaa266:1852d0f701efccde2ba91e6ce3da6a01@onlyforapptest.myshopify.com/admin/products/".$id.".json");
	$datajson = json_encode(array('product' => $params));				
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$datajson );				
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		'Content-Length: ' . strlen($datajson))
	);
	$response = curl_exec ($ch);
	
	curl_close ($ch); 
	return $response;
	
}