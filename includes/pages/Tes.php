<?
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/inc/db/connection.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/system/bricks/adminFunctions.php");
	
//$products = getBrickData("productsToScrape",false,"*",false,$searchobject,$connection);			
/* $collections = array();
$searchobject = false;

$insert = false;
$insert["title"] = "Testing Jeppe 3";
print $id = saveBrickData("scraper_productlist",$insert,false,"UGMQ-SAFX-7337-ZIKR-PNUI-6648-RK",$connection);

print "<br>";
 */
/* $productLists = getBrickData("scraper_productlist",false,"*",false,$searchobject,$connection);			

if(sizeof($productLists) > 0 AND is_array($productLists)){
	foreach($productLists as $productList){

		if($productList["productUrls"]){
			
			print "<pre>";
				print_r(json_decode($productList["productUrls"],true));
			print "</pre>";
		}
	}
} */
/* $attributesArray2 = array();
$attributesArray2[] = array(
	"systemname"=>"identifier",
	"name"=>"identifier",
	"type"=>"text"
);	
$attributesArray2[] = array(
	"systemname"=>"tags",
	"name"=>"tags",
	"type"=>"text"
);	
createAttribute("productsToScrape",$attributesArray2,$connection);
 */
/* $attributesArray2 = array(); 
$attributesArray2[] = array(
	"systemname"=>"srapeProduct",
	"name"=>"srapeProduct",
	"type"=>"trueFalse"
);	


$attributesArray2[] = array(
	"systemname"=>"uploadProductToShopify",
	"name"=>"uploadProductToShopify",
	"type"=>"trueFalse"
);	

$attributesArray1 = array(); 
$attributesArray1[] = array(
	"systemname"=>"srapeProduct",
	"name"=>"srapeProduct",
	"type"=>"trueFalse"
);
$attributesArray1[] = array(
	"systemname"=>"customerId",
	"name"=>"customerId",
	"type"=>"string"
);	

createAttribute("scraper_product",$attributesArray2,$connection);
createAttribute("productsToScrape",$attributesArray1,$connection);

$attributesArray2[] = array(
	"systemname"=>"srapeProduct",
	"name"=>"srapeProduct",
	"type"=>"trueFalse"
);	

$attributesArray3 = array();
$attributesArray3[] = array(
	"systemname"=>"uploadCollectionToShopify",
	"name"=>"uploadCollectionToShopify",
	"type"=>"trueFalse"
);	

createAttribute("scraper_productlist",$attributesArray3,$connection);
 */
 
$datetime = new DateTime();
echo $datetime->format(DateTime::ATOM);
exit();
$params = array(
	"product" => array(
		"title" => "Burton Custom Freestyle1",
		"body_html"=> "<strong>Good snowboard!</strong>",
		"vendor"=> "Burton",
		"product_type"=> "Snowboard",
		"published_at" => "", 
		"variants" => array(
				array(
					"option1" => "Blue",
					"price" => "10.00",
					"sku" => "123"
				),
				array(
					"option1" => "Black",
					"price" => "20.00",
					"sku" => "123"
				)
			)
		),
		"options"=> array(
			array(
				"name" => "Color",
				"values" => array(
				  "Blue",
				  "Black"
				)
			)
		)
	);

$ch = curl_init("https://7bfff0cd775a3252fd7e666b3c8c75c0:7a3c839287613bff5c571dd9d9bd3386@heino-cykler.myshopify.com/admin/products.json ");
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
print_r($response);
 
?>