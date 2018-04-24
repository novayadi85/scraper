<?
	session_start();
	header('Access-Control-Allow-Origin: *');
	require_once($_SERVER["DOCUMENT_ROOT"]."/backend/inc/db/connection.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/backend/system/bricks/adminFunctions.php");

	//$elements = new Elements($connection);
	
	
	if (isset($_POST["mode"]) && $_POST["mode"] == "removeElement") {
		if(isset($_POST["id"]) && is_numeric($_POST["id"])){
			$elements->deleteElement($_POST["id"]);
			print json_encode(array("error" => false));
		}
		else{
			print json_encode(array("error" => true));
		}
	}
	
	if (isset($_POST["mode"]) && $_POST["mode"] == "getScraperOption") {
		$out["error"] = false;
		$out["data"] = array();
		$parent = false;
		if(isset($_POST["parentId"])){
			$parent = $_POST["parentId"];
			$_SESSION["cId"] = $parent;
			if(!$parent){
				unset($_SESSION["cId"]);
			}
		}
		else{
			unset($_SESSION["cId"]);
		}

		$scrapers = getBrickData("scraper",false,"*",$parent,false,$connection);
		if(!empty($scrapers)){
			foreach ($scrapers as $scraper) {
				$out["data"][] = array(
					"id" => $scraper["id"],
					"name" => $scraper["name"],
				);
			}
		}
		
		$productLists = getBrickData("scraper_productlist",false,"*",$parent,false,$connection);		
		if(!empty($productLists)){
			foreach ($productLists as $productList) {
				$out["lists"][] = array(
					"id" => $productList["id"],
					"name" => $productList["name"],
				);
			}
		}
		
		print json_encode($out);
	}
	
	if (isset($_POST["mode"]) && $_POST["mode"] == "getCustomerOption") {
		$out["error"] = false;
		$out["data"] = array();
		$searchobject = false;
		$searchobject[] = array(
			"fieldname" => "deleted",
			"searchtype" => "=",
			"value" => "false"
		);
		
		$searchobject[] = array(
			"fieldname" => "developerMode",
			"searchtype" => "=",
			"value" => "true"
		);
		
		$customers = getBrickData("customer",false,"*",false,$searchobject,$connection);
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
	
	if (isset($_POST["mode"]) && $_POST["mode"] == "getScraper") {
		$out["error"] = false;
		$out["data"]["name"] = "";
		$out["data"]["parentData"]["tag"] = "";
		$out["data"]["parentData"]["label"] = "";
		$out["data"]["data"][0]["tag"] = "";
		$out["data"]["data"][0]["label"] = "";
		$out["data"]["data"][0]["type"] = "text";
		$out["data"]["data"][0]["attr"] = "";
		$headData = false;
		$stdAttributes = getAttributesForBrick("scraper_product",$connection);
		foreach($stdAttributes as $stdAttribute){
			if($stdAttribute == "name") continue; 
			if($stdAttribute == "href") continue; 
			if($stdAttribute == "product") continue; 
			
			$attr = ""; 
			$type = "text";
			$tag = "";
			if("metaDescription" == $stdAttribute){
				$attr = "content";
				$type = "attr";
				$tag = "meta[name=\"description\"]";
			}
			
			if("title" == $stdAttribute){
				$tag = "title";
			}
			
			if("canonical" == $stdAttribute){
				$tag = "link[rel=\"canonical\"]";
				$attr = "href";
				$type = "attr";
			}
			
			$headData[] = array(
				"attr" => $attr,
				"label" => $stdAttribute,
				"tag" => $tag,
				"type" => $type,
			);
		}
		

		if (!empty($_POST["id"])) {
			//list($scraper) = $elements->getElementData("sl_scraper",$_POST["id"],"*");
			list($scraper) = getBrickData("scraper",$_POST["id"],"*",false,false,$connection);
			
			if (!empty($scraper)) {
				$scraper["headData"] = json_decode($scraper["headData"]);
				$scraper["parentData"] = json_decode($scraper["parentData"]);
				$scraper["data"] = json_decode($scraper["data"]);
				$scraper["tags"] = json_decode($scraper["tags"]);
				$scraper["element"] = json_decode($scraper["element"]);
				$out["data"] = $scraper;
				if(file_exists($_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Scraper/import/data/".$_POST["id"].".json")){
					$contents = $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Scraper/import/data/".$_POST["id"].".json";
					$out["output"] = file_get_contents($contents);
				}
				 
			}
		}
		else{
			$scraper = array();
			$scraper["headData"] = $headData;
			/* $scraper["data"] = array();
			$scraper["parentData"] = array();
			$scraper["element"] = array(); */
			$out["data"] = $scraper;
		}

		print json_encode($out);
	}
	
	if (isset($_POST["mode"]) && $_POST["mode"] == "saveScraper") {
		header('Content-Type: application/json; charset=utf-8');
		$out["error"] = false;
		$id = $_POST["id"];
		$name = $_POST["name"];
		$url = $_POST["url"];
		$head = $_POST["head"];
		$element = $_POST["element"];
		$parentData = $_POST["parentData"];
		$data = $_POST["data"];
		$tags = $_POST["tags"];
		$type = $_POST["typeScraper"];
		$note = $_POST["note"];
		$pagination = $_POST["pagination"];
		
		/* print_r($_POST["element"]);
		print_r($_POST["head"]); */
		
		$fields = false;
		$fields["name"] = $name;
		$fields["urls"] = $url;
		$fields["headData"] = json_encode($head);
		$fields["parentData"] = json_encode($parentData);
		$fields["data"] = json_encode($data);
		$fields["tags"] = json_encode($tags);
		$fields["element"] = json_encode($element);
		$fields["type"] = $type;
		$fields["note"] = $note;
		$fields["paginationAttributes"] = json_encode($pagination);
		$fields["unique_attribute"] = $_POST["unique_attribute"];
		$parent = $_POST["parentId"];
		$outputs = false; $brickData = array();
		$unique = $_POST["unique_attribute"];
		if($unique == ""){
			$unique = "href";
		}
		
		if($type == "shopify_product"){
			$table = "scraper_product";
			$brickData = getBrickData($table,false,array("$unique"),$parent,false,$connection);
			$outputs = json_decode($_POST["output"],true);
			$schema = getAttributesForBrick($table,$connection);
		}
		else if($type == "shopify_productlist"){
			$table = "scraper_productlist";
			$brickData = getBrickData($table,false,array("$unique"),$parent,false,$connection);
			$outputs = json_decode($_POST["output"],true);
			
			$schema = getAttributesForBrick($table,$connection);
		}
		
		if(sizeof($brickData)){
			foreach($brickData as $key => $brick){
				if(!empty($brick["$unique"])){
					$brickData[$brick["$unique"]] = $brick["id"];
				}
				unset($brickData[$key]);
			}
		}
		
		if (!empty($id)) {
			$out["id"] =  saveBrickData("scraper",$fields,$parent,$id,$connection);
		}
		else {
			$out["id"] =  saveBrickData("scraper",$fields,$parent,false,$connection);
		}
		
		//if($outputs && sizeof($outputs)){
		if(isset($_POST["output"]) && !empty($_POST["output"])){
			/* foreach($outputs as $output){
				if(is_array($output)){
					$insert = array();
					foreach($output as $key => $option){
						if(!is_array($option)){
							if(in_array($key,$schema)){
								$insert["$key"] = trim(($option));
							} 
							if( $key == "title"){
								$insert["name"] = trim(($option));
							}
							
							if( $key == "url"){
								$insert["href"] = $option;
							}
						}
						else{
							if(in_array($key,$schema)){
								$insert["$key"] = json_encode($option);
							}
						}
					}

					if(sizeof($insert)){
						$id = false;
						if(isset($brickData[$insert["$unique"]])){
							$id = $brickData[$insert["$unique"]];
						}
						saveBrickData("$table",$insert,$parent,$id,$connection);
					}
				}
				
				
			} */
			
			//save in file
			$path = $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Scraper/import/data/";
			try{
				$filesave = fopen($path . $out["id"] . ".json", "w");;
				fwrite($filesave, $_POST["output"]);
			}
			catch(Exception $e){
				
			}
		}
		print json_encode($out);
	}
	
	if (isset($_POST["mode"]) && $_POST["mode"] == "preview") {
		$urls = $_REQUEST["urls"];
		$out = array();
		$out["urls"] = array();
		if(isset($_POST["pagination"]["params"]) && !empty($_POST["pagination"]["params"])){
			$param = $_POST["pagination"]["params"];
			$min = $_POST["pagination"]["min"];
			$max = $_POST["pagination"]["max"];
			$extension = $_POST["pagination"]["extension"];
			foreach ($urls as $key => $url) {
				$i = "";
				if($key <= 1){
					$out["urls"][$key] = $url;
					$path = parse_url($url, PHP_URL_PATH);
					$url = str_replace($path,"",$url);
					if(isset($extension)  && !empty($extension)){
						$path = str_replace(".".$extension,$i,$path);
					}
					
					for($i = $min; $i <= $max; $i++ ){
						$page = str_replace("%%number%%",$i,$param);
						$page = str_replace("%%path%%",$path,$page);
						$out["urls"][] = $url . $page ;	
					}
				}	
			}
		}
		$out["title"] = "<h3>This data is a preview of your first url and the paginations</h3>";
		$out["urls"] = implode("<br>",$out["urls"]);
		print json_encode($out);
		
	}
	
	if (isset($_POST["mode"]) && $_POST["mode"] == "historyElement") {
		$out["error"] = false;
		print json_encode($out);
	}
	
	if (isset($_POST["mode"]) && $_POST["mode"] == "getPending") {
		$searchobject = false;
		 /* $searchobject[] = array(
			"fieldname" => "status",
			"searchtype" => "=",
			"value" => "1"
		);  */
		$lists = getBrickData("productsToScrape",false,"*",false,$searchobject,$connection);			
		//print_r($lists);
		$data = array();
		$print = array();
		if(sizeof($lists)){
			$no = 0;
			
			foreach($lists as $list){
				$print[] = "<tr>";
				$print[] = "<td>$no</td>";		
				$print[] = "<td><span class=\"linkId\" data-url=\"".$list["link"]."\">".$list["link"]."</span></td>";
				$print[] = "<td>".$list["lastUpdateDate"]."</td>";	
				$print[] = "<td></td>";	
				$print[] = "</tr>";
				$no++;
			}
			
		}
		print json_encode(array("data" => join($print)));
	}
	
	if (isset($_POST["mode"]) && $_POST["mode"] == "setCustomerAsDev") {
		$_SESSION["cId"] = $_POST["selected"];
		list($customer) = getBrickData("customer",$_POST["selected"],"*",false,false,$connection);		
		$lists = getBrickData("scraper",false,"*",$_SESSION["cId"],$searchobject,$connection);
		$_SESSION["cName"] = $customer["name"];
		foreach($lists as $customerData){
			if($customerData["type"] == "shopify_productlist"){
				$_SESSION["customerData"]["shopify_productlist"][] = $customerData;
			}
			if($customerData["type"] == "shopify_product"){
				$_SESSION["customerData"]["shopify_product"][] = $customerData;
			}
		}
		echo json_encode(array("data" => $_SESSION["cId"])); 
	}
	
?>