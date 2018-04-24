<?
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/inc/db/connection.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/backend/system/bricks/adminFunctions.php");
if(!empty($_GET["history"])){
	list($scrapers) = getBrickData("scraper",$_GET["history"],"*",false,false,$connection);
	if(file_exists($_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Scraper/import/data/".$scrapers["id"].".json")){
		$contents = $_SERVER["DOCUMENT_ROOT"]."/system/includes/pages/Scraper/import/data/".$scrapers["id"].".json";
		$contents = file_get_contents($contents);
		$contents = json_decode($contents,true);
	}
	$schema = getAttributesForBrick("scraper_productlist",$connection);
	$scraper_products = getBrickData("scraper_product",false,"*",false,false,$connection);
	$scraper_productsList = getBrickData("scraper_productlist",false,"*",false,false,$connection);
	$th = "";
	foreach($schema as $k => $sch){
		$th .= "<th>".ucfirst($sch)."</th>";
	}
}
?>
<hr>
<div class="row">
	<div class="col-md-12">
		<h3>Listings</h3>
		<hr>
		<table id="example" class="table table-striped dataTable" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>NO#</th>
					<?=$th;?>
				</tr>
			</thead>
			<tbody>
				<?
				$i = 0 ;
				foreach($scraper_productsList as $scraper_products){
					$i++;
					print "<tr>";
					print "<td>{$i}</td>";
					foreach($scraper_products as $key => $list){
						if(in_array($key,$schema)){
							print "<td>{$list}</td>";
						}
					}
					print "</tr>";
				}
				?>
			</tbody>
			<tfoot>
				<tr>
					<?=$th;?>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

<script>
$(document).ready(function() {
	$('table').DataTable({
		"orderable":  false       
	});
});
</script>