<?php 
if(isset($_GET["logout"]) && $_GET["logout"] == "true"){
	session_destroy();
	unset($_SESSION["cId"]);
}

if(isset($_GET["history"])){
	include_once( $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Scraper/History.php") ;
}
else { ?>
<style>
input.link-block {
    margin: 0 20px 0 10px;
	display:none;
}
.output-scraper-area , .csvToshopify{
	display:none;
}
.bordering{
	padding:10px;
	border: 1px dotted #999;
}
.productlist-only ,.product-only{
	display:none;	
}
.output-results-ajax > ul , .output-results-ajax > ul > ul{
    padding: 0;
}
.output-results-ajax > ul li{
	list-style:none;
}
.height-less{
	height:100px;
	overflow-y:scroll;
}
.dd-handle{
	height:auto;
}
select.form-control.has-error {
    border: #fbc9c9 1px solid;
}
</style>
<div class="row">
	<div class="col-md-12">
    	<div class="row" style="display:none;">
			<div class="col-md-4">
				<div class="form-group">
					<label class="control-label">Select Customer</label>
					<select class="form-control selected-customer">
					
					</select>
				</div>
			</div>
		</div>
		<?
		if(isset($_SESSION["cId"]) && !empty($_SESSION["cId"]) && $_SESSION["cId"]){
			
		?>
		<div class="row">
			<div class="col-md-4">
				<div class="form-group">
					<a href="/system/index.php?page=scraper&logout=true">Change Customer</a>
				</div>
			</div>
		</div>
		<?
		}
		?>
        <div class="row">
        	<div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Select scraper</label>
                    <select class="form-control selected-scraper">
                    </select>
                </div>
				<div class="form-group" data-step="2" data-intro="Select type of scraper !">
					<label class="control-label">Type of scraper</label>
					<select class="form-control type-scraper">
						<option value="0">Just a screaper</option>
						<option value="shopify_productlist">Shopify productlist</option>
						<option value="shopify_product">Shopify Product</option>
						<option value="shopify_page">Shopify page</option>
						
					</select>
				</div>
                <div class="form-group" data-step="1" data-intro="Set name for the scraper.. !">
                    <label class="control-label">Name</label>
                    <input type="text" class="form-control name-scraper" />
                </div>
				
				
            </div>
			
        </div>
		<div class="row product-only">
			<div class="col-md-4">
				<div class="form-group">
					<label class="control-label">Scrape by</label><br>
					<div class="btn-group selected-product-data" data-toggle="buttons">
						<label class="btn btn-default active" data-target="#url">
						<input type="radio" value="url" class="toggle"> Url </label>
						
						<label class="btn btn-default" data-target="#list">
						<input type="radio" value="list" class="toggle"> List </label>
					</div>
				</div>
			</div>
		</div>
		
		
    	<div class="form-group" id="url" data-step="3" data-intro="Add the urls that want to scrap!">
    		<label class="control-label">URL</label>
        	<textarea class="form-control input-scraper" style="min-height:200px;"></textarea>
    	</div>
		
		<div class="row">
			<div class="form-group col-md-4" id="list" style="display:none;">
				<label class="control-label">List</label>
				<select class="form-control input-list-scraper">
					<option value="0">Choose List</option>
				</select>
				<input type="checkbox" value="1" class="child-too"> Also scrap its child
			</div>
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
        <div data-step="4" data-intro="Setup the tags that want to scrap as data !">
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
                    		<input type="radio" class="unique_attribute" value="" name="unique_attribute"> &nbsp;&nbsp;
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
                	<label class="control-label">Attribute</label>
                </div>
               
                 <div class="col-md-1">
                	<label class="control-label"><span class="link-block" style="display:none;">Link</span></label>
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
                    
					<div class="col-md-1 wrap-delete-data-scraper">
						<div class="form-group">
							<input type="checkbox" value="value" name="link" class="link-block data-link-scraper">
                    		<a href="javascript:;" title="Delete" class="delete-data-scraper" style="color:#444;display:inline-block; margin-top:8px;">
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
		<br>
		<div class="row">
			<div class="col-md-12">
				<label class="control-label">Element Data (Under Body)</label>
				<div class="row wrap-show-element-scraper">
					<div class="col-md-12">
						<div class="form-group">
							<button type="button" class="btn green btn-sm show-element-scraper">Add data</button>
						</div>
					</div>
				</div>
				<div class="col-md-12 element-scrapper" style="float: none; margin-bottom: 10px; display: none;">
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
						
						<div class="data-element-scraper">
							<div class="col-md-4">
								<div class="form-group">
									<input type="text" class="form-control element-tag-scraper" placeholder="h1">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class="form-control element-label-scraper" placeholder="eg. h1">
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<select class="form-control element-type-scraper">
										<option value="text">Text</option>
										<option value="html">Html</option>
										<option value="attr">Attribute</option>
									</select>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<input type="text" class="form-control element-attr-scraper" disabled="disabled" placeholder="eg. content">
								</div>
							</div>
							<div class="col-md-1 wrap-delete-element-scraper">
								<div class="form-group">
									<a href="javascript:;" title="Delete" class="delete-element-scraper" style="color:#444; display:inline-block; margin-top:8px;">
										<i class="fa fa-trash" style="font-size:18px;"></i>
									</a>
								</div>
							</div>
						</div>
						<div class="wrap-add-element-scraper">
							<div class="col-md-12">
								<div class="form-group">
									<button type="button" class="btn green btn-sm add-element-scraper">Add data</button>
								</div>
							</div>                        
						</div>
					</div>
				</div>
				
			</div>
        </div>
		
		<div class="" style="margin-top:15px;">
			<label class="control-label">Scrape paginations pages <input type="checkbox" value="" name="pagination"></label>
			<div class="col-md-12" style="float:none;">
				<div class="row" style="padding-top:5px; background:#eee;">
					
					<div class="col-md-12">
						<div class="pagination-content" style="display:none;">
							<div class="row">
								<div class="col-md-8">
									<div class="form-group">
										<label>For each url - scrape also</label>
										<input  placeholder="Extra variable" type="text" class="form-control extra_url">
										<span class="help">eq: %%path%%?page=%%number%%.html</span>
									</div>
														
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>Extension</label>
										<input  placeholder="extension" type="text" class="form-control extension">
										<span class="help">eq: html (this will remove)</span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label>Start number</label>
										<input type="number" class="form-control minp" value="2">
									</div>	
									<div class="form-group">
										<button class="btn green preview-pagination">Preview</button>
									</div>
									
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>End number</label>
										<input type="number" class="form-control maxp" value="10">
									</div>	
									
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>Choose Scraper (when has another structure)</label>
										<select name="scraper-pagination">
											<option>Choose scraper</option>
										</select>
									</div>	
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		
		</div>
		</div>
		
        <div class="form-group" style="margin-top:15px;">
        	<button data-step="6" data-intro="Let's start the scrap and wait...!" type="button" class="btn green import-scraper"><i class="fa fa-circle"></i>  Start Scrap</button>
            <button data-step="5" data-intro="Save your configuration above to use next scrap if you want to.!" type="button" class="btn blue save-scraper"><i class="fa fa-floppy-o"></i>  Save scraper data for later use</button>
            <button data-step="10" data-intro="Possibility to modify your data..!" type="button" class="slideButtons btn green product-only modify-field-scraper" data-target="#toogle-1"><i class="fa fa-pencil-square-o"></i>  Modify Fields in CSV</button>
			<button type="button" class="slideButtons btn green save-csv  product-only " data-step="11" data-intro="Off course able to download as CSV!" ><i class="fa fa-file-excel-o"></i>  Save CSV</button>
			<button type="button" data-step="12" data-intro="Yeah, you can upload to Shopify shop..!"  class="slideButtons btn green  product-only  csvToshopify"><i class="fa fa-download"></i>  Send to Shopify</button>
		</div>
		
		<div class="resultsLinks">
		
		</div>
        
		<div data-step="9" data-intro="The table should display here..."></div>
		<div id="toogle-1" class="form-group" style="margin-top:15px;display:none;">
			<div class="test1 pull-right" style="margin-bottom: 15px;">
				<div class="tools" style="display: inline-block;"><span class="btn history btn-default"><i class="fa fa-file-excel-o" aria-hidden="true"></i></span></div>
				<div class="tools" style="display: inline-block;"><span class="btn settings btn-default"><i class="fa fa-wrench" aria-hidden="true"></i></span></div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<form id="tb1">
					<table id="tbl1" class="table table-bordered table-striped table-condensed flip-content">
						
					</table>
					</form>
				</div>
			</div>
		</div>
		<br>
		</br>
		
		<div class="form-group row json-collection" style="margin-bottom:10px;display:none;padding-top:5px;">
			<label class="control-label col-md-12"><strong>Json Collection</strong></label>
			<div class="col-md-12">
				<textarea rows="5" class="form-control output_collection"></textarea>		
			</div>	
			
		</div>	
			
		
		<div class="form-group form-collection" style="margin-bottom:10px;display:none;padding-top:5px;">
			<div class="row">
				<div class="form-group" style="margin-bottom:15px;
					height: auto;
					overflow: hidden;">
					<label class="control-label col-md-12"><strong>Make Collection</strong></label>
					<div class="col-md-3">
						<label class="control-label">Choose page meta tag label</label>
						<select class="form-control collection_key" id="collection_key">
							<option value="0"> Key </option>
						</select>
					</div>
					<div class="col-md-3">
						<label class="control-label">And Choose label from "Data"</label>
						<select class="form-control collection_value" id="collection_value">
							<option value="0"> Value </option>
						</select>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<button type="button" class="btn green create-collection">Make Collection (json) </button>
						<button type="button" class="btn green create-smartCollection">Create Collection</button>
						
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="form-group group_output_collection" style="display:none;">
						<label class="control-label">Output Collection</label>
						<textarea class="form-control output_collection"></textarea> 
					</div>
				</div>
			</div>
			
		</div>

		<div class="row" data-step="8" data-intro="Here will show button to show them as table..!">
		<div class="form-group col-md-6 export-data" style="margin-top:15px;display:none;">
			<button type="button" class="btn green show-array">See data in array</button>
			<button type="button" class="btn productlist-only green create-smartCollection_v2">Create Collections</button>
			<!--<button type="button" class="btn green import-csv">Get CSV for data</button>-->
			<button  type="button" class="btn green import-csv">Get data for CSV</button>
			<button  type="button" class="btn blue show-result">Result</button>
			<button  type="button" class="btn blue show-data" style="display:none;">See data</button>
		</div>
		</div>
		
		<div class="form-group" data-step="7" data-intro="Result of scrap should display here as json data.">
        	<div class="output-scraper-area">
				<label class="control-label">Output</label>
				<textarea class="form-control output-scraper" style="min-height:200px;"></textarea>
			</div>
        </div>
		<div class="form-group">
        	<div class="output-results-ajax">
				
			</div>
        </div>
        <div class="form-group wrap-noresult-scraper" style="display:none;">
        	<label class="control-label">URL without results</label>
			<textarea class="form-control noresult-scraper" style="min-height:200px;"></textarea>
        </div>
		
		<div class="form-group col-md-6 pull-right text-right" style="margin-top:15px;">
			<button type="button" data-value="" class="btn default selected-button-history"><i class="fa fa-file-o"></i>  History</button>
			<button type="button" data-value="" class="btn red selected-button-remove"><i class="fa fa-trash-o"></i>  Remove</button>
		</div>
    </div>
</div>

<style type="text/css">
	.has-error .form-control { border-color:#a94442 !important; }
	.show-result {display:none;}
</style>
<div class="modal fade" id="ajax_response" role="basic" aria-hidden="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
				<h4 class="modal-title">Ajax Content</h4>
			</div>
			<div class="modal-body">

			</div>
			<div class="modal-footer">
				<button type="button" class="btn default" data-dismiss="modal">Close</button>
				<button type="button" class="btn blue saveSorting">Save changes</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="ajax_data" role="basic" aria-hidden="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
				<h4 class="modal-title">Data</h4>
			</div>
			<div class="modal-body">

			</div>
			<div class="modal-footer">
				<button type="button" class="btn default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="ajax_collection" role="basic" aria-hidden="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
				<h4 class="modal-title">Create Collection</h4>
			</div>
			<div class="modal-body">

			</div>
			<div class="modal-footer">
				<button type="button" class="btn default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>



<div class="modal fade" id="ajax_history" role="basic" aria-hidden="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
				<h4 class="modal-title">History Data</h4>
			</div>
			<div class="modal-body">
				<table id="tbl_history" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th>#ID</th>
							<th>Path</th>
							<th>Date</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn default" data-dismiss="modal">Close</button>
				<button type="button" class="btn blue saveSorting">Save changes</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="fieldsData" role="basic" aria-hidden="false">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
				<h4 class="modal-title">Want to show</h4>
			</div>
			<div class="modal-body">
				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-cogs"></i>Choose Field to show
						</div>
					</div>
					<div class="portlet-body">
						<div class="row">
							<div class="col-md-12">
								<div class="hereFields">
								
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn default" data-dismiss="modal">Close</button>
				<button type="button" class="btn success saveField">Save Changes</button>
			</div>
		</div>
	</div>
</div>
<?
}
?>
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
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript" src="//jeppekjaersgaard.dk/system/resources/assets/apps/scripts/jquery.doubleScroll.js?v=<? print time();?>"></script>
<script>
var selectedCustomer = 0;

function getCustomerOptions1(){
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
			
			$('#modal-selected-customer').html(opt);	
			
		},
		complete: function(){
			$('#customer').addClass("in").show();
			$('#customer').modal("show");
			$('body').progress('open');
		}
	});
}

<?
if(isset($_SESSION["cId"]) && !empty($_SESSION["cId"]) && $_SESSION["cId"]){
	?>
	selectedCustomer = '<? print $_SESSION["cId"]; ?>';
	//$('.selected-customer').val('<? print $_SESSION["cId"]; ?>').trigger('change');
	<?
}
else{
	?>
	getCustomerOptions1();
	<?
}
?>

$( document ).ready(function() {
	<?
	if(isset($_GET["logout"]) && $_GET["logout"] == "true"){
	?>
		//window.open("/system/index.php?page=scraper","_self")
	<?
	}
	?>
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
				$('#customer').removeClass("in").hide();
				
			}
		});
	});
	
});
</script>

<script type="text/javascript" src="//jeppekjaersgaard.dk/system/resources/assets/apps/modules/scraper.js?v=<? print time();?>"></script>
