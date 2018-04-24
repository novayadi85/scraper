<?
/* $id = 12345;
file_put_contents($_SERVER["DOCUMENT_ROOT"].'/system/logs/local.log', $id . " , uploaded , ". date("Y-m-d H:i:s"). '\r\n' . PHP_EOL, FILE_APPEND);
	 */
$storeKey = "https://7bfff0cd775a3252fd7e666b3c8c75c0:7a3c839287613bff5c571dd9d9bd3386@heino-cykler.myshopify.com";
	 

$ch = curl_init($storeKey."/admin/redirects/count.json");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec ($ch);
$body = json_decode($response,true); 
print_r($body); 