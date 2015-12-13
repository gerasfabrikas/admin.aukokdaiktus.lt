$(document).ready(function(){

	//cities list
	
	/*var valueSelected0 = $('select[name="user_region"]').val();
	$.post( "ajax.php", { getRegionChild: valueSelected0})
	  .done(function( data ) {
		$('select[name="user_city"]').html(data);
	});*/
	
    $('select[name="user_region"]').on('change', function (e) {
		var optionSelected = $("option:selected", this);
		var valueSelected = this.value;
		$.post( "ajax.php", { getRegionChild: valueSelected })
		  .done(function( data ) {
			$('select[name="user_city"]').html(data);
		});
	});
	
	//subcategories list
    $('select[name="need_cat"]').on('change', function (e) {
		var optionSelected = $("option:selected", this);
		var valueSelected = this.value;
		$.post( "ajax.php", { getCatChild: valueSelected })
		  .done(function( data ) {
			$('select[name="need_subcat"]').html(data);
		});
	});
	
    $('select[name="need_cat2"]').on('change', function (e) {
		var optionSelected = $("option:selected", this);
		var valueSelected = this.value;
		$.post( "ajax.php", { getCatChild: valueSelected, excludeCat: $(this).data('exclude') })
		  .done(function( data ) {
			$('select[name="need_subcat"]').html(data);
		});
	});
	
	//daiktai subcats edit
    $('span[data-editsubcid]').click(function (e) {
		$('input[name="editableid"]').val($(this).data('editsubcid'));
		$('input[name="editable"]').val($(this).html());
		$('select[name="cat"]').val($(this).data('parent'));
		$(this).parent().append($( "#edit_subc" ));
		$( "#edit_subc" ).show();
	});
	
});

