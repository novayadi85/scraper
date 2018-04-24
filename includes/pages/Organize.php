<style>
.lbl-box.blue{
	background: #32c5d2;
    padding: 2px 10px;
    color: #fff;
}
.bars, .chart, .pie{
	height: auto !important;
}
</style>
<div class="portlet light portlet-fit portlet-datatable bordered">
	<div class="portlet-title">
		<div class="caption">
			<i class="icon-settings font-dark"></i>
			<span class="caption-subject font-dark sbold uppercase">Data of ProductList </span>
		</div>
	</div>
	<div class="portlet-body"  ng-app="scraper" ng-controller="organizer">
		<div class="dd" id="nestable_lists">
			<ol class="dd-list">
				<li data-id="{{item.id}}" class="dd-item" ng-repeat="item in results">
					<div data-id="{{item.id}}" class="dd-handle">{{item.h1}} </div>
					<span  style="position:absolute;display: inline-block;float: right;right: 5px;top: 5px;">
					<a ng-click="detail()" class="lbl-box blue">{{item.founds}}</a>
					</span>
					<ol class="dd-list" ng-if="item.children.length > 0" ng-include="'/system/includes/pages/Organize/Template/list.html'">

					</ol>
				</li>
			</ol>
		</div>
		
		<div id="modal_response" class="modal fade" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
				  <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">{{temp.h1}}</h4>
				  </div>
				  <div class="modal-body">
					<div class="hide">
					
					</div>
					<div class="row">
						<div class="col-md-4">
							<canvas class="chart chart-bar" chart-data="data" chart-labels="labels" 
							chart-series="series" chart-click="onClick" ></canvas> 
						</div>
						<div class="col-md-6">
							<p>Products: # {{temp.founds}} of produtcs on the product list</p>
							<p>Uploaded: # {{temp.uploaded}} of produtcs that we have uploaded to shopify</p>
							<p>Quotes: # {{temp.quotes}} of produtcs that we have scraped and process</p>
							<p>Collektion made in Shopify: {{temp.uploadCollectionToShopify}}</p>
						</div>
					</div>
					
					
				  </div>
				  <div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
				</div>
			</div>
		</div>
		
	</div>
</div>



<script type="text/javascript">
	angular.module('scraper', ['chart.js'])
		// Optional configuration
	  .config(['ChartJsProvider', function (ChartJsProvider) {
		// Configure all charts
		ChartJsProvider.setOptions({
		  chartColors: ['#FF5252', '#f71703'],
		  responsive: false
		});
		// Configure all line charts
		ChartJsProvider.setOptions('line', {
		  showLines: true
		});
	  }])
		.controller('organizer', ['$scope', '$http' ,'$location', '$window', function ($scope, $http , $location ,$window) {
			$scope.list = {
				'name' : '',
				'parent' : '',
				'id' : false
			};
			$scope.temp = {};
			$('body').progress('open');
			$scope.results = [];
			
			$scope.labels = ["Products","Uploaded","Quotes"];
			$scope.data = [
				[65, 59, 80]
			];
			
			$scope.search = function () {
				$http.post("/system/includes/pages/Organize/Ajax/ajax.response.php",{params:$scope.user , action: 'getLists'})
					.then(function (response) {
						if(!response.data['list']){
							toastr.error("Sorry , No data found yet!","Error");
					   
						}
						else{
							$scope.results = response.data['list'];
							$('#nestable_lists').nestable({maxDepth:4,
								dropCallback: function(details){
									$scope.reloadList(details);
								}
							});
						}
						
					})
					.catch(function (err) {
					   toastr.error("Something error , check your connection !","Error");
					   $('body').progress('close');
					})
					.finally(function () {
						$('.nestable_lists').nestable('buttons');
						$('body').progress('close');
					});
				
			}
			
			$scope.reloadList = function(details) {
				var items  = $(details.sourceEl).html();
				$('body').progress('open');
				$http.post("/system/includes/pages/Organize/Ajax/ajax.response.php",{params:$scope.list , action: 'save' , details: details})
					.then(function(response) {
						$scope.search();
					})
					.finally(function () {
						$('body').progress('close');
					});
				
				
			}
			
			$scope.tree = function(trees) {
				
				return "<div>Test</div>";
			}
			
			$scope.redirect = function(){
				/** 
				var item = this.item;
				console.log(item);
				$window.location.href = "/system/index.php?page=Toscrape&listId=" + item['id'] ;
				**/
				return false;
			};
			
			$scope.detail = function(){
				var item = this.item;
				$scope.temp = item;
				$scope.data = [
					[item.founds, item.uploaded, item.quotes]
				];
				$("#modal_response").modal("show");			
			};
			
			//ng-click="remove()"
			
			angular.element(document).ready(function () {
				$scope.search();
			});

		}]);  
</script>


