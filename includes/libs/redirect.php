<?
session_start();
ini_set('max_execution_time', '0');
ini_set('max_input_time', '-1');
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/inc/db/connection.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/system/bricks/adminFunctions.php");
include_once $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Scraper/import/helper/data.php";
$customer = "OOBH-JFOT-2144-HJFO-DQPO-1432-KL";
$searchobject = false;
$searchobject[] = array(
	"fieldname" => "external_id",
	"searchtype" => "=",
	"value" => ""
); 
$histories = getBrickData("redirectUrls",false,'*',$customer,$searchobject,$connection);
$quotes = array_slice($histories, 0, 100);

$storeKey = getAPIStore($customer);
if(sizeof($quotes)){
	foreach($quotes as $history){
		
		if(!empty($history["external_id"])){
			$external_id = $history["external_id"];
			if($external_id){
				$ch = curl_init($storeKey."/admin/redirects/".$external_id.".json");
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
				
			} 
		}
		else{
			$ch = curl_init($storeKey."/admin/redirects.json");
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); 
			
		}
		
		$redirect = array(
			"path" => $history["path"],
			"target" => $history["target"],
		);
		
		$params = array("redirect" => $redirect);
		$datajson = json_encode($params);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$datajson );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($datajson))
		); 
		$response = curl_exec ($ch);
		$body = json_decode($response,true);
		if($body["redirect"]){
			$out["response"][] = $body;
			$insert = false;
			$insert["external_id"] = $body["redirect"]["id"];
			$out["saved"][] = saveBrickData("redirectUrls",$insert,$customer,$history["id"],$connection);
		}else{
			$insert = false;
			$insert["external_id"] = "false";
			$out["saved"][] = saveBrickData("redirectUrls",$insert,$customer,$history["id"],$connection);
		}
		curl_close ($ch); 
	}
}

print count($histories);
print "<br>";
print count($quotes);