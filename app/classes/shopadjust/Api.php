<?
/**
 * @copyright Copyright (c) 2018 ShopAdjust Inc.
 * @license Shareware
 * @author Komang Novayadi
 * @email novayadi85@gmail.com
 */
namespace App\Classes\ShopAdjust;
class Api{
	
	public $store;
	public $url;
	private $last_response_headers = null;
	public $connection;
	
	public function __construct($storeId){
		ini_set('max_execution_time', '0');
		ini_set('max_input_time', '-1');
		require_once($_SERVER["DOCUMENT_ROOT"]."/backend/inc/db/connection.php");
		require_once($_SERVER["DOCUMENT_ROOT"]."/backend/system/bricks/adminFunctions.php");
		$this->connection = $connection;
		$this->store = $storeId;
		$this->setStore();
	}
	
	private function getAPIStore($cId){
		$searchobject = false;
		$customer = getBrickData("customer",$cId,"*",false,$searchobject,$this->connection);
		if(sizeof($customer)){
			list($customer) = $customer;
			$apikey = $customer["api_key"];
			$api_password = $customer["api_password"];
			$storeUrl = $customer["shop"];
			$parseUrl = parse_url($storeUrl);
			$storeUrl = $parseUrl["host"];
			$storeKey = "https://$apikey:$api_password@$storeUrl";
			return $storeKey;
		}
		else{
			return false;
		}	
	}
	
	public function setStore(){
		if(empty($this->store)){
			return false;
		}
		
		$this->url = $this->getAPIStore($this->store);
		
	}
	
	public function call($method, $path, $params = array()) {
		$baseurl = $this->url . "/";
		$url = $baseurl.ltrim($path, '/');
		$query = in_array($method, array('GET','DELETE')) ? $params : array();
		$payload = in_array($method, array('POST','PUT')) ? json_encode($params) : array();
		$request_headers = in_array($method, array('POST','PUT')) ? array("Content-Type: application/json; charset=utf-8", 'Expect:') : array();
		$response = $this->curlHttpApiRequest($method, $url, $query, $payload, $request_headers);
		$response = json_decode($response, true);
		return (is_array($response) and (count($response) > 0)) ? array_shift($response) : $response;
	}
	
	private function curlHttpApiRequest($method, $url, $query='', $payload='', $request_headers=array()) {
		$url = $this->curlAppendQuery($url, $query);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "{$method}");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json')
		); 
		$response = curl_exec ($ch);
		curl_close ($ch); 
		return $response;
		
	}
	
	
	
	private function curlAppendQuery($url, $query) {
		if (empty($query)) return $url;
		if (is_array($query)) return "$url?".http_build_query($query);
		else return "$url?$query";
	}

	private function curlSetopts($ch, $method, $payload, $request_headers) {
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_USERAGENT, 'ohShopify-php-api-client');
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_ENCODING ,"");

		curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, $method);
		if (!empty($request_headers)) curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
		
		if ($method != 'GET' && !empty($payload))
		{
			if (is_array($payload)) $payload = http_build_query($payload);
			curl_setopt ($ch, CURLOPT_POSTFIELDS, $payload);
		}
	}

	private function curlParseHeaders($message_headers) {
		$header_lines = preg_split("/\r\n|\n|\r/", $message_headers);
		$headers = array();
		list(, $headers['http_status_code'], $headers['http_status_message']) = explode(' ', trim(array_shift($header_lines)), 3);
		foreach ($header_lines as $header_line)
		{
			list($name, $value) = explode(':', $header_line, 2);
			$name = strtolower($name);
			$headers[$name] = trim($value);
		}

		return $headers;
	}
	
}

