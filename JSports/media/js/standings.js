

jQuery( document ).ready(function() {

		jQuery.ajax({
			    url: 'index.php?option=com_jsports&view=standings&layout=show&tmpl=component',
			    data: '&programid=' + jQuery("#program-list").first().val(),
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



	
