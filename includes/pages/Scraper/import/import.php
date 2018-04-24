<div class="row">
	<div class="col-md-12">
    	
        <div class="row">
        	<div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Select scraper</label>
                    <select class="form-control selected-scraper">
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label">Name</label>
                    <input type="text" class="form-control name-scraper" />
                </div>
				<div class="form-group">
					<label class="control-label">Type of scraper</label>
					<select class="form-control type-scraper">
						<option value="0">Just a screaper</option>
						<option value="shopify_productlist">Shopify productlist</option>
						<option value="shopify_product">Shopify Product</option>
					</select>
				</div>
				
            </div>
			
        </div>

    	<div class="form-group">
    		<label class="control-label">URL</label>
        	<textarea class="form-control input-scraper" style="min-height:200px;"></textarea>
    	</div>
		
		<div class="form-group">
    		<label class="control-label">Note</label>
        	<textarea class="form-control input-note" style="min-height:100px;"></textarea>
    	</div>
		
		 <label class="control-label">Tags or string that the page must containe before its scraped. If not found, the the url will show in "URL without results" </label><br />
        <div class="row wrap-show-tag-scraper">
            <div class="col-md-12">
                <div class="form-group">
                    <button type="button" class="btn green btn-sm show-tag-scraper">Add data</button>
                </div>
            </div>
        </div>
		<div class="col-md-12 tags-scrapper" style="float:none; margin-bottom:10px;display:none;">
            <div class="row" style="padding-top:5px; background:#eee;">
            	<div class="col-md-11">
                	<label class="control-label">Tag</label>
                </div>
                
                <div class="col-md-1">
                    <label class="control-label">&nbsp;</label>
                </div>
                
                <div class="data-tags-scraper clearfix">
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="text" class="form-control tags-tag-scraper" placeholder='title or meta[name="description"]' />
                        </div>
                    </div>
                    
                    <div class="col-md-1 wrap-delete-tags-scraper">
                    	<div class="form-group">
                    		<a href="javascript:;" title="Delete" class="delete-tags-scraper" style="color:#444; display:inline-block; margin-top:8px;">
                            	<i class="fa fa-trash" style="font-size:18px;"></i>
							</a>
                    	</div>
                    </div>
                </div>
                <div class="wrap-add-tags-scraper">
                	<div class="col-md-12">
                    	<div class="form-group">
                        	<button type="button" class="btn green btn-sm add-tags-scraper">Add data</button>
						</div>
					</div>                        
                </div>
			</div>
		</div>
        
        <label class="control-label">Head Meta Data</label><br />
        <div class="row wrap-show-head-scraper">
            <div class="col-md-12">
                <div class="form-group">
                    <button type="button" class="btn green btn-sm show-head-scraper">Add data</button>
                </div>
            </div>
        </div>
        <div class="col-md-12 head-scrapper" style="float:none; margin-bottom:10px;display:none;">
            <div class="row" style="padding-top:5px; background:#eee;">
            	<div class="col-md-4">
                	<label class="control-label">Tag</label>
                </div>
                <div class="col-md-3">
                	<label class="control-label">Label</label>
                </div>
                <div class="col-md-2">
                	<label class="control-label">Get data</label>
                </div>
                <div class="col-md-2">
                	<label class="control-label">Attribute name</label>
                </div>
                <div class="col-md-1">
                    <label class="control-label">&nbsp;</label>
                </div>
                
                <div class="data-head-scraper">
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="text" class="form-control head-tag-scraper" placeholder='title or meta[name="description"]' />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="text" class="form-control head-label-scraper" placeholder='eg. title' />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                        	<select class="form-control head-type-scraper">
                            	<option value="text">Text</option>
                                <option value="html">Html</option>
                                <option value="attr">Attribute</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <input type="text" class="form-control head-attr-scraper" disabled placeholder='eg. content' />
                        </div>
                    </div>
                    <div class="col-md-1 wrap-delete-head-scraper">
                    	<div class="form-group">
                    		<a href="javascript:;" title="Delete" class="delete-head-scraper" style="color:#444; display:inline-block; margin-top:8px;">
                            	<i class="fa fa-trash" style="font-size:18px;"></i>
							</a>
                    	</div>
                    </div>
                </div>
                <div class="wrap-add-head-scraper">
                	<div class="col-md-12">
                    	<div class="form-group">
                        	<button type="button" class="btn green btn-sm add-head-scraper">Add data</button>
						</div>
					</div>                        
                </div>
			</div>
		</div>
        
        <div class="row">
            <div class="col-md-7" style="margin-bottom:10px;">
                <label class="control-label">Parent Data</label>
                <div class="row" style="padding-top:5px; background:#eee; margin:0;">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Tag</label>
                            <input type="text" class="form-control parent-tag-scraper" placeholder='.product or .product-detail' />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Label</label>
                            <input type="text" class="form-control parent-label-scraper" placeholder='eg. Product list or Product Detail' />
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-5" style="margin-bottom:10px;">
                <label class="control-label">Ignore If Parent Data</label>
                <div class="row" style="padding-top:5px; background:#eee; margin:0;">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="control-label">Tag</label>
                            <input type="text" class="form-control ignore-parent-tag-scraper" placeholder='.related-product' />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <label class="control-label">Data</label>
        <div class="col-md-12" style="float:none;">
            <div class="row" style="padding-top:5px; background:#eee;">
            	<div class="col-md-4">
                	<label class="control-label">Tag</label>
                </div>
                <div class="col-md-3">
                	<label class="control-label">Label</label>
                </div>
                <div class="col-md-2">
                	<label class="control-label">Get data</label>
                </div>
                <div class="col-md-2">
                	<label class="control-label">Attribute name</label>
                </div>
                <div class="col-md-1">
                    <label class="control-label">&nbsp;</label>
                </div>
                    
            	<div class="data-form-scraper">
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="text" class="form-control data-tag-scraper" placeholder='.product-title or [itemprop="title"]' />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="text" class="form-control data-label-scraper" placeholder='eg. Title' />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                        	<select class="form-control data-type-scraper">
                            	<option value="text">Text</option>
                                <option value="html">Html</option>
                                <option value="attr">Attribute</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <input type="text" class="form-control data-attr-scraper" disabled placeholder='eg. href' />
                        </div>
                    </div>
                    <div class="col-md-1 wrap-delete-data-scraper" style="display:none;">
                    	<div class="form-group">
                    		<a href="javascript:;" title="Delete" class="delete-data-scraper" style="color:#444; display:inline-block; margin-top:8px;">
                            	<i class="fa fa-trash" style="font-size:18px;"></i>
							</a>
                    	</div>
                    </div>
                </div>
                <div class="wrap-add-data-scraper">
                	<div class="col-md-12">
                    	<div class="form-group">
                        	<button type="button" class="btn green btn-sm add-data-scraper">Add data</button>
						</div>
					</div>                        
                </div>
            </div>
        </div>
        
        <div class="form-group" style="margin-top:15px;">
        	<button type="button" class="btn green import-scraper">Start Scrap</button>
            <button type="button" class="btn blue save-scraper">Save scraper data for later use</button>
			
		</div>
        <br />
		<br />
		
		<div class="form-group row json-collection" style="margin-bottom:10px;display:none;padding-top:5px;">
			<label class="control-label col-md-12"><strong>Json Collection</strong></label>
			<div class="col-md-12">
				<textarea rows="5" class="form-control output_collection"></textarea>		
			</div>	
			
		</div>	
			
		
		<div class="form-group row form-collection" style="margin-bottom:10px;display:none;padding-top:5px;">
			<label class="control-label col-md-12"><strong>Make Collection</strong></label>
			<div class="col-md-3">
				<label class="control-label">Choose page meta tag label</label>
			</div>
			
			<div class="col-md-3">
				<select class="form-control collection_key" id="collection_key">
					<option value="0"> Key </option>
				</select>
			</div>
			
			<div class="col-md-3">
				<label class="control-label">And Choose label from "Data"</label>
			</div>
			
			<div class="col-md-3">
				<select class="form-control collection_value" id="collection_value">
					<option value="0"> Value </option>
				</select>
			</div>
			
			<div class="col-md-12">
				<div class="form-group">
					<button type="button" class="btn green create-collection">Make Collection (json) </button>
				</div>
			</div>
			
			<div class="col-md-12">
				<div class="form-group group_output_collection" style="display:none;">
					<label class="control-label">Output Collection</label>
					<textarea class="form-control output_collection"></textarea> 
				</div>
			</div>
			
		</div>
		
        <div class="form-group">
        	<label class="control-label">Output</label>
			<textarea class="form-control output-scraper" style="min-height:200px;"></textarea>
        </div>
        <div class="form-group wrap-noresult-scraper" style="display:none;">
        	<label class="control-label">URL without results</label>
			<textarea class="form-control noresult-scraper" style="min-height:200px;"></textarea>
        </div>
		
		<div class="form-group col-md-6 export-data" style="margin-top:15px;display:none;">
			<button type="button" class="btn green show-array">See data in array</button>
			<button type="button" class="btn green import-csv">Get CSV for data</button>
		</div>
		
		<div class="form-group col-md-6 pull-right text-right" style="margin-top:15px;">
			<button type="button" data-value="" class="btn red selected-button-remove">Remove</button>
		</div>
    </div>
</div>

<style type="text/css">
	.has-error .form-control { border-color:#a94442 !important; }
</style>

<script type="text/javascript">
	var jsondata; 
	var jsoncollection; 
	
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
		
		$('.form-collection').show();

	}
	
	function getScraperOptions (selected) {
		var selected = selected || 0;
		
		$.ajax({
			type: "POST",
			url: "/backend/system/import/ajax/ajax.scraper.php?v="+Math.random(),
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
					$(".selected-button-remove").hide();
				}
				else{
					$(".selected-button-remove").show();
				}
				
			}
		});
	}
	
	
	
	$(document).ready(function () {
		getScraperOptions();
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
				url: "/backend/system/import/ajax/json.php?v="+Math.random(),
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
			
			if(typeScraper == "shopify_productlist"){ 
				$('.export-data .show-array').show();
				$('.export-data .import-csv').hide();
				$('.export-data').show();
				$(".form-collection").show();
				$(".json-collection").hide();
			}
			else if(typeScraper == "shopify_product"){
				$('.export-data .show-array').hide();
				$('.export-data .import-csv').show();
				$('.export-data').show();
				$(".form-collection").hide();
				$(".json-collection").show();
				
			}
			else {
				$('.export-data .show-array').hide();
				$('.export-data .import-csv').hide(); 
				$('.export-data').hide();
				$(".form-collection").hide();
				$(".json-collection").hide();
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
				url: "/backend/system/import/ajax/json.php?v="+Math.random(),
				data: {
					mode: "convert_to_array",
					postdata: jsondata, 
					collection : $(".output-scraper").val(),
					//jsoncollection: jsoncollection
				},
				dataType: 'html',
				success: function(obj){
					$("#ajax_response .modal-body").html(obj);
					$("#ajax_response").modal("show");
				}
			});
			
		});
		
		$(".import-csv").click(function(){
			var params = $("textarea.output-scraper").val();
			var parent = $(".parent-label-scraper").val();
			var name = $(".name-scraper").val();
			if(params == ""){
				/* $(".import-scraper").bind('click', function() {
					params = $("textarea.output-scraper").val();
				}); */
				toastr.error("Please scrap first to make collection..");
				return ;
			}
			var shopify = false;
			
			/* var confirmation = confirm("Do you want make the csv in Shopify format?");
			if (confirmation == true) {
				shopify = true;
			}  */
			
			var keys = $("#collection_key").val();
			var values = $("#collection_value").val();
			/*
			data: JSON.stringify({ Markers: markers }),
			contentType: "application/json; charset=utf-8",
			*/
			var sc = $(".output-scraper").val();

			$.ajax({
				type: "POST",
				url: "/backend/system/import/ajax/ajax.json.php?v="+Math.random(),
				//contentType: "application/json; charset=utf-8",
				data: {
					mode: "getCSV"	,
					params: sc,
					json_data: jsondata,
					collection: $('.output_collection').val(), 
					parent: parent,
					keys: keys,
					values: values,
					type: $(".type-scraper").val(), 
					shopify:shopify,
					name : name,
				},
				dataType: 'json',
				success: function(obj){
					if(obj.message){
						//window.location = obj.message;
						window.open(obj.message,'_blank');
					}
				}
			});
		});
		
		$('.selected-scraper').change(function () {
			var id = $(this).val();
			
			$.ajax({
				type: "POST",
				url: "/backend/system/import/ajax/ajax.scraper.php?v="+Math.random(),
				data: {
					id: id,
					mode: "getScraper"				
				},
				dataType: 'json',
				beforeSend: function(){
					$('.form-collection').hide();
					$(".json-collection").hide();
					if(id == 0){
						$(".selected-button-remove").hide();
					}
					else{
						$(".selected-button-remove").show();
					}
				},
				success: function(obj){
					if (!obj.error) {
						var name = obj.data.name;
						var input = obj.data.urls;
						var headData = obj.data.headData;
						var parentData = obj.data.parentData;
						var data = obj.data.data;
						var tags = obj.data.tags;
						var typeS = obj.data.type;
						var note = obj.data.note;
						
						if(typeS == "shopify_productlist"){
							$(".show-array").show();
							$(".export-data").show();
							$(".import-csv").hide();
							$(".form-collection").hide();
							$(".json-collection").hide();
						}
						else if(typeS == "shopify_product"){
							$(".show-array").hide();
							$(".import-csv").show();
							$(".export-data").show();
							$(".form-collection").hide();
							$(".json-collection").hide();
						}
						else {
							typeS = 0;
							$(".show-array").hide();
							$(".export-data").hide();
							$(".import-csv").hide();
							$(".form-collection").hide(); 
							$(".json-collection").hide();
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
							
							newForm.insertBefore(".wrap-add-head-scraper");
						}
						
						$('.parent-tag-scraper').val(parentData.tag);
						$('.parent-label-scraper').val(parentData.label);
						$('.ignore-parent-tag-scraper').val(parentData.ignore);
						
						var dataForm = $('.data-form-scraper').first().clone();
						$('.data-form-scraper').remove();
						
						$.each(data, function(i,form) {
							var newForm = dataForm.clone();
							
							newForm.find('.data-tag-scraper').val(form.tag);
							newForm.find('.data-label-scraper').val(form.label);
							newForm.find('.data-type-scraper').val(form.type);
							newForm.find('.data-attr-scraper').val(form.attr);
							
							if (form.type == "attr") {
								newForm.find('.data-attr-scraper').removeAttr('disabled');
							}
							else {
								newForm.find('.data-attr-scraper').attr('disabled','disabled');
							}
							
							if (i == 0) {
								newForm.find('.wrap-delete-data-scraper').hide();
							}
							else {
								newForm.find('.wrap-delete-data-scraper').show();
							}
							
							newForm.insertBefore(".wrap-add-data-scraper");
						});
						$('.selected-button-remove').attr("data-value",id);
						$('.output-scraper').val('');
						$('.wrap-noresult-scraper').hide();
						$('.noresult-scraper').val('');
					}
				}
				
				/*  */
				
			});
		});
		
		$('.selected-button-remove').click(function(){
			var id = $('.selected-scraper').val();
			$.ajax({
				type: "POST",
				dataType: "json",
				url: "/backend/system/import/ajax/ajax.scraper.php?v="+Math.random(),
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
				
				data.push(setting);
			});
			
			if (!error) {
				$.ajax({
					type: "POST",
					url: "/backend/system/import/ajax/ajax.scraper.php?v="+Math.random(),
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
						mode: 'saveScraper'						
					},
					dataType: 'json',
					success: function(obj){
						if (obj.error) {
							toastr['error']("Failed to save scraper", "Error!");
						}
						else {
							toastr['success']("Scraper is now saved", "Success!");
							getScraperOptions(obj.id);
						}
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
			
			
			var urls = [];
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
			
			
			request['tag'] = $('.parent-tag-scraper').val();
			request['label'] = $('.parent-label-scraper').val();
			request['ignore'] = $('.ignore-parent-tag-scraper').val();
			request['data'] = [];
			
			$('.data-form-scraper').each(function () {
				var tag = $(this).find('.data-tag-scraper').val();
				var label = $(this).find('.data-label-scraper').val();
				var type = $(this).find('.data-type-scraper').val();
				var attr = $(this).find('.data-attr-scraper').val();
				
				var data = {};
				data["label"] = label;
				data["tag"] = tag;
				data["type"] = type;
				data["attr"] = attr;
				
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
					}
				});
			}

			// Make Request
			if (urls.length && !error) {
				var output = [];
				var noresult = [];
				
				$('.output-scraper').attr('disabled','disabled').val('');
				$('.noresult-scraper').val('');
				$('.wrap-noresult-scraper').hide();
				
				$.ajax({
					type: "POST",
					url: "/backend/system/import/ajax/ajax.output.php?v="+Math.random(),
					data: {
						urls: urls					
					},
					dataType: 'json',
					beforeSend: function(){
						$(".form-collection").hide();
						$(".output_collection").val("");
					},
					success: function(obj){
						//console.log(urls);
						if (obj.length) {
							$.each(obj, function(i,html) {
								var x = i;
								var wrap = $(html);
								var dom_nodes  = $($.parseHTML(html));
								var headMeta = {};
								var scrap = {};
								var skip = false;
								// Handle request (possible multiple)
								$.each(requests, function(i,request) {
									skip = false;
									
									if (request.tags.length) {	
										$.each(request.tags, function(i,data) {
											/* if($(wrap).find(data.tag).length <= 0){
												skip = true;
											} */
											
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
									
									/* skip = false; */
									
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
							loadData();
							output = JSON.stringify(output);
							output = output.replace(/'/g, "\\'")
							
							$('.output-scraper').removeAttr('disabled').val(output);
							
							if (noresult.length) {
								$('.noresult-scraper').val(noresult.join('\n'));
								$('.wrap-noresult-scraper').show();
							}
							
						}
					},
					complete: function(){
						if(typeScraper == "shopify_productlist"){
							$(".form-collection").show();
							$(".json-collection").hide();
						}
						else{
							$(".form-collection").hide();
							$(".json-collection").show();
						}
						
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
		
		$(document).off('click','.delete-data-scraper');
		$(document).on('click','.delete-data-scraper', function () {
			var form = $(this).closest('.data-form-scraper');
			form.remove();
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
	});
</script>