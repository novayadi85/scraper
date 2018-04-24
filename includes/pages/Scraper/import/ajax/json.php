<?php
session_start();
include_once $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Scraper/import/helper/data.php";
include_once $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Scraper/import/helper/string.php";

$Helper_String = new Helper_String;
/* error_reporting(false); */
function friendly_seo_string($vp_string){   
    $vp_string = trim($vp_string);    
    $vp_string = html_entity_decode($vp_string);    
    $vp_string = strip_tags($vp_string);    
    $vp_string = strtolower($vp_string);    
    $vp_string = preg_replace('~[^ a-z0-9_.]~', ' ', $vp_string);   
    $vp_string = preg_replace('~ ~', '-', $vp_string);
    $vp_string = preg_replace('~-+~', '-', $vp_string);
    return $vp_string;
}
function clean($string) {
	$string = strtolower($string);    
	$string = str_replace('--', '-', $string);
    $string = str_replace(' ', '-', $string);

    return preg_replace('/[^A-Za-z0-9\-]/', '', $string); 
}
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

// $csv = $_SERVER["DOCUMENT_ROOT"]."backend/system/import/csvFormat/product_template.csv";

function csv_to_array($filename='', $delimiter=';')
{
    if(!file_exists($filename) || !is_readable($filename))
        return FALSE;

    $header = NULL;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== FALSE)
    {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
        {
			if(!$header){
				$header = $row;
				
			} else {
			   $data[] = array_combine($header, $row);
			}
		}
        fclose($handle);
    }
    return $data;
}

$shopify_format = array(
	"Handle",
	"Title",
	"Body",
	"Vendor",
	"Type",
	"Tags",
	"Published",
	"Option1 Name",
	"Option1 Value",
	"Option2 Name",
	"Option2 Value",
	"Option3 Name",
	"Option3 Value",
	"Variant SKU",
	"Variant Grams",
	"Variant Inventory Tracker",
	"Variant Inventory Qty",
	"Variant Inventory Policy",
	"Variant Fulfillment Service",
	"Variant Price",
	"Variant Compare At Price",
	"Variant Requires Shipping",
	"Variant Taxable",
	"Variant Barcode",
	"Image Src",
	"Image Alt Text",
	"Gift Card",
	"Google Shopping / MPN",
	"Google Shopping / Age Group",
	"Google Shopping / Gender",
	"Google Shopping / Google Product Category",
	"SEO Title",
	"SEO Description",
	"Google Shopping / AdWords Grouping",
	"Google Shopping / AdWords Labels",
	"Google Shopping / Condition",
	"Google Shopping / Custom Product",
	"Google Shopping / Custom Label 0",
	"Google Shopping / Custom Label 1",
	"Google Shopping / Custom Label 2",
	"Google Shopping / Custom Label 3",
	"Google Shopping / Custom Label 4",
	"Variant Image,Variant Weight Unit"
);

function find_tags($collection , $query){
	if(is_array($collection) && sizeof($collection)){
		$tags = array();
		foreach($collection as $key => $val){
			if(in_array($query,$collection[$key])){
				$tags[] = $key;
			}
		}
		$tags = array_filter($tags);
		if(sizeof($tags)){
			return implode(",",$tags);
		}
		else {
			return "";
		}
		
	}
	return "";
}



if($_REQUEST["mode"] == "sendCSVFile"){$array = array();
	parse_str($_REQUEST["postData"] ,$array );
	
	$stdFields =  array(
		"title",
		"body_html",
		"vendor",
		"product_type",
		"tags",
		"handle",
		"metafields_global_description_tag",
		"images"
	);
	
	/* 
	$data = array();
	$handles =  array();
	foreach($array as $key => $values){
		if(is_array($values)){
			foreach($values as $k => $value){
				$params = array();
				$handles[] = $value["handle"];
				foreach($value as $k2 => $val){
					$params[$k2] = $val;
				}
				$data[$k] = $params;
			}
		}
	}
	
	$handles = implode(",",$handles); 
	*/
	if(!testShop()){
		$out["error"] = true;
		$out["msg"] = "invalid connection to API Shopify";
		print json_encode($out);
		exit();
	}
	
	$ch = curl_init($storeKey."/admin/products.json?fields=id,title,handle");
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json')
	);
	$products = curl_exec ($ch);
	curl_close ($ch); 
	$products = json_decode($products,true);
	//print_r($products);exit(); 
	if(is_array($products)){
		
		foreach($products["products"] as $product){
			$products[$product["handle"]] = $product["id"];
			$products[$product["title"]] = $product["id"];
		}
		unset($products["products"]);
	}
	
	$server_output = array(
		"updated",
		"created",
		"imported",
		"errors"
	);
	
	$values = $array["fields"];
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

	
	//foreach($array as $key => $values){
		if(is_array($values)){
			foreach($values as $k => $value){
				/* if(isset($value["parent"])){
					$value["handle"] = clean($value["parent"]);
					$value["title"] = trim($value["parent"]);
				}
				else{
					$value["handle"] = clean($value["handle"]);
				} */
				$value["handle"] = clean($value["handle"]);
				$params = array();
				$metafields = array();
				if(!empty($value["parent"])){
					$value["handle"] = clean(trim($value["parent"]));
					$value["title"] = trim($value["parent"]);
				} 
				
				foreach($value as $k2 => $val){
					if(isset($array["header"][$k2]) && !empty($array["header"][$k2]) && $array["header"][$k2] !="Undefined"){
						if($array["header"][$k2] == "src"){
							$val = array("src" => $val);
							$params["images"] = array($val);
						}
						else{
							if(!empty($value["parent"])){
								$params[$array["header"][$k2]] = $val;
								$params["title"] = trim($value["parent"]);
								$params["handle"] = clean(trim($value["parent"]));
							}
							else{
								$params[$array["header"][$k2]] = $val;
							}
							
						}
					}
				}
				
				//print_r($value);
				
				if(sizeof($params)){
					$variants = array();
					
					foreach($params as $x => $param){
						if(in_array($x,$variantsData)){
							if($x == "price"){
								$param = preg_replace('/\D/', '', $param);
								if($param > 100 && $param/100 < 0){
									$param = 0;
								}
							}
							$variants[$x] = $param;
							unset($params[$x]);
						}
					}
					
					if(sizeof($variants)){
						if(!isset($variants["option1"])){
							$variants["option1"] = "Default";
						}
						$params["variants"] = array($variants);
						if(isset($variants["option1_name"])){
							$variants["option1"] = $variants["option1_name"];
						}
						
						if(!empty($value["parent"])){
						
							$params["variants"] = array($variants);
							$params["options"] = array(
								array(
									"name" => "Variants"
									
								)
							);	
						}
					}
					
					if(isset($value["spConfig"])){
						$nvariants = array();
						$results = json_decode($value["spConfig"],true);
						$attributes = array();
						if(is_array($results) && sizeof($results)){
							foreach($results["attributes"] as $item){
								$values = array();
								$options = array();
								if(isset($item["options"])){
									foreach($item["options"] as $option){
										$values[] = $option["label"];
										$attributes[$option["id"]] = $option["label"];
									}
								}
								$nvariants["options"][] = array(
									"name" => $item["label"],
									"values" => $values
								);
								
							}

							if(isset($results["in_stockOp"])){

								//option1
								foreach($results["in_stockOp"] as $kk => $stocks){
									$options = array();
									$x = 1;
									foreach($stocks as $k => $stock){
										$options["option".$x++] = $attributes[$k];
										$options["price"] = $results["basePrice"];
										$options["compare_at_price"] = $results["oldPrice"];
									}
									
									$nvariants["variants"][] = $options;
								}
							}
						}
						
						
						if(sizeof($nvariants)){
							$params["variants"] =  $nvariants["variants"];
							if(!empty($nvariants["options"])){
								$params["options"] = $nvariants["options"];
							}
						}
						
					}
					
					if(empty($value["shortText"])){
						$body_html = strip_tags($params["body_html"]);
						$value["shortText"] = $Helper_String->truncate($body_html,92);
					}
					
					/* print_r($value["labelText"]); 
					continue;  */

					$datajson = json_encode(array('product' => $params));
					if(isset($products[strtolower($value["handle"])])){
						$params["id"] = $products[$value["handle"]];
						$id = $params["id"];
						$datajson = json_encode(array('product' => $params));
						$ch = curl_init($storeKey."/admin/products/{$id}.json");
						curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
						curl_setopt($ch, CURLOPT_POSTFIELDS,$datajson );
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_HTTPHEADER, array(
							'Content-Type: application/json',
							'Content-Length: ' . strlen($datajson))
						);
						$response = curl_exec ($ch);
						$response = json_decode($response , true);
						if(isset($response["errors"])){
							$server_output["errors"][] = $response["errors"];
						}
						else{
							if(isset($_REQUEST["formId"]) && !empty($_REQUEST["formId"]))
							updateProductData($response,$_REQUEST["formId"]);
							if(isset($value["shortText"]) && !empty($value["shortText"])){
								$metafields['metafield'] = array(
									'namespace' => "Extra", 
									'key' => "shortText",
									'value' => $value["shortText"],
									'value_type' => "string"
								);
								
								$server_output["meta"][] = updateMetafieldProduct($metafields,$response["product"]["id"]);	
							}
							
							if(isset($value["labelText"]) && !empty($value["labelText"])){
								$metafields['metafield'] = array(
									'namespace' => "Extra", 
									'key' => "labelText",
									'value' => $value["labelText"],
									'value_type' => "string"
								);
								
								$server_output["meta"][] = updateMetafieldProduct($metafields,$response["product"]["id"]);	
							}
							
							$server_output["updated"][] = $response;
						}
						
						curl_close ($ch);
					}
					else if(isset($products[strtolower($value["title"])])){
						$params["id"] = $products[$value["title"]];
						$id = $params["id"];
						$datajson = json_encode(array('product' => $params));
						$ch = curl_init($storeKey."/admin/products/{$id}.json");
						curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
						curl_setopt($ch, CURLOPT_POSTFIELDS,$datajson );
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_HTTPHEADER, array(
							'Content-Type: application/json',
							'Content-Length: ' . strlen($datajson))
						);
						$response = curl_exec ($ch);
						$response = json_decode($response , true);
						if(isset($response["errors"])){
							$server_output["errors"][] = $response["errors"];
						}
						else{
							if(isset($_REQUEST["formId"]) && !empty($_REQUEST["formId"]))
							updateProductData($response,$_REQUEST["formId"]);
							if(isset($value["shortText"]) && !empty($value["shortText"])){
								$metafields['metafield'] = array(
									'namespace' => "Extra", 
									'key' => "shortText",
									'value' => $value["shortText"],
									'value_type' => "string"
								);
								
								$server_output["meta"][] = updateMetafieldProduct($metafields,$response["product"]["id"]);	
							}
							
							if(isset($value["labelText"]) && !empty($value["labelText"])){
								$metafields['metafield'] = array(
									'namespace' => "Extra", 
									'key' => "labelText",
									'value' => $value["labelText"],
									'value_type' => "string"
								);
								
								$server_output["meta"][] = updateMetafieldProduct($metafields,$response["product"]["id"]);	
							}
							$server_output["updated"][] = $response;
						}
						curl_close ($ch);
					}
					else{
						/* $params["variants"] = array(
							array(
								"option1" => "Default",
								"price" => "10.00",
								"sku" => "123"
							)
						); */
						$ch = curl_init($storeKey."/admin/products.json");
						curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
						curl_setopt($ch, CURLOPT_POSTFIELDS,$datajson );
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_HTTPHEADER, array(
							'Content-Type: application/json',
							'Content-Length: ' . strlen($datajson))
						);
						$response = curl_exec ($ch);
						$response = json_decode($response , true);
						if(isset($response["errors"])){
							$server_output["errors"][] = $response["errors"];
						}
						else{
							if(isset($_REQUEST["formId"]) && !empty($_REQUEST["formId"]))
							updateProductData($response,$_REQUEST["formId"]);
							if(isset($value["shortText"]) && !empty($value["shortText"])){
								$metafields['metafield'] = array(
									'namespace' => "Extra", 
									'key' => "shortText",
									'value' => $value["shortText"],
									'value_type' => "string"
								);
								
								$server_output["meta"][] = updateMetafieldProduct($metafields,$response["product"]["id"]);	
							}
							
							if(isset($value["labelText"]) && !empty($value["labelText"])){
								$metafields['metafield'] = array(
									'namespace' => "Extra", 
									'key' => "labelText",
									'value' => $value["labelText"],
									'value_type' => "string"
								);
								
								$server_output["meta"][] = updateMetafieldProduct($metafields,$response["product"]["id"]);	
							}
							$server_output["created"][] = $response;
						}
						
						curl_close ($ch);
					}
					
					//print_r($response);
					
					//print_r($params);
				}
				
			}
		}
	//}
	
	$updated = count($server_output["updated"]);
	$created = count($server_output["created"]);
	
	$out = array(
		"meta" => $server_output["meta"],
		"updated" => count($server_output["updated"]),
		"created" =>  count($server_output["created"]),
		"imported" => ($updated + $created),
		"errors" => count($server_output["errors"])
	);
	
	print json_encode($out);

}

if($_REQUEST["mode"] == "saveCSVFile"){
	$path = $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Scraper/files/";
	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
	{
		$filename = "exportdata.csv";
		if($_REQUEST["name"]){
			$filename = $_REQUEST["name"].".csv";;
		}
		$str = array();
		parse_str($_REQUEST["postData"] ,$str );
		$_SESSION["json_scraper"] = $str["fields"];

		$txt = array_to_csv($str["fields"] , "");
		$randomname = "exportdata-" . time() . ".csv";
		$filesave = fopen($path . $randomname, "w") or die("Unable to open file!");
		fwrite($filesave, $txt);
		$id = saveCSV(array("path" => $path . $randomname , "title" => $randomname));
		print json_encode(array("token" => $id, "message" => $_SERVER["REQUEST_URI"] . "&action=download&mode=saveCSVFile&name=".$filename));
		exit(); 
	}
	else{
		$filename = "exportdata.csv";
		if($_REQUEST["name"]){
			$filename = $_REQUEST["name"].".csv";
		}
		$array2csv = $_SESSION["json_scraper"];
		$file = array_to_csv($array2csv , $filename);
		/* $randomname = "exportdata-" . time() . ".csv";
		$filesave = fopen($path . $randomname, "w") or die("Unable to open file!");
		$txt = array_to_csv($array2csv , "");
		fwrite($filesave, $txt); */
		//$id = saveCSV(array("path" => $path . $randomname , "name" => $randomname));
		print $file;
		exit(); 
	}
	
}

if($_REQUEST["mode"] == "getCSV"){
	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
	{ 
		$scraper = json_decode($_POST["params"],true);
		$collection = json_decode($_POST["collection"],true);
		/* if ($_SERVER["REMOTE_ADDR"] == "125.162.138.234") {
			print_r($_POST["params"]);
			print_r($scraper);
			print_r($collection);
			exit();
		} */
		
		$parent = $_POST["parent"];
		
		$key = $_POST["keys"];
		$value = $_POST["values"];
		if($_POST["type"] == "shopify_productlist"){
			$type = "collections";
		}
		else{
			$type = "tags";
		}
		
		if(isset($collection["label"])){
			$key = $collection["label"];
		}
		
		$collection = $collection["collection"];
		
		if($type == "tags"){
			//$collection = array();
			if(sizeof($scraper) && is_array($scraper)){
				foreach($scraper as $index => $data){
					if(isset($data[$parent]) && is_array($data[$parent])){
						foreach($data[$parent] as $k => $val){
							//$collection[$data[$key]][] = $val[$value];
							//$scraper[$index][$parent][$k][$type] = $data[$key];
							//$scraper[$index][$parent][$k][$type] = find_tags($collection,$data[$key]);
							$scraper[$index][$parent][$k][$type] = find_tags($collection,$val[$value]);
						}
					}
				}
			}
		}

		$array2csv = array();
		if(sizeof($scraper) && is_array($scraper)){
			foreach($scraper as $ii => $arr){
				if(isset($arr[$parent]) && is_array($arr[$parent])){
					 foreach($arr[$parent] as $key => $item){
						if(is_array($item)){
							foreach($item as $k => $v ){
								if(isset($_POST["shopify"]) && $_POST["shopify"] == "true"){
									if(!in_array($k,$shopify_format)){
										continue;
									}
									else{
										if(is_array($v)){
											$v = json_encode($v);
										}
										
										$array2csv[$ii][$k] = $v;
									}
								}
								else{
									
									if(is_array($v)){
										$v = json_encode($v);
									}
									
									$array2csv[$ii][$k] = $v;
								} 

								
							}
							
						} 
					} 
				}
			}
		}
		
		$_SESSION["json_scraper"] = $array2csv;
		print json_encode(array("message" => $_SERVER["REQUEST_URI"] . "&action=download&mode=getCSV&name=".$_POST["name"]));
		exit(); 
	}
	else {
		
		$array2csv = $_SESSION["json_scraper"];
		//print_r($array2csv);
		$filename = "example.csv";
		if($_REQUEST["name"]){
			$filename = $_REQUEST["name"].".csv";;
		}
		$file = array_to_csv($array2csv , $filename);
		print $file;
		exit(); 
	} 

}

if($_REQUEST["mode"] == "convert_to_array"){
	//header('Content-Type: application/json');
	$params =  $_POST["params"];
	$_POST["collection"] = utf8_decode($_POST["collection"]);
	$collection = json_decode($_POST["collection"],true);
	$scraper = $collection;
	
	if(sizeof($collection)<= 0 || !is_array($collection)){
		$scraper = $_POST["postdata"];
	} 
	
	print "<pre>";
	print_r($scraper);
	print "</pre>";
}

if($_REQUEST["mode"] == "create_collection"){
	$out["error"] = false;
	$parent = $_POST["parent"];
	$key = $_POST["keys"];
	$value = $_POST["values"];
	if($_POST["type"] == "shopify_productlist"){
		$type = "collections";
	}
	else{
		$type = "tags";
	}
	
	$collection = array();
	if(sizeof($_POST["data"]) && is_array($_POST["data"])){
		foreach($_POST["data"] as $index => $data){
			if(isset($data[$parent]) && is_array($data[$parent])){
				foreach($data[$parent] as $k => $val){
					$collection[$data[$key]][] = $val[$value];
					$_POST["data"][$index][$parent][$k][$type] = $data[$key];
				}
			}
		}
	}
	$collection_json = array();
	$collection_json["label"] = $key;
	$collection_json["collection"] = $collection;
	
	if(sizeof($collection) <= 0){
		$out["true"] = false;
	}
	
	$out["collection"] = $collection_json;
	$out["jsondata"] = $_POST["data"];
	
	print json_encode($out);
}



