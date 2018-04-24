<?php 

namespace App\Classes\Siteloom;

class Connection
{
	var $mysqli;
	
    public  function connect()
    {
		$host	= "";
		$user	= "";
		$pass	= "";
		$db		= "";
		
		$this->mysqli = mysqli_connect($host, $user, $pass,$db);
		return $this->mysqli;
    }
	
	function query($sql){
		$list = $this->mysqli->query($sql);
		return $list;
	}
	
}