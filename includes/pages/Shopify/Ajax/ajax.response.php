<?
error_reporting(true);
ini_set('max_execution_time', 300);
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/inc/db/connection.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/system/bricks/adminFunctions.php");
include_once $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Scraper/import/helper/data.php";
include_once $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Shopify/Helper/Data.php";
include_once $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Scraper/import/helper/string.php";
include_once $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Shopify/Class/Tree.php";

$Helper_String = new Helper_String;
$searchobject = false;
$customer = false;

if(!testShop()){
	$out["error"] = true;
	$out["msg"] = "invalid connection to API Shopify";
	print json_encode($out);
	exit();
}

if (isset($_POST["mode"]) && $_POST["mode"] == "getCustomerOption") {
	$out["error"] = false;
	$out["data"] = array();
	
	$customers = getBrickData("customer",false,"*",false,false,$connection);
	if(!empty($customers)){
		foreach ($customers as $customer) {
			$out["data"][] = array(
				"id" => $customer["id"],
				"name" => $customer["name"],
			);
		}
	}
	
	print json_encode($out);
}

if (isset($_POST["mode"]) && $_POST["mode"] == "loadData") {
	$out["error"] = false;
	$out["data"] = array();
	$table = array();
	$customer = $_POST["customer"];
	$type = $_POST["type"];
	$tags = $_POST["tags"];
	$status = $_POST["status"];
	//statusApi
	$productsToScrapes = getBrickData("productsToScrape",false,'*',false,false,$connection);			
	
	if($type == "lists"){
		$productsToListing = array();
		$scraper_products = getBrickData("scraper_product",false,"*",$customer,$searchobject,$connection);
		foreach($scraper_products as $b => $scraper_product){
				$productsToListing[$scraper_product["href"]] = $scraper_product;
			}
		if($status != "all"){
			$searchobject[] = array(
				"fieldname" => "statusApi",
				"searchtype" => "=",
				"value" => $status
			); 
		}
		
		$productLists = getBrickData("scraper_productlist",false,"*",false,$searchobject,$connection);		
		$trId = $_SESSION["cId"];
		
		if($_POST["parentTr"] != "false"){
			$trId = $_POST["parentTr"];
		}
		
		$Tree = new ElmTree;
		$productLists = $Tree->buildTree($productLists,$trId);

		
		if(sizeof($productLists)){
			$i = 0;
			$productsToScrapeTags = array();
			foreach($productsToScrapes as $b => $productsToScrape){
				if(isset($productsToScrapeTags[$productsToScrape["parentid"]])){
					array_push($productsToScrapeTags[$productsToScrape["parentid"]],$productsToScrape["link"]);
				}
				else{
					$productsToScrapeTags[$productsToScrape["parentid"]][] = $productsToScrape["link"];
				}
				
				$productsToScrapeListing[$productsToScrape["link"]] = $productsToScrape;
			}
			
			foreach($productLists as $x => $productList){
				$i++;
				$results = json_decode($productList["productUrls"], true);
				$urls = array();
				$title = $productList["title"];
				$paginationUrls = (json_decode($productList["paginationUrls"], true));
				$paginationUrls = array_unique($paginationUrls);
				if ($_SERVER["REMOTE_ADDR"] == "182.253.140.231") {
					
				}
				if(sizeof($results)){
					foreach($results as $result){
						if(is_array($urls) && is_array($result)){
							$urls = array_unique(array_merge_recursive($urls,$result),SORT_REGULAR);
						}
						else if(is_array($urls) && !is_array($result)){
							$urls[] = $result;
						}
						if(isset($collections[$title]) && is_array($collections[$title])){
							$array1 = array_map("trim",$collections[$title]);
							$array2 = array_map("trim",$urls);
							$collections[$title] = array_unique(array_merge_recursive($array1,$array2),SORT_REGULAR);
						}
						else{
							$collections[$title] = $urls;
						}
					}
				} 
				
			
				if(isset($productList["children"])){
					$table[$x][] = "<span syle=\"width: 28px;text-align: center;\" data-id=\"".$productList["id"]."\" class=\"row-details row-details-close\"></span>"; 
				}
				else{
					$table[$x][] =  "<span syle=\"width: 28px;text-align: center;\"></span>";
				}
				$foundProducts = count($urls);
				if(count($urls) < count($productsToScrapeTags[$productList["id"]])){
					$foundProducts = count($productsToScrapeTags[$productList["id"]]);
				}
				
				$countUnscraped = 0;
				$countscraped = 0;
				if(sizeof($productsToScrapeTags[$productList["id"]])){
					foreach($productsToScrapeTags[$productList["id"]] as $link){
						if(isset($productsToListing[$link])){
							$countscraped = $countscraped + 1;
						}
						else{
							$countUnscraped = $countUnscraped + 1;
						}
					}
				}
				
				
				$table[$x][] = $productList["href"]; 
				$table[$x][] = $foundProducts; 
				$table[$x][] = $countscraped; 
				$table[$x][] = $countUnscraped; 
				$table[$x][] = count($paginationUrls); 
				$table[$x][] = convertUtf8($productList["h1"]); 
				$table[$x][] = $productList["createddate"]; 
				$table[$x][] = $productList["lastUpdateDate"]; 
				if($productList["statusApi"] == "uploaded"){
					$table[$x][] = '<button data-type="list" type="button" class="btn blue-hoki show-to-detail btn-sm" data-url="'.$productList["href"].'" data-brick="'.$productList["id"].'"> See data in modal</button><button  data-type="list" type="button" class="btn green-haze send-to-shopify btn-sm" data-brick="'.$productList["id"].'"> Resend to Shopify </button><a href="/system/index.php?page=Toscrape&listId='.$productList["id"].'" type="button" class="btn blue btn-sm btn-view-scrape" target="_blank"> Srape products  </a><a data-url="'.$productList["href"].'" data-brick="'.$productList["id"].'" type="button" class="btn default btn-sm rescrape-list" target="_blank"> reScrape List  </a>'; 
				}
				else{
					$table[$x][] = '<button data-type="list" type="button" class="btn blue-hoki show-to-detail btn-sm" data-url="'.$productList["href"].'" data-brick="'.$productList["id"].'"> See data in modal</button><button  data-type="list" type="button" class="btn green-haze send-to-shopify btn-sm" data-brick="'.$productList["id"].'"> Send to Shopify </button><a href="/system/index.php?page=Toscrape&listId='.$productList["id"].'" type="button" class="btn blue btn-sm  btn-view-scrape" target="_blank"> Srape products  </a><a data-url="'.$productList["href"].'" data-brick="'.$productList["id"].'" type="button" class="btn default btn-sm rescrape-list" target="_blank"> reScrape List  </a>'; 
				
				}
				$out["result"][] = $productList["id"];
			}
		}
	}
	else if($type == "products"){
		$searchobject = array();
		if($status != "all"){
			$searchobject[] = array(
				"fieldname" => "statusApi",
				"searchtype" => "=",
				"value" => $status
			); 
		}
		$products = getBrickData("scraper_product",false,"*",$customer,$searchobject,$connection);
		//print_r($products);
				//print_r($editId);
		$onlineProducts = getProducts();
		$tagFilters = array();
		$table = array();
		if(sizeof($products)){
			$urls = array();
			$searchobject = false;
			$editId = false;
			if($tags){
				$editId = $tags;
				if(sizeof($productsToScrapes)){
					foreach($productsToScrapes as $k => $productsToScrape){
						if(in_array($productsToScrape["parentid"],$editId)){
							if(!empty($productsToScrape["tags"])){
								$tagFilters[] = $productsToScrape;
							}
							
							
						}
						
					}
				}
				$searchobject = false;
				
			}
			else{
				$tagFilters = $productsToScrapes;	
			}

			if(sizeof($tagFilters)){
				foreach($tagFilters as $j => $tagFilter){
					$urls[] = $tagFilter["link"];
					$urls[] = $tagFilter["href"];
				}
			}
			
			
			//$productLists = getBrickData("scraper_productlist",$editId,"*",false,$searchobject,$connection);
			
			//productsToScrapes
			
			/* if(sizeof($productLists)){
				foreach($productLists as $x => $productList){
					$results = json_decode($productList["productUrls"], true);
					$title = $productList["h1"];
					if(sizeof($results)){
						foreach($results as $result){
							//$collections[$title][] = $result;
							if(isset($collections[$title]) && is_array($result)){
								$array1 = array_map("trim",$collections[$title]);
								$array2 = array_map("trim",$result);
								$collections[$title] = array_merge_recursive($array1,$array2);	
							}
							else{
								$collections[$title][] = $result;
							} 
							
							if(is_array($urls) && is_array($result)){
								$urls = array_merge_recursive($urls,$result);
							}
						}
					}
				}
			} */
			
			$i = 0;
			
			$test = array();
			foreach($products as $x => $product){
				$testUrls[] = $product["href"];
				if(isset($product["canonical"]) && !empty($product["canonical"])){
					if(!in_array($product["canonical"] , $urls)) continue;
				}
				else{
					if(!in_array($product["href"] , $urls)) continue;
				}

				$i++;
				$body = array();
				$tagging = array();
				$newHandle = clean($product["h1"]);
				if(isset($product["handle"]) && !empty($product["handle"])){
					$newHandle = $product["handle"];
				}
				if(isset($onlineProducts[$newHandle])){
					//$tagging[] = $onlineProducts[$newHandle]["tags"];
				}
				
				if(isset($product["canonical"]) && !empty($product["canonical"])){
					$tags = findTags($productsToScrapes,$product["canonical"],"href",$tagging,false," | ");
					if(empty($tags)){
						$tags = findTags($productsToScrapes,$product["href"],"link",$tagging,false," | ");
					}
				}
				else{
					$tags = findTags($productsToScrapes,$product["href"],"link",$tagging,false," | ");
				}
				
				/* $regularPrice = "";
				$afterPrice = "";
				$offerPrice = "";
				
				if($product["price"] ){
					$regularPrice = preg_replace('/\D/', '', $product["price"]) / 100;
					if($regularPrice < 0){
						$regularPrice = 0;
					}
				}
				
				if($product["afterPrice"] ){
					$afterPrice = preg_replace('/\D/', '', $product["afterPrice"]) / 100;
					if($afterPrice < 0){
						$afterPrice = 0;
					}
				}
				
				if($product["offerPrice"] ){
					$offerPrice = preg_replace('/\D/', '', $product["offerPrice"]) / 100;
					if($offerPrice < 0){
						$offerPrice = 0;
					}
				} */
				
				$regularPrice = 0 ;
				$afterPrice = 0 ;
				$offerPrice = 0 ;
				$comparePrice = 0 ;
				
				if($product["price"]){
					$regularPrice = preg_replace('/\D/', '', $product["price"]) / 100;
					if($regularPrice < 0){
						$regularPrice = 0;
					}
				}
				
				if($product["afterPrice"] ){
					$afterPrice = preg_replace('/\D/', '', $product["afterPrice"]) / 100;
					if($afterPrice < 0){
						$afterPrice = 0;
					}
				}
				
				if($product["offerPrice"] ){
					$offerPrice = preg_replace('/\D/', '', $product["offerPrice"]) / 100;
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
				$salePrice = "";
				$comparePrice = min(array_filter(array($regularPrice,$offerPrice, $afterPrice, $comparePrice)));
				if(is_numeric($comparePrice) && $comparePrice != $regularPrice){
					$salePrice = number_format($comparePrice,2,",",".") . " kr";
				}
				
				//$tagging = find_tags($collections,$product["href"],$tagging);
				$tesImages = json_decode($product["images"],true);
				if(is_array($tesImages)){
					$product["images"] = $tesImages[0];
				}
				$table[$x][] = $i; 
				$table[$x][] = $product["h1"]; 
				$table[$x][] = "<img src='".$product["images"]."' width=\"80\" height=\"60\">"; 
				$table[$x][] = $product["title"];
				$table[$x][] = (!empty($product["canonical"])) ? $product["canonical"] : $product["href"]; 
				$table[$x][] = convertUtf8($tags);
				$table[$x][] = $newHandle; 
				$table[$x][] = $product["price"];
				$table[$x][] = $salePrice;
				$table[$x][] = $product["brand"];
				$table[$x][] = $product["uploadProductToShopify"]; 
				if($product["statusApi"] == "uploaded"){
					$table[$x][] = '<button data-type="product" type="button" class="btn blue-hoki show-to-detail btn-sm" data-brick="'.$product["id"].'"> See data in modal</button><button  data-type="product" type="button" class="btn green-haze send-to-shopify btn-sm" data-brick="'.$product["id"].'"> Resend to Shopify </button><button  data-type="product" type="button" class="btn default rescrape-shopify btn-sm" data-url="'.$product["href"].'"> Re Scrape </button>'; 
				}
				else{
					$table[$x][] = '<button data-type="product" type="button" class="btn blue-hoki show-to-detail btn-sm" data-brick="'.$product["id"].'"> See data in modal</button><button  data-type="product" type="button" class="btn green-haze send-to-shopify btn-sm" data-brick="'.$product["id"].'"> Send to Shopify </button><button  data-type="product" type="button" class="btn default rescrape-shopify btn-sm" data-url="'.$product["href"].'"> Re Scrape </button>'; 
			
				}
				
				
				
				$test[$product["canonical"]] = $product["handle"];
				$out["result"][] = $product["id"];
			}
			//print_r($testUrls); exit();
			/* print_r(json_encode($test));
			exit(); */
		}
	}
	
	else{
		if($status != "all"){
			$searchobject[] = array(
				"fieldname" => "statusApi",
				"searchtype" => "=",
				"value" => $status
			); 
			
			if($type == "page"){
				$searchobject[] = array(
					"fieldname" => "pageList",
					"searchtype" => "=",
					"value" => NULL
				); 
			}
			
		}
		$pages = getBrickData("scraper_page",false,"*",$customer,$searchobject,$connection);
		$onlinePages = getPages();
		$table = array();
		if(sizeof($pages)){
			$i = 0;
			foreach($pages as $x => $page){
				if($type == "pages" && !$page["pageList"]){
					continue;
				}
				
				if($type == "page" && $page["pageList"]){
					continue;
				}
				
				$i++;
				$body = array();
				$table[$x][] = $i; 
				$table[$x][] = $page["h1"]; 
				if(empty($page["h1"])){
					$page["h1"] = $page["title"];
				}
				$table[$x][] = $page["title"];
				$table[$x][] = $page["canonical"]; 
				$newHandle  = clean($page["h1"]); 
				$table[$x][] = $newHandle; 
				$table[$x][] = $page["lastUpdateDate"]; 
				if($page["statusApi"] == "uploaded"){
					$table[$x][] = '<button data-type="page" type="button" class="btn blue-hoki show-to-detail btn-sm" data-brick="'.$page["id"].'"> See data in modal</button><button  data-type="page" type="button" class="btn green-haze send-to-shopify btn-sm" data-brick="'.$page["id"].'"> Resend to Shopify </button>'; 
				}
				else{
					$table[$x][] = '<button data-type="page" type="button" class="btn blue-hoki show-to-detail btn-sm" data-brick="'.$page["id"].'"> See data in modal</button><button  data-type="page" type="button" class="btn green-haze send-to-shopify btn-sm" data-brick="'.$page["id"].'"> Send to Shopify </button>'; 
			
				}
				
				$out["result"][] = $page["id"];
				
			}
		}
	}
	
	
	
	$tr = array();
	if(sizeof($table)){
		foreach($table as $tds){
			$tr[] = "<tr>";
				if(sizeof($tds)){
					foreach($tds as $td){
						$tr[] = "<td>".$td."</td>";
					}
				}
				
			$tr[] = "</tr>";
		}
	}
	
	if($_POST["parentTr"] != "false"){
		print json_encode(array("data" => join($tr) , "count" => count($table)) );
		die();
	}

	$table = array_values($table);
	print json_encode(array("data" => $table , "count" => count($table) , "out" => $out));
	die();
}


if (isset($_POST["mode"]) && $_POST["mode"] == "getData") {
	$out["error"] = false;
	$out["data"] = array();
	$customer = $_POST["customer"];
	if(!empty($customer)){
		$searchobject = false;
		$productLists = getBrickData("scraper_productlist",false,"*",false,$searchobject,$connection);
		$products = getBrickData("scraper_product",false,"*",$customer,$searchobject,$connection);
		$lists = getBrickData("productsToScrape",false,"*",false,$searchobject,$connection);	
		$listings = $lists;
		$xlistings = array();
		$trs = array();
		if(sizeof($lists)){
			$no = 0;
			foreach($listings as $k => $list){
				$xlistings[$list["parentid"]]["urls"][] = $list["link"];
			}
		}
		
		
		$Tree = new ElmTree();
		$listTree = $Tree->buildTree($productLists,$customer);
		$ids = array();
		foreach($listTree as $list){
			$ids[] = $list;
			if(isset($list["children"])){
				$new =  $Tree->combineDataArray($list["children"],$list["id"]);
				$array1 = array_map("trim",$ids);
				$array2 = array_map("trim",$new);
				$ids = array_unique(array_merge_recursive($array1,$array2),SORT_REGULAR);
			}
		}
		
		/*  print_r($listTree);
		exit(); */
		$productLists = $listTree;
		
		
		$tagMulti = $Tree->drawDropdown($listTree,$customer,true);
		
		$collections = array();
		if(!empty($productLists) && sizeof($productLists)){
			foreach($productLists as $productList){
				if(isset($xlistings[$productList["id"]])){
					$urls = array();
					if(!empty($productList["h1"])){
						$title = $productList["h1"];
					}
					else{
						$title = $productList["title"];
					}
					foreach($xlistings[$productList["id"]]["urls"] as $result){
						if(is_array($urls) && is_array($result)){
							$urls = array_unique(array_merge_recursive($urls,$result),SORT_REGULAR);
						}
						else if(is_array($urls) && !is_array($result)){
							$urls[] = $result;
						}
					}
					
					$collections[$title] = $urls;
					$tags[$productList["id"]] = convertUtf8($title);
					
				}
				
				/* if(!empty($productList["productUrls"])){
					
					if(json_decode($productList["productUrls"]) === true){
						$productUrls = json_decode($productList["productUrls"],true);
						if(sizeof($productUrls)){
							foreach($productUrls as $k => $urls){
								if(sizeof($urls)){	
									if(!empty($productList["h1"])){
										$title = $productList["h1"];
									}
									else{
										$title = $productList["title"];
									}
									
									if(isset($collections[$title]) && is_array($collections[$title])){
										try{
											$array1 = array_map("trim",$collections[$title]);
											$array2 = array_map("trim",$urls);
											$collections[$title] = array_unique(array_merge_recursive($array1,$array2),SORT_REGULAR);
											$tags[$productList["id"]] = convertUtf8($title);
										}
										catch(Exception $e){
											
										}	
									}
									else{
										$collections[$title] = $urls;
										$tags[$productList["id"]] = convertUtf8($title);
									}					
								}
							}
						}
					}
					else{
						if(!empty($productList["h1"])){
							$title = $productList["h1"];
						}
						else{
							$title = $productList["title"];
						}
						$tags[$productList["id"]] = convertUtf8($title);
					}
					
				} */
				 
			} 	
		}
		
		$out["collections"] = $collections;
		$out["tags"] = $tags;
		$out["tagsMulti"] = $tagMulti ;
	}
	else{
		$out["error"] = true;
	}
	
	print json_encode($out);
}


if (isset($_POST["mode"]) && $_POST["mode"] == "send2Shopify") {
	$out["error"] = false;
	$out["data"] = array();
	$array = array();
	$table = array();
	//$customer = $_POST["customer"];
	$type = $_POST["type"];
	$id = $_POST["id"];
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
			$productLists = getBrickData("scraper_productlist",false,"*",$customer,$searchobject,$connection);
			$productsToScrapes = getBrickData("productsToScrape",false,'*',false,$searchobject,$connection);			

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
				$allproducts = getBrickData("scraper_product",false,"*",false,$searchobject,$connection);
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
										
										if(in_array($item["label"],$filloptions)){
											$item["label"] = $item["label"] . rand(1,5);
										}
										$filloptions[] = $item["label"];
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
							
							$datetime = new DateTime();
							$published_at =  $datetime->format(DateTime::ATOM);
							$params["tags"] = convertUtf8($tags);
							$params["published_at"] = $published_at;
							
							if ($_SERVER["REMOTE_ADDR"] == "120.188.83.138") {
								//print_r($params); exit();
							}
							
							
							
							$datajson = json_encode(array('product' => $params));
							
		
							if(isset($products[strtolower($value["handle"])])){
								
								$params["id"] = $products[$value["handle"]];
								$id = $params["id"];
								$chd = curl_init($storeKey."/admin/products.json?handle=".$value["handle"]);
								curl_setopt($chd, CURLOPT_CUSTOMREQUEST, "GET");
								curl_setopt($chd, CURLOPT_RETURNTRANSFER, true);
								$exsistNot = curl_exec ($chd);
								curl_close($chd);
								$exsistNot = json_decode($exsistNot , true);
								if(sizeof($exsistNot["products"])){
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
								
								if((isset($response["errors"])) && $response["errors"] == "Not Found"){
									
									/* $ch = curl_init($storeKey."/admin/products.json");
									curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
									$datajson = json_encode(array('product' => $params));
									curl_setopt($ch, CURLOPT_POSTFIELDS,$datajson );
									curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
									curl_setopt($ch, CURLOPT_HTTPHEADER, array(
										'Content-Type: application/json',
										'Content-Length: ' . strlen($datajson))
									);
									$response = curl_exec ($ch);
									$response = json_decode($response , true); */
								}
								
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
								if((isset($response["errors"])) && $response["errors"] == "Not Found"){
									
									/* $ch = curl_init($storeKey."/admin/products.json");
									curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
									$datajson = json_encode(array('product' => $params));
									curl_setopt($ch, CURLOPT_POSTFIELDS,$datajson );
									curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
									curl_setopt($ch, CURLOPT_HTTPHEADER, array(
										'Content-Type: application/json',
										'Content-Length: ' . strlen($datajson))
									);
									$response = curl_exec ($ch);
									$response = json_decode($response , true); */
								}
								
								
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
								//print_r($response);
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
		
		if($type == "lists" || $type == "list"){
			$searchobject = false;
			/* $products = getBrickData("scraper_product",false,"*",$customer,$searchobject,$connection);
			if(is_array($products)){
				foreach($products as $key => $product){
					$products[$product["href"]] = $product[];
					unset($products[$key]);
				}
			} */
			
			if(is_array($id)){
				$productLists = getBrickData("scraper_productlist",false,"*",$customer,$searchobject,$connection);
				if(sizeof($productLists) && is_array($productLists)){
					foreach($productLists as $index => $productList){
						if(!in_array($productList["id"],$id)){
							unset($productLists[$index]);
						}
					}
				}
			}
			else{
				$productLists = getBrickData("scraper_productlist",$id,"*",$customer,$searchobject,$connection);
			}
			
			$collections = getCollections();
			if(sizeof($collections) && is_array($collections)){
				foreach($collections as $i => $collection){
					$title = strtolower($collection["title"]);
					$collections[$title] = $collection["id"];
					unset($collections[$i]);
				}
			}
			
			
			if(sizeof($productLists) && is_array($productLists)){
				foreach($productLists as $index => $productList){
					/* if(!empty($productList["productUrls"])){
					$productUrls = json_decode($productList["productUrls"],true);
						if(sizeof($productUrls)){
							foreach($productUrls as $k => $urls){
								if(sizeof($urls)){
									foreach($urls as $k2 => $url){
										
									}
								}
							}
						}
					} */
					
					$params = array();
					if(empty($productList["h1"])){
						$productList["h1"] = $productList["title"];
					}
					$title = strtolower($productList["h1"]);
					$params["colection_id"] = false;
					if(isset($collections[$title])){
						$params["colection_id"] = $collections[$title];
						if(empty($params["colection_id"])){
							$params["colection_id"] = false;
						}
					}
					getMetafield("shortText","collection");
					//admin/collections/#{id}/metafields.json
					/**
					if(isset($value["shortText"]) && !empty($value["shortText"])){
						$metafields['metafield'] = array(
							'namespace' => "Extra", 
							'key' => "shortText",
							'value' => $value["shortText"],
							'value_type' => "string"
						);
						
						$server_output["meta"][] = updateMetafieldProduct($metafields,$response["product"]["id"]);	
					}
					**/
					$others = array();
					$others["body_html"] = utf8_decode($productList["textArea_1"]);
					$others["meta"] = array("shortText" => $productList["meta_field_2"]);
					$params["title"] = convertUtf8($productList["h1"]);
					$params["column"] = "tag";
					$params["relation"] = "equals";
					$params["condition"] = convertUtf8($productList["h1"]);
					$response = create_collection($params,$params["colection_id"],$others);	
					print_r($response);
					$updates = array();
					$updates["statusApi"] = "uploaded";
					$updates["uploadCollectionToShopify"] = "false";
					if(isset($response["smart_collection"]["handle"])){
						$updates["handle"] = $response["smart_collection"]["handle"];
						$server_output["created"][] = $response;
						//print "First";
					}
					else{
						$others["body_html"] = $productList["textArea_1"];
						$response = create_collection($params,$params["colection_id"],$others);	
						print_r($response);
						if(isset($response["smart_collection"]["handle"])){
							$updates["handle"] = $response["smart_collection"]["handle"];
							$server_output["created"][] = $response;
							/* print "Second"; */
							//print_r($others);
						}
						else{
							$server_output["errors"][] = $response;
						}
						
					}
					
					$out["id"][] = saveBrickData("scraper_productlist",$updates,false,$productList["id"],$connection);
					
					//print_r($productList);
				}	
			}
			
			
		}
		
		if($type == "pages" || $type == "page"){
			$searchobject = false;
			$products = getBrickData("scraper_product",false,"*",$customer,$searchobject,$connection);
			$productLists = getBrickData("scraper_productlist",false,"*",$customer,$searchobject,$connection);
			$allpages = getBrickData("scraper_page",false,"*",$customer,$searchobject,$connection);	
			if(is_array($id)){
				$pages = $allpages;
				if(sizeof($pages) && is_array($pages)){
					foreach($pages as $index => $page){
						if(!in_array($page["id"],$id)){
							unset($pages[$index]);
						}
					}
				}
			}
			else{
				$pages = getBrickData("scraper_page",$id,"*",$customer,$searchobject,$connection);
			}
			
			
			$ireplaces = array();
			
			foreach($productLists as $productList){
				if(!empty($productList["href"])){
					
					if(empty($productList["h1"]))
						$productList["h1"] = $productList["title"];
					
					$handle = clean($productList["h1"]);
					
					if(!empty($product["handle"])){
						$handle = $product["handle"];
					}
					
					$ireplaces[$productList["href"]] = "/collections/".$handle;
				}
			}
			
			foreach($products as $product){
				if(!empty($productList["href"])){
					if(empty($product["h1"]))
						$product["h1"] = $product["title"];
					
					$handle = clean($product["h1"]);
					
					if(!empty($product["handle"])){
						$handle = $product["handle"];
					}
					
					
					$ireplaces[$product["href"]] = "/products/".$handle;
				}
			}
			
			foreach($allpages as $page){
				if(!empty($page["href"])){
					if(empty($page["h1"]))
						$page["h1"] = $page["title"];
					
					$handle = clean($page["h1"]);
					
					if(!empty($page["handle"])){
						$handle = $page["handle"];
					}
					
					$ireplaces[$page["href"]] = "/pages/".$handle;
				}
			}
			
			$pagesOnline = getPages();
			
			$pathCustomer = $_SERVER["DOCUMENT_ROOT"]."/system/customer/".$_SESSION["cId"];
			$path = $_SERVER["DOCUMENT_ROOT"]."/system/customer/".$_SESSION["cId"]."/images/";					
			$linkPath = "https://jeppekjaersgaard.dk/system/customer/".$_SESSION["cId"]."/images/";
			if(sizeof($pages) && is_array($pages)){
				foreach($pages as $index => $page){
					$content = $page["textArea_1"];
					if(isset($page["imagesList"]) || isset($page["pageList"])){
						$imagesList = json_decode($page["imagesList"],true);
						$pageList = json_decode($page["pageList"],true);
						if(is_array($imagesList)){
							foreach($imagesList as $List){
								$imageInfo = pathinfo($List);
								if(file_exists($path . $imageInfo['basename'])){
									$file = $linkPath . $imageInfo['basename'];
									$content = str_replace($List,$file,$content);
								}
								else{
									if(!is_dir($pathCustomer)){
										mkdir($pathCustomer, 0777, true);
										mkdir($path, 0777, true);
									}
									
									if(!is_dir($path)){
										mkdir($path, 0777, true);
									}

									try{
										$imageInfo = pathinfo($List);
										$filesave = fopen($path . $imageInfo['basename'], "w");;
										fwrite($filesave, file_get_contents($List));
										$file = $linkPath . $imageInfo['basename'];
										$content = str_replace($List,$file,$content);
									}
									catch(Exception $e){
										
									}
								}
									
								
							}
						}
						
						if(is_array($pageList)){
							foreach($pageList as $List){
								//ireplaces
								if(isset($ireplaces[$List])){
									$content = str_replace($List,$ireplaces[$List],$content);
								}
							}
						}

					}
					
					
					//replace content
					$doc = new DOMDocument;

					// This is a reasonable use of the @ operator as malformed HTML will produce
					// a lot of warnings. Please don't shoot me ;)
					@$doc->loadHTML('<?xml encoding="utf-8" ?>' . $content); 

					// Get the links.
					$links = $doc->getElementsByTagName('a');
					foreach ($links as $link) {
					  // Change the value of an attribute based on the current value.
						if ($link->getAttribute('href')) {
							if(isset($ireplaces[$link->getAttribute('href')])){
								$link->setAttribute('href', $ireplaces[$link->getAttribute('href')]);
							}
						}
					}

					// Get the images.
					$images = $doc->getElementsByTagName('img');
					
					foreach ($images as $image) {
					  // Change the value of an attribute based on the current value.
						$imageInfo = pathinfo($image->getAttribute('src'));
						if(file_exists($path . $imageInfo['basename'])){
							$file = $linkPath . $imageInfo['basename'];
							$image->setAttribute('src', $file);
						}
						else{
							if(!is_dir($pathCustomer)){
								mkdir($pathCustomer, 0777, true);
								mkdir($path, 0777, true);
							}
							
							if(!is_dir($path)){
								mkdir($path, 0777, true);
							}

							try{
								$imageInfo = pathinfo($image->getAttribute('src'));
								$filesave = fopen($path . $imageInfo['basename'], "w");;
								fwrite($filesave, file_get_contents($image->getAttribute('src')));
								$file = $linkPath . $imageInfo['basename'];
								$image->setAttribute('src', $file);
							}
							catch(Exception $e){
								
							}
							
						}
						
					}

					// Get the new HTML
					$content = $doc->saveHTML();
					$params = array();
					if(empty($page["h1"])){
						$page["h1"] = $page["title"];
					}
					$params["body_html"] = $content;
					$params["metafields_global_description_tag"] = $page["metaDescription"];
					$params["title"] = $page["h1"];
					$params["handle"] = clean($page["h1"]);
					
					if(isset($pagesOnline[$params["handle"]])){
						$response = create_page($params,$pagesOnline[$params["handle"]]["id"]);
					}
					else{
						$response = create_page($params);
					}
					
					if(isset($response["page"])){
						$updates = array();
						$updates["statusApi"] = "uploaded";
						$updates["handle"] = $response["page"]["handle"];
						$out["id"][] = saveBrickData("scraper_page",$updates,false,$page["id"],$connection);
						$server_output["created"][] = $response;
					}
					else{
						$server_output["errors"][] = $response;
						$msg = $response["errors"];
					}
						
				}
			}	
		}
		
		//
		/**
		mode:create_collection
		params:colection_id=&title=MTB+cykel&column=tag&relation=equals&condition=tag1
		**/
		
		$updated = count($server_output["updated"]);
		$created = count($server_output["created"]);
		
		
		$out = array(
			"meta" => $server_output["meta"],
			"updated" => count($server_output["updated"]),
			"created" =>  count($server_output["created"]),
			"imported" => ($updated + $created),
			"errorss" => $server_output["errors"],
			
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
	print json_encode($out);
	die();
}

if (isset($_POST["mode"]) && $_POST["mode"] == "showModal") {
	$out["error"] = false;
	$out["data"] = array();
	$type = $_POST["type"];
	$id = $_POST["id"];
	if($type == "list"){
		$lists = getBrickData("scraper_productlist",$id,"*",$customer,$searchobject,$connection);
		$stdAttributes = getAttributesForBrick("scraper_productlist",$connection);
		$stdAttributes = array(
			"title" => "title",
			"tag" => "h1",
			"textArea_1" => "textArea_1",
			"metaDescription" => "metaDescription",
			"images" => "images",
			"productUrls" => "productUrls",
		);
		$stdAttributes["meta_field_1"] = "meta_field_1";
		$stdAttributes["meta_field_2"] = "meta_field_2";
	}
	else if($type == "product"){
		$lists = getBrickData("scraper_product",$id,"*",$customer,$searchobject,$connection);
		//$stdAttributes = getAttributesForBrick("scraper_product",$connection);
		$productsToScrapes = getBrickData("productsToScrape",false,'*',false,$searchobject,$connection);			

		$productLists = getBrickData("scraper_productlist",false,"*",$customer,$searchobject,$connection);
		$urls = array();
		if(sizeof($productLists)){
			foreach($productLists as $x => $productList){
				$results = json_decode($productList["productUrls"], true);
				$title = $productList["h1"];
				if(!empty($results)){
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
		$stdAttributes = stdHeaders();
		$stdAttributes["brand"] = "brand";
		$stdAttributes["tags"] = "tags";
		$stdAttributes["canonical"] = "canonical";
		$stdAttributes["labelText"] = "labelText";
		$stdAttributes["shortText"] = "shortText";
		$stdAttributes["textArea_2"] = "textArea_2";
		$stdAttributes["meta_field_1"] = "meta_field_1";
		$stdAttributes["meta_field_2"] = "meta_field_2";
	}
	else{
		$lists = getBrickData("scraper_page",$id,"*",$customer,$searchobject,$connection);
		$stdAttributes = getAttributesForBrick("scraper_page",$connection);
		$stdAttributes = array(
			"title" => "title",
			"tag" => "h1",
			"textArea_1" => "textArea_1",
			"metaDescription" => "metaDescription"
		);
		
	}

	$html = array();
	$textarea = array(
		"textArea_1",
		"metaDescription",
		"images",
		"meta_field_1",
		"meta_field_2",
		"images",
		"productUrls" => "productUrls",
		
	);
	
	if(is_array($lists)){
		foreach($lists as $list){
			$out["title"] = "<h2>".convertUtf8($list["h1"])."</h2>";
			if(is_array($list)){
				$list["handle"] = clean($list["h1"]);
				$tagging = "";
				
				//$tagging = find_tags($collections,$list["href"],$tagging);
				$tagging = findTags($productsToScrapes,$list["href"],"href",$tagging,false," | ");

				$list["tags"] = $tagging;
				foreach($list as $key => $value){
					//$value = utf8_decode($value);
					$value = convertUtf8($value);
					if(in_array($key,array_keys($stdAttributes))){
						$form = "<div class=\"form-group\">";
							$form .= "<label><strong>".strtoupper($key)."</strong></label>";
							if(in_array($key,$textarea)){
								
								$form .= "<textarea rows=\"5\" name=\"$key\" class=\"form-control\">$value";
								$form .= "</textarea>";
							}
							else{
								$form .= "<input name=\"$key\" value=\"$value\" class=\"form-control\" type=\"text\">";
							}
							
						
						$form .= "</div>";
						$html[] = $form;
					}
					else{
						
					}
					
				}
			}
		}
		
	}
	

	$out["stdAttributes"] = $stdAttributes;
	$out["data"] = $lists;
	$out["html"] = join($html);
	print json_encode($out);
	die();
}

if (isset($_POST["mode"]) && $_POST["mode"] == "changeStatus") {
	$out["error"] = false;
	$out["data"] = array();
	$type = $_POST["type"];
	$ids = $_POST["params"];
	if(sizeof($ids)){
		if($type == "lists"){
			$table = "scraper_productlist";
		}
		else if($type == "products"){
			$table = "scraper_product";
		}
		else if($type == "pages"){
			$table = "scraper_page";
		}
		
		$lists = getBrickData("$table",false,"*",false,$searchobject,$connection);
		if(sizeof($lists) && is_array($lists)){
			foreach($lists as $list){
				if(!in_array($list["id"],$ids)) continue;
				$fields = false;
				$fields["statusApi"] = 'not_uploaded';
				$out["updated"][] = saveBrickData("$table",$fields,false,$list["id"],$connection);
			}
		}
		
	}
	
	print json_encode($out);
	die();
	
}

if (isset($_POST["mode"]) && $_POST["mode"] == "removeItems") {
	$out["error"] = false;
	$out["data"] = array();
	$type = $_POST["type"];
	$ids = $_POST["params"];
	if(sizeof($ids)){
		if($type == "lists"){
			$table = "scraper_productlist";
		}
		else if($type == "products"){
			$table = "scraper_product";
		}
		else if($type == "pages"){
			$table = "scraper_page";
		}
		if(sizeof($ids) && is_array($ids)){
			foreach($ids as $id){
				$out["deleted"][] = deleteBrick($id,"$table",true,$connection);
			}
		} 
		
	}
	
	print json_encode($out);
	die();
	
}


if (isset($_POST["mode"]) && $_POST["mode"] == "queueItems") {
	$out["error"] = false;
	$out["data"] = array();
	$type = $_POST["type"];
	$ids = $_POST["id"];
	if(!empty($_POST["params"])){
		$ids = $_POST["params"];
	}
	
	if(sizeof($ids)){
		if($type == "lists"){
			$table = "scraper_productlist";
		}
		else if($type == "products"){
			$table = "scraper_product";
		}
		else if($type == "pages"){
			$table = "scraper_page";
		}
		$trId = $_SESSION["cId"];
		$lists = getBrickData("$table",false,"*",false,$searchobject,$connection);
		
		
		if($type == "lists"){
			$Tree = new ElmTree();
			$listTree = $Tree->buildTree($lists,$_SESSION["cId"]);
			$ids = array();
			foreach($listTree as $list){
				$ids[] = $list["id"];
				if(isset($list["children"])){
					$new =  $Tree->combineData($list["children"],$list["id"]);
					$array1 = array_map("trim",$ids);
					$array2 = array_map("trim",$new);
					$ids = array_unique(array_merge_recursive($array1,$array2),SORT_REGULAR);
				}
			}
			
			/* 
			print count($lists);
			print count($ids);
			exit();  */
		}
		
		
		
		
		if(sizeof($lists) && is_array($lists)){
			foreach($lists as $list){
				if(!in_array($list["id"],$ids)) continue;
				$fields = false;
				$fields["statusApi"] = 'not_uploaded';
				if($type == "lists"){
					$fields["uploadCollectionToShopify"] = 'true';
				}
				if($type == "products"){
					$fields["uploadProductToShopify"] = 'true';
				}
				$out["updated"][] = saveBrickData("$table",$fields,false,$list["id"],$connection);
			}
		}
		
	}
	
	print json_encode($out);
	die();
	
}
