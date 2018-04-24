<?php 
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/inc/db/connection.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/system/bricks/adminFunctions.php");
  if(file_exists($_SERVER["DOCUMENT_ROOT"]. "/system/app/controllers/".ucfirst($page).".php")){
	include_once($_SERVER["DOCUMENT_ROOT"]. "/system/app/controllers/".ucfirst($page).".php");

}

if(is_dir($_SERVER["DOCUMENT_ROOT"]. "/system/includes/pages/".ucfirst($page))){
	if(file_exists($_SERVER["DOCUMENT_ROOT"]. "/system/includes/pages/". ucfirst($page) ."/index.php")){
		include_once($_SERVER["DOCUMENT_ROOT"]. "/system/includes/pages/". ucfirst($page) ."/index.php");
	}
	include_once($_SERVER["DOCUMENT_ROOT"]. "/system/includes/pages/".ucfirst($page).".php");
}

if(file_exists($_SERVER["DOCUMENT_ROOT"]. "/system/includes/pages/".ucfirst($page).".php")){
	include_once($_SERVER["DOCUMENT_ROOT"]. "/system/includes/pages/".ucfirst($page).".php");
}
else{
	include_once($_SERVER["DOCUMENT_ROOT"]. "/system/includes/pages/404.php");
} 

?>