/*
	This selector enables a redirect from the team profile based on their menu
	selection.
*/
jQuery("#profile-actions").change(function(){
 	window.location = jQuery(this).val();
});

