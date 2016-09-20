

jQuery(document).ready(function($) {           //wrapper

    $("#lookup-primary").click(function() {
        var this2 = this;
        $.post(sn_settings.ajax_url, {
           _ajax_nonce: sn_settings.nonce,
            action: "settings_lookup_primary",
            geonetwork: $("#input-geonet-name").val(),
        }, function(data) {
        	response = $.parseJSON(data);
        	
        	if("invalid" == response.hostname) {
        		// Handle error here
        		return;
        	}

        	$('#input-geonet-hostname').val(response.hostname);
        	$('#input-geonet-hostname-h').val(response.hostname);
        	
        	$('#input-geonet-address').val(response.address);
        	$('#input-geonet-address-h').val(response.address);
        	
        	$('#input-geonet-resource').val(response.resource);
        	$('#input-geonet-resource-h').val(response.resource);
        	
        	if("" != $('#input-node-springname')) {
        		spring = $('#input-node-springname').val();
        		geonet = $('#input-geonet-name').val();
        		uri = spring+"."+geonet+".uk" 
        		$('#info-spring-uri').text("spring://"+uri);
        		$('#input-spring-uri-h').val(uri);
        	}
        });
        
        return false;
    });
});

// 3858f62230ac3c915f300c664312c63f