<?
session_start();
header('Access-Control-Allow-Origin: *');
error_reporting(true);
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/inc/db/connection.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/system/bricks/adminFunctions.php");
include_once $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Scraper/import/helper/data.php";
include_once $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Shopify/Helper/Data.php";
include_once $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Scraper/import/helper/string.php";

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

/* function clean($string) {
	$string = strtolower($string);    
	$string = str_replace('-', '', $string);
    $string = str_replace(' ', '-', $string);

    return preg_replace('/[^A-Za-z0-9\-]/', '', $string); 
} */

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
	"MPN",
	"Google Age Group",
	"GoogleGender",
	"Google Google Product Category",
	"SEO Title",
	"SEO Description",
	"Google AdWords Grouping",
	"Google AdWords Labels",
	"Google Condition",
	"Google Custom Product",
	"Google Custom Label 0",
	"Google Custom Label 1",
	"Google Custom Label 2",
	"Google Custom Label 3",
	"Google Custom Label 4",
	"Variant Image",
	"Variant Weight Unit"
);

function json_decode_nice($json, $assoc = TRUE){
    $json = str_replace("\n","\\n",$json);
    $json = str_replace("\r","",$json);
    $json = preg_replace('/([{,]+)(\s*)([^"]+?)\s*:/','$1"$3":',$json);
    $json = preg_replace('/(,)\s*}$/','}',$json);
    return json_decode($json,$assoc);
}

/* function find_tags($collection , $query){
	if(is_array($collection) && sizeof($collection)){
		$tags = array();
		foreach($collection as $key => $val){
			if(in_array($query,$collection[$key])){
				$key = str_replace(" - REFANSHOP.DK","",$key);
				$tags[] = $key;
				//return $key;
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
} */

if($_REQUEST["mode"] == "showFields"){
	$html = "";
	if(is_array($_POST["th"])){
		foreach($shopify_format as $shopify){
			$disable = "";
			$shopifyKey = $shopify;
			if($shopify == "Handle" || $shopify == "Tags"){
				$disable = " disabled checked";
				$shopifyKey = strtolower($shopify);
				//temp[]
				if($shopify){
					$html .= "<div class=\"form-group\">
						<input $disable name=\"$shopifyKey\" type=\"checkbox\">
						<label class=\"control-label\">$shopify</label>
					</div>";
				}
				
			}
			//here
			
		}
		
		foreach($_POST["th"] as $th){
			$disable = " checked ";
			if($th == "handle" || $th == "tags"){
				continue;
			}
			if(!in_array($th,$shopify_format)){
				if(isset($_POST["thHidden"]) && in_array($th,$_POST["thHidden"])){
					$disable = " ";
				}
				$html .= "<div class=\"form-group\">
					<input $disable name=\"$th\" type=\"checkbox\">
					<label class=\"control-label\">$th</label>
				</div>";
			}
		} 
	}
	$out["html"] = $html;
	print json_encode($out);
	exit();
}

$var = $_POST["params"];

$options = '
<option value="">Undefined</option>
<option value="tags">Tags</option>
<option value="barcode">Barcode</option>
<option value="compare_at_price">Compare At Price</option>
<option value="handle">Handle</option>
<option value="src">Image URL</option>
<option value="option1_name">Option1 Name</option>
<option value="option1">Option1 Value</option>
<option value="option2_name">Option2 Name</option>
<option value="option2">Option2 Value</option>
<option value="option3_name">Option3 Name</option>
<option value="option3">Option3 Value</option>
<option value="price">Price</option>
<option value="body_html">Product Description</option>
<option value="title">Product Title</option>
<option value="product_type">Product Type</option>
<option value="metafields_global_description_tag">SEO Description</option>
<option value="metafields_global_title_tag">SEO Title</option>
<option value="sku">SKU</option>
<option value="variant_title">Variant Title (legacy)</option>
<option value="weight_unit">Variant Weight Unit</option>
<option value="vendor">Vendor</option>
<option value="inventory_quantity">Inventory</option>
<option value="grams">Weight in Grams</option></select>';

//<option value="variant_image">Variant Image</option>
if($_REQUEST["mode"] == "prepare_csv"){	
	if(!testShop()){
		$out["error"] = true;
		$out["msg"] = "invalid connection to API Shopify";
		print json_encode($out);
		exit();
	}	
	$collections = array();
	$searchobject = false;
	$customer =  $_POST["customer"];
	$_urls = $_POST["urls"];
	$parent =  $_POST["parent"];
	$key = $_POST["keys"];
	$value = $_POST["values"];
	if($_POST["type"] == "shopify_productlist"){
		$type = "collections";
	}
	else{
		$type = "tags";
	}
	$collections = array();
	$productLists = getBrickData("scraper_productlist",false,"*",$customer,$searchobject,$connection);
	$_urls = explode("\n",$_urls);
	$searchobject = false;
	/* $searchobject[] = array(
		"fieldname" => "href",
		"searchtype" => "in",
		"value" => $urls
	);  */
	
	$productsToScrapes = getBrickData("productsToScrape",false,'*',false,false,$connection);			

	$products = getBrickData("scraper_product",false,"*",$customer,$searchobject,$connection);
	$onlineProducts = getProducts();
	
	getMetafield("shortText");
	getMetafield("labelText");
	
	if(sizeof($productLists)){
		foreach($productLists as $productList){
			if(!empty($productList["productUrls"])){
				$productUrls = json_decode($productList["productUrls"],true);
				
				if(sizeof($productUrls)){
					foreach($productUrls as $k => $urls){
						if(sizeof($urls)){	
							if(!empty($productList["h1"])){
								$title = $productList["h1"];
								//$collections[$productList["h1"]] = $urls;							}
							}
							else{
								$title = $productList["title"];
								//$collections[$productList["title"]] = $urls;
							}
							
							if(isset($collections[$title])){
								$array1 = array_map("trim",$collections[$title]);
								$array2 = array_map("trim",$urls);
								$collections[$title] = array_unique(array_merge_recursive($array1,$array2),SORT_REGULAR);
							}
							else{
								$collections[$title] = $urls;
							}
							/* foreach($urls as $u => $url){
								
							} 	 */						
						}
					}
				}
			}
			 
		}
	} 	
	
	//tags
	if($type == "tags"){
		if(sizeof($products) && is_array($products)){
			foreach($products as $index => $product){
				if(!in_array($product["href"],$_urls)){
					//unset($products[$index]);
					//continue;
				}
					//$products[$product["handle"]] = $product;
					unset($products[$index]["product"]);
					$tagging = array();
					$newHandle = clean($product["title"]);
					if(isset($onlineProducts[$newHandle])){
						$tagging[] = $onlineProducts[$newHandle]["tags"];
					}
					
					//$tagging .= find_tags($collections,$product["href"]);
					$taggs =  findTags($productsToScrapes,$product["href"],"href",$tagging,false," , ");

					$products[$index][$type] = $taggs;
					$products[$index]["parent"] = str_replace("- REFANSHOP.DK","",$data["title"]);
					$products[$index]["handle"] = $newHandle;
				
				
			} 
		}
	}
	
	$scraper = $products;
	$stdAttributes = getAttributesForBrick("scraper_product",$connection);
	$stdAttributes[] = "tags";	
	$stdAttributes[] = "handle";	
	$array2csv = array();
	
	if(sizeof($scraper) && is_array($scraper)){
		foreach($scraper as $key => $item){
			foreach($item as $k => $v ){
				$v = str_replace("Se alle produkter fra ","",$v);
				
				if(isset($_POST["shopify"]) && $_POST["shopify"] == "true"){
					if(!in_array($k,$shopify_format)){
						continue;
					}
					else{
						if(is_array($v)){
							$v = json_encode($v);
						}
						
						$array2csv[$key][$k] = $v;
					}
				}
				else{
					if(!in_array($k,$stdAttributes)){
						continue;
					}
					else{
						if(is_array($v)){
							$v = json_encode($v);
						}
						if($k == "images"){
							if(is_array($v)){
								$v = json_encode($v);
							}
							else{
								if(!empty($v)){
									$tmpv = $v;
									$result = json_decode($v ,true);
									if ($result === FALSE) {
										$v = $v;
									}
									else{
										$v = $result[0];
									}
									
									if(empty($v)){
										$v = $tmpv;
									}
									
								}
								else{
									
								}
							} 
						}
						
						if($k == "price" || $k == "beforePrice" || $k == "offerPrice" || $k == "comparePrice"){
							$v = preg_replace('/\D/', '', $v) / 100;
							if($v/100 < 0){
								$v = "";
							}
						}
						if($k == "spConfig"){
							$v = stripslashes($v);
							$json = str_replace('["{','{',$v);
							$json = str_replace('}"]','}',$json);
							$v = $json;
						}
						
						
						$array2csv[$key][$k] = $v;
					}
					
				} 	
			}
			
		}
	}
	
	
	
	//print_r($array2csv);
	
	
	$_SESSION["json_scraper"] = $array2csv;
	list($headers) = $array2csv;
	
	
	
	$out = array();
	$table = array();
	$out["table"] = false;
	$out["fields"] = array_keys($headers);
	
	if(!in_array("tags",$out["fields"])){
		$out["fields"][] = "tags";	
		
	}
	
	if(!in_array("handle",$out["fields"])){
		$out["fields"][] = "handle";	
	}

	if(is_array($array2csv)){
		$table[] = "<tr>";
			foreach($out["fields"] as $header){
				$width = "";
				if($header == "tags" || $header == "handle"){
					$width = "20%";
				}
				$table[] = "<th class=\"$header\" width=\"$width\"><label>".$header."</label>";
				$table[] = "<select class=\"form-control header-select-$header\" name=\"header[$header]\">";
					$table[] = $options;
				$table[] = "</select>";
				$table[] = "</th>";
			}
		$table[] = "</tr>";
		foreach($array2csv as $th => $tds){
			if(is_array($tds)){
				$table[] = "<tr>";
				foreach($tds as $key => $td){
					
					if($key == "tags" || $key == "handle"){
						$table[] = "<td class=\"$key\"><textarea name=\"fields[$th][$key]\" class=\"form-control\">".$td."</textarea></td>";
					}
					else if($key == "shortText" || $key == "labelText" || $key == "spConfig" || $key == "textArea_1" || $key == "textArea_2"){
						$table[] = "<td class=\"$key\"><textarea name=\"fields[$th][$key]\" readonly class=\"form-control\">$td</textarea></td>";
					}
					
					else{
						$table[] = "<td class=\"$key\">".$td."<input type=\"hidden\" name=\"fields[$th][$key]\" value=\"$td\"></td>";
					}
					
				}
				$table[] = "</tr>";
			}
			
		}
	}
	$out["json"] = $array2csv;
	$out["table"] = join($table);
	
	echo json_encode($out);
	
}

if($_REQUEST["mode"] == "prepare_csv1"){
	//$scraper = json_decode($_POST["params"],true);
	$scraper = '';
	$collection = json_decode($_POST["collection"],true);
	
	if(sizeof($scraper)<= 0 || !is_array($scraper)){
		$scraper = $_POST["json_data"];
	}
	
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
	
	$onlineProducts = getProducts();
	$searchobject = false;
	$parentId = $_REQUEST["formId"];
	
	$products = getBrickData("product",false,"*",$parentId,$searchobject,$connection);
	//$productLists = getBrickData("scraper_productlist",false,"*",$parentId,$searchobject,$connection);
	
	if(sizeof($products) && is_array($products)){
		foreach($products as $k => $product){
			$products[$product["handle"]] = $product;
			unset($products[$k]);
		} 
	}
	
	
	/* if(sizeof($productLists)){
		foreach($productLists as $productList){
			
		}
	} */

	
	
	if($type == "tags"){
		//$collection = array();
		if(sizeof($scraper) && is_array($scraper)){
			foreach($scraper as $index => $data){
				if(isset($data[$parent]) && is_array($data[$parent])){
					foreach($data[$parent] as $k => $val){
						$tagging = array();
						$newHandle = clean($data[$parent][$k]["title"]);
						if(isset($onlineProducts[$newHandle])){
							$tagging[] = $onlineProducts[$newHandle]["tags"];
						}
						
						$tagging .= find_tags($collection,$val[$value]);
						//$tags = findTags($productsToScrapes,$link,"href",array("Doom","Tes"),false," | ");

						$scraper[$index][$parent][$k][$type] = $tagging;
						$scraper[$index][$parent][$k]["parent"] = str_replace("- REFANSHOP.DK","",$data["title"]);
						$scraper[$index][$parent][$k]["handle"] = $newHandle;
						if(isset($val["handle"])){
							$scraper[$index][$parent][$k]["handle"] = clean($val["handle"]);
						}
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
	list($headers) = $array2csv;
	
	$out = array();
	$table = array();
	$out["table"] = false;
	$out["fields"] = array_keys($headers);
	
	if(!in_array("tags",$out["fields"])){
		$out["fields"][] = "tags";	
		
	}
	
	if(!in_array("handle",$out["fields"])){
		$out["fields"][] = "handle";	
	}
	
	if(is_array($array2csv)){
		$table[] = "<tr>";
			foreach($out["fields"] as $header){
				$width = "";
				if($header == "tags" || $header == "handle"){
					$width = "20%";
				}
				$table[] = "<th class=\"$header\" width=\"$width\"><label>".$header."</label>";
				$table[] = "<select class=\"form-control header-select-$header\" name=\"header[$header]\">";
					$table[] = $options;
				$table[] = "</select>";
				$table[] = "</th>";
			}
		$table[] = "</tr>";
		foreach($array2csv as $th => $tds){
			if(is_array($tds)){
				$table[] = "<tr>";
				foreach($tds as $key => $td){
					
					if($key == "tags" || $key == "handle"){
						$table[] = "<td class=\"$key\"><textarea name=\"fields[$th][$key]\" class=\"form-control\">".$td."</textarea></td>";
					}
					
					else{
						$table[] = "<td class=\"$key\">".$td."<input type=\"hidden\" name=\"fields[$th][$key]\" value=\"$td\"></td>";
					}
					
				}
				$table[] = "</tr>";
			}
			
		}
	}
	$out["json"] = $array2csv;
	$out["table"] = join($table);
	
	echo json_encode($out);
	
}

if($_REQUEST["mode"] == "getCSV"){
	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
	{ 
		//$_POST["collection"] = '{"label":"title","collection":{"GAVESÆT - REFANSHOP.DK":["Rose from Bulgaria gavesæt","Lavendel fra bulgarien gavesæt."],"Soyalys i glas - REFANSHOP.DK":["soyalys i glas"],"Refans sølvserie - REFANSHOP.DK":["Eau de parfume for hende. ","Parfume-deodorant for hende.","Shower-gel til hende","Hånd- og body-lotion","Eau de parfume til ham","Parfume-deodorant til ham","Shower-gel til ham."],"Varm grapefrugt - REFANSHOP.DK":["VG - Gel til brystopstramning ","VG - Opstrammende shower-skrub ","VG - Opstrammende body lotion","VG - natursæbe","Opstrammende gel til hele kroppen"],"Oliven kosmetikserien (ansigts dag og nat crem er parabenfri) - REFANSHOP.DK":["Anti-aging olivenshampoo","Anti-aging oliven-dagcreme","Foryngende oliven-natcreme","Anti-aging styrkende olivenhårmaske","Anti-aging fugtgivende hårspray","Anti-aging fugtgivende rensemælk","Naturlig olivensæbe","Anti-aging olivenshampoo","Anti-aging oliven-dagcreme","Foryngende oliven-natcreme","Anti-aging styrkende olivenhårmaske","Anti-aging fugtgivende hårspray","Anti-aging fugtgivende rensemælk","Naturlig olivensæbe"],"Mans Algebra serien - REFANSHOP.DK":["Summer Eau de Toilette ","Summer aftershave","Aftershave balm","Man´s Algebra Eau de toilette","Man´s Algebra after shave","Man´s Algebra Shower gel"],"Tetræ-kosmetikserien - REFANSHOP.DK":["Tetræ control gel","Tetræ ansigtsrense-gel","Tetræ skælshampoo til alle hårtyper","Tetræ skintonic til fedtet og rødmende hud","Tetræ forfriskende fodcreme","Tetræ peeling-ansigtsmaske","Antirødme roll-on","Tetræ naturlige fodsæbe"],"Refans garden - REFANSHOP.DK":["Davana Eau de Toilet","Fleur d\'oranger Eau de Toilet","Rosa Alba Eau de Toilet","Echrysantheme Eau de Toilet","Magnolia Eau de Toilet","Black Rose eau de Toilette for kvinder 50ml ","Rose Centifolia Eau de Toilette 50ml","Vanilla Sugar Eau de Toilette 50ml","Lucha Libre Eau de reaction 50ml","Coca Pura Eau de satisfaction 50ml","Ron Y Menta Eau de Temptation 50ml","Flor Negra Eau de Attraction 50ml","Viva la Revolution Eau de revolution, 50ml","Black Rose for Men Eau de Toilette, 50ml","Rosa Damascena Eau de Toilette 50ml"],"Rose fra Bulgarien - REFANSHOP.DK":["Rose from Bulgaria gavesæt"," Rose fra Bulgarien Shampoo shower gel","Fod-gel med naturligt rosenvand","Rosenvand","Ansigtscreme med naturligt rosenvand","Håndcreme med naturligt rosenvand ","Body deodorant med rosen vand","Skiveskåret toiletsæbe med rosenvand"],"Humle og salvie-serien - REFANSHOP.DK":["Mild og skummende rense gel","Ansigtstoner","Fugtighedscreme","T- zone"],"Bioenergi serien (parabenfri) - REFANSHOP.DK":["Bioenergi – balance","Bioenergi - opfriskende","Bioenergi - afslappende (carming)","Bioenergi – balance","Bioenergi - opfriskende","Bioenergi - afslappende (carming)"],"Kamille anti-rynke - REFANSHOP.DK":["Kamille anti-rynke øjenserie"],"Natur kollektion (de fleste er parabenfri) - REFANSHOP.DK":["Rose natursæbe 90g","Hav natursæbe 90g","Banan og yoghurt til udskæring.","Havbunds natur til udskæring.","Lavendel fodsæbe m. indstøbt natursvamp","Tea tree fodsæbe m. indstøbt natursvamp","Oliven sæbe i æske 100g","Middelhavs regn natur til udskæring.","Iris creme sæbe 95g","Orkide creme sæbe i gaveæske 95g","Figen Blad Sæbe 100g","Limonchelo natur til udskæring.","Linde (Tilia) natur til udskæring.","Flydende sæbe+salte fra Det Døde Hav","Flydende lavendelsæbe m. Provit. B5","Luksus sæbe - Rød og hvid vin i gaveæske","Mimosa sæbe","Æble og kanel natur til udskæring.","Solbær natur til udskæring.","Jasmin natur til udskæring.","Jordbærmiks natur til udskæring.","Vanilje og kokosnød natur til udskæring.","Vandmelon natur til udskæring.","Melon og abrikos natur til udskæring.","Liljekonval natursæbe lille","Mælk og havre natur til udskæring.","Rose garden natursæbe 85g skive med bandarole","Rose garden natur til udskæring.","Honning og mælk natur til udskæring.","Bitter mandel  til udskæring.","Holy lake muddersæbe","Bomuld og hvid te natursæbe til udskæring.","Grøn mandarin natur til udskæring.","Granatæble og papaya natur til udskæring.","Lavendel i gaveæske","Zdravez natursæbe 95g i gaveæske","Kokosnød og peanut natur til udskæring.","Heliotrope og Sandalwood til udskærring.","Lotus sæbe til udskærring.","Patchouli og bergamot natursæbe til udskærring.","Orange og rav natursæbe til udskærring.","Lavendel. natur, glycerinsæbe, 90g","Mango og orange glycerinsæbe. formstøbt 90g","Watermelon 90g","Jordbærmiks, 90g","lydende sæbe parabenfri med blomster eller frugtduft 330ml","Rose natursæbe 90g","Hav natursæbe 90g","Banan og yoghurt til udskæring.","Havbunds natur til udskæring.","Lavendel fodsæbe m. indstøbt natursvamp","Tea tree fodsæbe m. indstøbt natursvamp","Oliven sæbe i æske 100g","Middelhavs regn natur til udskæring.","Iris creme sæbe 95g","Orkide creme sæbe i gaveæske 95g","Figen Blad Sæbe 100g","Limonchelo natur til udskæring.","Linde (Tilia) natur til udskæring.","Flydende sæbe+salte fra Det Døde Hav","Flydende lavendelsæbe m. Provit. B5","Luksus sæbe - Rød og hvid vin i gaveæske","Mimosa sæbe","Æble og kanel natur til udskæring.","Solbær natur til udskæring.","Jasmin natur til udskæring.","Jordbærmiks natur til udskæring.","Vanilje og kokosnød natur til udskæring.","Vandmelon natur til udskæring.","Melon og abrikos natur til udskæring.","Liljekonval natursæbe lille","Mælk og havre natur til udskæring.","Rose garden natursæbe 85g skive med bandarole","Rose garden natur til udskæring.","Honning og mælk natur til udskæring.","Bitter mandel  til udskæring.","Holy lake muddersæbe","Bomuld og hvid te natursæbe til udskæring.","Grøn mandarin natur til udskæring.","Granatæble og papaya natur til udskæring.","Lavendel i gaveæske","Zdravez natursæbe 95g i gaveæske","Kokosnød og peanut natur til udskæring.","Heliotrope og Sandalwood til udskærring.","Lotus sæbe til udskærring.","Patchouli og bergamot natursæbe til udskærring.","Orange og rav natursæbe til udskærring.","Lavendel. natur, glycerinsæbe, 90g","Mango og orange glycerinsæbe. formstøbt 90g","Watermelon 90g","Jordbærmiks, 90g","lydende sæbe parabenfri med blomster eller frugtduft 330ml"],"Produktgruppe I - REFANSHOP.DK":["Produkt I","Produkt II"],"Sea SPA kosmetik - REFANSHOP.DK":["Sea ansigtstonic (til normal hud)","Sea shower gel (til normal hud)","Sea hår shampoo (til normal hud)"],"Lavendel from Bulgaria - REFANSHOP.DK":["Lavendel fra bulgarien gavesæt.","Shampoo shower gel","Ansigtscreme - Lavendel from Bulgaria","Håndcreme - Lavendel from Bulgaria","Fod-gel - Lavendel from Bulgaria","Sæbe - Lavendel from Bulgaria","Room spray - Lavendel from Bulgaria"],"Aromaterapi (parabenfri) - REFANSHOP.DK":["Mynte og dildfrø til udskæring.","Mynte og dild frø 90g","Æble og mynte til udskæring.","Rose til udskæring.","Mango og appelsin til udskæring.","Syren og nellike til udskæring.","Rosenblomst","Varm Grapefrugt til udskærring.","Cappuccino til udskæring","Ceder og bambus til udskæring","Figen og fresier til udskæring.","Sæben fra Den Hellige Sø","Lavendelsæbe i pakke","Queen rose sæbe","Svamp (rød) - Anti age","Svamp (gul) - Deodoriserende","Svamp (blå) - Forfriskende","Svamp (grøn) - Afslappende","Hjerte rose","Mynte og dildfrø til udskæring.","Mynte og dild frø 90g","Æble og mynte til udskæring.","Rose til udskæring.","Mango og appelsin til udskæring.","Syren og nellike til udskæring.","Rosenblomst","Varm Grapefrugt til udskærring.","Cappuccino til udskæring","Ceder og bambus til udskæring","Figen og fresier til udskæring.","Sæben fra Den Hellige Sø","Lavendelsæbe i pakke","Queen rose sæbe","Svamp (rød) - Anti age","Svamp (gul) - Deodoriserende","Svamp (blå) - Forfriskende","Svamp (grøn) - Afslappende","Hjerte rose"],"Lavendelfodserien - REFANSHOP.DK":["Afslappende lavendel-fodcreme","Afslappende lavendel-fod-gel","Afslappende lavendel-fodsæbe"],"Queen Rose - REFANSHOP.DK":["Queen Rose dag-essens","Queens rose nat-essens","Queens rose body lotion","Queens rose shower gel","Queens rose shampoo","Queens Rose ansigts- og body-mist","Queens Rose beskyttende læbebalsam","Mild sæbe til normal og sart hud"],"Aromaterapilys - REFANSHOP.DK":["Aromaterapilys - lavendel","Aromaterapilys - Queens Rose","Aromaterapilys - ylang-ylang"],"Parfumelys - REFANSHOP.DK":["Mælk og kanel","Æble og kanel","Bitter mandel","Kokosnød og hasselnød","Orienten","Tilia","Anti-tobakslys","Banan","Jasmin","Melon","Honning og mælk","Sort","Rød","Hvid","Gul","Stenlys med stenform i sort og grå","Rose","Fantasilys","Sweet Temptation","Stenlys- stor","Naturlys – kaffe","Rose - med gravering","Parfume candle Musk","soyalys i dåse","Parfumed candle Cedarwood","Parfumed candle Frangipani","Parfumed candle Liqueur","Parfumed candle Narcissus","Parfumed candle Gardenia","Bambuslys"],"Fresh Dew serien (parabenfri) - REFANSHOP.DK":["Figen og Fresier til udskæring","Ceder og Bambus til udskæring","Cappuccinosæbe til udskæring","Figen og Fresier til udskæring","Ceder og Bambus til udskæring","Cappuccinosæbe til udskæring"],"Peeling sæbe svampe (parabenfri) - REFANSHOP.DK":["Peeling sæbe svampe (med aromaterapi)","Peeling sæbesvamp økologiske med roser","Peeling sæbe svampe (med aromaterapi)","Peeling sæbesvamp økologiske med roser"],"Ansigtspleje serien - REFANSHOP.DK":["Exfollating face gel"],"Hånddesinfektion - REFANSHOP.DK":["Deep cleansing hand gel ","Deep cleansing hand gel "],"Sugar body scrub (parabenfri) - REFANSHOP.DK":["Sugar body scrub \" Rosa Alba\" ","Sugar body scrub \" Rosa Damascena\" 240g","Sugar body scrub \" I love you\" 240g","Sugar body scrub \" Japanese Cherry Blossom\" 240g","Sugar body scrub \"Wild Cherry\" 240g","Sugar body scrub \"Pink Grapefrugt\"\"","Sugar body scrub \" Hemp\" 240g","Sugar body scrub \" Rosa Alba\" ","Sugar body scrub \" Rosa Damascena\" 240g","Sugar body scrub \" I love you\" 240g","Sugar body scrub \" Japanese Cherry Blossom\" 240g","Sugar body scrub \"Wild Cherry\" 240g","Sugar body scrub \"Pink Grapefrugt\"\"","Sugar body scrub \" Hemp\" 240g"],"Paraben fri kosmetikserie (body and hand butter, shower cream og body mist) - REFANSHOP.DK":["Shower cream (paraben-fri)","Body cream-butter.","Hand cream-butter 75ml ","Body mist parabenfri","Shower cream (paraben-fri)","Body cream-butter.","Hand cream-butter 75ml ","Body mist parabenfri"],"Produkt rose (visse er økologiske) - REFANSHOP.DK":["Alkohol fri rose","Rosen vand","Eau de parfume Rose","Rose olie i en flaske","Økologisk rosen vand: Rosa Alba","Økologisk rosen vand: Rosa Damascena","Deodorant spray","Alkohol fri rose","Rosen vand","Eau de parfume Rose","Rose olie i en flaske","Økologisk rosen vand: Rosa Alba","Økologisk rosen vand: Rosa Damascena","Deodorant spray"],"Eau de cologne - REFANSHOP.DK":["Eau de cologne","Eau de cologne"],"Gavekort og Rejser - REFANSHOP.DK":["Gavekort","Reservering af lejligheder i Bulgarien"]}}';
		$scraper = json_decode($_POST["params"],true);
		$collection = json_decode($_POST["collection"],true);
		
		if(sizeof($scraper)<= 0 || !is_array($scraper)){
			$scraper = $_POST["json_data"];
		}
		
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

if($_REQUEST["mode"] == "get_history"){
	$histories	= getBrickData("csvfile",false,"*",false,false,$connection);
	$data = array();
	$table = array();
	if(sizeof($histories) && is_array($histories)){
		foreach($histories as $key => $history){
			$table[] = "<tr>";
				$table[] = "<td>".$history["id"]."</td>";
				//$table[] = "<td>".$history["name"]."</td>";
				$table[] = "<td>".$history["lastUpdateDate"]."</td>";
				$table[] = "<td>".basename($history["path"])."</td>";
				$table[] = "<td><a style=\"margin: 5px 0;\" data-elm=\"".$history["id"]."\" class=\"btn btn-success green\"><i class=\"fa fa-pointer\">Use</a></td>";
			$table[] = "</tr>";
		}
		
	}
	
	print json_encode(array("table" => join($table)));
	exit();
}

if($_REQUEST["mode"] == "load_csv"){
	$id = $_POST["id"];
	list($histories)	= getBrickData("csvfile",$id,"*",false,false,$connection);
	//$csvfile = file_get_contents($histories["path"]);
	$data = csv_to_array($histories["path"],",");
	list($headers) = $data;
	
	$out = array();
	$table = array();
	$out["table"] = false;
	$out["fields"] = array_keys($headers);
	
	if(!in_array("tags",$out["fields"])){
		$out["fields"][] = "tags";	
		
	}
	
	if(!in_array("handle",$out["fields"])){
		$out["fields"][] = "handle";	
	}
	
	if(is_array($data)){
		$table[] = "<tr>";
			foreach($out["fields"] as $header){
				$width = "";
				if($header == "tags" || $header == "handle"){
					$width = "20%";
				}
				$table[] = "<th class=\"$header\" width=\"$width\"><label>".$header."</label>";
				$table[] = "<select class=\"form-control header-select-$header\" name=\"header[$header]\">";
					$table[] = $options;
				$table[] = "</select>";
				$table[] = "</th>";
			}
		$table[] = "</tr>";
		foreach($data as $th => $tds){
			if(is_array($tds)){
				$table[] = "<tr>";
				foreach($tds as $key => $td){
					
					if($key == "tags" || $key == "handle"){
						$table[] = "<td class=\"$key\"><textarea name=\"fields[$th][$key]\" class=\"form-control\">".$td."</textarea></td>";
					}
					
					else{
						$table[] = "<td class=\"$key\">".$td."<input type=\"hidden\" name=\"fields[$th][$key]\" value=\"$td\"></td>";
					}
					
				}
				$table[] = "</tr>";
			}
			
		}
	}
	$out["json"] = $data;
	$out["table"] = join($table);
	
	echo json_encode($out);
}

if($_REQUEST["mode"] == "load_rules"){
	if(!testShop()){
		$out["error"] = true;
		$out["msg"] = "invalid connection to API Shopify";
		print json_encode($out);
		exit();
	}
	$collection = $_POST["collection"];
	$collections = json_decode($collection);
	$out = array();$options = "";$table ="";
	if($collections->collection){
		foreach($collections->collection as $key => $col){
			$title = str_replace(" - REFANSHOP.DK","",$key);
			$out["collections"][] = $title;
			$options .= "<option value=\"$title\">$title</option>";
		}
	}
	$html = '
		<form id="smart_collection">
		<input name="colection_id" value="" type="hidden">
		<div class="form-group row">
			<div class="col-md-6">
				<label>Collection</label><br>
				<select name="title" class="ui-select">
					'.$options.'
				</select>
			</div>
		</div>
			
		<div class="form-group row">
			<div class="col-md-6">
				<label>Column</label><br>
				<select class="ui-select" name="column">
					<option value="tag">Product tag</option>
				</select>
			</div>
			<div class="col-md-6">
				<label>Relation</label><br>
				<select width="100%" class="ui-select rule-relation" name="relation">
				  <option value="equals">is equal to</option>
				  <!--<option value="not_equals" >is not equal to</option>
				  <option value="greater_than" >is greater than</option>
				  <option value="less_than">is less than</option>
				  <option value="starts_with">starts with</option>
				  <option value="ends_with">ends with</option>
				  <option value="contains">contains</option>
				  <option value="not_contains">does not contain</option>-->
			  </select>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-md-12">
				<label>Condition (tags)</label><br>
				<input name="condition" type="text" class="form-control">
				<span class="help">Use comma to made multiple tag conditions, eq: Apple, Microsoft, Google</span>
			</div>
		</div>
		</form>
	';
	$smart_collection = getCollections();
	//print_r($smart_collection);
	$list = '';
	if(sizeof($smart_collection)){
		$table .="<hr><h2>List Collections</h2><table  class=\"table display\" cellspacing=\"0\" width=\"100%\">";
			$table.= "<tr>";
				$table.= "<th>#ID</th>";
				$table.= "<th>Title</th>";
				$table.= "<th>Handle</th>";
				$table.= "<th>Product conditions</th>";
				$table.= "<th style=\"width:100px;text-align:right;\">Action</th>";
			$table.= "</tr>";
			$format = 'Product %s for %s to %s';
			foreach($smart_collection as $_collection){
				$productCondition = " - ";
				$tags = array();
				$list .= '<li class="dd-item" data-cid="'.$_collection["id"].'" data-handle="'.$_collection["handle"].'">
					<div class="dd-handle">'.$_collection["title"].'</div>';
				if(isset($_collection["rules"]) && sizeof($_collection["rules"])){
					
					foreach($_collection["rules"] as $rule){
						$arrTags = explode(",",$rule["condition"]);
						$tags = array_merge($tags,$arrTags);
						$productCondition = "";
						foreach($_collection["rules"] as $rule){
							$productCondition .= sprintf($format,$rule["column"],$rule["relation"],$rule["condition"]) . "<br>";
						}
					}
					$list .= '<span  data-elm="'.$_collection["id"].'" class="remove-collection glyphicon glyphicon-remove" style="right:2px; top:7px; z-index:10; position:absolute; cursor:pointer;"></span>';
					$list .= '<span  data-elm="'.$_collection["id"].'" class="edit-collection glyphicon glyphicon-edit" style="right: 20px;top:7px;z-index:10;position:absolute;cursor:pointer;"></span>';
					
					if(sizeof($tags) && !empty($tags)){
						$list .= "<ol class=\"dd-list\">";
						foreach($tags as $tag){
							$list .= '<li data-tags="'.$tag.'" class="dd-item">
								<div class="dd-handle">Tags : '.$tag.'</div>';
							$list .= '</li>'; 
						}
						$list .= '</ol>';
					}	
				}
				
				$list .= '</li>';
				
				
				$table.= "<tr>";
					$table .= "<td>".$_collection["id"]."</td>";
					$table .= "<td>".$_collection["title"]."</td>";
					$table .= "<td>".$_collection["handle"]."</td>";
					$table .= "<td>".$productCondition."</td>";
					$table .= "<td>
					<a style=\"margin: 5px 0;\" data-elm=\"".$_collection["id"]."\" class=\"pull-right edit-collection btn btn-primary success\"><i class=\"fa fa-pencil\"></i> </a>&nbsp;
					<a style=\"margin: 5px 0;\" data-elm=\"".$_collection["id"]."\" class=\"pull-right remove-collection btn btn-primary red\"><i class=\"fa fa-trash\"></i></a></td>";
				$table .= "</tr>";
			}
		$table .="</table>";
	}
	else{
		$list .= '<li class="dd-item">
					<div class="dd-handle">Home</div>
				</li>';
	}
	
	$products = getProducts();
	$productlist = "";
	if(sizeof($products)){
		foreach($products as $product){
				$productCondition = " - ";
				$productlist .= '<li class="dd-item" data-tags="'.$product["tags"].'" data-id="'.$product["title"].'">
					<div class="dd-handle">'.$product["title"].'</div>
				</li>';
				
		}
	}
	
	
	$html .='<h3>Arrange Rules</h3><hr>';
	$html .='<div class="row"><div class="col-md-5">
		<div class="portlet box green ">
		<div class="portlet-title">
			<div class="caption">
				Products
			</div>
		</div>
		<div class="portlet-body" style="position:relative;">
		<div class="dd" id="nestable_list_products">
			<ol class="dd-list">'.$productlist.'</ol>
		</div></div></div></div>';
	$html .='<div class="col-md-6">
				<div class="portlet box green ">
					<div class="portlet-title">
						<div class="caption">
							Collections
						</div>
					</div>
					<div class="portlet-body" style="position:relative;">
						<div class="dd" id="nestable_list_pages">
							<ol class="dd-list">'.$list.'</ol>
						</div>
					</div>
				</div>
			</div>
		</div>';

	//$html .= $table;
	$out["html"] = $html;
	echo json_encode($out);
}

if($_REQUEST["mode"] == "create_collection"){
	
	if(!testShop()){
		$out["error"] = true;
		$out["msg"] = "invalid connection to API Shopify";
		print json_encode($out);
		exit();
	}
	
	$out = array();
	$params = array();
	parse_str($_REQUEST["params"],$params);
	$response = create_collection($params,$params["colection_id"]);
	$out = json_decode($response,true);
	echo json_encode($out);
}


if($_REQUEST["mode"] == "edit_collection"){
	if(!testShop()){
		$out["error"] = true;
		$out["msg"] = "invalid connection to API Shopify";
		print json_encode($out);
		exit();
	}
	
	if(is_numeric($_POST["id"])){
		$response = getCollections($_POST["id"]);
		//$out = $response;
		$tags = array();
		if(isset($response["smart_collection"])){
			if(sizeof($response["smart_collection"]["rules"])){
				foreach($response["smart_collection"]["rules"] as $rules){
					$tags[] = $rules["condition"];
				}
			}
			$out["selected"] = $response["smart_collection"]["title"];
			$out["title"] = "<option selected value=\"".$response["smart_collection"]["title"]."\">".$response["smart_collection"]["title"]."</option>";
			$out["id"] = $response["smart_collection"]["id"];
		}
		$out["tags"] = implode(",",$tags);
		echo json_encode($out);
	}
	
}

if($_REQUEST["mode"] == "delete_collection"){
	if(!testShop()){
		$out["error"] = true;
		$out["msg"] = "invalid connection to API Shopify";
		print json_encode($out);
		exit();
	}
	
	$out = array();
	if(is_numeric($_POST["id"])){
		$response = delete_collection($_POST["id"]);
		$out = json_decode($response,true);
		echo json_encode($out);
	}
	else{
		$out["error"] = "true";
	}
	
}
if($_REQUEST["mode"] == "reloadPages"){
	
	if(!testShop()){
		$out["error"] = true;
		$out["msg"] = "invalid connection to API Shopify";
		print json_encode($out);
		exit();
	}
	
	//$smart_collection = getCollections();
	$out =array(); $response=array();
	$dataArrays = json_decode($_POST["listings"],true);
	//print_r($dataArrays);
	if(sizeof($dataArrays) && is_array($dataArrays)){
		foreach($dataArrays as $arr){
			if(!isset($arr["cid"])){
				$params = array("title" => $arr["name"]);

				$resP =  create_collection($params);
				$response["new"][] = $resP;
				$cid = json_decode($resP,true);
				if(isset($cid["smart_collection"])){
					$arr["cid"] = $cid["smart_collection"]["id"];
				}
				else{
					continue;
				}
			}
			
			if(isset($arr["children"]) && sizeof($arr["children"])){
				$tags = array();
				foreach($arr["children"] as $child){
					if(!empty($child["tags"])){
						//tags
						$arrTags = explode(",",$child["tags"]);
						$tags = array_merge($tags,$arrTags);

					}
					else{
						//title
						
					}
				}
				
				if(is_numeric($arr["cid"]) && sizeof($tags) && is_array($tags)){
					$tags = implode(",",$tags);
					$params = array(
						"title" => $arr["name"],
						"column" => "tag",
						"relation" => "equals",
						"condition" => $tags,
					);
					$response[$arr["cid"]] =  create_collection($params,$arr["cid"]);
				}
			}
		}
		
		
		
	}
	
	$smart_collection = getCollections();
	$list = "<ol class=\"dd-list testload\">";
	if(sizeof($smart_collection)){
		foreach($smart_collection as $_collection){
			$tags = array();
			$list .= '<li class="dd-item" data-cid="'.$_collection["id"].'" data-handle="'.$_collection["handle"].'">
				<div class="dd-handle">'.$_collection["title"].'</div>';
			if(isset($_collection["rules"]) && sizeof($_collection["rules"])){
				
				foreach($_collection["rules"] as $rule){
					$arrTags = explode(",",$rule["condition"]);
					$tags = array_merge($tags,$arrTags);
				}
				$list .= '<span  data-elm="'.$_collection["id"].'" class="remove-collection glyphicon glyphicon-remove" style="right:2px; top:7px; z-index:10; position:absolute; cursor:pointer;"></span>';
				$list .= '<span  data-elm="'.$_collection["id"].'" class="edit-collection glyphicon glyphicon-edit" style="right: 20px;top:7px;z-index:10;position:absolute;cursor:pointer;"></span>';
				
				if(sizeof($tags) && !empty($tags)){
					$list .= "<ol class=\"dd-list\">";
					foreach($tags as $tag){
						$list .= '<li data-tags="'.$tag.'" class="dd-item">
							<div class="dd-handle">Tags : '.$tag.'</div>';
						$list .= '</li>'; 
					}
					$list .= '</ol>';
				}	
			}
			$list .= '</li>';
		}
	}
	$list .= "</ol>";
	$products = getProducts();
	$productlist = "<ol class=\"dd-list\">";
	if(sizeof($products)){
		
		foreach($products as $product){
				$productCondition = " - ";
				$productlist .= '<li class="dd-item" data-tags="'.$product["tags"].'" data-id="'.$product["title"].'">
					<div class="dd-handle">'.$product["title"].'</div>
				</li>';
				
		}
	}
	$productlist .= "</ol>";
	$out["collections"] = $list;
	$out["products"] = $productlist;
	$out["response"] = $response;
	print json_encode($out);
	exit();
}

function see_tree($arrays){
	$data = '<ul>';
	foreach($arrays as $key => $array){
		if(is_array($array)){
			if(!is_numeric($key)){
				$data .= "<li><hr><b>".$key."</b></li>";
			}
			$data .= "<li>";
			$data .= see_tree($array);
			$data .= "</li>";
		}
		else{
			if(!is_numeric($key)){
				$data .= "<li><hr><b>".$key."</b> : " . ($array) . "</li>";
			}
			else{
				$data .=  "<li>". ($array) . "</li>";
			}
			
		}
	}
	$data .= '</ul>';
	return $data;
}

function cleanText($str){
	$str = str_replace("Ñ" ,"&#209;", $str);
	$str = str_replace("ñ" ,"&#241;", $str);
	$str = str_replace("ñ" ,"&#241;", $str);
	$str = str_replace("Á","&#193;", $str);
	$str = str_replace("á","&#225;", $str);
	$str = str_replace("É","&#201;", $str);
	$str = str_replace("é","&#233;", $str);
	$str = str_replace("ú","&#250;", $str);
	$str = str_replace("ù","&#249;", $str);
	$str = str_replace("Í","&#205;", $str);
	$str = str_replace("í","&#237;", $str);
	$str = str_replace("Ó","&#211;", $str);
	$str = str_replace("ó","&#243;", $str);
	$str = str_replace("“","&#8220;", $str);
	$str = str_replace("”","&#8221;", $str);

	$str = str_replace("‘","&#8216;", $str);
	$str = str_replace("’","&#8217;", $str);
	$str = str_replace("—","&#8212;", $str);

	$str = str_replace("–","&#8211;", $str);
	$str = str_replace("™","&trade;", $str);
	$str = str_replace("ü","&#252;", $str);
	$str = str_replace("Ü","&#220;", $str);
	$str = str_replace("Ê","&#202;", $str);
	$str = str_replace("ê","&#238;", $str);
	$str = str_replace("Ç","&#199;", $str);
	$str = str_replace("ç","&#231;", $str);
	$str = str_replace("È","&#200;", $str);
	$str = str_replace("è","&#232;", $str);
	$str = str_replace("•","&#149;" , $str);

	$str = str_replace("¼","&#188;" , $str);
	$str = str_replace("½","&#189;" , $str);
	$str = str_replace("¾","&#190;" , $str);
	$str = str_replace("½","&#189;" , $str);

	return $str;
}

if($_REQUEST["mode"] == "see_data"){
	header('Content-Type: application/json; charset=utf-8');
	$print = array();
	$out = array("error" => false);
	$value = "";
	//$value = utf8_decode( $_POST["value"]);
	if(empty($value)){
		$value = $_POST["value"];
	}
	
	$_POST["value"] = json_decode($value,true);
	
	if(empty($_POST["value"])){
		$_POST["value"] = $_POST["postdata"];
	}
	
	
	if(!empty($_POST["value"]) && isset($_POST["value"])){
		$out["html"] = see_tree($_POST["value"]);
	} 
	print json_encode($out);
	
	exit();
}

if($_REQUEST["mode"] == "pre_save_data"){
	$_SESSION["output"] = $_POST["output"];
	print json_encode(array("error" => false));
}

if($_REQUEST["mode"] == "save_data"){
	$urlstomerge = array();
	$type = $_POST["typeScraper"];
	$outputs = $_POST["output"];
	if($_POST["output"] == "session"){
		$outputs = $_SESSION["output"];
	}
	$unique = $_POST["unique_attribute"];
	$parent = $_POST["parentId"];
	$customer = $_POST["parentId"];
	$parentData = $_POST["parentData"];
	if($unique == ""){
		$unique = "href";
	}
	
	$listPendingdata = array();
	$searchobject = false;
	$searchobject[] = array(
		"fieldname" => "deleted",
		"searchtype" => "=",
		"value" => 'false'
	);  
	$listPendings = getBrickData("productsToScrape",false,"*",false,$searchobject,$connection);			
	if(sizeof($listPendings)){
		foreach($listPendings as $key => $listPending){
			$listPendingdata[$listPending["link"]] = $listPending;
			$listPendingdata[$listPending["identifier"]][] = $listPending["link"];
			$listPendingdataCompare[$listPending["parentid"]][$listPending["link"]] = $listPending;
		}
	}

	if($type == "shopify_product"){
		$table = "scraper_product";
		$searchobject = false;
		/* 
		$searchobject[] = array(
			"fieldname" => "status",
			"searchtype" => "=",
			"value" => "0"
		);  
		*/
		//set 
		$searchobject = false;
		$brickData = getBrickData($table,false,"*",$parent,false,$connection);
		$schema = getAttributesForBrick($table,$connection);
	}
	else if($type == "shopify_productlist"){
		$table = "scraper_productlist";
		$brickData = getBrickData($table,false,"*",$parent,false,$connection);
		$schema = getAttributesForBrick($table,$connection);
	}
	else{
		$table = "scraper_page";
		$brickData = getBrickData($table,false,"*",$parent,false,$connection);
		$schema = getAttributesForBrick($table,$connection);
	}

	if(sizeof($brickData)){
		foreach($brickData as $key => $brick){
			if(!empty($brick["$unique"])){
				$brickData[$brick["$unique"]] = $brick;
			}
			unset($brickData[$key]);
		}
	}
	
	
	$productsToScraped = array();
	
	if($outputs && sizeof($outputs)){
		$dataInsert = array();
		if($type == "shopify_productlist"){
			$paginationUrls = array();
			
			foreach($outputs as $key => $output){
				$productImages = array();
				if(isset($output["paginationUrls"])){
					$paginationUrls = array_push_before($paginationUrls,$output["paginationUrls"],count($paginationUrls));
					$productUrls = $output["productUrls"];
					$output["productUrls"] = array();
					$output["productUrls"][$output["redirect"]] = $productUrls;
					foreach($outputs as $xoutput){
						if(!isset($xoutput["paginationUrls"])){
							if(in_array($xoutput["redirect"],$output["paginationUrls"])){
								/* 
								$productUrls = array(
									$xoutput["redirect"] => $xoutput["productUrls"]
								); 
								*/
								$output["productUrls"][$xoutput["redirect"]] = $xoutput["productUrls"];
							}
						}
					}	
				}
				
				//get content
				foreach($output as $contentHtml){
					if(!is_array($contentHtml)){
						$html = $contentHtml;
						$doc = new DOMDocument();
						$doc->loadHTML($html);
						$xpath = new DOMXPath($doc);
						$productImages[] = $xpath->evaluate("string(//img/@src)");
					}
				}

				
				if(!in_array($output["redirect"] , $paginationUrls)){
					$dataInsert[$output["redirect"]] = $output;
					$dataInsert[$output["redirect"]]["pageList"] = json_encode($productUrls);
					$dataInsert[$output["redirect"]]["imagesList"] = json_encode($productImages);
				}
				
			}
			/* print_r($paginationUrls);
			print_r(count($dataInsert));
			exit(); */
		}
		else if($type == "shopify_product"){
			$brickDataLists = getBrickData("scraper_productlist",false,"*",false,false,$connection);
			foreach($brickDataLists as $indx => $brickList){
				$brickDataLists[$brickList["id"]] = $brickList;
				unset($brickDataLists[$indx]);
			}
			
			foreach($outputs as $key => $output){
				//redirectUrls
				
				if(isset($dataInsert[$output["$unique"]])){
					//$dataInsert[$output["canonical"]] = $output;
					$dataInsert[$output["$unique"]]["redirectUrls"][] = $output["redirect"];
				}
				else{
					$dataInsert[$output["$unique"]] = $output;
					$dataInsert[$output["$unique"]]["redirectUrls"] = array($output["redirect"]);
				}
				
				if(isset($listPendingdata[$output["$unique"]])){
					if(is_array($listPendingdata[$output["$unique"]]) && !empty($listPendingdata[$output["$unique"]])){
						$AredirectUrls = array_unique($listPendingdata[$output["$unique"]]);
						$dataInsert[$output["$unique"]]["redirectUrls"] = $AredirectUrls;
					}
				}
				
				/* if(!isset($output["images"]) || empty($output["images"]) && !empty($output["baseImage"])){
					$dataInsert[$output["$unique"]]["images"] = $output["baseImage"];
				} */
				
			}
		}
		else{
			
			foreach($outputs as $key => $output){
				$productUrls = array();
				$productImages = array();
				if(isset($output["productUrls"])){
					foreach($output["productUrls"] as $xx => $url){
						if (filter_var($url, FILTER_VALIDATE_URL)) {
							$f = pathinfo($url, PATHINFO_EXTENSION);
							if((strlen($f) > 0)){
								if(@is_array(getimagesize($url))){
									$productImages[] = $url;
								}
								else{
									$productUrls[] = $url;
								}
							}
							else{
								$productUrls[] = $url;
							}
							
						}
						else{
							unset($output["productUrls"][$xx]);
						}
					}

					unset($output["productUrls"]);
				}
				
				if(isset($output["images"]) && !is_array($output["images"]) && filter_var($output["images"], FILTER_VALIDATE_URL)) {
					$f = pathinfo($output["images"], PATHINFO_EXTENSION);
					if((strlen($f) > 0)){
						if(@is_array(getimagesize($output["images"]))){
							$productImages[] = $output["images"];
						}
					}
					
				}
				
				$dataInsert[$output["redirect"]] = $output;
				$dataInsert[$output["redirect"]]["pageList"] = json_encode($productUrls);
				$dataInsert[$output["redirect"]]["imagesList"] = json_encode($productImages);
				
			}
		}
		
	}
	
	$out = array();
	if($dataInsert && sizeof($dataInsert)){
		foreach($dataInsert as $output){
			if(is_array($output)){
				$insert = array();
				foreach($output as $key => $option){
					if($key == "imagesList" && !empty($option)){
						$images = json_decode($option);
						if(is_array($images)){
							foreach($images as $image){
								$pathCustomer = $_SERVER["DOCUMENT_ROOT"]."/system/customer/".$customer;
								$path = $_SERVER["DOCUMENT_ROOT"]."/system/customer/".$customer."/images/";
								
								if(!is_dir($pathCustomer)){
									mkdir($pathCustomer, 0777, true);
									mkdir($path, 0777, true);
								}
								
								if(!is_dir($path)){
									mkdir($path, 0777, true);
								}

								try{
									$imageInfo = pathinfo($image);
									$filesave = fopen($path . $imageInfo['basename'], "w");;
									//if(!file_exists($path . $imageInfo['basename']))
									fwrite($filesave, file_get_contents($image));
								}
								catch(Exception $e){
									
								}
							}
						}
					}
					
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
						$id = $brickData[$insert["$unique"]]["id"];
					}
					
					if(isset($insert["redirectUrls"])){
						if(!empty($insert["redirectUrls"])){
							$insert["redirectUrls"] = str_replace("\\/", "/", $insert["redirectUrls"]);
							$insert["redirectUrls"] = json_encode($insert["redirectUrls"],true);
						}
						
						/* 
						if(is_array($insert["redirectUrls"])){
							if(!is_array($brickData[$insert["$unique"]]["redirectUrls"])){
								$brickData[$insert["$unique"]]["redirectUrls"] = json_decode($brickData[$insert["$unique"]]["redirectUrls"],true);
								$array1 = array_map("trim",$insert["redirectUrls"]);
								$array2 = array_map("trim",$brickData[$insert["$unique"]]["redirectUrls"]);
								$array = array_unique(array_merge_recursive($array1,$array2),SORT_REGULAR);
								$insert["redirectUrls"] = json_encode($array);
							}
						}
						*/
						
						//find all tags
						$parentData = $_POST["parentData"];
						if(isset($output[$parentData]) && sizeof($output[$parentData])){
							foreach($output[$parentData] as $k => $stags){
								if(empty($stags["images"]) && isset($stags["baseImage"])){
									$stags["images"] = $stags["baseImage"];
								}
								if(is_array($stags)){
									foreach($stags as $keyData => $tags){
										if(!is_array($tags)){
											if(in_array($keyData,$schema)){
												if(!isset($insert["$keyData"]))
												$insert["$keyData"] = trim(($tags));
												
												//
											} 
										}
										else{
											if(in_array($keyData,$schema)){
												if(!isset($insert["$keyData"]))
												$insert["$keyData"] = json_encode($tags);
											}
										}
									}
								}
							}
						}
						
					}
					
					if($id && $type == "shopify_productlist"){
						$productUrls = $brickData[$insert["$unique"]]["productUrls"];
						if(json_decode($productUrls) == true){
							$array1 = json_decode($productUrls,true);
						}
						if(!is_array($array1)){
							$array1 = array();
						}
					
						$array2 = json_decode($insert["productUrls"],true);
						$testUrls = array_merge_recursive($array1,$array2);
						$insert["productUrls"] = json_encode($testUrls);
						
						$bpaginationUrls = $brickData[$insert["$unique"]]["paginationUrls"];
						if(json_decode($bpaginationUrls) == true){
							$array3 = json_decode($bpaginationUrls,true);
						}
						
						if(!is_array($array3)){
							$array3 = array();
						}
						
						$array4 = json_decode($insert["paginationUrls"],true);
						$cpaginationUrls = array_merge_recursive($array3,$array4);
						if(count($cpaginationUrls)){
							$cpaginationUrls[] = $insert["href"];
						}
						
						$insert["paginationUrls"] = json_encode($cpaginationUrls);
						
						
						if(sizeof($_POST["noresult"])){
							$array5 = json_decode($insert["paginationUrls"],true);
							foreach($array5 as $kk => $paginationUrl){
								if(in_array($paginationUrl , $_POST["noresult"])){
									unset($array5[$kk]);
								}
							}
							
							$insert["paginationUrls"] = json_encode($array5);
						}
						
						
						
						
					}
					
					
					
					
					$insert["statusApi"] = "not_uploaded";
					$out["dataInsert"] = $dataInsert;
					//$out["insert"] = $insert;
					$out["countToInsert"] = count($dataInsert);
					$ids = saveBrickData("$table",$insert,$customer,$id,$connection);

					if($_REQUEST["saveLinks"] == "true"){
						
						if(is_array(($insert))){
							$dataIns = $insert;
							file_put_contents($_SERVER["DOCUMENT_ROOT"].'/system/logs/product.log', print_r($listPendingdata, true));
							//foreach($insert as $dataIns){
								if(!empty($dataIns["productUrls"])){
									$dataIns["productUrls"] = json_decode($dataIns["productUrls"],true);
									//print_r($dataIns["productUrls"]);
									if(is_array($dataIns["productUrls"])){
										foreach($dataIns["productUrls"] as $url){
											
											if(is_array($url)){
												foreach($url as $u){
													$insertLink = false;
													$insertLink["link"] = $u;
													$insertLink["status"] = 0;
													if(!isset($listPendingdataCompare[$ids][$u]) && !in_array($productsToScraped[$ids])){
														$productsToScraped[$ids][] = $u;
														$out["saveLinks"][] = saveBrickData("productsToScrape",$insertLink,$ids,false,$connection);			
													}
													else if(isset($listPendingdataCompare[$ids][$u]) && !in_array($productsToScraped[$ids])){
														$productsToScraped[$ids][] = $u;
														$insertLink["tags"] = "";
														$insertLink["identifier"] = "";
														$out["saveLinks"][] = saveBrickData("productsToScrape",$insertLink,$ids,$listPendingdataCompare[$ids][$u]["id"],$connection);
													}
													
													
												}
											}
											else{
												$insertLink = false;
												$insertLink["link"] = $url;
												$insertLink["status"] = 0;
												
												if(!isset($listPendingdataCompare[$ids][$url]) && !in_array($productsToScraped[$ids])){
													$productsToScraped[$ids][] = $url;
													$out["saveLinks"][] = saveBrickData("productsToScrape",$insertLink,$ids,false,$connection);			
												}
												else if(isset($listPendingdataCompare[$ids][$url]) && !in_array($productsToScraped[$ids])){
													$productsToScraped[$ids][] = $url;
													$insertLink["tags"] = "";
													$insertLink["identifier"] = "";
													$out["saveLinks"][] = saveBrickData("productsToScrape",$insertLink,$ids,$listPendingdataCompare[$ids][$url]["id"],$connection);
												}
											}
											
										}
									}
									
								} 
							//}
							
						}
						
						
					}
					
					$dataScraped = array();
					if($_REQUEST["changeStatus"] == "true"){
						foreach($dataInsert as $key => $dataInsert){
							if(!empty($dataInsert["redirect"])){
								if(!empty($dataInsert["canonical"])){
									$dataScraped[$dataInsert["redirect"]] = array(
										"canonical" => $dataInsert["canonical"],
										"unique" => $dataInsert["$unique"],
									);
								}
								else{
									$dataScraped[$dataInsert["redirect"]] = array(
										"canonical" => $dataInsert["redirect"],
										"unique" => $dataInsert["$unique"],
									);
									
								}
								
							}
							if (filter_var($key, FILTER_VALIDATE_URL)) {
								$urlstomerge[] = $key;
							}
						}
						
						
						
						foreach($listPendings as $pending){
							if(is_array($dataScraped) && isset($dataScraped[$pending["link"]])){
								$status = array(
									"status" => 0,
									"tags" => convertUtf8($brickDataLists[$pending["parentid"]]["h1"]),
									"identifier" => $dataScraped[$pending["link"]]["unique"],
									"href" => $dataScraped[$pending["link"]]["canonical"]
								);

								if(isset($brickDataLists[$pending["parentid"]])){
									
									$status = array(
										"status" => 1,
										"srapeProduct" => "false",
										"tags" => $brickDataLists[$pending["parentid"]]["h1"],
										"identifier" => $dataScraped[$pending["link"]]["unique"],
										"href" => $dataScraped[$pending["link"]]["canonical"]
									);
									
									$out["changeStatus"][] = saveBrickData("productsToScrape",$status,false,$pending["id"],$connection);
									
								}
								
								
								
								
							}
							
						}
						
					}
					
					
					$out["ids"][] = $ids;
					$out["table"][] = $table;
					$out["insert"][] = $insert;	
					$out["productsToScraped"] = $productsToScraped;	
					//print_r($productsToScraped);
				}
			}
			
			
		}
		
		//save in file
		/* $path = $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Scraper/import/data/";
		try{
			$filesave = fopen($path . $out["id"] . ".json", "w");;
			fwrite($filesave, $_POST["output"]);
		}
		catch(Exception $e){
			
		} */
	}
	
	print json_encode($out);
	
	exit();
}

if($_REQUEST["mode"] == "prepare_collection"){
	
	if(!testShop()){
		$out["error"] = true;
		$out["msg"] = "invalid connection to API Shopify";
		print json_encode($out);
		exit();
	}
	
	$collections = array();
	$productLists = getBrickData("scraper_productlist",false,"*",$customer,$searchobject,$connection);
	/* if(sizeof($productLists)){
		foreach($productLists as $productList){
			if(!empty($productList["productUrls"])){
				$productUrls = json_decode($productList["productUrls"],true);
				
				if(sizeof($productUrls)){
					foreach($productUrls as $k => $urls){
						if(sizeof($urls)){	
							if(!empty($productList["h1"])){
								$title = $productList["h1"];
								//$collections[$productList["h1"]] = $urls;							}
							}
							else{
								$title = $productList["title"];
								//$collections[$productList["title"]] = $urls;
							}
							
							if(isset($collections[$title])){
								$array1 = array_map("trim",$collections[$title]);
								$array2 = array_map("trim",$urls);
								$collections[$title] = array_unique(array_merge_recursive($array1,$array2),SORT_REGULAR);
							}
							else{
								$collections[$title] = $urls;
							}
													
						}
					}
				}
			}
			 
		}
	} */ 	
	//lookup collection
	$out = array();
	$options = "";
	$table ="";
	$collections = $productLists;
	if($collections){
		foreach($collections as $key => $col){
			if(empty($col["h1"])) $col["h1"] = $col["title"];
			$title = str_replace(" - REFANSHOP.DK","",$col["h1"]);
			$out["collections"][] = $title;
			$options .= "<option value=\"$title\">$title</option>";
		}
	}
	$html = '
		<form id="smart_collection">
		<input name="colection_id" value="" type="hidden">
		<div class="form-group row">
			<div class="col-md-6">
				<label>Collection</label><br>
				<select name="title" class="ui-select">
					'.$options.'
				</select>
			</div>
		</div>
			
		<div class="form-group row">
			<div class="col-md-6">
				<label>Column</label><br>
				<select class="ui-select" name="column">
					<option value="tag">Product tag</option>
				</select>
			</div>
			<div class="col-md-6">
				<label>Relation</label><br>
				<select width="100%" class="ui-select rule-relation" name="relation">
				  <option value="equals">is equal to</option>
				  <!--<option value="not_equals" >is not equal to</option>
				  <option value="greater_than" >is greater than</option>
				  <option value="less_than">is less than</option>
				  <option value="starts_with">starts with</option>
				  <option value="ends_with">ends with</option>
				  <option value="contains">contains</option>
				  <option value="not_contains">does not contain</option>-->
			  </select>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-md-12">
				<label>Condition (tags)</label><br>
				<input name="condition" type="text" class="form-control">
				<span class="help">Use comma to made multiple tag conditions, eq: Apple, Microsoft, Google</span>
			</div>
		</div>
		</form>
	';
	$smart_collection = getCollections();
	//print_r($smart_collection);
	$list = '';
	if(sizeof($smart_collection)){
		$table .="<hr><h2>List Collections</h2><table  class=\"table display\" cellspacing=\"0\" width=\"100%\">";
			$table.= "<tr>";
				$table.= "<th>#ID</th>";
				$table.= "<th>Title</th>";
				$table.= "<th>Handle</th>";
				$table.= "<th>Product conditions</th>";
				$table.= "<th style=\"width:100px;text-align:right;\">Action</th>";
			$table.= "</tr>";
			$format = 'Product %s for %s to %s';
			foreach($smart_collection as $_collection){
				$productCondition = " - ";
				$tags = array();
				$list .= '<li class="dd-item" data-cid="'.$_collection["id"].'" data-handle="'.$_collection["handle"].'">
					<div class="dd-handle">'.$_collection["title"].'</div>';
				if(isset($_collection["rules"]) && sizeof($_collection["rules"])){
					
					foreach($_collection["rules"] as $rule){
						$arrTags = explode(",",$rule["condition"]);
						$tags = array_merge($tags,$arrTags);
						$productCondition = "";
						foreach($_collection["rules"] as $rule){
							$productCondition .= sprintf($format,$rule["column"],$rule["relation"],$rule["condition"]) . "<br>";
						}
					}
					$list .= '<span  data-elm="'.$_collection["id"].'" class="remove-collection glyphicon glyphicon-remove" style="right:2px; top:7px; z-index:10; position:absolute; cursor:pointer;"></span>';
					$list .= '<span  data-elm="'.$_collection["id"].'" class="edit-collection glyphicon glyphicon-edit" style="right: 20px;top:7px;z-index:10;position:absolute;cursor:pointer;"></span>';
					
					if(sizeof($tags) && !empty($tags)){
						$list .= "<ol class=\"dd-list\">";
						foreach($tags as $tag){
							$list .= '<li data-tags="'.$tag.'" class="dd-item">
								<div class="dd-handle">Tags : '.$tag.'</div>';
							$list .= '</li>'; 
						}
						$list .= '</ol>';
					}	
				}
				
				$list .= '</li>';
				
				
				$table.= "<tr>";
					$table .= "<td>".$_collection["id"]."</td>";
					$table .= "<td>".$_collection["title"]."</td>";
					$table .= "<td>".$_collection["handle"]."</td>";
					$table .= "<td>".$productCondition."</td>";
					$table .= "<td>
					<a style=\"margin: 5px 0;\" data-elm=\"".$_collection["id"]."\" class=\"pull-right edit-collection btn btn-primary success\"><i class=\"fa fa-pencil\"></i> </a>&nbsp;
					<a style=\"margin: 5px 0;\" data-elm=\"".$_collection["id"]."\" class=\"pull-right remove-collection btn btn-primary red\"><i class=\"fa fa-trash\"></i></a></td>";
				$table .= "</tr>";
			}
		$table .="</table>";
	}
	else{
		$list .= '<li class="dd-item">
					<div class="dd-handle">Home</div>
				</li>';
	}
	
	$products = getProducts();
	$productlist = "";
	if(sizeof($products)){
		foreach($products as $product){
				$productCondition = " - ";
				$productlist .= '<li class="dd-item" data-tags="'.$product["tags"].'" data-id="'.$product["title"].'">
					<div class="dd-handle">'.$product["title"].'</div>
				</li>';
				
		}
	}
	
	$html .='<button type="button" class="btn blue saveCollection">Save</button>';
	$html .='<h3>Arrange Rules</h3><hr>';
	$html .='<div class="row"><div class="col-md-5">
		<div class="portlet box green ">
		<div class="portlet-title">
			<div class="caption">
				Products
			</div>
		</div>
		<div class="portlet-body" style="position:relative;max-height:600px;overflow-y:scroll;">
		<div class="dd" id="nestable_list_products">
			<ol class="dd-list">'.$productlist.'</ol>
		</div></div></div></div>';
	$html .='<div class="col-md-6">
				<div class="portlet box green ">
					<div class="portlet-title">
						<div class="caption">
							Collections
						</div>
					</div>
					<div class="portlet-body" style="position:relative;max-height:600px;overflow-y:scroll;">
						<div class="dd" id="nestable_list_pages">
							<ol class="dd-list">'.$list.'</ol>
						</div>
					</div>
				</div>
			</div>
		</div>';

	//$html .= $table;
	$out["html"] = $html;
	echo json_encode($out);
	
}



if($_REQUEST["mode"] == "get_List"){
	$id = $_POST["id"];
	list($productList) = getBrickData("scraper_productlist",$id,"*",false,$searchobject,$connection);
	$paginationUrls = (json_decode($productList["paginationUrls"], true));
	$paginationUrls = array_unique($paginationUrls);
	$productList["paginationUrls"] = $paginationUrls;
	$productList["pages"] = count($productList["paginationUrls"]);
	$html = array();
	$ceils = ceil($productList["pages"] / 10);
	$mods = $productList["pages"] % 10;
	$last = 0;
	for($ii=1; $ii <= $ceils; $ii++){
		if($ii == $ceils){
			$html[] = "<button data-url=\"".$productList["href"]."\" data-min=\"". $last."\" data-max=\"".($last + $mods)."\" style=\"margin: 0 5px;\" class=\"btn btn-scrapeList\">Page ".  $last ."-" . ($last + $mods)."</button>";
		}
		else{
			$html[] = "<button data-url=\"".$productList["href"]."\" data-min=\"". $last."\" data-max=\"".($ii * 10)."\" style=\"margin: 0 5px;\" class=\"btn btn-scrapeList\">Page ". $last ."-" . ($ii * 10) ."</button>";
		}
		
		$last = $last + 10;

	}
	
	echo json_encode(array("data" => $productList, "html" => join($html) ,"ceils" => $ceils ,"mods" => $mods  ) );
}
	
