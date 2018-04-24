<div ng-app="scraper" ng-controller="redirect">
	<div class="row">
		<div class="col-12">		
			<div class="portlet box">		
				<div class="portlet-body">
					<div class="btn-group">
						<div class="btn-group">
							<a href="javascript:;" ng-click="customer()" class="btn green render-now"> Render Redirect URLS </a>
						</div>
						<div class="btn-group">
							<a href="javascript:;" ng-click="send()" class="btn green send-now"> Send Alls to Shopify </a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="list-results">
		<table id="table1" class="table table-product table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th scope="col"> Type </th>
					<th scope="col"> Path </th>
					<th scope="col"> Target </th>
					
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="item in results">
					<td>{{item.type}}</td>
					<td>{{item.path}}</td>
					<td>{{item.target}}</td>
					
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<th scope="col"> Type </th>
					<th scope="col"> Path </th>
					<th scope="col"> Target </th>
					
				</tr>
			</tfoot>
			
		</table>
	</div>
	
	<div id="modal_response" class="modal fade" role="dialog">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<form ng-submit="render()" name="userForm">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Choose Customer</h4>
					</div>
					<div class="modal-body">
						<div class="form-group row">
							<label class="col-sm-3 control-label">Customer</label>
							<div class="col-sm-9">
								<div class="input-group1">
									<select class="form-control" ng-model="selectedCustomer">
										<option ng-repeat="x in customers" value="{{x.id}}">{{x.name}}</option>
									</select>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-primary green">Save</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	angular.module('scraper', [])
		.controller('redirect', ['$scope', '$http', function ($scope, $http) {
			$scope.list = {
				'type' : '',
				'target' : '',
				'path' : ''
			};
			$("#table1").hide();
			$(".send-now").hide();
			$scope.selectedCustomer = false;
			$('body').progress('open');
			$scope.results = [];
			$scope.render = function () {
				$http.post("/system/includes/pages/Redirect/Ajax/ajax.response.php",{params:$scope.selectedCustomer , action: 'getLists'})
					.then(function (response) {
						if(!response.data['list']){
							toastr.error("Sorry , No data found yet!","Error");
						
						}
						else{
							$scope.results = response.data['list'];
							$(".send-now").show();
						}
						
					})
					.catch(function (err) {
					   toastr.error("Something error , check your connection !","Error");
					   $('body').progress('close');
					})
					.finally(function () {
						$("#table1").show();
						$("#modal_response").modal("hide");
						$('body').progress('close');
					});
				
			}
			
			$scope.load = function () {
				$http.post("/system/includes/pages/Redirect/Ajax/ajax.response.php",{params:$scope.user , action: 'getCustomers'})
					.then(function (response) {
						if(!response.data['list']){
							toastr.error("Sorry , No data found yet!","Error");
					   
						}
						else{
							$scope.customers = response.data['list'];
						}
						
					})
					.catch(function (err) {
					   toastr.error("Something error , check your connection !","Error");
					   $('body').progress('close');
					})
					.finally(function () {
						$('body').progress('close');
					});
				
			}
			
			$scope.send = function () {
				$('body').progress('open');
				
				$http.post("/system/includes/pages/Redirect/Ajax/ajax.response.php",{params:$scope.results , customer: $scope.selectedCustomer , action: 'send'})
					.then(function (response) {
						if(!response.data['error']){
							toastr.error("Sorry , No data found yet!","Error");
						}
						else{
							toastr.sucess("Success , Uploaded!","Success");
							// window.open(response.data['message'], '_blank');
						}
						
					})
					.catch(function (err) {
					   toastr.error("Something error , check your connection !","Error");
					   $('body').progress('close');
					})
					.finally(function () {
						$('body').progress('close');
					});
				
			}
			
			$scope.customer = function () {
				$("#modal_response").modal("show");
			}

			angular.element(document).ready(function () {
				$scope.load();
			});

		}]);  
</script>