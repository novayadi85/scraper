<?
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/inc/db/connection.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/system/bricks/adminFunctions.php");
$searchobject = false;
$searchobject[] = array(
	"fieldname" => "srapeProduct",
	"searchtype" => "=",
	"value" => 'true'
);  
$urls = getBrickData("productsToScrape",false,array("link"),false,$searchobject,$connection);
foreach($urls as $url){
	$urlToScrape[] = $url["link"];
}

/* print "<pre>";
print_r($urlToScrape);
print "</pre>"; */

$_REQUEST["listId"] = 'AUVD-PEPV-7929-TAXT-HDMX-7842-OQ';

?>

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
				id: 'AUVD-PEPV-7929-TAXT-HDMX-7842-OQ',
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
				id: '<? print $_REQUEST["listId"];?>',
				mode:'getScraper'
			}, 
			beforeSend: function(){
				
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
				doitNow();
			}
		});
	}

	function doitNow() {
		var ids = [];
		ids = '<? print json_encode($urlToScrape);?>';
		ids = JSON.parse(ids);
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
							setTimeout(function(){
								location.reload()
							},500);
							
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
	};
		
});
</script>