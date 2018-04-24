<?php 
class ElmTree {
	
	public $compareList = array();
	public $productsListingPending = array();
	public $productsListingUploaded = array();
	
	function convertUtf8($str){
		$str = str_replace("Ã¦","æ",$str);
		$str = str_replace("Ã¥","å",$str);
		$str = str_replace("Ã¸","ø",$str);
		return $str;
	}
	
	function buildTree($_elements, $parentId = 0) {
		$branch = array();
		foreach($_elements as $element) {
			$element['h1'] = $this->convertUtf8($element['h1']);
			$founds = 0;
			$uploaded = 0;
			$pending = 0;
			if(isset($element["productUrls"])){
				if(isset($this->compareList[$element['id']])){
					$founds = count($this->compareList[$element['id']]);
				}
			}
			
			if(isset($this->productsListingUploaded[$element['id']])){
				if($founds){
					$uploaded = count($this->productsListingUploaded[$element['id']]);
				}
			}
			
			if(isset($this->productsListingPending[$element['id']])){
				if($founds){
					$pending = count($this->productsListingPending[$element['id']]);
				}
			}
			
			$element['founds'] = $founds;
			$element['quotes'] = $pending;
			$element['uploaded'] = $uploaded;
			if ($element['parentid'] == $parentId) {
				$children = $this->buildTree($_elements, $element['id']);
				if ($children) {
					$element['children'] = $children;
				}
				$branch[] = $element;
			}
		}

		return $branch;
	}
	
	function drawUrls($listOfItems , $parent = 0){
		$output = array();
		foreach ($listOfItems as $item) {
			$productUrls  = json_decode($item["productUrls"],true);
			if(is_array($productUrls) && sizeof($productUrls)){
				foreach($productUrls as $url){
					$output[] = $url;
				}
			}
		}
		return $output;
		
	}
	
	function drawTable($listOfItems , $parent = 0, $deleteIcon = false){
		$output = array();
		if($parent){
			$output[] = "<tr class=\"details\">";
		}
		foreach ($listOfItems as $item) {
			if(empty($item["position"])){
				$item["position"] = 0;
			}
			$output[] = "<td data-parent=\"$parent\" class=\"dd-item\" data-id=\"".$item["cid"]."\">";
				$output[]  = $item["name"];
				if(isset($item["children"])){
					
					$output[] = $this->drawTable($item["children"] , $item["cid"]); 
					
				}
				
			$output[] ="</td>";
			
			
		}

		if($parent){
		   $output[] = "</tr>";
		}
		
		return join($output);
		
	}
	
	function combineData($lists , $parent = 0){
		$data = array();
		foreach($lists as $list){
			if($list["parentid"] == $parent){
				$data[] = $list["id"];
			}
			if(isset($list["children"])){
				$new = $this->combineData($list["children"],$list["id"]);
				$array1 = array_map("trim",$data);
				$array2 = array_map("trim",$new);
				$data = array_unique(array_merge_recursive($array1,$array2),SORT_REGULAR);
			}
		}
		return $data;
	}
	
	function combineDataArray($lists , $parent = 0){
		$data = array();
		foreach($lists as $list){
			if($list["parentid"] == $parent){
				$data[] = $list;
			}
			if(isset($list["children"])){
				$new = $this->combineDataArray($list["children"],$list["id"]);
				$array1 = array_map("trim",$data);
				$array2 = array_map("trim",$new);
				$data = array_unique(array_merge_recursive($array1,$array2),SORT_REGULAR);
			}
		}
		return $data;
	}
	
	function drawTree($listOfItems , $parent = 0, $deleteIcon = false){
		$output = array();
		if($parent){
			$output[] = "<ol class=\"dd-list\">";
		}
		foreach ($listOfItems as $item) {
			if(empty($item["position"])){
				$item["position"] = 0;
			}
			$output[] = "<li data-parent=\"$parent\" class=\"dd-item\" data-id=\"".$item["id"]."\">
				<div class=\"dd-handle\">
					".$this->convertUtf8($item["name"])."
				</div>";
				
				if(isset($item["children"])){
					
					$output[] = $this->drawTree($item["children"] , $item["cid"]); 
					
				}
				
			$output[] ="</li>";
			
			
		}

		if($parent){
		   $output[] = "</ol>";
		}
		
		return join($output);
		
	}
	
	function drawDropdown($listOfItems , $parent = 0, $hideUL = false){
		$output = array();
		
		if($parent && !$hideUL){
			$output[] = "<ul class=\"dropdown-menu\">";
		}
		
		else if($hideUL){
			$output[] = "";
		}
		
		foreach ($listOfItems as $item) {	
			if(isset($item["children"])){
				$output[] = "<li data-brick=\"".$item["id"]."\" class=\"dropdown-submenu\"><a href=\"#\">".$this->convertUtf8($item["h1"])."</a>";
				$output[] = $this->drawDropdown($item["children"] , $item["id"]); 
				$output[] ="</li>";
			}
			else{
				$output[] = "<li data-brick=\"".$item["id"]."\"><a href=\"#\">".$this->convertUtf8($item["h1"])."</a>";
				$output[] ="</li>";
			}
		}

		if($parent && !$hideUL){
		   $output[] = "</ul>";
		} 
		else if($hideUL){
			$output[] = "";
		}
		
		return join($output);
		
	}
	
}