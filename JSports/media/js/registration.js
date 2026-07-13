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


function refreshTeamlist() {

	
	const lastprogramplayed = jQuery('#jform_lastprogramplayed').val();

	console.log('last program played =', lastprogramplayed);

	jQuery.ajax({
	    url: 'index.php?option=com_jsports&task=ajax.getRegistrationTeamList&tmpl=component',
	    type: 'POST',
	    dataType: 'html',
	    data: { programid: jQuery('#jform_lastprogramplayed').val() },
	    success: function (data) {
	      const $sel = jQuery('#jform_teamid');
	      $sel.html(data);

	    },
	    error: function (xhr) {
	      console.log('AJAX error:', xhr.status, xhr.responseText);
	    }
	  });

}
