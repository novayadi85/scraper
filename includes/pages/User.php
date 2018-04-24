<?
$User = new App\Controllers\User();

/* $searchobject = false;
$searchobject[] = array(
	"fieldname" => "url",
	"searchtype" => "=",
	"value" => "http://www.refan.com"
);


$data = false;
$data["name"] = "Refan";
$data["url"] = "http://www.refan.com";
$data["active"] = "true";

$customers = $User->show($searchobject);
$isRefanCreated = false;
if(sizeof($customers) > 0 AND is_array($customers)){
	foreach($customers as $customer){
		if($customer["url"] == "http://www.refan.com"){
			$isRefanCreated = true;
		}
	}
} */

?>
<div class="portlet light portlet-fit portlet-datatable bordered">
	<div class="portlet-title">
		<div class="caption">
			<i class="icon-settings font-dark"></i>
			<span class="caption-subject font-dark sbold uppercase">User List</span>
		</div>
	</div>
	<div class="portlet-body"  ng-app="scraper" ng-controller="user">
		<div class="btn-group">
			<a ng-click="openForm()" class="btn blue" title="Add New">Create New</a>
		</div>
		<br>
		<div class="table-container">
			<br>
			<!--<form ng-submit="search()">
				<input placeholder="Name" type="text" ng-model="user.name"> 
				<input type="submit" value="Search">
			</form>-->
			<div>
				
				<table id="table1" class="table table-product table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th scope="col"> ID.</th>
							<th scope="col"> Name </th>
							<th scope="col"> Shop </th>
							<th scope="col"> FreeApps </th>
							<th scope="col"> Updated </th>
							<th scope="col"> Action </th>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="item in results">
							<td>{{item.id}}</td>
							<td>{{item.name}}</td>
							<td>{{item.shop}}</td>
							<td>{{item.getsAppsForFree}}</td>
							<td>{{item.lastUpdateDate}}</td>
							<td><a ng-click="openData()" data-id="{{item.id}}" class="btn green">Edit</a><a ng-click="deleteData()" data-id="{{item.id}}" class="btn red">Remove</a></td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<th scope="col"> ID.</th>
							<th scope="col"> Name </th>
							<th scope="col"> Shop </th>
							<th scope="col"> FreeApps </th>
							<th scope="col"> Updated </th>
							<th scope="col"> Action </th>
						</tr>
					</tfoot>
					
				</table>
				
			</div>
			<?php 
			/* if(!$isRefanCreated){
				$User->store($data);
			}
			else{
				echo "Refan is already created";
			}
			$customers = $User->show();
			print "<pre>";
			print_r($customers);
			print "</pre>";
			
			$systemname = "scraper";
			$existingAttributes = getAttributesForBrick($systemname,$connection);

			
			print "<pre>";
				print_r($existingAttributes);
			print "</pre>"; */
			
			
			?>
	   </div>
	   <div id="modal_response" class="modal fade" role="dialog">
			<div class="modal-dialog modal-md">
				<div class="modal-content">
				  <form ng-submit="addUser()" name="userForm">
				  <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Create Customer</h4>
				  </div>
				  <div class="modal-body">
					
					<div class="form-group row">
						<label class="col-sm-3 control-label">Name</label>
						<div class="col-sm-9">
							<div class="input-group1">
								<input ng-model="user.name" type="text" name="name" class="form-control" required>
							</div>
							
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 control-label">Domain Url</label>
						<div class="col-sm-9">
							<div class="input-group1">
								<input ng-model="user.url" type="text" name="url" class="form-control" required>
							</div>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-3 control-label">Shopify Shop</label>
						<div class="col-sm-9">
							<div class="input-group1">
								<input ng-model="user.shop" type="text" name="shop" class="form-control" required>
								<span class="help-block"><i>eq: https://shopname.myshopify.com</i></span>
							</div>
							
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-3 control-label">API key</label>
						<div class="col-sm-9">
							<div class="input-group1">
								<textarea ng-model="user.api_key" class="form-control" name="api_key" rows="3"></textarea>
							</div>
							
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 control-label">API password</label>
						<div class="col-sm-9">
							<div class="input-group1">
								<textarea ng-model="user.api_password" class="form-control" name="api_password" rows="3"></textarea>
							</div>
							
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 control-label">developerMode</label>
						<div class="col-sm-9">
							<div class="input-group1">
							<select ng-model="user.developerMode" name="developerMode" class="form-control">
								<option ng-repeat="x in developerModes">{{x}}</option>
							</select>
								
							</div>
							
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 control-label">getsAppsForFree </label>
						<div class="col-sm-9">
							<div class="input-group1">
							<select ng-model="user.getsAppsForFree" name="getsAppsForFree" class="form-control">
								<option ng-repeat="x in getsAppsForFrees">{{x}}</option>
							</select>
								
							</div>
							
						</div>
					</div>
				  </div>
				  <div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" ng-disabled="userForm.name.$dirty && userForm.name.$invalid ||  
userForm.url.$dirty && userForm.url.$invalid" class="btn btn-primary green">Save</button>
				  </div>
				  </form>
				</div>
			</div>
		</div>
	   
	</div>
</div>



<script type="text/javascript">
	
	angular.module('scraper', [])
		.controller('user', ['$scope', '$http', function ($scope, $http) {
			$scope.developerModes = ["true", "false",""];
			$scope.getsAppsForFrees = ["true", "false",""];
			$scope.user = {
				'name' : '',
				'url' : '',
				'shop' : '',
				'api_key' : '',
				'api_password' : '',
				'developerMode' : '',
				'getsAppsForFree': '',
				'id' : false
			};
			$('body').progress('open');
			$scope.results = [];
			$scope.search = function () {
				/** 
				$http.post("/system/includes/pages/User/Ajax/response.php",{params:$scope.user , action: 'getCustomers'})
					.then(function(response) {
						$scope.results = response.data['customers'];
					});
					
				**/
				
				$http.post("/system/includes/pages/User/Ajax/response.php",{params:$scope.user , action: 'getCustomers'})
					.then(function (response) {
						$scope.results = response.data['customers'];
					})
					.catch(function (err) {
					   toastr.error("Something error , check your connection !","Error");
					   $('body').progress('close');
					})
					.finally(function () {
						$('body').progress('close');
					});
				
			}
			
			angular.element(document).ready(function () {
				$scope.search();
			});
			
			$scope.openData = function(params) {
				var item = this.item;
				$scope.user = {
					'name' : item['name'],
					'url' : item['url'],
					'shop' : item['shop'],
					'api_key' : item['api_key'],
					'api_password' : item['api_password'],
					'developerMode' : item['developerMode'],
					'getsAppsForFree' : item['getsAppsForFree'],
					'id' : item['id']

				};
				$("#modal_response").modal("show");
			};
			
			$scope.deleteData = function(params) {
				var item = this.item;
				swal({
				  title: "Are you sure?",
				  text: "Once deleted, you will not be able to recover this customer!",
				  icon: "warning",
				  buttons: true,
				  dangerMode: true,
				})
				.then((willDelete) => {
				  if (willDelete) {
					  $http.post("/system/includes/pages/User/Ajax/response.php",{params: item, action: 'remove'})
						.then(function(response) {
							$scope.results = response.data['customers'];
							if(response.status == '200'){
								/**
								swal("Poof! Customer has been deleted!", {
								  icon: "success",
								});
								**/
								toastr.success("Poof! Customer has been deleted!","Success");
							}
							else{
								swal("Sorry! Customer can't delete!", {
								  icon: "error",
								});
							}
						});
					
				  } 
				});
				
				
			};
			
			$scope.openForm = function() {
				$scope.resetForm();
				$("#modal_response").modal("show")
			};
			
			$scope.resetForm = function(){
			   $scope.user = {};
			};
			
			$scope.addUser = function () {
				var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
				  '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.?)+[a-z]{2,}|'+ // domain name
				  '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
				  '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
				  '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
				  '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
				//pattern.test( $scope.user['id'])
				console.log($scope.user);
				
				$http.post("/system/includes/pages/User/Ajax/response.php",{params:$scope.user , action: 'add'})
					.then(function(response) {
						if(response.data['id']){
							$scope.results = response.data['customers'];
							$("#modal_response").modal("hide");
							$scope.resetForm();
							if(response.status == '200'){
								toastr.success("Success..");
							}
						}
						else{
							if(response.data['message']){
								toastr.error(response.data['message'],'Error');
							}
							else{
								toastr.error("Error..",'error');
							}
						}
					});
				
			};
			
		}]);
	  
		  
</script>