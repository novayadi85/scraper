<?php
namespace App\Controllers;

class Scraper{
	
	function index(){
		//global 
		include $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Scraper/Scraper.php";
	}
}