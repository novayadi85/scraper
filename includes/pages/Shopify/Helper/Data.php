<?php 
function convertUtf8($str){
	$str = str_replace("Ã¦","æ",$str);
	$str = str_replace("Ã¥","å",$str);
	$str = str_replace("Ã¸","ø",$str);
	$str = str_replace("Ã©¸","é",$str);
	return $str;
}
function find_tags($collection , $query, $ex = ""){
	$collection = array_filter($collection);
	if(is_array($collection) && sizeof($collection)){
		$tags = array();
		foreach($collection as $key => $val){
			if(is_array($collection[$key]) && in_array($query,$collection[$key])){
				$key = str_replace(" - REFANSHOP.DK","",$key);
				$tags[] = $key;
			}
		}
		$tags = array_filter($tags);
		if(sizeof($tags)){
			//$tags = array_unique($tags,SORT_REGULAR);
			$tags = implode(",",$tags);
			return $tags;
		}
		else {
			return "";
		}
		
	}
	return "";
}

function findTags($productsToScrapes , $link , $type = "link" , $exists = false , $return = "array" , $delimeter = ","){
	if(sizeof($productsToScrapes) > 0 AND is_array($productsToScrapes)){
		if($exists){
			if(is_array($exists)){
				$tags = $exists;
			}
			else{
				$tags = array($exists);
			}
		}
		
		foreach($productsToScrapes as $productsToScrape){
			if($productsToScrape[$type] == $link){
				if (!in_array($productsToScrape["tags"],$tags)) {
					$tags[] = $productsToScrape["tags"];
				}
			}
		}
		
		$tags = array_filter($tags);
	}
	if($return != "array"){
		return implode("$delimeter" , $tags);
	}
	return $tags;
} 

function clean($string) {
	$string = strtolower($string);    
	$string = str_replace('-', '', $string);
    $string = str_replace(' ', '-', $string);
	$string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
	$string = str_replace('--', '-', $string);
    return  $string;
}

function stdHeaders(){
	return array(
		"metaDescription" => "title",
		"h1" => "title",
		"textArea_1" => "body_html",
		"images" => "src",
		"price" => "price",
		"skuNumber" => "sku",
		"brand" => "vendor",
		"comparePrice" => "compare_at_price",
		"tags" => "tags",
		"handle" => "handle",
	);
	
}

function variantsData(){
	$variantsData = array(
		"barcode",
		"compare_at_price",
		"grams",
		"weight",
		"weight_unit",
		"inventory_quantity",
		"option1_name",
		"option1",
		"option2_name",
		"option2",
		"option3_name",
		"option3",
		"price",
		"sku",
		"variant_title",
	);
	return $variantsData;
}