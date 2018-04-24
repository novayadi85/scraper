<?php 

namespace App\Classes\Siteloom;

class Helper
{
	function helloWorld(){
		echo "Test";
	}
    public function dropdown($options,$value)
    {
		$html = array();
        foreach($options as $key => $val){
			$selected = "";
			if($key == $value){
				$selected = " selected ";
			}
			$html[] = "<option $selected value=\"$key\">$val</option>";
		}
		return join($html);
    }
	
	function years($year = ""){
		if(empty($year)) $year = date("Y");
		$starting_year  =date('Y', strtotime('-5 year'));
		$ending_year = date('Y', strtotime('+10 year'));
		$html = array();
		for($starting_year; $starting_year <= $ending_year; $starting_year++) {
			$selected = "";
			if($starting_year == $year){
				$selected = " selected ";
			}
			$html[] = "<option $selected value=\"$starting_year\">$starting_year</option>";
		}
		return join($html);
	}
	function months($month = ""){
		if(empty($month)) $month = date("m");
			$month = strtotime(date('Y').'-'.date('m').'-'.date('j').' - 12 months');
			$end = strtotime(date('Y').'-'.date('m').'-'.date('j').' + 0 months');
			$val=1;
			$html = array();
			while($month < $end){
				$selected = (date('F', $month)==date('F'))? ' selected' :'';
				$html[] = '<option'.$selected.' value='.$val.'>'.date('F', $month).'</option>'."\n";
				$month = strtotime("+1 month", $month);
				$val++;
			}
		return join($html);
			
	}
}