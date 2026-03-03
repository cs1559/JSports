
jQuery(function ($) {
  $('#jform_sponsorid').on('change', function () {
    refreshSponsorships();
    /* refreshAssets(); */
  });

  if ($('#jform_sponsorid').val()) {
    $('#jform_sponsorid').trigger('change');
  }
});


function refreshSponsorships() {

	const current = jQuery('#jform_sponsorshipid').val();
	  const desired = (current && current !== '') ? String(current) : String(window.JSportsDefaults?.sponsorshipid || '');

	  console.log('sponsorship current=', current, 'desired=', desired);

	  jQuery('#jform_sponsorshipid').empty();

	  jQuery.ajax({
	    url: 'index.php?option=com_jsports&task=ajax.getSponsorships&tmpl=component',
	    type: 'POST',
	    dataType: 'html',
	    data: { sponsorid: jQuery('#jform_sponsorid').val() },
	    success: function (data) {
	      const $sel = jQuery('#jform_sponsorshipid');
	      $sel.html(data);

	      if (desired && $sel.find('option[value="' + desired + '"]').length) {
	        $sel.val(desired);
	      }

	      // only use defaults once (optional)
	      window.JSportsDefaults.sponsorshipid = '';
	    },
	    error: function (xhr) {
	      console.log('AJAX error:', xhr.status, xhr.responseText);
	    }
	  });

}

function refreshAssets() {

	const currentassetid = jQuery('#jform_assetid').val(); // save selection
	
	
	console.log('current asset id =', currentassetid, 'type=', typeof currentassetid);
	console.log('options count=', $('#jform_assetid option').length);
	console.log('selected after set=', $('#jform_assetid').val());
	
	
	console.log('asset id value', currentassetid);
	
	jQuery('#jform_assetid').empty();    // empty value
	       
	jQuery.ajax({
		
			    url: 'index.php?option=com_jsports&task=ajax.getAssets&tmpl=component',
			    data: '&sponsorid=' + jQuery("#jform_sponsorid option:selected").val(), 
			    type: 'POST',
				dataType: 'html',
				data: {
					sponsorid: jQuery("#jform_sponsorid").val()
				},
				beforeSend: function() {
					console.log('sending request to refresh Asset list ');
				},	
	    		success: function(data){
	    			jQuery("#jform_assetid").html(data);
					if (currentassetid) {
					  jQuery('#jform_assetid').val(currentassetid); // restore selection
					}
	    		},
	    		complete: function() {
	  				//spinner.setAttribute('hidden','');
	    		},
				error: function (xhr) {
				  console.log('AJAX error:', xhr.status, xhr.responseText);
				}    		
			});		

}