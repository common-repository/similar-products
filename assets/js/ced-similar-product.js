jQuery(document).ready(function(){
	jQuery('.ced_open_similar_products').click(function(){
		jQuery(this).next().show();
	});
	jQuery('.ced_similar_products_close').click(function(){
		jQuery(this).closest('.ced_similar_products').hide();
	});	
});	