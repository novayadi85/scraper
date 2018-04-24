<?php 
session_start();
//error_reporting(false);
include_once $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Collections/Helper.php";

if($_REQUEST["mode"] == "get_collection"){
	$out = array();
	$collections = getCollections();
	$products = getProducts();
	$out["collections"] = $collections;
	$out["products"] = $products;
	print json_encode($out);
	exit();
}

if($_REQUEST["mode"] == "update_product"){
	$out = array();
	$out["message"] = "Failed to do it..";
	$array1 = $_POST["tags"];
	$array2 = $_POST["ntags"];
	
	if(empty($array1)){
		$array1 = array();
	}
	
	if(empty($array2)){
		$array2 = array();
	}
	if(!empty($array1)){
		$array1 = explode(",",$array1);
	}
	
	if(!empty($array2)){
		$array2 = explode(",",$array2);
	}

	$tags = array_unique(array_merge($array1,$array2), SORT_REGULAR);
	
	if(!empty($tags)){
		$params = array(
			"id" => $_POST["id"],
			"tags" => implode(",",$tags )
		);
		$out["obj"] = updateProduct($_POST["id"],$params);
		$out["message"] = "Success";
	}
	print json_encode($out);
	exit();
}