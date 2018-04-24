<?
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/inc/db/connection.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/system/bricks/adminFunctions.php");
$searchobject = false;
$productsLists = getBrickData("scraper_productlist",false,"*",false,$searchobject,$connection);
print "<pre>";
print_r($productsLists);
print "</pre>";