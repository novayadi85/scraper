<?php 

function ApiTestShop($storeData = false){
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