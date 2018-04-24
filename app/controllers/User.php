<?php
namespace App\Controllers;

class User{
	
	function index(){
		print "Test";
	}
	
	function show($searchobject = false){
		global $connection;
		$customers = getBrickData("customer",false,"*",false,$searchobject,$connection);
		return $customers;
	}
	
	function store($data = array()){
		global $connection;
		$id = saveBrickData("customer",$data,false,false,$connection);	
		return $id;
	}
}