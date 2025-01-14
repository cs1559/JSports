function refreshGroupList() {

	jQuery('#jform_grouping').html();
		
	jQuery.ajax({
		
			    url: 'index.php?option=com_jsports&task=ajax.buildGroupList&tmpl=component',
			    data: '&programid=' + jQuery("#jform_programid option:selected").val(),
			    type: 'POST',
				dataType: 'html',
				beforeSend: function() {
					spinner.removeAttribute('hidden');
				},	
	    		success: function(data){
	    			jQuery("#jform_grouping").html(data);
	    		},
	    		complete: function() {
	  				spinner.setAttribute('hidden','');
	    		}	    		
			});		

}
