<?
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/inc/db/connection.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/system/bricks/adminFunctions.php");
include_once $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/User/Helper/Data.php";

$searchobject = false;
$request_body = file_get_contents('php://input');
$ajax = json_decode($request_body,true);
if($ajax["action"] == "getCustomers"){
	$out = array();
	$searchobject = false;
	$searchobject[] = array(
		"fieldname" => "deleted",
		"searchtype" => "=",
		"value" => "false"
	);
	$customers = getBrickData("customer",false,"*",false,$searchobject,$connection);
	$out["customers"] = $customers;
	print json_encode($out);
}

if($ajax["action"] == "add"){
	
	$out = array();
	$fields = false;
	$fields = $ajax["params"];
	$id = false;
	if(isset($fields["id"]) && $fields["id"]){
		$id = $fields["id"];
	}
	
	if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$fields["shop"])) {
		$out["error"] = true;
		$out["message"] = "Invalid Shop URL.";
		print json_encode($out);
		exit();
	}
	
	if(empty($fields["api_key"]) || empty($fields["api_password"])){
		$out["error"] = true;
		$out["message"] = "Api detail can't empty.";
		print json_encode($out);
		exit();
	}
	
	if(!$id){
		$searchobject = false;
		$searchobject[] = array(
			"fieldname" => "shop",
			"searchtype" => "=",
			"value" => $fields["shop"]
		);
		
		$customers = getBrickData("customer",false,"*",false,$searchobject,$connection);
		if(sizeof($customers) > 0 AND is_array($customers)){
			$out["error"] = true;
			$out["message"] = "Customer with this shop url still exsist.";
			print json_encode($out);
			exit();
		}
	}
	
	//test API
	$apikey = $fields["api_key"];
	$api_password = $fields["api_password"];
	$storeUrl = $fields["shop"];
	$parseUrl = parse_url($storeUrl);
	$storeUrl = $parseUrl["host"];
	$authenticate = "https://$apikey:$api_password@$storeUrl";
	if(!ApiTestShop($authenticate)){
		$out["error"] = true;
		$out["message"] = "Invalid api data, please try another.";
		print json_encode($out);
		exit();
	}

	$id = saveBrickData("customer",$fields,false,$id,$connection);	
	$searchobject = false;
	$searchobject[] = array(
		"fieldname" => "deleted",
		"searchtype" => "=",
		"value" => "false"
	);
	$customers = getBrickData("customer",false,"*",false,$searchobject,$connection);
	$out["customers"] = $customers;
	$out["id"] = $id;
	print json_encode($out);	
}

if($ajax["action"] == "remove"){
	$out = array();
	$fields = false;
	$fields["deleted"] = "true";
	$params = $ajax["params"];
	$ids = false;
	$ids = $params["id"];
	$id = saveBrickData("customer",$fields,false,$ids,$connection);	
	$searchobject = false;
	$searchobject[] = array(
		"fieldname" => "deleted",
		"searchtype" => "=",
		"value" => "false"
	);
	$customers = getBrickData("customer",false,"*",false,$searchobject,$connection);
	$out["customers"] = $customers;
	$out["id"] = $id;
	print json_encode($out);
}