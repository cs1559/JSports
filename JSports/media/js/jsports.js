/*
	This selector enables a redirect from the team profile based on their menu
	selection.
*/
jQuery("#profile-actions").change(function(){
 	window.location = jQuery(this).val();
});


/* 
	Implement IntersectionObverser to support ad impressions
*/
/*
document.querySelectorAll('.jsports-ad').forEach((ad) => {

    const adId = ad.dataset.adid;
	const adType = ad.dataset.type;

    // Prevent duplicate impressions (client-side)
    if (sessionStorage.getItem('ad_seen_' + adId)) {
        return;
    }

    const observer = new IntersectionObserver((entries, obs) => {

        entries.forEach(entry => {

            if (entry.isIntersecting) {

                const moduleId = ad.dataset.module || 0;

                fetch('/index.php?option=com_jsports&task=campaign.impression', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
						
                    },
                    body: JSON.stringify({
                        id: adId,
                        module: moduleId,
						type: adtype
                    })
                });

                // mark as seen
                sessionStorage.setItem('ad_seen_' + adId, '1');

                obs.unobserve(ad); // stop watching
            }

        });

    }, {
        threshold: 0.5
    });

    observer.observe(ad);

});

*/
