// Used to auto load the standings page via AJAX passing no programid.

jQuery( document ).ready(function() {

		jQuery.ajax({
			    url: 'index.php?option=com_jsports&view=standings&layout=show&tmpl=component',
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

// Function to retrieve standings based on change in the program list.
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


	
