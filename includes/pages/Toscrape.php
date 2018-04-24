<?
include_once $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Shopify/Class/Tree.php";
$searchobject = false;
if(isset($_GET["status"])){
	$status = $_GET["status"];
	$searchobject[] = array(
		"fieldname" => "status",
		"searchtype" => "=",
		"value" => $status
	);  
}

$lists = getBrickData("productsToScrape",false,"*",false,$searchobject,$connection);	
$listings = $lists;
$xlistings = array();
$trs = array();
if(sizeof($lists)){
	$no = 0;
	foreach($listings as $k => $list){
		$xlistings[$list["parentid"]]["urls"][] = $list["link"];
		if($list["status"] == "0"){
			$list["status"] = "Pending";
		}
		
		if($list["status"] == "1"){
			$list["status"] = "Saved";
		}
		$trs[$list["link"]] = array("id" => $list["id"], "html" => "
			
				<td scope=\"col\"> <span class=\"linkId\" data-url=\"".$list["link"]."\">".$list["link"]."</span></td>
				<td scope=\"col\">".$list["href"]."</td>
				<td scope=\"col\"> ". $list["lastUpdateDate"]."</td>
				<td scope=\"col\"> ". $list["status"]." </td>
			
		");
	}
}


$searchobject = false;

function Build2Tree($listOfItems , $parent = 0, $deleteIcon = false){
	global $xlistings;
	$urls = array();
	foreach($listOfItems as $x => $productList){
		if($productList["id"] == $parent) {
			$results = json_decode($productList["productUrls"], true);
			$title = $productList["title"];
			$paginationUrls = (json_decode($productList["paginationUrls"], true));
			
			/* 
			if(sizeof($results)){
				foreach($results as $result){
					if(is_array($urls) && is_array($result)){
						$urls = array_unique(array_merge_recursive($urls,$result),SORT_REGULAR);
					}
					else if(is_array($urls) && !is_array($result)){
						$urls[] = $result;
					}
				}
			}
			else if(isset($xlistings[$productList["id"]])){
				foreach($xlistings[$productList["id"]]["urls"] as $result){
					if(is_array($urls) && is_array($result)){
						$urls = array_unique(array_merge_recursive($urls,$result),SORT_REGULAR);
					}
					else if(is_array($urls) && !is_array($result)){
						$urls[] = $result;
					}
				}
			} 
			*/
			
			if(isset($xlistings[$productList["id"]])){
				foreach($xlistings[$productList["id"]]["urls"] as $result){
					if(is_array($urls) && is_array($result)){
						$urls = array_unique(array_merge_recursive($urls,$result),SORT_REGULAR);
					}
					else if(is_array($urls) && !is_array($result)){
						$urls[] = $result;
					}
				}
			}
			
			if(isset($productList["children"])){
				$urls[] = Build2Tree($productList["children"] , $productList["id"]); 
			}
		}
	}
	return $urls;
}

$listParent = $_REQUEST["listId"];

if(!isset($_REQUEST["listId"]) || empty($_REQUEST["listId"])){
	$_REQUEST["listId"] = 'AUVD-PEPV-7929-TAXT-HDMX-7842-OQ';
}




$urls = array();
$productLists = getBrickData("scraper_productlist",false,"*",false,$searchobject,$connection);
$ElmTree = new ElmTree;
$productLists = $ElmTree->buildTree($productLists,0);	
if(sizeof($productLists) && is_array($productLists)){
	$urls = Build2Tree($productLists,$listParent);
} 

/* print "<pre>";
print_r($xlistings);
print "</pre>"; */



/* $test = 'https://www.heino-cykler.dk/cykler/triathlon-cykel/stevens-super-trofeo-2017-carbon-triathloncykel-med-shimano-ultegra.html';
$test2 = 'https://www.heino-cykler.dk/cykler/mtb/specialized-sj-fsr-comp-carbon-29-2017-fullsuspension-mtb-med-roval-traverse-29-alu-hjul.html';
$searchobject = false;
 $searchobject[] = array(
	"fieldname" => "link",
	"searchtype" => "=",
	"value" => $test
); 
$searchobject[] = "OR";
 $searchobject[] = array(
	"fieldname" => "link",
	"searchtype" => "=",
	"value" => $test2
);  */


?>


<style>
.dataTables_wrapper {
    padding: 10px;
}
</style>
<script>

var jsonData ;
var jsondata ;
var requests ;
var parentId;
var uniqueAttribute;

$(document).ready(function () {
	getData();
	var getFromBetween = {
		results:[],
		string:"",
		getFromBetween:function (sub1,sub2) {
			if(this.string.indexOf(sub1) < 0 || this.string.indexOf(sub2) < 0) return false;
			var SP = this.string.indexOf(sub1)+sub1.length;
			var string1 = this.string.substr(0,SP);
			var string2 = this.string.substr(SP);
			var TP = string1.length + string2.indexOf(sub2);
			return this.string.substring(SP,TP);
		},
		removeFromBetween:function (sub1,sub2) {
			if(this.string.indexOf(sub1) < 0 || this.string.indexOf(sub2) < 0) return false;
			var removal = sub1+this.getFromBetween(sub1,sub2)+sub2;
			this.string = this.string.replace(removal,"");
		},
		getAllResults:function (sub1,sub2) {
			// first check to see if we do have both substrings
			if(this.string.indexOf(sub1) < 0 || this.string.indexOf(sub2) < 0) return;

			// find one result
			var result = this.getFromBetween(sub1,sub2);
			// push it to the results array
			this.results.push(result);
			// remove the most recently found one from the string
			this.removeFromBetween(sub1,sub2);

			// if there's more substrings
			if(this.string.indexOf(sub1) > -1 && this.string.indexOf(sub2) > -1) {
				this.getAllResults(sub1,sub2);
			}
			else return;
		},
		get:function (string,sub1,sub2) {
			this.results = [];
			this.string = string;
			this.getAllResults(sub1,sub2);
			return this.results;
		}
	};
	
	function getTables(){
		$.ajax({
			type: "POST",
			url: "/system/includes/pages/Scraper/import/ajax/ajax.scraper.php?v="+Math.random(),
			dataType: 'json',
			data: {
				id: '<? print $_SESSION["customerData"]["shopify_product"][0]["id"]; ?>',
				mode:'getPending'
			}, 
			beforeSend: function(){
				$('body').progress('open');
			},
			success : function(objs){
				$('#table1 tbody').html(objs.data);
			},
			complete: function(){
				$('#table1').DataTable({
					"dom": '<"top"iflp<"clear">>rt<"bottom"iflp<"clear">>',
					"pageLength": 50
				});
				$('body').progress('close');
			}
		});
	}
	//'AUVD-PEPV-7929-TAXT-HDMX-7842-OQ'
	function getData(){
		$.ajax({
			type: "POST",
			url: "/system/includes/pages/Scraper/import/ajax/ajax.scraper.php?v="+Math.random(),
			dataType: 'json',
			data: {
				id: '<? print $_SESSION["customerData"]["shopify_product"][0]["id"]; ?>',
				mode:'getScraper'
			}, 
			beforeSend: function(){
				$('body').progress('open');
			},
			success : function(objs){
				jsonData = objs.output;
				requests = [];
				request = {};
				var dataRequest = objs.data;
				$.each(dataRequest.parentData, function(k,v) {
					request[k] = v;
				});
				
				request["data"] = dataRequest.data;
				request["element"] = dataRequest.element;
				request["head"] = dataRequest.headData;
				request["tags"] = dataRequest.tags;
				requests.push(request);
				parentId = dataRequest.parentid;
				uniqueAttribute = dataRequest.unique_attribute;
			},
			complete: function(){
				//getTables();
				$('body').progress('close');
			}
		});
	}

	
	$("table.dataTable").dataTable ({
		"dom": '<"top"iflp<"clear">>rt<"bottom"iflp<"clear">>',
		"pageLength": 50
	});
	
	$(document).on('click','.scrap-now', function () {
		var ids = [];
		$(".linkId").each(function(i){
			ids[i] = $(this).attr("data-url");
		});
		var output = [];
		var noresult = [];
		$.ajax({
			type: "POST",
			url: "/system/includes/pages/Scraper/import/ajax/ajax.output.php?v="+Math.random(),
			dataType: 'json',
			data: {
				urls: ids
			}, 
			beforeSend: function(){
				$('body').progress('open');
			},
			success : function(objs){
				var obj = objs.data;
				var paginations = objs.paginations;
				var parentLabel = 'product';
				urls = objs.urls;
				
				if (obj.length) {
					$.each(obj, function(i,html) {
						var x = i;
						var wrap = $(html);
						var dom_nodes  = $($.parseHTML(html));
						var headMeta = {};
						var bodyElement = {};
						var scrap = {};
						var skip = false;
						
						
						//var spConfig = html.substring(html.lastIndexOf("Product.Config(")+1,html.lastIndexOf("});"));
						var spConfig = getFromBetween.get(html,"Product.Config(",");");
						
						// Handle request (possible multiple)
						$.each(requests, function(i,request) {
							skip = false;
							parentLabel = request.label;
							console.log(request);							
							taging = request.tags;
							if (taging != null && taging.length) {	
								$.each(request.tags, function(i,data) {
									
									if($(wrap).filter(data.tag).length <= 0){
										skip = true;
									}
									
									if(skip){
										var query = data.tag;
										query = query.replace("#", "");
										var isFind = $(dom_nodes).filter(query);
										
										if(isFind.length <=0) {
											skip = true;
										}
										
										if(skip){
											var needleRegex = query;
											var isFind = html.search(needleRegex);
											
											if(isFind < 0){
												skip = true;
											}
											else{
												skip = false;
											}
										} 
										
										
									} 
									
									
								});
							}
							
							if(!skip){
								// Head Meta Data
								//console.log
								if (request.head.length) {	
									$.each(request.head, function(i,data) {
										
										wrap.find(data.tag).each(function () {
											var value = "";
											// Parsing "Type" request
											if (data.type == "text") {
												value = $(this).text();	
											}
											else if (data.type == "html") {
												value = $(this).html();	
											}
											else if (data.type == "attr") {
												value = $(this).attr(data.attr);	
											}
											
											
											
											headMeta[data.label] = value;
										});
										
										if(wrap.find(data.tag).length <= 0){
											headMeta[data.label] = $("body").find(data.tag);	
										}

										wrap.filter(data.tag).each(function () {
											var value = "";
											
											if (data.type == "text") {
												value = $(this).text();	
											}
											else if (data.type == "html") {
												value = $(this).html();	
											}
											else if (data.type == "attr") {
												value = $(this).attr(data.attr);	
											}
											
											headMeta[data.label] = value;
										});
									});
								}
							
								
								//element
								if (request.element.length) {	
									$.each(request.element, function(i,data) {
										wrap.find(data.tag).each(function () {
											var value = "";
											// Parsing "Type" request
											if (data.type == "text") {
												value = $(this).text();	
											}
											else if (data.type == "html") {
												value = $(this).html();	
											}
											else if (data.type == "attr") {
												value = $(this).attr(data.attr);	
											}
											
											
											
											headMeta[data.label] = value;
										});
										

										wrap.filter(data.tag).each(function () {
											var value = "";
											// Parsing "Type" request
											if (data.type == "text") {
												value = $(this).text();	
											}
											else if (data.type == "html") {
												value = $(this).html();	
											}
											else if (data.type == "attr") {
												value = $(this).attr(data.attr);	
											}
											
											headMeta[data.label] = value;
										});
									});
								}
								
								headMeta['redirect'] = urls[x];
								headMeta['url'] = urls[x];
								headMeta['spConfig'] = spConfig;
								
								
								if (wrap.find(request.tag).length) {							
									
									// Parent Data
									wrap.find(request.tag).each(function () {
										
										var single = $(this);
										var singleScrap = {};
										// Data
										$.each(request.data, function(i,data) {									
											single.find(data.tag).each(function () {
												var value = "";
												
												// Parsing "Type" request
												if (data.type == "text") {
													value = $(this).text();	
												}
												else if (data.type == "html") {
													value = $(this).html();	
												}
												else if (data.type == "attr") {
													value = $(this).attr(data.attr);	
												}
												
												// Checking "Ignore If Parent" if not empty
												var ignoreData = false;
												if (request['ignore'] != '') {
													if ($(this).closest(request['ignore']).length > 0) {
														ignoreData = true;
													}
												}
												
												if (!ignoreData) {
													// If found multiple data tag. Make it array
													if (single.find(data.tag).length > 1) {
														
														if (!(data.label in singleScrap)) {
															singleScrap[data.label] = [];
														}
														
														singleScrap[data.label].push(value);
													}
													else {
														singleScrap[data.label] = value;
													}
												}
											});
										});
										
										if (!$.isEmptyObject(singleScrap)) {
											if (!$.isArray(scrap[request.label])) {
												scrap[request.label] = [];	
											}
											
											scrap[request.label].push(singleScrap);
										}
									});
									
								}
								
							}
						});
						
						

						if (!$.isEmptyObject(scrap)) {
							
							// Merge head meta and scrap data
							if (!$.isEmptyObject(headMeta)) {
								$.extend(headMeta, scrap);
								scrap = headMeta;
							}
							output.push(scrap);
						}
						else {
							noresult.push(urls[i]);
						}								
					});
					
					jsondata = output;
					
					output = JSON.stringify(output);
					output = output.replace(/'/g, "\\'")
					$('.output-scraper').removeAttr('disabled').val(output);
					
					if (noresult.length) {
						$('.noresult-scraper').val(noresult.join('\n'));
						$('.wrap-noresult-scraper').show();
					}
					
					$.ajax({
						type: "POST",
						url: "/system/includes/pages/Scraper/import/ajax/ajax.json.php?v="+Math.random(),
						
						data: {
							output: jsondata, 
							mode: 'save_data',
							unique_attribute: uniqueAttribute,
							typeScraper	:  'shopify_product'	,
							parentId: parentId,
							parentData: parentLabel,
							changeStatus: true
						},
						success: function(){
							//getTables();
							/* setTimeout(function(){
								location.reload()
							},500); */
							
							$('body').progress('close');
						},
						error: function(xmlhttprequest, textstatus, message) {
							if(textstatus==="timeout") {
								alert("got timeout try again...");
							} else {
								alert(textstatus);
							}
						}
					});
				}
			},
			error: function(xmlhttprequest, textstatus, message) {
				if(textstatus==="timeout") {
					alert("got timeout try again...");
				} else {
					alert(textstatus);
				}
			}
		});
	});
		
});
</script>
<div class="row">
	<div class="col-12">		
		<div class="portlet box">		
			<div class="portlet-body">
				<div class="btn-group">
					<a href="javascript:;" class="btn yellow scrap-now"> Scrap visible now </a>
				</div>
				<div class="table-scrollable">
					<div class="table-conatiner products-only">
						<table id="table1" class="table dataTable table-striped table-bordered table-hover">
							<thead>
								<tr>
									<th scope="col"> No. </th>
									<th scope="col"> Link </th>
									<th scope="col"> Href </th>
									<th scope="col"> Updated </th>
									<th scope="col"> Status </th>
								</tr>
							</thead>
							<tbody>
							<?
							if(sizeof($trs)){
								$no = 0;
								foreach($trs as $l => $tr){
									if(!in_array($l , $urls)) continue;
									$no++;
									//setCrons
									$update = false;
									$update["srapeProduct"] = "true";
									$id = saveBrickData("productsToScrape",$update,false,$tr["id"],$connection); 
									print "<tr>";
									print "<td>$no</td>";
									print $tr["html"];
									print "</tr>";
									?>
									
									<?
								}
							} 
							
							?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>