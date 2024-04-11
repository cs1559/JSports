

jQuery( document ).ready(function() {
    console.log( "ready!" );
    val = jQuery('#filter_teamid').val();
    if (val === 0) {
		alert('hide element');
		jQuery('.button-new').prop('disabled',true);
	} else {
		alert('show button');
	}

});


