<div class="row">
	<div class="col-md-12">
		<div class="row hide">
			<div class="col-md-4">
				<div class="form-group">
					<label class="control-label">Select Customer</label>
					<select class="form-control ui-select selected-customer">
					
					</select>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="clearfix">
				<div class="col-md-12">
					<div class="form-group row">
						<label class="control-label col-md-12">Choose type</label>
						<div class="btn-group col-md-12 btn-choose-type" data-toggle="buttons">
							<label class="btn btn-default active">
							<input type="radio" value="pages" class="toggle"> Page </label>
							<!--<label class="btn btn-default">
							<input type="radio" value="page" class="toggle"> Page </label>
							-->
							<label class="btn btn-default">
							<input type="radio" value="lists" class="toggle"> Product List </label>
							<label class="btn btn-default">
							<input type="radio" value="products" class="toggle"> Product </label>
							
						</div>
					</div>
				</div>
				
			</div>
		</div>
		<div class="row">
			<div class="clearfix">
				<div class="col-md-12">
					<div class="form-group row">
						<label class="control-label col-md-12">Show products with these tags</label>
						<div class="col-md-12">				
							<!--<div class="btn-group">
								<div class="product-only">
									<select placeholder="Choose tags" class="selected-tags ui-select choosen form-control" name="tags">			
										<option value="0">All tags</option>
									</select>
								</div>
							</div>-->
							<div class="btn-group">
								<div class="dropdown product-only">
									<a id="dLabel" role="button" data-toggle="dropdown" class="btn btn-primary lblTags" data-target="#" >
										<span class="selected" style="margin-right: 10px;">All Tags</span><span class="caret"></span>
									</a>
									<ul class="dropdown-menu multi-level" role="menu" aria-labelledby="dropdownMenu">
										<li class="last-child"><a style="text-align: left;" class="btn blue align-left"> All Tags </a></li>
									</ul>
									
								</div>
							</div>
							
							<div class="btn-group selected-status" data-toggle="buttons">
								<label class="btn btn-default list-only active">
								<input type="radio" value="all" class="toggle"> All lists </label>
								
								<label class="btn btn-default product-only">
								<input type="radio" value="all" class="toggle"> All products </label>
								
								<label class="btn btn-default page-only pages-only">
								<input type="radio" value="all" class="toggle"> All pages </label>
								
								
								<label class="btn btn-default">
								<input type="radio" value="uploaded" class="toggle"> Uploaded </label>
								<label class="btn btn-default">
								<input type="radio" value="not_uploaded" class="toggle"> Not uploaded </label>
							</div>
						</div>
						<div class="col-md-12">	
							<div class="btn-group">
								<div class="product-only">
									<input type="checkbox" checked class="withchilds" value="true"><label>Show with its childs?</label>
								</div>
							</div>
						</div>
												
					</div>
					<div class="form-group row">
						<div class="col-md-12">	
							<div class="btn-group">
								<a href="javascript:;" class="btn yellow upload-list" style="display:none;"> Upload shown list </a>
								<a href="javascript:;" class="btn grey-cascade change-status"> Remove status </a>
								<a href="javascript:;" class="btn red remove-item"> Remove Items </a>
								<a href="javascript:;" class="btn green queue-items"> Send to Shopify (Queue) </a>
								<a target="__blank" href="/system/index.php?page=Organize" class="btn green organize-list list-only" style="display:none;"> Organize  list </a>
								
							</div>
							
						</div>
					</div>
					
				</div>
				
			</div>
		</div>
		<div class="row">
			<div class="col-12">		
				<div class="portlet box">		
					<div class="portlet-body">
						<table id="table1" class="table dataTable  table-list table-striped table-bordered table-hover">
							<thead>
								<tr>
									<th scope="col"> &nbsp; </th>
									<th scope="col"> Url </th>
									<th scope="col"> Products  </th>
									<th scope="col"> Scraped  </th>
									<th scope="col"> UnScraped  </th>
									<th scope="col"> Pagination  </th>
									<th scope="col"> Tag </th>
									<th scope="col"> Created </th>
									<th scope="col"> Updated </th>
									<th scope="col"> Action  </th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<th scope="col"> &nbsp; </th>
									<th scope="col"> Url </th>
									<th scope="col"> Products  </th>
									<th scope="col"> Scraped  </th>
									<th scope="col"> UnScraped  </th>
									<th scope="col"> Pagination  </th>
									<th scope="col"> Tag </th>
									<th scope="col"> Created </th>
									<th scope="col"> Updated </th>
									<th scope="col"> Action  </th>
								</tr>
							</tfoot>
						</table>


						<table id="table2" class="table dataTable table-product table-striped table-bordered table-hover">
							<thead>
								<tr>
									<th scope="col"> No.</th>
									<th scope="col"> H1</th>
									<th scope="col"> Img</th>
									<th scope="col">Title</th>
									<th scope="col"> Canonical </th>
									<th scope="col"> Tags </th>
									<th scope="col"> Handle </th>
									<th scope="col"> Price </th>
									<th scope="col"> Brand </th>
									<th scope="col"> Offer </th>
									<th scope="col"> Queue </th>
									<th scope="col"> Action </th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<th scope="col"> No.</th>
									<th scope="col"> H1</th>
									<th scope="col"> Img</th>
									<th scope="col">Title</th>
									<th scope="col"> Canonical </th>
									<th scope="col"> Tags </th>
									<th scope="col"> Handle </th>
									<th scope="col"> Price </th>
									<th scope="col"> Brand </th>
									<th scope="col"> Offer </th>
									<th scope="col"> Queue </th>
									<th scope="col"> Action </th>
								</tr>
							</tfoot>
						</table>

						<table id="table3" class="table dataTable table-page table-striped table-bordered table-hover">
							<thead>
								<tr>
									<th scope="col"> No. </th>
									<th scope="col"> H1 </th>
									<th scope="col"> Title </th>
									<th scope="col"> Canonical </th>
									<th scope="col"> Handle </th>
									<th scope="col"> Updated </th>
									<th scope="col"> Action  </th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<th scope="col"> No. </th>
									<th scope="col"> H1 </th>
									<th scope="col"> Title </th>
									<th scope="col"> Canonical </th>
									<th scope="col"> Handle </th>
									<th scope="col"> Updated </th>
									<th scope="col"> Action  </th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>  
			</div>  
		</div>
	</div>
</div>


<div id="modal_response" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">Shopify</h4>
		  </div>
		  <div class="modal-body">
			<p>Some text in the modal.</p>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			<button type="button" class="btn btn-primary green">Update</button>
		  </div>
		</div>
	</div>
</div>

