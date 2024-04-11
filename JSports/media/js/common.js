

function refreshDivisonList() {

	jQuery('#jform_divisionid').html();
		
	jQuery.ajax({
		
			    url: 'index.php?option=com_jsports&task=ajax.buildDivisionList&tmpl=component',
			    data: '&programid=' + jQuery("#jform_programid option:selected").val(),
			    type: 'POST',
				dataType: 'html',
				beforeSend: function() {
					spinner.removeAttribute('hidden');
				},	
	    		success: function(data){
	    			jQuery("#jform_divisionid").html(data);
	    		//	jQuery("#standings-container").focus();
	    		},
	    		complete: function() {
	  				spinner.setAttribute('hidden','');
	    		}	    		
			});		

}

function refreshTeamList() {

	jQuery('#jform_teamid').html();
		
	jQuery.ajax({
		
			    url: 'index.php?option=com_jsports&task=ajax.buildTeamList&tmpl=component',
			    data: '&programid=' + jQuery("#jform_programid option:selected").val() + 
			    	'&divisionid=' + jQuery("#jform_divisionid option:selected").val(),
			    type: 'POST',
				dataType: 'html',
				beforeSend: function() {
					spinner.removeAttribute('hidden');
				},	
	    		success: function(data){
	    			jQuery("#jform_teamid").html(data);
	    		//	jQuery("#standings-container").focus();
	    		},
	    		complete: function() {
	  				spinner.setAttribute('hidden','');
	    		}	    		
			});		

}


function refreshTeamAndOpponentList() {

	jQuery('#jform_teamid').html();
		
	jQuery.ajax({
		
			    url: 'index.php?option=com_jsports&task=ajax.buildTeamList&tmpl=component',
			    data: '&programid=' + jQuery("#jform_programid option:selected").val() + 
			    	'&divisionid=' + jQuery("#jform_divisionid option:selected").val(),
			    type: 'POST',
				dataType: 'html',
				beforeSend: function() {
					spinner.removeAttribute('hidden');
				},	
	    		success: function(data){
	    			jQuery("#jform_teamid").html(data);
	    			jQuery("#jform_opponentid").html(data);
	    		//	jQuery("#standings-container").focus();
	    		},
	    		complete: function() {
	  				spinner.setAttribute('hidden','');
	    		}	    		
			});		

}