

jQuery( document ).ready(function() {
    console.log( "ready!" );
    val = jQuery('#filter_teamid').val();
    if (val === 0) {
		jQuery('.button-new').prop('disabled',true);
	} else {
		jQuery('.button-new').prop('disabled',false);
	}

});


