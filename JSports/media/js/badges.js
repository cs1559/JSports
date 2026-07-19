function refreshTeamlist(selectedTeamId) {

	jQuery.ajax({
	    url: 'index.php?option=com_jsports&task=ajax.getProgramTeamList&tmpl=component',
	    type: 'POST',
	    dataType: 'html',
	    data: { programid: jQuery('#jform_programid').val() },
	    success: function (data) {
	      const $sel = jQuery('#jform_teamid');
	      $sel.html(data);
	      if (selectedTeamId) {
	        $sel.val(String(selectedTeamId)).trigger('change');
	      }
	    },
	    error: function (xhr) {
	      console.log('AJAX error:', xhr.status, xhr.responseText);
	    }
	  });

}