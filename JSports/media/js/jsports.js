// @TODO This needs to be fixed.  This deals with the order in which JS files are loaded

/*
	This selector enables a redirect from the team profile based on their menu
	selection.
*/
jQuery("#profile-actions").change(function(){
 	window.location = jQuery(this).val();
});

jQuery("#program-list").change(function(){

		jQuery.ajax({
			    url: 'index.php?option=com_jsports&view=standings&layout=show&tmpl=component',
			    data: '&programid=' + jQuery("#program-list option:selected").val(),
			    type: 'POST',
				dataType: 'html',
				beforeSend: function() {
					spinner.removeAttribute('hidden');
				},	
	    		success: function(data){
	    			jQuery("#standings-container").html(data);
	    			jQuery("#standings-container").focus();
	    		},
	    		complete: function() {
	  				jQuery("#standings-activity-image").html("");   
	  				spinner.setAttribute('hidden','');
	    		}	    		
			});		

	
	
	
	
});

