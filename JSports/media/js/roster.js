

jQuery( document ).ready(function() {
    console.log( "ready!" );
    val = jQuery('#jform_id').val();
    if (val == 0) {
		hideStaffElements();
		return;
	}
	
	type = jQuery('#jform_classification').val();
	if (type == "P") {
		hideStaffElements();
	} else {
		showStaffElements();
	}
});

jQuery("#jform_classification").change(function(){

	val = jQuery("#jform_classification option:selected").val();
	
	switch (val) {
		case 'S':
			showStaffElements();
			break;
		default:
			hideStaffElements();
			break;
	}
	
});

function showStaffElements() {
			jQuery("#jform_role").show();
			jQuery("#jform_role-lbl").show();
			jQuery("#jform_userid").show();
			jQuery("#jform_userid-lbl").show();
			jQuery("#jform_email").show();
			jQuery("#jform_email-lbl").show();
			jQuery("#jform_staffadmin").show();
			jQuery("#jform_staffadmin-lbl").show();
			jQuery("#jform_playernumber").hide();
			jQuery("#jform_playernumber-lbl").hide();			
}

function hideStaffElements() {
			jQuery("#jform_role").hide();
			jQuery("#jform_role-lbl").hide();
			jQuery("#jform_userid").hide();
			jQuery("#jform_userid-lbl").hide();
			jQuery("#jform_email").hide();
			jQuery("#jform_email-lbl").hide();
			jQuery("#jform_staffadmin").hide();
			jQuery("#jform_staffadmin-lbl").hide();
			jQuery("#jform_playernumber").show();
			jQuery("#jform_playernumber-lbl").show();				
}

