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
				$allproducts = getBrickData("scraper_product",$id,"*",false,$searchobject,$connection);
				//get product 
			}
			
			//$onlineProducts = getProducts();
			$products = ExploreProduct($allproducts);
			
			getMetafield("shortText");
			getMetafield("labelText");
			if(sizeof($allproducts)){
				$values = $allproducts;
				if(is_array($values)){
					
					foreach($values as $k => $value){	
						if(isset($value["handle"]) && !empty($value["handle"])){
							$value["handle"] = clean($value["h1"]);
						}
						else{
							$value["handle"] = clean($value["h1"]);
						}
						
						$params = array();
						$metafields = array();
						$regularPrice = 0 ;
						$afterPrice = 0 ;
						$offerPrice = 0 ;
						$comparePrice = 0 ;
						if($value["price"]){
							$regularPrice = preg_replace('/\D/', '', $value["price"]) / 100;
							if($regularPrice < 0){
								$regularPrice = 0;
							}
						}
						
						if($value["afterPrice"] ){
							
							$afterPrice = preg_replace('/\D/', '', $value["afterPrice"]) / 100;
							if($afterPrice < 0){
								$afterPrice = 0;
							}
						}
						
						if($value["offerPrice"] ){
							$offerPrice = preg_replace('/\D/', '', $value["offerPrice"]) / 100;
							if($offerPrice < 0){
								$offerPrice = 0;
							}
						}
						
						if($value["comparePrice"] ){
							$comparePrice = preg_replace('/\D/', '', $value["comparePrice"]) / 100;
							if($comparePrice < 0){
								$comparePrice = 0;
							}
						}
						
						$value["comparePrice"] = max(array_filter(array($regularPrice,$offerPrice, $afterPrice, $comparePrice)));
						/* if(is_numeric($value["comparePrice"]) && $regularPrice > $value["comparePrice"]){
							$value["comparePrice"] = $regularPrice;
							$value["price"] = $value["comparePrice"];
						} */
						if(is_numeric($value["comparePrice"])){
							$value["price"] = min(array_filter(array($regularPrice,$offerPrice, $afterPrice, $comparePrice)));
							$value["price"] = number_format($value["price"],2,"."," ");
						}
						
						if(!empty($value["parent"])){
							$value["handle"] = clean(trim($value["parent"]));
							$value["title"] = trim($value["parent"]);
						} 
						
						foreach($value as $k2 => $val){
							if(isset($array["header"][$k2]) && !empty($array["header"][$k2]) && $array["header"][$k2] !="Undefined"){
								if($array["header"][$k2] == "src"){
									$arrayImages = json_decode($val,true);
									$val = array("src" => $val);
									$params["images"] = array($val);
									if(is_array($arrayImages)){
										$valImages = array();
										foreach($arrayImages as $arrayImage){
											$valImages[] = array("src" => $arrayImage);
										}
										$val = $valImages;
										$params["images"] = $val; 
										//$val = array("src" => $arrayImages[0]);
										//$params["images"] = array($val);
										
									}	
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
						
						$params["vendor"] = str_replace("Se alle produkter fra ","",$params["vendor"]);

						
						
						if(sizeof($params)){
							$variants = array();
							$pricePrimary = $params["price"];
							foreach($params as $x => $param){
								if(in_array($x,$variantsData)){
									if($x == "price"){
										//$param = preg_replace('/\D/', '', $param);
										$param = preg_replace('/\D/', '', $param) / 100;
										if($param/100 < 0){
											$param = 0;
										}
										$pricePrimary = $param;
									}
									
									$variants[$x] = $param;
									if($x == "compare_at_price" && empty($param)){
										unset($variants[$x]);
									}

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
								
								if(isset($variants["compare_at_price"]) && empty($variants["compare_at_price"])){
									unset($variants["compare_at_price"]);
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
							
							
							


							if(isset($value["spConfig"]) && !empty($value["spConfig"])){
								$nvariants = array();
								$value["spConfig"] = stripslashes($value["spConfig"]);
								$value["spConfig"] = str_replace('["{','{',$value["spConfig"]);
								$value["spConfig"] = str_replace('}"]','}',$value["spConfig"]);
								$results = json_decode($value["spConfig"],true);
	
								$attributes = array();
								$pricings = array();
								if(is_array($results) && sizeof($results)){
									$filloptions = array();
									foreach($results["attributes"] as $item){
										$values = array();
										$options = array();
										if(isset($item["options"])){
											foreach($item["options"] as $option){
												if($pricePrimary > $option["oldPrice"]){
													$option["oldPrice"] = $pricePrimary;
												}
												$values[] = $option["label"];
												$attributes[$option["id"]] = $option["label"];
												$pricings[$option["id"]] = array(
													"basePrice" => $option["price"],
													"oldPrice" => $option["oldPrice"]
												);
											}
										}
										$filloptions[] = $item["label"];
										if(in_array($item["label"],$filloptions)){
											$item["label"] = $item["label"] . rand(1,5);
										}
										$nvariants["options"][] = array(
											"name" => $item["label"] ,
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
												$basePrice = $results["basePrice"];
												$oldPrice = $results["oldPrice"];
												if(isset($pricings[$k]) && is_numeric($pricings[$k]["basePrice"])){
													$basePrice = ($results["basePrice"] + $pricings[$k]["basePrice"]);
												}
												
												if(isset($pricings[$k]) && is_numeric($pricings[$k]["oldPrice"])){
													$oldPrice = $pricings[$k]["oldPrice"];
												}
												
												if($basePrice){
													$basePrice =  preg_replace('/\D/', '', $basePrice) ;
												}
												if($oldPrice){
													$oldPrice =  preg_replace('/\D/', '', $oldPrice) ;
												}
												
												$options["price"] = $basePrice;
												$options["compare_at_price"] = $oldPrice;
												if($basePrice == $oldPrice && $oldPrice != $value["comparePrice"] && $value["comparePrice"] > 0){
													$options["compare_at_price"] = $value["comparePrice"];
												}
												
												if(empty($options["compare_at_price"])){
													unset($options["compare_at_price"]);
												}
												
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
							
							print "<pre>";
							print_r($params);
							
							if(empty($value["shortText"])){
								$body_html = strip_tags($params["body_html"]);
								$value["shortText"] = $Helper_String->truncate($body_html,92);
							} 
							
							$newHandle = clean($value["title"]);
							/* 
							if(isset($onlineProducts[$newHandle])){
								$tagging[] = $onlineProducts[$newHandle]["tags"];
							}  
							*/
							
							//$tagging = find_tags($collections,$value["href"],$tagging);
							
							
							if(isset($value["canonical"]) && !empty($value["canonical"])){
								$tags = findTags($productsToScrapes,$value["canonical"],"href",$tagging,false,",");
								if(empty($tags)){
									$tags = findTags($productsToScrapes,$value["href"],"link",$tagging,false,",");
								}
							}
							else{
								$tags = findTags($productsToScrapes,$value["href"],"link",$tagging,false,",");

							}
							
							$params["tags"] = convertUtf8($tags);
							
							if ($_SERVER["REMOTE_ADDR"] == "120.188.83.138") {
								//print_r($params); exit();
							}
							
							 
							$datajson = json_encode(array('product' => $params));

							if(isset($products[strtolower($value["handle"])])){
								print "Edit 1";
								$params["id"] = $products[$value["handle"]];
								$id = $params["id"];
								$chd = curl_init($storeKey."/admin/products.json?handle=".$value["handle"]);
								curl_setopt($chd, CURLOPT_CUSTOMREQUEST, "GET");
								curl_setopt($chd, CURLOPT_RETURNTRANSFER, true);
								$exsistNot = curl_exec ($chd);
								curl_close($chd);
								$exsistNot = json_decode($exsistNot , true);
								if(sizeof($exsistNot["products"]) && $id){
									$ch = curl_init($storeKey."/admin/products/{$id}.json");
									curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
								}
								else{
									$ch = curl_init($storeKey."/admin/products.json");
									curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
								}
								
								
								$datajson = json_encode(array('product' => $params));
								
								
								curl_setopt($ch, CURLOPT_POSTFIELDS,$datajson );
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($ch, CURLOPT_HTTPHEADER, array(
									'Content-Type: application/json',
									'Content-Length: ' . strlen($datajson))
								);
								$response = curl_exec ($ch);
								$response = json_decode($response , true);
								
								print "Masuk";
								print_r($response);
								if(isset($response["errors"])){
									$server_output["errors"][] = $response["errors"];
									$server_output["errors"][] = $params;
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
									$updates = array();
									print_r($response);
									if(isset($response["product"]["handle"])){
										$updates["statusApi"] = "uploaded";
										$updates["onlineId"] = $response["product"]["id"];
										$updates["handle"] = $response["product"]["handle"];
										$updates["uploadProductToShopify"] = "false";
										saveBrickData("scraper_product",$updates,false,$value["id"],$connection);
									}
									
									$server_output["updated"][] = $response;
								}
								
								curl_close ($ch);
							}
							else if(isset($products[strtolower($value["title"])])){
								print "Edit 2";
								$params["id"] = $products[$value["title"]];
								$id = $params["id"];
								$chd = curl_init($storeKey."/admin/products.json?handle=".$value["handle"]);
								curl_setopt($chd, CURLOPT_CUSTOMREQUEST, "GET");
								curl_setopt($chd, CURLOPT_RETURNTRANSFER, true);
								$exsistNot = curl_exec ($chd);
								curl_close($chd);
								$exsistNot = json_decode($exsistNot , true);
								if(sizeof($exsistNot["products"])){
									$ch = curl_init($storeKey."/admin/products/{$id}.json");
								}
								else{
									$ch = curl_init($storeKey."/admin/products.json");
								}
								
								$datajson = json_encode(array('product' => $params));
								//$ch = curl_init($storeKey."/admin/products/{$id}.json");
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
									$server_output["errors"][] = $params;
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
									$updates = array();
									print_r($response);
									if(isset($response["product"]["handle"])){
										$updates["statusApi"] = "uploaded";
										$updates["onlineId"] = $response["product"]["id"];
										$updates["handle"] = $response["product"]["handle"];
										$updates["uploadProductToShopify"] = "false";
										saveBrickData("scraper_product",$updates,false,$value["id"],$connection);
									}
									
									$server_output["updated"][] = $response;
								}
								curl_close ($ch);
							}
							else{
								print "Create";
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
								print "<pre>";
								print_r($params);
								if(isset($response["errors"])){
									$server_output["errors"][] = $response["errors"];
									$server_output["errors"][] = $params;
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
									$updates = array();
									print_r($response);
									if(isset($response["product"]["handle"])){
										$updates["statusApi"] = "uploaded";
										$updates["onlineId"] = $response["product"]["id"];
										$updates["handle"] = $response["product"]["handle"];
										$updates["uploadProductToShopify"] = "false";
										saveBrickData("scraper_product",$updates,false,$value["id"],$connection);
									}
									
									$server_output["created"][] = $response;
								}
								
								curl_close ($ch);
							}

						}
					}
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
			"errors" => $server_output["errors"],
			"server_output" => json_decode($server_output)
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

/* $searchobject = false;
$searchobject[] = array(
	"fieldname" => "uploadProductToShopify",
	"searchtype" => "=",
	"value" => "true"
); 
$quoteProducts = getBrickData("scraper_product",false,"*",false,$searchobject,$connection);
$quotes = array_slice($quoteProducts, 0, 50);
$response = array();
if(sizeof($quotes)){
	foreach($quotes as $quote){
		$response[$quote["id"]] = send2Shopify($quote["id"]);
	}
}  */
send2Shopify("TNOP-SNSY-8399-YINI-UDFM-1631-YR");
print "<pre>";
print_r($response);