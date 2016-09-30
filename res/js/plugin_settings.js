

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

jQuery(document).ready(function($) {           //wrapper
	
	if(!$("#cert-generate-button").length) {
		return;
	}
	
    $("#cert-generate-button").click(function() {
    	$('#error-banner').hide();
    	
    	if($("#input_cert_passphrase").val() != $("#input_cert_passcheck").val()) {
    		$('#error-banner-text').text("Passphrases do not match");
    		$('#error-banner').show();
    		return;
    	}

    	var this2 = this;
        $.post(sn_settings.ajax_url, {
           _ajax_nonce: sn_settings.nonce,
            action: "settings_generate_certificate",
            passphrase: $("#input_cert_passphrase").val(),
            email: $("#input_cert_email").val(),
        }, function(json) {
        	response = $.parseJSON(json);
        	if(response.result == "error") {
        		$('#error-banner-text').text(response.reason);
        		$('#error-banner').show();
        		return;
        	}
        	
        	location.reload();
        	
        });
        
        return false;
    });
});


jQuery(document).ready(function($) {           //wrapper
	
	if(!$("#node-register-button").length) {
		return;
	}
	
    $("#node-register-button").click(function() {
    	$('#error-banner').hide();

    	var this2 = this;
        $.post(sn_settings.ajax_url, {
           _ajax_nonce: sn_settings.nonce,
            action: "settings_node_register",
        	}, function(data) {
        		location.reload();
        	});
        
        return false;
    });
});

jQuery(document).ready(function($) {           //wrapper
	button = null;
	state = 'enabled';
	
	if(!$("#node-state-enable").length) {
		if(!$("#node-state-disable").length) {
			return;
		} else {
			button = $("#node-state-disable");
			
			state = 'disabled';
		}
	} else {
		button = $("#node-state-enable");
		state = 'enabled';
	}

	
    button.click(function() {
    	$('#error-banner').hide();

    	var this2 = this;

        $.post(sn_settings.ajax_url, {
           _ajax_nonce: sn_settings.nonce,
            action: "settings_node_state",
            state: state,
        	}, function(data) {
        		location.reload();
        	});
        
        return false;
    });
});


jQuery(document).ready(function($) {
	if(!$("#key-reset-button").length) {
		return;
	}
	
	$("#key-reset-button").click(function() {
		$("#key-reset-button").hide();
		$('#input-validate-request').val("");
		$("#key-reset-form").show();
		return false;
	});
	
	$("#key-reset-button-actual").click(function() {
		springname = $('#input-validate-request').val();
		
	       $.post(sn_settings.ajax_url, {
	           _ajax_nonce: sn_settings.nonce,
	            action: "settings_certificate_reset",
	            validation: springname,
	            state: state,
	        	}, function(data) {
	        		response = $.parseJSON(data);
	        		if(response.result == "error") {
	        	 		$('#error-banner-text').text("Your springname you supplied was not correct -- cannot reset certificates");
	            		$('#error-banner').show();
	            		return;
	        		}
	        		
	        		location.reload();
	        	});
		return false;
	});
	
	$("#key-reset-button-cancel").click(function() {
		$("#key-reset-button").show();
		$("#key-reset-form").hide();
		$('#error-banner').hide();
		return false;
	});
});
