var jsondata; 
	var jsoncollection; 
	
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
	
	function cleanUp(){
		var typeScraper =  $(".type-scraper").val();
		$('.show-data').hide();
		$(".form-collection").hide();
		$(".json-collection").hide();
		$('.show-array').hide();
		$('.productlist-only').hide();
		$('.product-only').hide();
		$('.resultsLinks').hide();
		$('.show-result').hide();
	}
	
	function clearconsole()
	{ 
	   if(window.console )
	   {    
		console.clear();  
	   }
	}
	 
	function loadData(){
		var collection_key = new Array();
		var collection_value = [];
		$(".data-form-scraper").each(function(i){
			collection_value[i] = $(this).find(".data-label-scraper").val();
		});
		
		$(".data-head-scraper").each(function(i){
			collection_key[i] = $(this).find(".head-label-scraper").val();
		});
		
		var select1 = $("#collection_key");
		var select2 = $("#collection_value");
		
		$('option', select1).remove();
		$.each(collection_key, function(text, key) {
			var option = new Option(key, key);
			select1.append($(option));
		});
		
		$('option', select2).remove();
		$.each(collection_value, function(text, key) {
			var option = new Option(key, key);
			select2.append($(option));
		});
		
		//$('.form-collection').show();

	}
	
	function getScraperOptions (selected) {
		var selected = selected || 0;
		
		$.ajax({
			type: "POST",
			url: "/system/includes/pages/Scraper/import/ajax/ajax.scraper.php?v="+Math.random(),
			dataType: 'json',
			data: {
				mode: 'getScraperOption'
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
				
				$('.selected-scraper').html(opt);
				
				
				
				
				if(selected == "" || !selected || selected == 0){
					$(".selected-button-history").hide();
					$(".selected-button-remove").hide();
				}
				else{
					$(".selected-button-history").show();
					$(".selected-button-remove").show();
				}
				
			}
		});
	}
	
	function getCustomerOptions(){
		var selected = selectedCustomer || 0;
		
		$.ajax({
			type: "POST",
			url: "/system/includes/pages/Scraper/import/ajax/ajax.scraper.php?v="+Math.random(),
			dataType: 'json',
			data: {
				mode: 'getCustomerOption'
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
				
				$('.selected-customer').html(opt);
				
				if(selected == "" || !selected || selected == 0){
					$(".selected-button-history").hide();
					$(".selected-button-remove").hide();
				}
				else{
					$(".selected-button-history").show();
					$(".selected-button-remove").show();
				}
				
				if(selectedCustomer){
					$('.selected-customer').trigger('change');
				}
				
			}
		});
	}
	
	
	
	$(document).ready(function () {
		getCustomerOptions();
		//getScraperOptions();
		$(".create-collection").click(function(){
			if($('.output-scraper').val() == ""){
				toastr.error("Please scrap first to make collection..");
				return ;
			}
			var keys = $("#collection_key").val();
			var values = $("#collection_value").val();
			var parent = $(".parent-label-scraper").val();
			$.ajax({
				type: "POST",
				url: "/system/includes/pages/Scraper/import/ajax/json.php?v="+Math.random(),
				data: {
					mode: "create_collection",
					parent : parent,
					data: jsondata, 
					keys: keys,
					values : values,
					type : $(".type-scraper").val()
				},
				dataType: 'json',
				beforeSend : function(){
					$('.output_collection').attr('disabled','disabled').val('');
					//$('.output-scraper').attr('disabled','disabled').val('');
				},
				success: function(obj){
					if(!obj.error){
						var collection = obj.collection;
						collection = JSON.stringify(collection);
						collection = collection.replace(/'/g, "\\'");
						retrievedJSON = JSON.stringify(obj.jsondata);
						retrievedJSON = retrievedJSON.replace(/'/g, "\\'");
						jsoncollection = retrievedJSON;
						$('.output_collection').removeAttr('disabled').val(collection);
						$('.group_output_collection').show();
						
						var json_array = $.parseJSON (retrievedJSON);
						//$('.output-scraper').removeAttr('disabled').val(retrievedJSON);
					}
					 
				}
			}); 
			
			
		});
		$('.type-scraper').change(function(){
			var typeScraper = $(this).val();
			var name = $('.name-scraper').val();
			var shop = $('.selected-scraper').find(":selected").text();
			$("#url").show();
			$("#list").hide();
			$(".product-only").hide();
			if(typeScraper == "shopify_productlist"){ 
				$('.export-data .import-csv').hide();
				$('.export-data').show();
				$(".link-block").show();
				$(".parent-label-scraper").val("productList");
				if(name == ''){
					$('.name-scraper').val("Product List - "+shop);
				}
				
			}
			else if(typeScraper == "shopify_product"){
				if(name == ''){
					$('.name-scraper').val("Product - "+shop);
				}
				$(".product-only").show();
				$(".selected-product-data .btn.active").trigger('click');
				
			}
			else if(typeScraper == "shopify_page"){ 
				$(".link-block").show();
			}
			else {
				$('.export-data .show-array').hide();
				$('.export-data .import-csv').hide(); 
				$('.export-data').hide();
				$(".form-collection").hide();
				$(".json-collection").hide();
				$(".link-block").hide();
				if(name == ''){
					$('.name-scraper').val("Scraper - "+shop);
				}
			}
			
			
		});
		
		
		
		$(".showShopifyHelp").click(function(){
			//alert('"Handle","Title","Body (HTML)","Vendor","Variant SKU","Variant Inventory Qty","Variant Price","Variant Grams","Image Src"');
			$("#ajax_shopify").modal("show");
		});
		
		
		$(".show-array").click(function(){
			if($('.output-scraper').val() == ""){
				toastr.error("Please scrap first to make collection..");
				return ;
			}
			
			$.ajax({
				type: "POST",
				url: "/system/includes/pages/Scraper/import/ajax/json.php?v="+Math.random(),
				data: {
					mode: "convert_to_array",
					postdata: jsondata, 
					collection : $(".output-scraper").val(),
					//jsoncollection: jsoncollection
				},
				beforeSend: function(){
					$(".output-results-ajax").html('');
				},
				dataType: 'html',
				success: function(obj){
					//$("#ajax_response .modal-body").html(obj);
					//$("#ajax_response").modal("show");
					$(".output-results-ajax").html(obj);
				}
			});
			
		});
		
		$(".import-csv").click(function(){
			var params = $("textarea.output-scraper").val();
			var parent = $(".parent-label-scraper").val();
			var name = $(".name-scraper").val();
			if(params == ""){
				toastr.error("Please scrap first to make collection..");
				return ;
			}
			var shopify = false;

			var keys = $("#collection_key").val();
			var values = $("#collection_value").val();
			var sc = $(".output-scraper").val();

			$.ajax({
				type: "POST",
				url: "/system/includes/pages/Scraper/import/ajax/ajax.json.php?v="+Math.random(),
				//contentType: "application/json; charset=utf-8",
				data: {
					mode: "prepare_csv"	,
					//params: sc,
					//json_data: jsondata,
					collection: $('.output_collection').val(), 
					customer: $('.selected-customer').val(), 
					parent: parent,
					keys: keys,
					values: values,
					type: $(".type-scraper").val(), 
					shopify:shopify,
					name : name,
					urls: $(".input-scraper").val()
				},
				dataType: 'json',
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
						$(".slideButtons").show();
						$("#tbl1").html(obj.table);
						$("#toogle-1").show();
						
						$("table#tbl1").find("th").each(function(index){
							//th[index] = $(this).text();
							var text = $(this).find('label').text();
							if(text.length <= 0){
								text = "Undefined";
							}
							$(this).find("select.header-select-"+text).val(text)
						});
						
						$('html, body').animate({
							scrollTop: $("#toogle-1").offset().top
						}, 1000);
						
						/* if(obj.message){
							window.open(obj.message,'_blank');
							$(".slideButtons").show();
						}
						else{
							$(".slideButtons").hide();
						} */
						$('form#tb1').doubleScroll();
						$('body').progress('close');
					}

				}
			});
		});

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
					
					$('.selected-scraper').html(opt);
					
					
					
					if(selected == "" || !selected || selected == 0){
						$(".selected-button-history").hide();
						$(".selected-button-remove").hide();
					}
					else{
						$(".selected-button-history").show();
						$(".selected-button-remove").show();
					}
					
					
					var opt = '<option value="0">Choose List</option>';
				
					if(!obj.error) {
						$.each(obj.lists, function(i, op) {
							var optSelected = '';
							
							if (selected == op.id) {
								var optSelected = 'selected="selected"';
							}
							
							opt += '<option value="'+op.id+'" '+optSelected+'>'+op.name+'</option>';
						});
						
						$('.input-list-scraper').html(opt);
					}
					
					
				},
				complete: function(){
					$('.selected-scraper').val(0).trigger('change');
					$('body').progress('close');
				}
			});
			
		});
		
		$('.selected-scraper').change(function () {
			var id = $(this).val();
			cleanUp();
			$.ajax({
				type: "POST",
				url: "/system/includes/pages/Scraper/import/ajax/ajax.scraper.php?v="+Math.random(),
				data: {
					id: id,
					mode: "getScraper"				
				},
				dataType: 'json',
				beforeSend: function(){
					$('.form-collection').hide();
					$(".json-collection").hide();
					if(id == 0){
						$(".selected-button-history").hide();
						$(".selected-button-remove").hide();
					}
					else{
						$(".selected-button-history").show();
						$(".selected-button-remove").show();
					}
				},
				success: function(obj){
					if (!obj.error) {
						var name = obj.data.name;
						var input = obj.data.urls;
						var headData = obj.data.headData;
						var elementData = obj.data.element;
						var parentData = obj.data.parentData;
						var data = obj.data.data;
						var tags = obj.data.tags;
						var typeS = obj.data.type;
						var note = obj.data.note;
						var unique = obj.data.unique_attribute;
						var paginations = obj.data.paginationAttributes;
						if(typeS == "shopify_productlist"){
							//$(".show-array").show();
							//$(".export-data").show();
							//$(".import-csv").hide();
							//$(".form-collection").hide();
							//$(".json-collection").hide();
						}
						else if(typeS == "shopify_product"){
							//$(".show-array").hide();
							//$(".import-csv").show();
							//$(".export-data").show();
							//$(".form-collection").hide();
							//$(".json-collection").hide();
						}
						else {
							typeS = 0;
							//$(".show-array").hide();
							//$(".export-data").hide();
							//$(".import-csv").hide();
							//$(".form-collection").hide(); 
							//$(".json-collection").hide();
						}
						
						$('.name-scraper').val(name);
						$('.input-scraper').val(input);
						$('.type-scraper').val(typeS);
						$('.input-note').val(note);
						
						
						var dataTags = $('.data-tags-scraper').first().clone();
						$('.data-tags-scraper').remove();
						
						if (tags != null) {
							$('.tags-scrapper').show();
							$('.wrap-show-tag-scraper').hide();
							
							$.each(tags, function(i,form) {
								var newForm = dataTags.clone();
								
								newForm.find('.tags-tag-scraper').val(form.tag);
								
								newForm.insertBefore(".wrap-add-tags-scraper");
							});
						}
						else {
							$('.tags-scrapper').hide();
							$('.wrap-show-tag-scraper').show();
							
							var newForm = dataTags.clone();
							newForm.find('.tags-tag-scraper').val('');
							
							newForm.insertBefore(".wrap-add-tags-scraper");
						}
						
						var dataHead = $('.data-head-scraper').first().clone();
						$('.data-head-scraper').remove();
						
						if (headData != null) {
							$('.head-scrapper').show();
							$('.wrap-show-head-scraper').hide();
							
							$.each(headData, function(i,form) {
								var newForm = dataHead.clone();
								
								newForm.find('.head-tag-scraper').val(form.tag);
								newForm.find('.head-label-scraper').val(form.label);
								newForm.find('.head-type-scraper').val(form.type);
								newForm.find('.head-attr-scraper').val(form.attr);
								newForm.find('.unique_attribute').val(form.label);
								if (form.type == "attr") {
									newForm.find('.head-attr-scraper').removeAttr('disabled');
								}
								else {
									newForm.find('.head-attr-scraper').attr('disabled','disabled');
								}
								
								newForm.insertBefore(".wrap-add-head-scraper");
							});
						}
						else {
							$('.head-scrapper').hide();
							$('.wrap-show-head-scraper').show();
							
							var newForm = dataHead.clone();
							newForm.find('.head-tag-scraper').val('');
							newForm.find('.head-label-scraper').val('');
							newForm.find('.head-type-scraper').val('text');
							newForm.find('.head-attr-scraper').val('').attr('disabled','disabled');
							newForm.find('.unique_attribute').val('');
							newForm.insertBefore(".wrap-add-head-scraper");
						}
						
						var dataElement = $('.data-element-scraper').first().clone();
						$('.data-element-scraper').remove();
						
						if (elementData != null) {
							$('.element-scrapper').show();
							$('.wrap-show-element-scraper').hide();
							
							$.each(elementData, function(i,form) {
								var newForm = dataElement.clone();
								
								newForm.find('.element-tag-scraper').val(form.tag);
								newForm.find('.element-label-scraper').val(form.label);
								newForm.find('.element-type-scraper').val(form.type);
								newForm.find('.element-attr-scraper').val(form.attr);
								newForm.find('.unique_attribute').val(form.label);
								if (form.type == "attr") {
									newForm.find('.element-attr-scraper').removeAttr('disabled');
								}
								else {
									newForm.find('.element-attr-scraper').attr('disabled','disabled');
								}
								
								newForm.insertBefore(".wrap-add-element-scraper");
							});
						}
						else {
							$('.element-scrapper').hide();
							$('.wrap-show-element-scraper').show();
							
							var newForm = dataElement.clone();
							newForm.find('.element-tag-scraper').val('');
							newForm.find('.element-label-scraper').val('');
							newForm.find('.element-type-scraper').val('text');
							newForm.find('.element-attr-scraper').val('').attr('disabled','disabled');
							newForm.find('.unique_attribute').val('');
							newForm.insertBefore(".wrap-add-element-scraper");
						} 
						
						if(typeof parentData.tag != 'undefined'){
							$('.parent-tag-scraper').val(parentData.tag);
						}
						
						
						$('.parent-label-scraper').val(parentData.label);
						$('.ignore-parent-tag-scraper').val(parentData.ignore);
						
						if(unique){
							$(".unique_attribute[value="+unique+"]").prop("checked",true);
						}
						
						$("input[name='pagination']").prop('checked',false);
						$("input[name='pagination']").trigger('change');
						if(paginations  != null && paginations  != 'null'){
							paginations = JSON.parse(paginations);
							if(paginations.params != null) {
								$('.extra_url').val(paginations.params);
								$('.minp').val(paginations.min);
								$('.maxp').val(paginations.max);
								$('.extension').val(paginations.extension);
								$("input[name='pagination']").prop('checked',true);
								$("input[name='pagination']").trigger('change');
							}
							
						}
						
						
						var dataForm = $('.data-form-scraper').first().clone();
						$('.data-form-scraper').remove();
						
						$.each(data, function(i,form) {
							var newForm = dataForm.clone();
							
							newForm.find('.data-tag-scraper').val(form.tag);
							newForm.find('.data-label-scraper').val(form.label);
							newForm.find('.data-type-scraper').val(form.type);
							newForm.find('.data-attr-scraper').val(form.attr);
							
							newForm.find('.data-link-scraper').prop('checked',false);
							if(form.link == 'true'){
								newForm.find('.data-link-scraper').prop('checked',true);
							}
							if (form.type == "attr") {
								newForm.find('.data-attr-scraper').removeAttr('disabled');
							}
							else {
								newForm.find('.data-attr-scraper').attr('disabled','disabled');
							}
							
							if (i == 0) {
								//newForm.find('.wrap-delete-data-scraper').hide();
							}
							else {
								//newForm.find('.wrap-delete-data-scraper').show();
							}
							
							newForm.insertBefore(".wrap-add-data-scraper");
						});
						$('.selected-button-remove').attr("data-value",id);
						$('.selected-button-history').attr("data-value",id);
						$('.output-scraper').val('');
						$('.wrap-noresult-scraper').hide();
						$('.noresult-scraper').val('');
						$('#tbl1').html('');
						if(obj.output){
							$('.output-scraper').val(obj.output);
							$('.show-data').show();
							$('.show-result').show();
							$('.show-history').show();
							if(typeS == "shopify_productlist"){
								$('.show-array').show();
								$('.productlist-only').show();
								$('.product-only').hide();
							}
							else{
								$('.show-array').hide();
								$('.productlist-only').hide();
								$('.product-only').show();
								$('.export-data').show();
								
							}
						}
					}
					$('body').progress('close');
				},
				complete: function(){
					$('.type-scraper').trigger('change');
					
				}
				
				/*  */
				
			});
		});
		
		$('.selected-button-history').click(function(){
			var id = $('.selected-scraper').val();
			$.ajax({
				type: "POST",
				dataType: "json",
				url: "/system/includes/pages/Scraper/import/ajax/ajax.scraper.php?v="+Math.random(),
				data: {
					id: id,
					mode: "historyElement"
				},
				success: function(resp){
					window.open("http://jeppekjaersgaard.dk/system/index.php?page=scraper&history="+id , '_blank');
				},
				complete: function(){
					
				}
			});
		});
		
		$('.selected-button-remove').click(function(){
			var id = $('.selected-scraper').val();
			$.ajax({
				type: "POST",
				dataType: "json",
				url: "/system/includes/pages/Scraper/import/ajax/ajax.scraper.php?v="+Math.random(),
				data: {
					id: id,
					mode: "removeElement"
				},
				success: function(resp){
					if(resp.error){
						toastr['error']("Unsuccess delete", "Error!");
					}
					else {
						toastr['success']("Success deleted", "Success!");
					}
					
				},
				complete: function(){
					getScraperOptions();
				}
			});
		});
		
		$('.save-scraper').click(function () {
			var error = false;
			var id = $('.selected-scraper').val();
			var name = $('.name-scraper');
			var url = $('.input-scraper');
			var typeScraper = $('.type-scraper').val();
			var note = $('.input-note').val();
			var unique_attribute = $('input[name="unique_attribute"]:checked').val();
			if(unique_attribute === ""){
				unique_attribute = "href";
			}
			name.closest('.form-group').removeClass('has-error');
			if (name.val() == "") {
				name.closest('.form-group').addClass('has-error');
				error = true;
			}
			
			url.closest('.form-group').removeClass('has-error');
			if (url.val() == "") {
				url.closest('.form-group').addClass('has-error');
				error = true;
			}
			
			var tags = [];
			if ($('.tags-scrapper').is(":visible")) {
				$('.data-tags-scraper').each(function () {
					var tag = $(this).find('.tags-tag-scraper');
					
					var setting = {};
					setting["tag"] = tag.val();
					
					tag.closest('.form-group').removeClass('has-error');
					if (tag.val() == "") {
						tag.closest('.form-group').addClass('has-error');
						error = true;
					}
					tags.push(setting);
				});
			}
			
			var headMeta = [];
			
			if ($('.head-scrapper').is(":visible")) {
				$('.data-head-scraper').each(function () {
					var tag = $(this).find('.head-tag-scraper');
					var label = $(this).find('.head-label-scraper');
					var type = $(this).find('.head-type-scraper');
					var attr = $(this).find('.head-attr-scraper');
					
					
					var setting = {};
					setting["tag"] = tag.val();
					setting["label"] = label.val();
					setting["type"] = type.val();
					setting["attr"] = attr.val();
					
					
					tag.closest('.form-group').removeClass('has-error');
					if (tag.val() == "") {
						tag.closest('.form-group').addClass('has-error');
						error = true;
					}
					
					label.closest('.form-group').removeClass('has-error');
					if (label.val() == "") {
						label.closest('.form-group').addClass('has-error');
						error = true;
					}
					
					attr.closest('.form-group').removeClass('has-error');
					if (attr.val() == "" && type.val() == "attr") {
						attr.closest('.form-group').addClass('has-error');
						error = true;
					}
					
					headMeta.push(setting);
				});
			}
			
			var bodyElement = [];
			
			if ($('.element-scrapper').is(":visible")) {
				$('.data-element-scraper').each(function () {
					var tag = $(this).find('.element-tag-scraper');
					var label = $(this).find('.element-label-scraper');
					var type = $(this).find('.element-type-scraper');
					var attr = $(this).find('.element-attr-scraper');
					
					
					var setting = {};
					setting["tag"] = tag.val();
					setting["label"] = label.val();
					setting["type"] = type.val();
					setting["attr"] = attr.val();
					
					
					tag.closest('.form-group').removeClass('has-error');
					if (tag.val() == "") {
						tag.closest('.form-group').addClass('has-error');
						error = true;
					}
					
					label.closest('.form-group').removeClass('has-error');
					if (label.val() == "") {
						label.closest('.form-group').addClass('has-error');
						error = true;
					}
					
					attr.closest('.form-group').removeClass('has-error');
					if (attr.val() == "" && type.val() == "attr") {
						attr.closest('.form-group').addClass('has-error');
						error = true;
					}
					
					bodyElement.push(setting);
				});
			}
			
			var parent = {};
			var parentTag = $('.parent-tag-scraper');
			var parentLabel = $('.parent-label-scraper');
			var parentIgnore = $('.ignore-parent-tag-scraper');
			parent["tag"] = parentTag.val();
			parent["label"] = parentLabel.val();
			parent["ignore"] = parentIgnore.val();
			
			parentTag.closest('.form-group').removeClass('has-error');
			if (parentTag.val() == "") {
				parentTag.closest('.form-group').addClass('has-error');
				error = true;
			}
			
			parentLabel.closest('.form-group').removeClass('has-error');
			if (parentLabel.val() == "") {
				parentLabel.closest('.form-group').addClass('has-error');
				error = true;
			}
			
			
			var data = [];
			
			$('.data-form-scraper').each(function () {
				var tag = $(this).find('.data-tag-scraper');
				var label = $(this).find('.data-label-scraper');
				var type = $(this).find('.data-type-scraper');
				var attr = $(this).find('.data-attr-scraper');
				var link = $(this).find('.data-link-scraper');
				
				var setting = {};
				setting["tag"] = tag.val();
				setting["label"] = label.val();
				setting["type"] = type.val();
				setting["attr"] = attr.val();
				setting["link"] = false;
				
				if(link.is(":checked")){
					setting["link"] = true;
				}
				
				tag.closest('.form-group').removeClass('has-error');
				if (tag.val() == "") {
					tag.closest('.form-group').addClass('has-error');
					error = true;
				}
				
				label.closest('.form-group').removeClass('has-error');
				if (label.val() == "") {
					label.closest('.form-group').addClass('has-error');
					error = true;
				}
				
				attr.closest('.form-group').removeClass('has-error');
				if (attr.val() == "" && type.val() == "attr") {
					attr.closest('.form-group').addClass('has-error');
					error = true;
				}
				
				data.push(setting);
			});
			
			var parentId = $('.selected-customer').val();
			var output = $('.output-scraper').val();
			
			var pagination = {};
			if($("input[name='pagination']").is(":checked")){
				pagination['params'] = $('.extra_url').val();
				pagination['extension'] = $('.extension').val();
				pagination['min'] = $('.minp').val();
				pagination['max'] = $('.maxp').val();
			}
			
			if (!error) {
				$.ajax({
					type: "POST",
					url: "/system/includes/pages/Scraper/import/ajax/ajax.scraper.php?v="+Math.random(),
					data: {
						id: id,
						name: name.val(),
						url: url.val(),
						note: note,
						head: headMeta,
						parentData: parent,
						data: data,
						tags : tags,
						typeScraper : typeScraper,
						parentId: parentId,
						output: output,
						unique_attribute : unique_attribute,
						pagination : pagination,
						element: bodyElement,
						mode: 'saveScraper'						
					},
					dataType: 'json',
					beforeSend: function(){
						$('body').progress('open');
					},
					success: function(obj){
						if (obj.error) {
							toastr['error']("Failed to save scraper", "Error!");
						}
						else {
							toastr['success']("Scraper is now saved", "Success!");
							getScraperOptions(obj.id);
						}
					},
					complete: function(){
						$('body').progress('close');
					}
					
				});
			}
			else {
				toastr['error']("Please fill the required fields", "Error!");
			}
		});
		
		
		$('.import-scraper').click(function () {
			
			// Validate Form
			var error = false;
			
			var inputUrl = $('.input-scraper');
			var typeScraper = $('.type-scraper').val();
			
			inputUrl.closest('.form-group').removeClass('has-error');
			
			if (inputUrl.val() == "") {
				inputUrl.closest('.form-group').addClass('has-error');
				error = true;
			}
			
			if($("#list").is(":visible") && $(".input-list-scraper").val() == 0){
				$(".input-list-scraper").addClass('has-error');
				error = true;
			}
			
			$('#tbl1').html('');
			$(".output-results-ajax").html('');
			cleanUp(); //clean all
			if ($('.head-scrapper').is(":visible")) {
				$('.data-head-scraper').each(function () {
					var tag = $(this).find('.head-tag-scraper');
					var label = $(this).find('.head-label-scraper');
					var type = $(this).find('.head-type-scraper');
					var attr = $(this).find('.head-attr-scraper');
					
					tag.closest('.form-group').removeClass('has-error');
					if (tag.val() == "") {
						tag.closest('.form-group').addClass('has-error');
						error = true;
					}
					
					label.closest('.form-group').removeClass('has-error');
					if (label.val() == "") {
						label.closest('.form-group').addClass('has-error');
						error = true;
					}
					
					attr.closest('.form-group').removeClass('has-error');
					if (attr.val() == "" && type.val() == "attr") {
						attr.closest('.form-group').addClass('has-error');
						error = true;
					}
				});
			}
			
			if ($('.element-scrapper').is(":visible")) {
				$('.data-element-scraper').each(function () {
					var tag = $(this).find('.element-tag-scraper');
					var label = $(this).find('.element-label-scraper');
					var type = $(this).find('.element-type-scraper');
					var attr = $(this).find('.element-attr-scraper');
					
					tag.closest('.form-group').removeClass('has-error');
					if (tag.val() == "") {
						tag.closest('.form-group').addClass('has-error');
						error = true;
					}
					
					label.closest('.form-group').removeClass('has-error');
					if (label.val() == "") {
						label.closest('.form-group').addClass('has-error');
						error = true;
					}
					
					attr.closest('.form-group').removeClass('has-error');
					if (attr.val() == "" && type.val() == "attr") {
						attr.closest('.form-group').addClass('has-error');
						error = true;
					}
				});
			}
			
			var parentTag = $('.parent-tag-scraper');
			var parentLabel = $('.parent-label-scraper');
			
			parentTag.closest('.form-group').removeClass('has-error');
			if (parentTag.val() == "") {
				parentTag.closest('.form-group').addClass('has-error');
				error = true;
			}
			
			parentLabel.closest('.form-group').removeClass('has-error');
			if (parentLabel.val() == "") {
				parentLabel.closest('.form-group').addClass('has-error');
				error = true;
			}
			
			$('.data-form-scraper').each(function () {
				var tag = $(this).find('.data-tag-scraper');
				var label = $(this).find('.data-label-scraper');
				var type = $(this).find('.data-type-scraper');
				var attr = $(this).find('.data-attr-scraper');
				var link = $(this).find('.data-link-scraper');
				
				tag.closest('.form-group').removeClass('has-error');
				if (tag.val() == "") {
					tag.closest('.form-group').addClass('has-error');
					error = true;
				}
				
				label.closest('.form-group').removeClass('has-error');
				if (label.val() == "") {
					label.closest('.form-group').addClass('has-error');
					error = true;
				}
				
				attr.closest('.form-group').removeClass('has-error');
				if (attr.val() == "" && type.val() == "attr") {
					attr.closest('.form-group').addClass('has-error');
					error = true;
				}
				
				/* 				
				link.closest('.form-group').removeClass('has-error');
				if (attr.val() == "" && type.val() == "attr") {
					attr.closest('.form-group').addClass('has-error');
					error = true;
				} 
				*/
				
			});
			
			
			var urls = [];
			var parentUrls = [];
			var requests = [];
			
			// Build Array Request
			var request = {};
			
			
			request['tags'] = [];
			
			$('.data-tags-scraper').each(function () {
				var tag = $(this).find('.tags-tag-scraper').val();
				var data = {};
				data["tag"] = tag;
				
				if ( tag != '') {
					request['tags'].push(data);
				}
			});
			
			request['head'] = [];
			
			$('.data-head-scraper').each(function () {
				var tag = $(this).find('.head-tag-scraper').val();
				var label = $(this).find('.head-label-scraper').val();
				var type = $(this).find('.head-type-scraper').val();
				var attr = $(this).find('.head-attr-scraper').val();
				
				var data = {};
				data["label"] = label;
				data["tag"] = tag;
				data["type"] = type;
				data["attr"] = attr;
				
				if (label != '' && tag != '' && type != '') {
					if (type == 'attr' && attr != '') {
						request['head'].push(data);
					}
					
					if (type != 'attr') {
						request['head'].push(data);
					}
				}
			});
			
			request['element'] = [];
			$('.data-element-scraper').each(function () {
				var tag = $(this).find('.element-tag-scraper').val();
				var label = $(this).find('.element-label-scraper').val();
				var type = $(this).find('.element-type-scraper').val();
				var attr = $(this).find('.element-attr-scraper').val();
				
				var data = {};
				data["label"] = label;
				data["tag"] = tag;
				data["type"] = type;
				data["attr"] = attr;
				
				if (label != '' && tag != '' && type != '') {
					if (type == 'attr' && attr != '') {
						request['element'].push(data);
					}
					
					if (type != 'attr') {
						request['element'].push(data);
					}
				}
			});
			
			
			request['tag'] = $('.parent-tag-scraper').val();
			request['label'] = $('.parent-label-scraper').val();
			request['ignore'] = $('.ignore-parent-tag-scraper').val();
			request['data'] = [];
			
			/* var primary = {};
				primary["label"] = 'primary';
				primary["tag"] = 'parent';
				primary["type"] = 'text';
				primary["attr"] = attr; */
			
			$('.data-form-scraper').each(function () {
				var tag = $(this).find('.data-tag-scraper').val();
				var label = $(this).find('.data-label-scraper').val();
				var type = $(this).find('.data-type-scraper').val();
				var attr = $(this).find('.data-attr-scraper').val();
				var link = false;
				if($(this).find('.data-link-scraper').is(":checked")){
					link = true;
				}
				
				var data = {};
				data["label"] = label;
				data["tag"] = tag;
				data["type"] = type;
				data["attr"] = attr;
				data["link"] = link;
				
				if (label != '' && tag != '' && type != '') {
					if (type == 'attr' && attr != '') {
						request['data'].push(data);
					}
					
					if (type != 'attr') {
						request['data'].push(data);
					}
				}
			});
	
			requests.push(request);
			
			
			// Split URL
			var input = $('.input-scraper').val().split(/\n/);
			if (input.length) {
				$.each(input, function(i,url) {
					if (url != '') {
						urls.push(url);	
						parentUrls.push(url);	
					}
				});
			}
			
			var lists = false;
			var child = false;
			if($("#list").is(":visible") && urls.length <= 0){
				urls[0] = "1";
			}
			if($("#list").is(":visible")){
				lists = $('.input-list-scraper').val();
				if($(".child-too").is(":checked")){
					child = true;
				}
			}
			 
			
			// Make Request
			if (urls.length && !error) {
				var output = [];
				var noresult = [];
				
				$('.output-scraper').attr('disabled','disabled').val('');
				$('.noresult-scraper').val('');
				$('.wrap-noresult-scraper').hide();
				$('.show-result').hide();
				var pagination = {};
				if($("input[name='pagination']").is(":checked")){
					pagination['params'] = $('.extra_url').val();
					pagination['extension'] = $('.extension').val();
					pagination['min'] = $('.minp').val();
					pagination['max'] = $('.maxp').val();
					if($('.extra_url').val() == ""){
						toastr.error("Url failed..");
						$('.extra_url').closest('.form-group').addClass('has-error');
						$('.extra_url').focus();
						return false;
					}
				}
				
				var saveLinks = false;
				
				if(typeScraper == "shopify_productlist"){
					saveLinks = true;
				}
				
				var pending = false;
				$.ajax({
					type: "POST",
					url: "/system/includes/pages/Scraper/import/ajax/ajax.output.php?v="+Math.random(),
					data: {
						urls: urls,
						list: lists,
						saveLinks: saveLinks,
						child: child,
						type: typeScraper,
						parentId: $('.selected-customer').val(),
						pagination:pagination						
					},
					dataType: 'json',
					beforeSend: function(){
						$('body').progress('open');
						$(".form-collection").hide();
						$(".output_collection").val("");
						$('.show-data').hide();
						$('.resultsLinks').hide().html('').removeClass('bordering');
					},
					success: function(objs){
						var obj = objs.data;
						var paginations = objs.paginations;
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
									
									if (request.tags.length) {	
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
										console.log('Test2');
										headMeta['redirect'] = urls[x];
										headMeta['url'] = urls[x];
										headMeta['productUrls'] = [];
										if(headMeta['url'] == parentUrls[x]){
											headMeta['paginationUrls'] = paginations[x];
										}

										if(typeScraper == 'shopify_product')
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
															
															if ((typeScraper == 'shopify_page' || typeScraper == 'shopify_productlist')) {
																if(typeof value != 'undefined' && value != ""){
																	
																	if(data.link == true){
																		headMeta['productUrls'].push(value);
																		$('.resultsLinks').append(value+'<br>');
																	}
																	
																}
																	
															}
															else{
																
															}
															
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
							console.log(output);
							jsondata = output;
							loadData();
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
								beforeSend: function(){
									$(".import-csv").hide();
								},
								data: {
									output: jsondata, 
									noresult:noresult,
									mode: 'save_data',
									unique_attribute: $('.unique_attribute:checked').val(),
									typeScraper	:  $(".type-scraper").val()	,
									parentId: $('.selected-customer').val(),
									saveLinks : saveLinks,
									parentData: $(".parent-label-scraper").val()
								},
								success: function(){
									if(typeScraper == "shopify_product"){
										swal({
										  title: "Do you want to look table products ?",
										  icon: "info",
										  buttons: true,
										  dangerMode: true,
										})
										.then((willDelete) => {
										  if (willDelete) {
											$(".import-csv").trigger("click");
											$(".import-csv").show();
										  } else {
											swal("You can click \"Get data for CSV\" to show tables.!");
											$(".import-csv").show();
										  }
										});
									}
								}
							});
						}
					},
					complete: function(){
						$('.show-data').show();
						if(typeScraper == "shopify_productlist"){
							$(".form-collection").show();
							$(".json-collection").hide();
							$('.show-array').show();
							$('.productlist-only').show();
							$('.product-only').hide();
						}
						if(typeScraper == "shopify_product"){
							$(".form-collection").hide();
							$(".json-collection").hide();
							//$('.show-array').hide();
							$('.productlist-only').hide();
							$('.product-only').show();
							$('.export-data').show();
						}
						else{
							$(".form-collection").hide();
							$(".json-collection").hide();
							$('.show-array').show();
							$('.export-data').show();
							$('.productlist-only').hide();
							$('.product-only').hide();
							
						}
						
						if($('.resultsLinks').html().length){
							$('.resultsLinks').addClass('bordering').show();
						}
						toastr['success']("Scraper done", "Success!");
						$('.show-result').show();
						
						
						if(typeScraper == 'shopify_product'){
							$('.product-only').show();
						}
						
						$('body').progress('close');
					}
				});
				
				
				
			}
			else {
				toastr['error']("Please fill the required fields", "Error!");
			}
		});
		
		$('.add-data-scraper').click(function () {
			var form = $('.data-form-scraper').first().clone();
			form.find('input').val('');
			form.find('.data-type-scraper').val('text');
			form.find('.wrap-delete-data-scraper').show();
			form.find('.data-attr-scraper').attr('disabled','disabled');
			
			form.insertBefore(".wrap-add-data-scraper");
		});
		
		$(document).off('change','.element-type-scraper');
		$(document).on('change','.element-type-scraper', function () {
			var form = $(this).closest('.data-element-scraper');
			var attr = form.find('.element-attr-scraper');
			var type = $(this).val();
			
			if (type == 'attr') {
				attr.removeAttr('disabled');
				attr.focus();
			}
			else {
				attr.val('');
				attr.attr('disabled','disabled');
			}
		});
		
		$(document).off('change','.data-type-scraper');
		$(document).on('change','.data-type-scraper', function () {
			var form = $(this).closest('.data-form-scraper');
			var attr = form.find('.data-attr-scraper');
			var type = $(this).val();
			
			if (type == 'attr') {
				attr.removeAttr('disabled');
				attr.focus();
			}
			else {
				attr.val('');
				attr.attr('disabled','disabled');
			}
		});
		
		$('.show-tag-scraper').click(function () {
			$('.wrap-show-tag-scraper').hide();
			$('.tags-scrapper').show();
		});
		
		$('.add-tags-scraper').click(function () {
			var form = $('.data-tags-scraper').first().clone();
			form.find('input').val('');
			form.find('.tags-type-scraper').val('text');
			form.find('.tags-attr-scraper').attr('disabled','disabled');
			
			form.insertBefore(".wrap-add-tags-scraper");
		});
		
		$(document).off('click','.delete-tags-scraper');
		$(document).on('click','.delete-tags-scraper', function () {
			var form = $(this).closest('.data-tags-scraper');
			
			if ($('.tags-scrapper .data-tags-scraper').length > 1) {
				form.remove();
			}
			else {
				form.find('input').val('');
				form.find('.tags-type-scraper').val('text');
				form.find('.tags-attr-scraper').attr('disabled','disabled');
				$('.tags-scrapper').hide();
				$('.wrap-show-tag-scraper').show();
			}
		});
		
		$('.show-head-scraper').click(function () {
			$('.wrap-show-head-scraper').hide();
			$('.head-scrapper').show();
		});
		
		$('.show-element-scraper').click(function () {
			$('.wrap-show-element-scraper').hide();
			$('.element-scrapper').show();
		});
		
		$('.add-element-scraper').click(function () {
			var form = $('.data-element-scraper').first().clone();
			form.find('input').val('');
			form.find('.element-type-scraper').val('text');
			form.find('.element-attr-scraper').attr('disabled','disabled');
			
			form.insertBefore(".wrap-add-element-scraper");
		});
		
		$(document).off('click','.delete-data-scraper');
		$(document).on('click','.delete-data-scraper', function () {
			if($('.data-form-scraper').length > 1){
				var form = $(this).closest('.data-form-scraper');
				form.remove();
			}
			else{
				toastr.error("Default Data can't remove.. ");
				return ;
			}
			
		});
		
		$(document).off('click','.delete-element-scraper');
		$(document).on('click','.delete-element-scraper', function () {
			var form = $(this).closest('.data-element-scraper');
			
			if ($('.element-scrapper .data-element-scraper').length > 1) {
				form.remove();
			}
			else {
				form.find('input').val('');
				form.find('.element-type-scraper').val('text');
				form.find('.element-attr-scraper').attr('disabled','disabled');
				$('.element-scrapper').hide();
				$('.wrap-show-element-scraper').show();
			}
		});
		
		$('.add-head-scraper').click(function () {
			var form = $('.data-head-scraper').first().clone();
			form.find('input').val('');
			form.find('.head-type-scraper').val('text');
			form.find('.head-attr-scraper').attr('disabled','disabled');
			
			form.insertBefore(".wrap-add-head-scraper");
		});
		
		$(document).off('click','.delete-head-scraper');
		$(document).on('click','.delete-head-scraper', function () {
			var form = $(this).closest('.data-head-scraper');
			
			if ($('.head-scrapper .data-head-scraper').length > 1) {
				form.remove();
			}
			else {
				form.find('input').val('');
				form.find('.head-type-scraper').val('text');
				form.find('.head-attr-scraper').attr('disabled','disabled');
				$('.head-scrapper').hide();
				$('.wrap-show-head-scraper').show();
			}
		});
		
		
		$(document).off('click','.resultsLinks');
		$(document).on('click','.resultsLinks', function () {
			$(this).toggleClass('height-less');
		});
		$(document).off('change','.head-type-scraper');
		$(document).on('change','.head-type-scraper', function () {
			var form = $(this).closest('.data-head-scraper');
			var attr = form.find('.head-attr-scraper');
			var type = $(this).val();
			
			if (type == 'attr') {
				attr.removeAttr('disabled');
				attr.focus();
			}
			else {
				attr.val('');
				attr.attr('disabled','disabled');
			}
		});
		$(document).off('click','.modify-field-scraper');
		$(document).on('click','.modify-field-scraper', function () {
			if($("select.selected-scraper").val() <= 0){
				toastr.error("Please choose scraper first to make collection..");
				return ;
			}
			var target = $(this).attr("data-target");
			$(target).toggle();
			
		});
		$(document).off('click','.settings');
		$(document).on('click','.settings', function () {
			if($("#tbl1 tr").length <= 0){
				toastr.error("Please get csv data first to make collection..");
				return ;
			}
			var params = $("textarea.output-scraper").val();
			var parent = $(".parent-label-scraper").val();
			var name = $(".name-scraper").val();
			if(params == ""){
				toastr.error("Please scrap and set csv data first to make collection..");
				return ;
			}
			
			var th = {};
			var thHidden = {};
			
			$("table#tbl1").find("th").each(function(index){
				th[index] = $(this).find('label').text();
				if($(this).is(":hidden") || !$(this).is(":visible")){
					thHidden[index] = $(this).find('label').text();
				}
			});
			
			$.ajax({
				type: "POST",
				url: "/system/includes/pages/Scraper/import/ajax/ajax.json.php?v="+Math.random(),
				dataType: 'json',
				data: {
					mode: 'showFields',
					th: th,
					thHidden: thHidden
				},
				success: function(obj){
					$("#fieldsData .hereFields").html(obj.html);
					$("#fieldsData").modal("show");
				}
			});
			
			
		});
		
		$(document).off('click','#fieldsData .saveField');
		$(document).on('click','#fieldsData .saveField', function () {
			
			var elem = $("#fieldsData");
			var value = $(this).val();
			elem.find("input").each(function() {
				var column = "table#tbl1 ." + $(this).attr("name");
					
				if($(this).is(":checked")){
					if($(column).length){
						$(column).show();
					}
					
				}
				else{
					if($(column).length){
						$(column).hide();
					}
					
				}
				
			});
			
			
		});
		
		
		$(document).off('click','.csvToshopify');
		$(document).on('click','.csvToshopify', function () {
			//var params = $("textarea.output-scraper").val();
			/* var parent = $(".parent-label-scraper").val();
			var name = $(".name-scraper").val(); */
			/* if(params == ""){
				toastr.error("Please scrap and set csv data first to make collection..");
				return ;
			}
			
			if($("#tbl1 tr").length <= 0){
				toastr.error("Please get csv data first to make collection..");
				return ;
			} */
			
			var postData = $("#tb1").serialize();
			
			$.ajax({
				type: "POST",
				url: "/system/includes/pages/Scraper/import/ajax/json.php?v="+Math.random(),
				dataType: 'json',
				data: {
					mode: 'sendCSVFile',
					postData: postData,
					formId: $(".selected-scraper").val()
				},
				beforeSend: function(){
					$('body').progress('open');
					toastr.info("Don't the window until finish upload products to shopify.");
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
					
					
					if(obj.updated){
						toastr.success("Imported " + obj.imported);
					}
					
					if(obj.imported){
						toastr.success("Updated " + obj.updated);
					}
					
					if(obj.created){
						toastr.success("Created " + obj.created);
					}
					
					if(obj.errors){
						toastr.error("Errors When import" + obj.errors);
					}
					
					$('body').progress('close');
					
				}
			});
			
		});
		
		 
		$(document).off('click','.history');
		$(document).on('click','.history', function () {
			var formId = $(".selected-scraper").val();
			
			if(formId == 0){
				toastr.error("Please choose Scraper Id or save this scraper to see history.");
				return ;
			}

			
			$.ajax({
				type: "POST",
				url: "/system/includes/pages/Scraper/import/ajax/ajax.json.php?v="+Math.random(),
				dataType: 'json',
				data: {
					mode: 'get_history',
					formId: formId
				},
				success: function(obj){
					$("#ajax_history #tbl_history tbody").html(obj.table);
				},
				complete: function(){
					$("#ajax_history").modal("show");
					//$('#tbl_history').DataTable();
				}
			}); 
			
		});
		
		$(document).off('click','#tbl_history .btn-success');
		$(document).on('click','#tbl_history .btn-success', function () {
			var id = $(this).attr('data-elm');
			var btn = $(this);
			$.ajax({
				type: "POST",
				url: "/system/includes/pages/Scraper/import/ajax/ajax.json.php?v="+Math.random(),
				dataType: 'json',
				data: {
					mode: 'load_csv',
					id: id
				},
				beforeSend: function(){
					btn.text("waiting..");
				},
				success: function(obj){
					if(obj.table){
						$(".slideButtons").show();
						$("#tbl1").html(obj.table);
						$("#toogle-1").show();
						
						$("table#tbl1").find("th").each(function(index){
							var text = $(this).find('label').text();
							if(text.length <= 0){
								text = "Undefined";
							}
							$(this).find("select.header-select-"+text).val(text)
						});
						
						
					}
					
				},
				complete: function(){
					btn.text("Use");
					$("#ajax_history").modal("hide");
					$('html, body').animate({
						scrollTop: $("#toogle-1").offset().top
					}, 1000);
				}
			});
			
		});
		
		
		$(document).off('click','.save-csv');
		$(document).on('click','.save-csv', function () {
			var params = $("textarea.output-scraper").val();
			var parent = $(".parent-label-scraper").val();
			var name = $(".name-scraper").val();
			if(params == ""){
				toastr.error("Please  scrap and set csv data to make collection..");
				return ;
			}
			
			if($("#tbl1 tr").length <= 0){
				toastr.error("Please get csv data first to make collection..");
				return ;
			}
			
			var postData = $("#tb1").serialize();
			
			$.ajax({
				type: "POST",
				url: "/system/includes/pages/Scraper/import/ajax/json.php?v="+Math.random(),
				dataType: 'json',
				data: {
					mode: 'saveCSVFile',
					postData: postData
				},
				success: function(obj){
					if(obj.message){
						toastr.success("CSV file have been save in database.");
						var download = confirm("Do you want to download the csv file?");
						if (download == true) {
							window.open(obj.message,'_blank');
						} 
	
					}
					else{
						toastr.error("Sorry we can't save the csv file, please check again your data or permission of path.")
					}
					
				}
			});
			
		});
		
		
		$(document).off('click','.create-smartCollection');
		$(document).on('click','.create-smartCollection', function () {
			var collection = $(".output_collection").val();
			if(collection == ""){
				toastr.error("Please create json collection first!");
				return false;
			}
			$.ajax({
				type: "POST",
				url: "/system/includes/pages/Scraper/import/ajax/ajax.json.php?v="+Math.random(),
				dataType: 'json',
				data: {
					mode: 'load_rules',
					collection: collection
				},
				success: function(obj){
					$("#ajax_collection .modal-body").html(obj.html);
					$("#ajax_collection").modal("show");
				},
				complete: function(){
					$("#ajax_collection select.ui-select").chosen({width:'100%'});
					$("#ajax_collection input.ui-select").chosen({width:'100%',multiple:true});
					$('.dd').nestable({maxDepth:2,
						dropCallback: function(details){
							reloadNestable(details);
						}
					});
				}
			}); 

		});
		
		$(document).off('click','.create-smartCollection_v2');
		$(document).on('click','.create-smartCollection_v2', function () {
			$.ajax({
				type: "POST",
				url: "/system/includes/pages/Scraper/import/ajax/ajax.json.php?v="+Math.random(),
				dataType: 'json',
				data: {
					mode: 'prepare_collection',
					customer: $(".selected-customer").val()
				},
				success: function(obj){
					if(obj.error){
						if(obj.msg){
							toastr.error(obj.msg,"Error");
						}
						else{
							toastr.error("Something error , check your connection !","Error");
						}
						return ;
					}
					$("#ajax_collection .modal-body").html(obj.html);
					$("#ajax_collection").modal("show");
				},
				complete: function(){
					$("#ajax_collection select.ui-select").chosen({width:'100%'});
					$("#ajax_collection input.ui-select").chosen({width:'100%',multiple:true});
					$('.dd').nestable({maxDepth:2,
						dropCallback: function(details){
							reloadNestable(details);
						}
					});
				}
			}); 

		});
		
		$(document).off('click','.saveCollection');
		$(document).on('click','.saveCollection', function () {
			var postdata = $("form#smart_collection").serialize();
			$.ajax({
				type: "POST",
				url: "/system/includes/pages/Scraper/import/ajax/ajax.json.php?v="+Math.random(),
				dataType: 'json',
				data: {
					mode: 'create_collection',
					params: postdata
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
					
					if(obj.smart_collection){
						toastr.success("Collection created.");
						$("#ajax_collection").modal("hide");
					}
					else{
						toastr.error("There are something no valid.")
					}
					
					clearconsole();
				},
				complete: function(){
					$('body').progress('close');
				}
			});
		});
		$(document).off('click','.edit-collection');
		$(document).on('click','.edit-collection', function () {
			var id = $(this).attr("data-elm");
			$.ajax({
				type: "POST",
				url: "/system/includes/pages/Scraper/import/ajax/ajax.json.php?v="+Math.random(),
				dataType: 'json',
				data: {
					mode: 'edit_collection',
					id: id
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
					
					$("input[name='condition']").val(obj.tags);
					$("input[name='colection_id']").val(obj.id);
					$("#smart_collection select[name='title']").val('').trigger("chosen:updated");
					if(obj.title){
						if($("#smart_collection select[name='title'] option[value='"+obj.selected+"']").length <= 0){
							$("#smart_collection select[name='title']").append(obj.title);
						}
						
						$("#smart_collection select[name='title']").val(obj.selected);
						$("#smart_collection select[name='title']").trigger("chosen:updated");
					}
				},
				complete: function(){
					$('html, body').animate({
						scrollTop: $("#smart_collection").offset().top
					}, 1000);
					clearconsole();
				}
			});
		});
		
		
		$(document).on('click','.show-result', function () {
			$(".output-scraper-area").toggle();
		});
		$(document).off('click','.remove-collection');
		$(document).on('click','.remove-collection', function () {
			var tr = $(this).closest("tr");
			var li = $(this).closest("li");
			var id = $(this).attr("data-elm");
			$.ajax({
				type: "POST",
				url: "/system/includes/pages/Scraper/import/ajax/ajax.json.php?v="+Math.random(),
				dataType: 'json',
				data: {
					mode: 'delete_collection',
					id: id
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
					else {
						toastr.success("Collection deleted.");
						if((tr).length) tr.remove();
						if((li).length) li.remove();
					}
					
				},
				complete: function(){
					clearconsole();
					$('body').progress('close');
				}
			});
		});	
		$(document).on('change','input[name="pagination"]', function () {
			if($(this).is(":checked")){
				$(".pagination-content").show();
			}
			else{
				$(".pagination-content").hide();
			}
			
		});
		
		$(document).off('click','.show-data');
		$(document).on('click','.show-data', function () {	
			var value = $('.output-scraper').val();
			$.ajax({
				type: "POST",
				url: "/system/includes/pages/Scraper/import/ajax/ajax.json.php?v="+Math.random(),
				dataType: 'json',
				data: {
					mode: 'see_data',
					value: value,
					postdata : jsondata
				},
				beforeSend: function(){
					$(".output-results-ajax").html('');
					$('body').progress('open');
				},
				success: function(obj){
					$(".output-results-ajax").html(obj.html);
				},
				complete: function(){
					$('body').progress('close');
				}
			});
		});
		
		$(document).off('click','.preview-pagination');
		$(document).on('click','.preview-pagination', function () {	
			// Split URL
			var urls = [];
			var input = $('.input-scraper').val().split(/\n/);
			if (input.length) {
				$.each(input, function(i,url) {
					if (url != '') {
						urls.push(url);	
					}
				});
			}
			
			var pagination = {};
			pagination['params'] = $('.extra_url').val();
			pagination['extension'] = $('.extension').val();
			pagination['min'] = $('.minp').val();
			pagination['max'] = $('.maxp').val();
			
			
			$.ajax({
				type: "POST",
				url: "/system/includes/pages/Scraper/import/ajax/ajax.scraper.php?v="+Math.random(),
				dataType: 'json',
				data: {
					mode: 'preview',
					urls: urls,
					pagination:pagination
				},
				beforeSend: function(){
					$("#ajax_data .modal-body").html('');
						$("#ajax_data .modal-header").html('');
					$('body').progress('open');
				},
				success: function(obj){
					$("#ajax_data .modal-header").html(obj.title);
					$("#ajax_data .modal-body").html(obj.urls);
				},
				complete: function(){
					$("#ajax_data").modal("show");
					$('body').progress('close');
				}
			});
		});
		
		$(document).off('click','.selected-product-data > .btn');
		$(document).on('click','.selected-product-data > .btn', function () {	
			var target = $(this).attr("data-target");
			$("#list").hide();
			$("#url").hide();
			$(target).show();
		});
		
		
	});
	
	function reloadNestable(details){ 
		var items  = $(details.sourceEl).html();
		var list = $('#nestable_list_pages').nestable('serialize');
		list = JSON.stringify(list);
		$.ajax({
			type: "POST",
			url: "/system/includes/pages/Scraper/import/ajax/ajax.json.php?v="+Math.random(),
			dataType: 'json',
			traditional: true,
			data: {
				'mode': 'reloadPages',
				'listings':list
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
				
				if(obj.collections){
					$("#nestable_list_products").html(obj.products);
					$("#nestable_list_pages").html(obj.collections);
					console.log('Test');
				}
			},
			complete: function(){
				$('.nestable_list_products').nestable('buttons');
				$('.nestable_list_pages').nestable('buttons');
				$('body').progress('close');
				clearconsole();
			}
		});
	}