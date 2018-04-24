<style>
.grid button {
    margin-bottom: 10px;
}
.product_wrapper button{
	-webkit-transition:  background-color 0.5s;
	-moz-transition:  background-color 0.5s;
	-o-transition:  background-color 0.5s;
    transition:  background-color 0.5s;
}
.grid button.active{
	color: #FFFFFF;
    background-color: #afafaf;
}
button.btn.btn-product.btn-default {
    width: 230px;
    height: 60px;
    word-wrap: inherit !important;
    white-space: inherit;
}
button.btn.btn-product.btn-default span{
	max-height: 40px;
    overflow: hidden;
    display: block;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>
<script type="text/javascript">
	function get_collections(cid){
		var cid = cid || 0;

		$.ajax({
			type: "POST",
			url: "/system/includes/pages/Collections/ajax.php?v="+Math.random(),
			dataType: 'json',
			traditional: true,
			data: {
				'mode': 'get_collection'
			},
			success: function(obj){
				var collections = '';
				var products = '<h2>Products</h2>';
				if(obj.collections){
					$.each(obj.collections, function(key, value) {
						if(typeof value['title'] != 'undefined')
						collections += '<button title="'+value.tags+'" data-toggle="tooltip" type="button" data-tag="'+value.tags+'" data-id="'+key+'" class="btn btn-default btn-collection">'+value['title']+'</button>  ';
					});	
				}
				
				if(obj.products){
					$.each(obj.products, function(key, value) {
						if(typeof value['title'] != 'undefined')
						products += '<button title="'+value['title']+'" data-toggle="tooltip" data-tags="'+value['tags']+'" type="button" data-id="'+value['id']+'" class="btn btn-product btn-default"><span>'+value['title']+'</span></button>  ';
					});	
				}
				if(!cid){
					$('.collection_wrapper').html(collections);
					
				}

				$('.product_wrapper').html(products);
			},
			complete: function(){
				$('.btn-collection.active').trigger("click");
				$('[data-toggle="tooltip"]').tooltip(); 
			}
		});

	}
	
	function intersect(a, b) {
		var t;
		if (b.length > a.length) t = b, b = a, a = t; // indexOf to loop over shorter
		return a.filter(function (e) {
			return b.indexOf(e) > -1;
		});
	}
	
	$(document).ready(function () {
		get_collections();
		
		$(document).off('click','.btn-collection');
		$(document).on('click','.btn-collection', function (i) {
			$('.btn-collection').removeClass("active");
			$(this).addClass("active");
			var txt = $(this).text();
			var collectionTags = $(this).attr('data-tag');
			$(".product_wrapper button").removeClass("active");
			if(collectionTags != ""){
				collectionTags =  collectionTags.split(',');
				$(".product_wrapper button").each(function(index){
					var el = $(this);
					var tags = el.attr('data-tags');
					var array = tags.split(',');
					//var inters = intersect(collectionTags, array);
					for (key in collectionTags) {
						if(tags.includes(collectionTags[key])){
							el.addClass("active");
						}
					}
					/* if(inters.length){
						
					} */
					
				});
				
				
			}
			return false;
			
		});
		
		$(document).off('click','.btn-product');
		$(document).on('click','.btn-product', function () {
			var cid = ntags = $(".btn-collection.active").attr('data-id');
			var tags = $(this).attr("data-tags");
			var ntags = $(".btn-collection.active").attr("data-tag");
			var id =  $(this).attr("data-id");
			$.ajax({
				type: "POST",
				url: "/system/includes/pages/Collections/ajax.php?v="+Math.random(),
				dataType: 'json',
				traditional: true,
				data: {
					mode: 'update_product',
					tags:tags,
					ntags: ntags,
					id:id
				},
				success: function(xhr){
					if(xhr.obj){
						toastr.success(xhr.message);
						get_collections(cid);
					}
					else{
						toastr.error(xhr.message);
					}
					
				}
			});
			
		});

	
	});
</script>
<div class="row">
	<div class="col-md-12">
		<div class="grid collection_wrapper"></div><hr>
		<div class="grid product_wrapper">
			
		</div>
	</div>
</div>
