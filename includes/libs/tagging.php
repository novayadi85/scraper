<?
error_reporting(true);
$storeKey = "https://d40d09bd137b3f5cce8e0bb7ef9fc883:69934a6ff9bb504ea711da74b7ebbfed@shopadjust.myshopify.com";

function getProductsByPage($page = 1){
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

$ch = curl_init($storeKey."/admin/products/count.json");
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
		$tags = $product["tags"];
		$tags = explode(",", $tags);
		$tags = array_filter($tags);
		
		if(sizeof($tags)){
			if(!empty($product["vendor"])){
				if(!in_array("Mærke_".$product["vendor"] , $tags))
				$tags[] = "Mærke_".$product["vendor"];
			}
			
			foreach($tags as $tag){
				if (!stristr($tag, "_")) {
					if(!in_array("Collection_".$tag , $tags))
					$tags[] = "Collection_".$tag;
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
				
				print "<pre>";
				print_r($response);
				print "</pre>";
				
			}
			
		}
		
	}
	curl_close ($ch);
} 

