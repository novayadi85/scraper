<?php 
/* include composer autoload */
include_once  $_SERVER["DOCUMENT_ROOT"]."/system/vendor/autoload.php";

$params = array();
//set customer id
$customerId = "EBRG-ZADF-6482-TTFZ-FCZZ-2917-QR";
$Api = new \App\Classes\ShopAdjust\Api($customerId);


//call products
/* $params = array(
	"ids" => "584324415548,584324350012"
);

$response = $Api->call("GET","/admin/products.json",$params);
print "<pre>";
print_r($response);
print "</pre>"; */


//call customers 
//https://help.shopify.com/api/reference/customer#index|
$params = array(
	"query" => "lars"
);
 $response = $Api->call("GET","/admin/customers.json",$params);
print "<pre>";
print_r($response);
print "</pre>"; 


//call orders 
//https://help.shopify.com/api/reference/order#index
$datetime = new DateTime("2018-01-01");
$created_min =  $datetime->format(DateTime::ATOM);

$datetime = new DateTime("2018-05-30");
$created_max =  $datetime->format(DateTime::ATOM);
/* $params = array(
	"created_at_min" => $created_min,
	"created_at_max" => $created_max
); */
$params = array(
	"email" => "lars@indosoft.dk"
);
$response = $Api->call("GET","/admin/orders.json",$params);
/* print "<pre>";
print_r($response);
print "</pre>"; */