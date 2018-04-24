<script src="/system/resources/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<?
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/inc/db/connection.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/system/bricks/adminFunctions.php");
$searchobject = false;


//$productsLists = getBrickData("scraper_productlist",false,"*",false,$searchobject,$connection);

$searchobject[] = array(
	"fieldname" => "srapeProduct",
	"searchtype" => "=",
	"value" => 'true'
);  


$productsToScrapes = getBrickData("productsToScrape",false,"*",false,$searchobject,$connection);


/* $customers = getBrickData("customer",false,"*",false,false,$connection);
$data = array();
foreach($customers as $customer){
	foreach($productsToScrapes as $productsToScrape){
		if($productsToScrape["customerId"] == $customer["id"]){
			$data[$customer["id"]]["urls"][] =  $productsToScrape["link"];
		}
	}
}
 */
$searchobject = false;

/* $searchobject[] = array(
	"fieldname" => "type",
	"searchtype" => "=",
	"value" => 'shopify_product'
);
 */
/* $scrapers = getBrickData("scraper",false,"*",false,$searchobject,$connection);


$data = array();

foreach($customers as $customer){
	foreach($scrapers as $scraper){
		if($scraper["parentid"] == $customer["id"]){
			$data[$customer["id"]]["scraper"][] =  $scraper;
		}
	}
	
	foreach($productsLists as $productsList){
		if($productsList["parentid"] == $customer["id"]){
			$data[$customer["id"]]["productsList"][] =  $productsList;
		}
	}
}
 */


$urls = array_slice($productsToScrapes, 0, 50);
print "<pre>";
print_r($productsToScrapes);
//print_r($productsToScrapes);
print "</pre>"; 
exit();

if(sizeof($urls) <= 0){
	exit();
} 

foreach($urls as $url){
	$urlToScrape[] = $url["link"];
}

/* foreach($data as $key => $dataToScrape){
	foreach($urls as $url){
		print_r($url["parentid"]);
		print "<br>";
		if(in_array($url["parentid"],$dataToScrape["productsList"])){
			$data[$key]["urls"][] = $url["link"];
		}
	} 
}




print "<pre>";
print_r($data);
print "</pre>"; 
exit();
 */
