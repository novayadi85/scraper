<script type="text/javascript" src="//jeppekjaersgaard.dk/system/resources/assets/apps/scripts/jquery.doubleScroll.js?v=1520822871"></script>

<?php 
if(isset($_GET["lists"])){
	include_once( $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Shopify/Sections/Lists.php") ;
}
else {
	include_once( $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Shopify/Sections/Products.php") ;
}
?>
<style type="text/css">
.chosen-container-single .chosen-single {
    padding: 4px 0 0 8px;
    height: 35px;
}
.chosen-container-single .chosen-single div b{
	background-position: 0px 8px
}
.chosen-container-active.chosen-with-drop .chosen-single div b{
	background-position: 0px 8px
}
/**
.list-only , .product-only , .page-only , .pages-only {
	display: none;
}
.table-conatiner{
	display: none;
}
**/
table {
	display:none;
	width:100%;
}

.table .btn {
    display: block;
    width: 100%;
}

.dataTable .details {
    background-color: #fbfcfd;
}
.dataTable .details tr:nth-child(odd) td, .dataTable .details tr:nth-child(odd) th {
    background-color: #fbfcfd;
}
.dataTable .details tr:nth-child(even) td, .dataTable .details tr:nth-child(even) th {
    background-color: #fbfcfd;
}
.dataTables_wrapper {
    padding: 10px;
}
</style>
<div class="modal fade" id="customer" role="basic" aria-hidden="false">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
				<h4 class="modal-title">Set Customer as Developer Mode</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="hereFields">
							<select class="form-control" id="modal-selected-customer">
								<option></option>
							</select>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn default" data-dismiss="modal">Close</button>
				<button type="button" class="btn setDeveloperMode green saveField">Save</button>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="scrapeList" role="basic" aria-hidden="false">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
				<h4 class="modal-title">Scrape List</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="hereFields">
							
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn default" data-dismiss="modal">Close</button>
				
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	
	var jsondata ;
	var requests ;
	var parentId;
	var uniqueAttribute;
	
	var Scraper = {};
	Scraper.results = {};
	
	Scraper.init = function () {		
		if (Scraper.customer) {
			$.ajax({
				type: "POST",
				url: "/system/includes/pages/Shopify/Ajax/ajax.response.php?v="+Math.random(),
				data: {
					customer: Scraper.customer,
					mode: 'getData'						
				},
				dataType: 'json',
				success: function(obj){
					if(obj.error){
						if(obj.msg){
							toastr.error(obj.msg,"Error");
						}
						else{
							toastr.error("Something error , check your connection !","Error");
						}
						$('body').progress('close');
						return ;
					}
					else{
						Scraper.tags = obj.tags;
						Scraper.multiTags = obj.tagsMulti;
						
						
						Scraper.default();
					}
					
				},
				complete : function(){
					$('.upload-list').show();
				}
			});	
		}
	};
	
	Scraper.loadData = function (type , trId , link) {
		return false;
	};
	
	Scraper.loadData1 = function (type , trId , link) {
		var trId = trId || false;
		var link = link || false;
		if(!Scraper.customer){
			toastr.error("Something error , Customer undefined !","Error");
			return;
		}
		$('table-conatiner').hide();
		var type = type || $('.btn-choose-type .active').find('input').val();
		var tableClass = "table:visible";
		
		if(type == "products"){
			tableClass = "#table1";
			if( $.fn.DataTable.isDataTable('#table1') ) {
				$('#table1').dataTable().fnDestroy();
				$('#table1').empty();
			}
			 
		}
		else if(type == "lists"){
			tableClass = "#table2";
			if( $.fn.DataTable.isDataTable('#table2') ) {
				$('#table2').dataTable().fnDestroy();
				$('#table2').empty();
			} 
		}
		else if(type == "page" || type == "pages"){
			tableClass = "#table3";
			if( $.fn.DataTable.isDataTable('#table3') ) {
				$('#table3').dataTable().fnDestroy();
				$('#table3').empty();
			}
		}
		
		$(tableClass).show();
		
		$.ajax({
			url:  "/system/includes/pages/Shopify/Ajax/ajax.response.php?v="+Math.random(),
			type: 'POST',
			dataType: 'json',
			data: {
			  cmd : "refresh",
			  type : type,
			  tags : $('.selected-tags').val(),
			  status : $('.selected-status .active').find('input').val(),
			  mode : 'loadData',
			  table : tableClass,
			  customer: Scraper.customer,
			  parentTr : trId
			},
			beforeSend: function(){
				$('body').progress('open');
			},
			success: function(obj){		
				
				if(obj.error){
					if(obj.msg){
						toastr.error(obj.msg,"Error");
					}
					else{
						toastr.error("Something error , check your connection !","Error");
					}
					$('body').progress('close');
					return ;
				}
				$('.countData').html(obj.count);
				$(tableClass).find('tbody').html(obj.table);
				$(tableClass).closest('.table-conatiner').show();
			},
			complete: function(){
				if($(tableClass).find('tbody tr').length <= 0){
					//Scraper.loadData();
				}
				/* $(tableClass).dataTable ({
					"autoWidth":false, 
					"bSort": false,
					"info":true, 
					"JQueryUI":true, 
					"ordering":false, 
					"paging":true, 
					"scrollY":false, 
					"scrollCollapse":true,
					"pageLength": 50,
					"destroy": true,
					"aoColumns": [],
				}); */
				
				if(link){
					var aTags = $("table.table-product").find('td');
					var searchText = link;
					var found;
					for (var i = 0; i < aTags.length; i++) {
						var tes = aTags[i].textContent;
						tes = tes.toLowerCase();
						searchText = searchText.toLowerCase();
						searchText = searchText.trim();
						if ( tes.trim() == searchText) {
							found = $(aTags[i]);
							$('html, body').animate({
								scrollTop: ($(found).offset().top - 30)
							}, 2000);
							break;
						}
					}
				}
				var number = $("#number_record").val();
				$("table:visible tr:lt("+number+")").show();
				if(number != 'all'){
					$("table:visible tr:gt("+number+")").hide();
				}
				else{
					$("table:visible tr").show();
				}
				
				$('body').progress('close');
				
			},
			error: function(){
				$('body').progress('close');
			}
		});
		
		
	};
	
	Scraper.selectedTags = [];
	
	Scraper.default = function () {
		if(Scraper.tags) {
			var opt = '<option value="0">All tags</option>';
			var selected = selected || 0;
			$.each(Scraper.tags, function(id, op) {
				var optSelected = '';	
				if (selected == id) {
					var optSelected = 'selected="selected"';
				}
				opt += '<option value="'+id+'" '+optSelected+'>'+op+'</option>';
					
			});
			$('.selected-tags').html(opt);
			
		}	

		if(Scraper.multiTags) {
			$(Scraper.multiTags).insertBefore($(".dropdown-menu.multi-level .last-child"));
		}
	};
	
	Scraper.tesLoad = function(type , trId , link) {
		var trId = trId || false;
		var link = link || false;
		if(!Scraper.customer){
			toastr.error("Something error , Customer undefined !","Error");
			return;
		}
		
		
		if( $.fn.DataTable.isDataTable('#table1') ) {
			$('#table1').dataTable().fnDestroy();
			$('#table1 tbody').empty();
			$('#table1').hide();
		}
		else{
			$('#table1').hide();
		}
		
		if( $.fn.DataTable.isDataTable('#table2') ) {
			$('#table2').dataTable().fnDestroy();
			$('#table2 tbody').empty();
			$('#table2').hide();
		}
		else{
			$('#table2').hide();
		}
		
		if( $.fn.DataTable.isDataTable('#table3') ) {
			$('#table3').dataTable().fnDestroy();
			$('#table3 tbody').empty();
			$('#table3').hide();
		}
		else{
			$('#table3').hide();
		}
		
		var type = type || $('.btn-choose-type .active').find('input').val();
		
		
		if(type == "lists"){
			$('#table1').show();
			$('.btn-group').find(".list-only").show();
			$('.btn-group').find(".page-only").hide();
			$('.btn-group').find(".product-only").hide();
			var table = $('#table1').dataTable( {
				ajax: {
					url:  "/system/includes/pages/Shopify/Ajax/ajax.response.php?v="+Math.random(),
					type: 'POST',
					dataType: 'json',
					data: function(d){
					  d.cmd = "refresh",
					  d.type = type,
					  d.tags = Scraper.selectedTags,
					  d.status = $('.selected-status .active').find('input').val(),
					  d.mode = 'loadData',
					  d.table = 'lists',
					  d.customer = Scraper.customer,
					  d.parentTr = trId
					},
					dataSrc: function (json) {
						if(json.error == "true"){
							toastr.error(json.msg);
							return;
						}
						Scraper.results = json.out['result'];
						return json.data;
					}  
				},
				"columns": [
					{ "data": "0" },
					{ "data": "1" },
					{ "data": "2" },
					{ "data": "3" },
					{ "data": "4" },
					{ "data": "5" },
					{ "data": "6" },
					{ "data": "7" },
					{ "data": "8" },
					{ "data": "9" }
				],
				"order": [[0, 'asc']],
				"dom": '<"top"iflp<"clear">>rt<"bottom"iflp<"clear">>',
				"pageLength": 50,
				"scrollX": true
			} );
		}
		else if(type == "products"){
			$('#table2').show();
			$('.btn-group').find(".list-only").hide();
			$('.btn-group').find(".page-only").hide();
			$('.btn-group').find(".product-only").show();
			var table = $('#table2').dataTable( {
				ajax: {
					url:  "/system/includes/pages/Shopify/Ajax/ajax.response.php?v="+Math.random(),
					type: 'POST',
					dataType: 'json',
					data: function(d){
					  d.cmd = "refresh",
					  d.type = type,
					  d.tags = Scraper.selectedTags,
					  d.tagText = $('.selected-tags option:selected').text(),
					  d.status = $('.selected-status .active').find('input').val(),
					  d.mode = 'loadData',
					  d.table = 'lists',
					  d.customer = Scraper.customer,
					  d.parentTr = false
					},
					dataSrc: function (json) {
						if(json.error == "true"){
							toastr.error(json.msg);
							return;
						}
						Scraper.results = json.out['result'];
						return json.data;
					}  
				},
				"columns": [
					{ "data": "0" },
					{ "data": "1" },
					{ "data": "2" },
					{ "data": "3" },
					{ "data": "4" },
					{ "data": "5" },
					{ "data": "6" },
					{ "data": "7" },
					{ "data": "8" },
					{ "data": "9" },
					{ "data": "10" },
					{ "data": "11" }
				],
				"order": [[0, 'asc']],
				"dom": '<"top"iflp<"clear">>rt<"bottom"iflp<"clear">>',
				"pageLength": 50,
				"scrollX": true
			} );
		} 
		else if (type == "pages"){
			$('#table3').show();
			$('.btn-group').find(".list-only").hide();
			$('.btn-group').find(".page-only").show();
			$('.btn-group').find(".product-only").hide();
			
			var table = $('#table3').dataTable( {
				ajax: {
					url:  "/system/includes/pages/Shopify/Ajax/ajax.response.php?v="+Math.random(),
					type: 'POST',
					dataType: 'json',
					data: function(d){
					  d.cmd = "refresh",
					  d.type = type,
					  d.tags = Scraper.selectedTags,
					  d.status = $('.selected-status .active').find('input').val(),
					  d.mode = 'loadData',
					  d.table = 'lists',
					  d.customer = Scraper.customer,
					  d.parentTr = false
					},
					dataSrc: function (json) {
						if(json.error == "true"){
							toastr.error(json.msg);
							return;
						}
						Scraper.results = json.out['result'];
						return json.data;
					}   
				},
				"columns": [					
					{ "data": "0" },
					{ "data": "1" },
					{ "data": "2" },
					{ "data": "3" },
					{ "data": "4" },
					{ "data": "5" },
					{ "data": "6" }
				],
				"order": [[0, 'asc']],
				"dom": '<"top"iflp<"clear">>rt<"bottom"iflp<"clear">>',
				"pageLength": 50,
				"scrollX": true
			} );
		}
		
		if(link){
			var aTags = $("table").find('td');
			var searchText = link;
			var found;
			for (var i = 0; i < aTags.length; i++) {
				var tes = aTags[i].textContent;
				tes = tes.toLowerCase();
				searchText = searchText.toLowerCase();
				searchText = searchText.trim();
				if ( tes.trim() == searchText) {
					found = $(aTags[i]);
					$('html, body').animate({
						scrollTop: ($(found).offset().top - 30)
					}, 2000);
					break;
				}
			}
		}
		
		/* $('#table1').doubleScroll();
		$('#table2').doubleScroll();
		$('#table3').doubleScroll(); */
		
	};
	
	function getCustomerOptions(selected){
		var selected = selected || 0;
		$.ajax({
			type: "POST",
			url: "/system/includes/pages/Scraper/import/ajax/ajax.scraper.php?v="+Math.random(),
			dataType: 'json',
			data: {
				mode: 'getCustomerOption'
			},
			beforeSend: function(){
				$('body').progress('open');
				$('.upload-list').hide();
			},
			success: function(obj){
				var opt = '<option value="0">Choose Customer</option>';
				
				if(!obj.error) {
					$.each(obj.data, function(i, op) {
						var optSelected = '';
						
						if (selected == op.id) {
							var optSelected = 'selected="selected"';
						}
						
						opt += '<option value="'+op.id+'" '+optSelected+'>'+op.name+'</option>';
					});
				}
				
				$('.selected-customer').html(opt);	
				
				if(selected){
					$('.selected-customer').trigger('change');
				}
				else{
					$('#modal-selected-customer').html(opt);	
					$('#customer').modal("show");
				}
				
				
			},
			complete: function(){
				if(Scraper.customer){
					$('.btn-choose-type .active').trigger('click');
				}
				$('body').progress('close');
				
			}
		});
	}
	
	var selectedCustomer = 0;
	
	$(document).ready(function () {
		<?
		if(isset($_SESSION["cId"]) && !empty($_SESSION["cId"]) && $_SESSION["cId"]){
			?>
			selectedCustomer = '<? print $_SESSION["cId"]; ?>';
			getCustomerOptions(selectedCustomer);
			<?
		}
		else{
			?>
			getCustomerOptions(selectedCustomer);
			<?
		}
		?>
		
		$('.selected-customer').change(function () {
			var selected = 0;
			var id = $(this).val();
			$.ajax({
				type: "POST",
				url: "/system/includes/pages/Scraper/import/ajax/ajax.scraper.php?v="+Math.random(),
				dataType: 'json',
				data: {
					mode: 'getScraperOption',
					parentId: id
				},
				beforeSend: function(){
					$('body').progress('open');
					$('.upload-list').hide();
				},
				success: function(obj){
					var opt = '<option value="0">New scraper</option>';
					
					if(!obj.error) {
						$.each(obj.data, function(i, op) {
							var optSelected = '';
							
							if (selected == op.id) {
								var optSelected = 'selected="selected"';
							}
							
							opt += '<option value="'+op.id+'" '+optSelected+'>'+op.name+'</option>';
						});
					}
					Scraper.customer = id;
					$('.selected-scraper').html(opt);
					
				}, 
				complete: function(){
					Scraper.init();
					//Scraper.loadData();
					Scraper.tesLoad();
					$('body').progress('close');
				}
			});
			
		});
		 
		$(document).off('click','.btn-choose-type > .btn');
		$(document).on('click','.btn-choose-type > .btn', function () {
			var active = $(this).find('input').val();
			$('.table-conatiner').hide();
			$("[class~='only']").hide();
			var clas = active+"-only";
			
			if(active == "pages" || active == "page"){
				$('.btn-group').find(".list-only").hide();
				$('.btn-group').find(".page-only").show();
				$('.btn-group').find(".product-only").hide();
			}
			
			if(active == "product" || active == "products"){
				$('.btn-group').find(".list-only").hide();
				$('.btn-group').find(".page-only").hide();
				$('.btn-group').find(".product-only").show();
			}
			
			if(active == "list" || active == "lists"){
				$('.btn-group').find(".list-only").show();
				$('.btn-group').find(".page-only").hide();
				$('.btn-group').find(".product-only").hide();
			}
			
			/* setTimeout(function(){
				Scraper.loadData(active),500
			}); */
			
			setTimeout(function(){
				Scraper.tesLoad(active),500
			});
			
		});
		
		$(document).off('click','.selected-status > .btn');
		$(document).on('click','.selected-status > .btn', function () {
			setTimeout(function(){
				Scraper.tesLoad(),500
			});	
		});
		
		$(document).off('change','.selected-tags');
		$(document).on('change','.selected-tags', function () {
			setTimeout(function(){
				Scraper.tesLoad(),500
			});	
			
		});
		
		$(document).on('click','.upload-list', function () {
			var ids = [];
			var type = type || $('.btn-choose-type .active').find('input').val();
			$("tr:visible .show-to-detail").each(function(i){
				ids[i] = $(this).attr("data-brick");
			});
			
			if(ids.length <= 0){
				toastr.error("Not found data to change.","Error!");
				return;
			}
			
			$.ajax({
				type: "POST",
				url: "/system/includes/pages/Shopify/Ajax/ajax.response.php?v="+Math.random(),
				dataType: 'json',
				data: {
					mode: 'send2Shopify',
					type: type,
					id: ids
				},
				beforeSend: function(){
					$('body').progress('open');
					
				},
				success: function(obj){
					if(obj.error){
						if(obj.msg){
							toastr.error(obj.msg,"Error");
						}
						else{
							toastr.error("Something error , check your connection !","Error");
						}
						$('body').progress('close');
						return ;
					}
					else{
						if(obj.imported){
							swal("Imported !", obj.imported + " import success!", "success");
						}
						
						if(obj.errors){
							swal("Imported !",obj.msg, "error");
						}
						
					}
					
					setTimeout(function(){
						Scraper.tesLoad(),500
					});	
					$('body').progress('close');
				},
				error: function(jqXHR, textStatus){
					if(textStatus === 'timeout')
					{     
						toastr.error("Timeout problem","Error");
						$('.upload-list').trigger('click');
					}
				},
			}); 
		});
		
		
		$(document).on('click','.send-to-shopify', function () {
			var id = $(this).attr('data-brick');
			var type = $(this).attr('data-type');
			$.ajax({
				type: "POST",
				url: "/system/includes/pages/Shopify/Ajax/ajax.response.php?v="+Math.random(),
				dataType: 'json',
				data: {
					mode: 'send2Shopify',
					type: type,
					id: id
				},
				beforeSend: function(){
					$('body').progress('open');
					
				},
				success: function(obj){
					if(obj.error){
						swal("Sorry !", "There are something error!", "error");
					}
					else{
						if(obj.imported){
							swal("Imported !", obj.imported + "import success!", "success");
						}
						
						if(obj.errors){
							swal("Imported !",obj.msg, "error");
						}
						
					}
					
					setTimeout(function(){
						Scraper.tesLoad(),500
					});	
					$('body').progress('close');
				}
			});
		});
		
		
		
		$(document).off('click','table td .row-details');
		$(document).on('click', 'table td .row-details', function () {
            var nTr = $(this).parents('tr')[0];
			var td = $(this);
			
			if($(this).hasClass("row-details-open")){
				$(this).addClass("row-details-close").removeClass("row-details-open");   
				$(this).closest("tr").next("tr.details").remove();
			}  
			else{
				$.ajax({
					url:  "/system/includes/pages/Shopify/Ajax/ajax.response.php?v="+Math.random(),
					type: 'POST',
					dataType: 'json',
					data: {
					  cmd : "refresh",
					  type : 'lists',
					  tags : $('.selected-tags').val(),
					  status : $('.selected-status .active').find('input').val(),
					  mode : 'loadData',
					  customer: Scraper.customer,
					  parentTr : td.attr('data-id')
					},
					success: function(obj){
						var table = '<thead style="border-bottom: 1px solid #e7ecf1;">';
										table += '<tr>';
											table += '<th scope="col"> &nbsp; </th>';
											table += '<th scope="col"> Url </th>';
											table += '<th scope="col"> Products  </th>';
											table += '<th scope="col"> Scraped  </th>';
											table += '<th scope="col"> UnScraped  </th>';
											table += '<th scope="col"> Pagination  </th>';
											table += '<th scope="col"> Tag </th>';
											table += '<th scope="col"> Created </th>';
											table += '<th scope="col"> Updated </th>';
											table += '<th scope="col"> Action  </th>';
										table += '</tr>';
									table += '</thead>'; 
						if(obj.data != ''){
							table += '<tbody>' + obj.data + '</tbody>';
							$(td).addClass("row-details-open").removeClass("row-details-close");	
							$("<tr class=\"details\"><td>&nbsp;</td><td class=\"details\" colspan=\"7\"><table style=\"display:block;\" class=\"table table-bordered\">"+table+"</table></td></tr>").insertAfter(td.closest('tr'));
						}
					}
				});
				
				//$("<tr class=\"details\"><td><table><tbody>"+tableChild.table+"</tbody></table></td></tr>").insertAfter($(this).closest('tr'));
				
			}			
            
        });
		
		$(document).off('click','.show-to-detail');
		$(document).on('click','.show-to-detail', function () {
			var id = $(this).attr('data-brick');
			var type = $(this).attr('data-type');
			$.ajax({
				type: "POST",
				url: "/system/includes/pages/Shopify/Ajax/ajax.response.php?v="+Math.random(),
				dataType: 'json',
				data: {
					mode: 'showModal',
					type: type,
					id: id
				},
				beforeSend: function(){
					$('body').progress('open');
					$('#modal_response .modal-body').html('');
				},
				success: function(obj){
					if(obj.error){
						swal("Sorry !", "There are something error!", "error");
						$('body').progress('close');
					}
					else{
						$('#modal_response .modal-header').html(obj.title);
						$('#modal_response .modal-body').html(obj.html);
						$('#modal_response').modal('show');
					}
					
				},
				complete: function(){
					$('body').progress('close');
				}
			});
		});
		
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
		
		function scrapeList(ids , min,max){
			var min = min || false;
			var max = max || false;
			var urlstodo = [];
			urlstodo[0] = ids;
			var listId = '<? print $_SESSION["customerData"]["shopify_productlist"][0]["id"]; ?>';
			
			if(listId === ''){
				toastr.error("Element not found..");
				return;
			}
			
			var paginationsParams;
			var pagination = {};
			
			$.ajax({
				type: "POST",
				url: "/system/includes/pages/Scraper/import/ajax/ajax.scraper.php?v="+Math.random(),
				dataType: 'json',
				data: {
					id: listId,
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
					if(typeof dataRequest.paginationAttributes != null && dataRequest.paginationAttributes != "null" && dataRequest.paginationAttributes != null){
						paginationsParams = JSON.parse(dataRequest.paginationAttributes);	
						pagination['params'] =  paginationsParams.params;
						pagination['extension'] = paginationsParams.extension;
						pagination['min'] = paginationsParams.min;
						pagination['max'] =  paginationsParams.max;
						if(min != false){
							pagination['min'] = min;
						}
						
						if(min != false){
							pagination['max'] =  max;
						}
					}
					
					
					
					
					
				},
				complete: function(){
					var output = [];
					var noresult = [];
					
					$.ajax({
						type: "POST",
						url: "/system/includes/pages/Scraper/import/ajax/ajax.output.php?v="+Math.random(),
						dataType: 'json',
						data: {
							urls: urlstodo,
							pagination: pagination
						}, 
						beforeSend: function(){
							$('body').progress('open');
						},
						success : function(objs){
							var obj = objs.data;
							var paginations = objs.paginations;
							var parentLabel = 'list';
							urls = objs.urls;
							var jsondatax = {};
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
											//headMeta['spConfig'] = spConfig;
											if(headMeta['url'] == urlstodo[x]){
												headMeta['paginationUrls'] = paginations[x];
											}
											headMeta['productUrls'] = [];
											
											
											
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
																if(data.link == "true"){
																	headMeta['productUrls'].push(value);
																	console.log(value);
																}
																console.log("te");
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
								
								
								jsondatax = output;
								
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
										output: jsondatax, 
										mode: 'save_data',
										unique_attribute: uniqueAttribute,
										typeScraper	:  'shopify_productlist'	,
										parentId: parentId,
										parentData: parentLabel,
										changeStatus: true,
										saveLinks: true
									},
									success: function(){
										Scraper.tesLoad('lists',false,ids);
										toastr.success("Scrape done","Success!!");
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
				}
			});
		}
		
		function setRequests(ids){
			var urlstodo = [];
			urlstodo[0] = ids;
			//var listId = 'AUVD-PEPV-7929-TAXT-HDMX-7842-OQ';
			var listId = '<? print $_SESSION["customerData"]["shopify_product"][0]["id"]; ?>';
			if(listId === ''){
				toastr.error("Element not found..");
				return;
			}
			
			$.ajax({
				type: "POST",
				url: "/system/includes/pages/Scraper/import/ajax/ajax.scraper.php?v="+Math.random(),
				dataType: 'json',
				data: {
					id: listId,
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
					var output = [];
					var noresult = [];
					$.ajax({
						type: "POST",
						url: "/system/includes/pages/Scraper/import/ajax/ajax.output.php?v="+Math.random(),
						dataType: 'json',
						data: {
							urls: urlstodo
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
										Scraper.tesLoad('products',false,ids);
										toastr.success("Scrape done","Success!!");
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
				}
			});
		}
		
		//Scraper.loadData
		
		$(document).on('click','.rescrape-shopify', function () {
			setRequests($(this).attr("data-url"));
		});
		
		$(document).on('click','.rescrape-list', function () {
			var id = $(this).attr("data-brick");
			var url = $(this).attr("data-url");
			$.ajax({
				type: "POST",
				url: "/system/includes/pages/Scraper/import/ajax/ajax.json.php?v="+Math.random(),
				
				data: {
					mode: 'get_List',
					id : id
					
				},
				dataType: 'json',
				success: function(obj){
					//scrapeList();	
					
					paginationUrls = obj.data['pages'];
					console.log(paginationUrls);
					if(paginationUrls > 10){
						//show popup
						$('#scrapeList .modal-body .hereFields').html(obj.html);
						$('#scrapeList').modal("show");
					}
					else{
						scrapeList(url);	
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
		
		$(document).on('click','.remove-item', function () {
			var ids = [];
			$("table:visible tr .show-to-detail").each(function(i){
				ids[i] = $(this).attr("data-brick");
			});
			
			if(ids.length <= 0){
				toastr.error("Not found data to change.","Error!");
				return;
			}
			
			swal("Are you sure to delete shown items..?", {
				  buttons: {
					cancel: "Cancel!",
					catch: {
					  text: "Yes I'm Sure!",
					  value: "catch",
					},
					defeat: {
					  text: "No, Remove all items on this page.",
					  value: "defeat",
					},
				  },
				})
				.then((value) => {
				  switch (value) {
				 
					case "defeat":
						$.ajax({
							type: "POST",
							url: "/system/includes/pages/Shopify/Ajax/ajax.response.php?v="+Math.random(),
							dataType: 'json',
							data: {
								mode: 'removeItems',
								type: $('.btn-choose-type .active').find('input').val(),
								params: Scraper.results
							},
							beforeSend: function(){
								$('body').progress('open');
								
							},
							success: function(obj){
								Scraper.tesLoad();
							},
							complete: function(){
								$('body').progress('close');
							},
							error: function(){
								$('body').progress('close');
							}
						});
					    break;
				 
					case "catch":
						$.ajax({
							type: "POST",
							url: "/system/includes/pages/Shopify/Ajax/ajax.response.php?v="+Math.random(),
							dataType: 'json',
							data: {
								mode: 'removeItems',
								type: $('.btn-choose-type .active').find('input').val(),
								params: ids
							},
							beforeSend: function(){
								$('body').progress('open');
								
							},
							success: function(obj){
								Scraper.tesLoad();
							},
							complete: function(){
								$('body').progress('close');
							},
							error: function(){
								$('body').progress('close');
							}
						});
						
						break;
				 
					default:
						return ;
				  }
				});
			
		});
		
		$(document).on('click','.change-status', function () {
			var ids = [];
			$("table:visible tr .show-to-detail").each(function(i){
				ids[i] = $(this).attr("data-brick");
			});
			
			if(ids.length <= 0){
				toastr.error("Not found data to change.","Error!");
				return;
			}
			$.ajax({
				type: "POST",
				url: "/system/includes/pages/Shopify/Ajax/ajax.response.php?v="+Math.random(),
				dataType: 'json',
				data: {
					mode: 'changeStatus',
					id: ids,
					type: $('.btn-choose-type .active').find('input').val(),
					params: Scraper.results
				},
				beforeSend: function(){
					$('body').progress('open');
					
				},
				success: function(obj){
					Scraper.tesLoad();
				},
				complete: function(){
					$('body').progress('close');
				},
				error: function(){
					$('body').progress('close');
				}
			});
		});
		
		$(document).on('click','.queue-items', function () {
			var ids = [];
			$("table:visible tr .show-to-detail").each(function(i){
				ids[i] = $(this).attr("data-brick");
			});
			
			if(ids.length <= 0){
				toastr.error("Not found data to change.","Error!");
				return;
			}
			
			
			$.ajax({
				type: "POST",
				url: "/system/includes/pages/Shopify/Ajax/ajax.response.php?v="+Math.random(),
				dataType: 'json',
				data: {
					mode: 'queueItems',
					id: ids,
					type: $('.btn-choose-type .active').find('input').val(),
					params: Scraper.results
				},
				beforeSend: function(){
					$('body').progress('open');
					
				},
				success: function(obj){
					//Scraper.tesLoad();
				},
				complete: function(){
					$('body').progress('close');
				},
				error: function(){
					$('body').progress('close');
				}
			});
		});
		
		$(document).on('click','.btn-view-scrape', function (e) {
			e.preventDefault();
			var href = $(this).attr("href");
			
			swal({
			  title: "Do you want to only show status pending?",
			  icon: "info",
			  buttons: true,
			  dangerMode: false,
			})
			.then((onlyActive) => {
			  if (onlyActive) {
				
				window.open(href + "&status=0");
			  } else {
				window.open(href);
			  }
			});
		});
		 
		$(document).on('click','.btn-scrapeList ', function () { 
			var url = $(this).attr("data-url");
			var min = $(this).attr("data-min");
			var max = $(this).attr("data-max");
			scrapeList(url,min , max);
			$("#scrapeList").modal("hide");
		});
		
		$(document).on('change','#number_record', function () {
			var number = $(this).val();
			$("table:visible tr:lt("+number+")").show();
			if(number != 'all'){
				$("table:visible tr:gt("+number+")").hide();
			}
			else{
				$("table:visible tr").show();
			}
			
			
		});
		
		$(document).on('click','.dropdown-menu li a', function (e) {
			var arrIds = [];
			var labelTag = [];
			arrIds.push($(this).closest("li").attr('data-brick')); 
			labelTag.push($(this).text()); 
			if($('input.withchilds').is(":checked")){
				$(this).next('ul').find("li").each(function(){
					arrIds.push($(this).attr('data-brick')); 
					labelTag.push($(this).find("a").first().text()); 
				});
			}
			
			

			$(".lblTags span.selected").text(labelTag.join(", "));
			Scraper.selectedTags = arrIds;
			e.stopPropagation();
			e.preventDefault();
			Scraper.tesLoad();
		});
		
		$('.setDeveloperMode').click(function(){
		
		var selected = $('#modal-selected-customer').val();
		
		if(selected == "0"){
			toastr.error("Please choose the customer..");
			return ;
		}
		
		$.ajax({
			type: "POST",
			url: "/system/includes/pages/Scraper/import/ajax/ajax.scraper.php?v="+Math.random(),
			dataType: 'json',
			data: {
				mode: 'setCustomerAsDev',
				selected: selected
			},
			beforeSend: function(){
				$('body').progress('open');
			},
			success: function(obj){
				if(obj.data){
					toastr.success("Customer was set..");
					$('.selected-customer').val(obj.data).trigger('change');
				}
				else{
					toastr.error("Customer invalid to set..");
				}
				$('body').progress('close');
			},
			complete: function(){
				$('#customer').modal("hide");
				
			}
		});
	});
		
	});
</script>
	
