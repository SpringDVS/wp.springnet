PopupAnchor = function(id, anchor) {
	this.id = id;
	this.anchor = anchor;
};

var SnetbExplorerClient = {
	    network: "",
	    category: "",
	    activeAnchor: undefined,
	    request: function(network, category) {

	    	var $j = jQuery.noConflict();
	    	
	    	var oldcat = SnetbExplorerClient.category;

	    	if(oldcat != "") {
	    		$j('#snetb-explorer-'+oldcat).removeClass("tab-button-active");
	    	}
	    	SnetbExplorerClient.category = category;
	    	$j('#snetb-explorer-'+category).addClass("tab-button-active");
	    	SnetbExplorerClient.applyMessage(" ");
	    	
	    	
	    	SnetbExplorerClient.network = network;
	    	SnetbExplorerClient.filter = category;
	        
	        $j('#spring-explorer-loader').show();

	    	$j.post(sn_gateway_bulletin.ajax_url, {
	            _ajax_nonce: sn_gateway_explorer.nonce,
	             action: "gateway_bulletin_explore",
	             network: network,
	             category: category
	    	}, function(response) {
	    		var data = $j.parseJSON(response);
	    		$j('#spring-explorer-loader').hide();
	    		if(data.status != 'ok') {
	    			SnetbExplorerClient.applyMessage("Error occured");
	    			return;
	    		}
	    		SnetbExplorerClient.apply(data.content);
	    	});
	    },
	    
	    filterCat: function(category) {
	    	SnetbExplorerClient.request(SnetbExplorerClient.network, category);
	    	return false;
	    },
	    
	    requestUid: function(node, uid) {
	    	var $j = jQuery.noConflict();
	    	$j('#spring-explorer-loader').show();
	    	$j.post(sn_gateway_bulletin.ajax_url, {
	            _ajax_nonce: sn_gateway_explorer.nonce,
	             action: "gateway_bulletin_explore",
	             network: node,
	             uid: uid
	    	}, function(response) {
	    		var data = $j.parseJSON(response);
	    		$j('#spring-explorer-loader').hide();
	    		if(data.status != 'ok') {
	    			SnetbExplorerClient.applyMessage("Error occured");
	    			return;
	    		}
	    		SnetbExplorerClient.applyDetails(data.content);
	    	});
	    	
	    	this.swapClickEvent(node,uid, true);
	    	return false;
	    },
	    
	    requestProfile: function(node) {
	    	var $j = jQuery.noConflict();
	    	$j('#spring-explorer-loader').show();
	    	$j.post(sn_gateway_bulletin.ajax_url, {
	            _ajax_nonce: sn_gateway_explorer.nonce,
	             action: "gateway_bulletin_explore",
	             profile: node,
	    	}, function(response) {
	    		var data = $j.parseJSON(response);
	    		$j('#spring-explorer-loader').hide();
	    		if(data.status != 'ok') {
	    			console.log("Error occurred");
	    			return;
	    		}
	    		SnetbExplorerClient.applyProfile(data.content);
	    	});
	    	return false;
	    },
	    
	    safeEid: function(node) {
	    	 return "snetbx303-" + node.replace(/\./g, "-");
	    },
	    
	    swapClickEvent: function (node,uid, displayed) {
	    	var $j = jQuery.noConflict();
	    	var prefix = this.safeEid(node);
	    	var idAnchor = prefix+"-open-"+uid;
	    	var e = $j('#'+idAnchor);
	    	e.unbind();

	    	if(displayed == true) {
	    		e.click(function() {
	    			SnetbExplorerClient.hideDetails(node,uid);
	    		});
	    		
	    	} else {
	    		e.click(function() {
	    			SnetbExplorerClient.requestUid(node,uid);
	    		});
	    	}
	    	
	    },

	    apply: function(listing) {
	    	var html = [];
	    	for(var i = 0; i < listing.length; i++) {
	    		var prefix = this.safeEid(listing[i].node);
	    		
	    		var idDetail = prefix+"-detail-" + listing[i].uid;
	    		var idAnchor = prefix+"-open-"+listing[i].uid;
	    		
	    		var html = html.concat([
	    		 '<tr>',
	    		 '<td>',
	    		 '<a class="title-link" href="javascript:void(0);" id="'+idAnchor+'">',
	    		 	listing[i].title,
	    		 '</a>',
	    		 '</td>',
	    		 '</tr>',
	    		 '<tr class="detail" id="'+idDetail+'">',
	    		 '<td ></td>',
	    		 '</tr>'
	    		]);		
	    	}
	    	
	    	
	    	var $j = jQuery.noConflict();
	    	
	    	$j('#snetb-explorer-listing').html(html.join('\n'));

	    	for(i = 0; i < listing.length; i++) {
	    		var uid = listing[i].uid;
	    		var node = listing[i].node;

	    		var v = function(uid, node, ref) {
	    			prefix = ref.safeEid(node);
	    			idAnchor = prefix+"-open-"+uid;
	    			$j("#"+idAnchor).on('click', function() {
		    			SnetbExplorerClient.requestUid(node,uid);
		    		});
	    		};
	    		v(uid, node,this);
	    	}

	    },
	    
	    applyDetails: function(details) {

	    	var prefix = this.safeEid(details.node);
	    	var idDetail = prefix+"-detail-" + details.uid;
	    	var idNodeName = prefix+"-name-" + details.uid;
	    	
	    	var $j = jQuery.noConflict();
	    	var html = [
	    	    '<td>',
	    	    details.content,
	    	    '<div>',
	    	    '<a class="node-name" id="'+ idNodeName +'">',
	    	    	details.node,
	    	    '</a>',
	    	    '</div>',
	    	    '</td>'
	    	].join("\n");
	    	
	    	var e = $j('#'+idDetail); 
	    	e.html(html);
	    	
	    	$j('#'+idNodeName).click(function(){
	    		if(SnetbExplorerClient.activeAnchor !== undefined) {
	    			$j('#'+SnetbExplorerClient.activeAnchor.id).remove();
	    			SnetbExplorerClient.activeAnchor = undefined;
	    		}
	    		var idPopup = prefix+"-popup";
	    		SnetbExplorerClient.activeAnchor = new PopupAnchor(idPopup, this.id)
	    		SnetbExplorerClient.requestProfile(details.node);
	    	});
	    	
	    	e.show();
	    },
	    
	    applyProfile: function(details) {
	    	
	    	var $j = jQuery.noConflict();
	    	var pid = SnetbExplorerClient.activeAnchor.id;
	    	var anchor = SnetbExplorerClient.activeAnchor.anchor;
    		var pos = $j('#'+anchor).position();
    		var width = $j('#'+anchor).width();
    		
    		var m = [
    		         '<h3 class="snetbx-popup">' + details.name + '</h3>',
    		         '<a target="_BLANK" class="snetbx-popup" href="' + details.website + '">' + details.website + '</a>'
    		         ].join('\n');
	    	SnetbExplorerClient.popup(m, pid, pos, width);
	    	
	    	$j('body').click(snetbClosePopup);
	    },
	    
	    applyMessage: function(error) {
	    	var html = [
    		 '<tr>',
    		 '<td>'+error+'</td>',
    		 '</tr>'
    		];			    	
	    	var $j = jQuery.noConflict();
	    	$j('#snetb-explorer-listing').html(html.join('\n'));
	    },
	    
	    hideDetails: function (node,uid) {
	    	var $j = jQuery.noConflict();
	    	var prefix = this.safeEid(node);
	    	var idDetail = prefix+"-detail-" + uid;
	    	$j('#'+idDetail).hide();
	    	this.swapClickEvent(node, uid, false);
	    },
	    
	    popup: function(message, id, pos, width) {
	    	var $j = jQuery.noConflict();
	    	var popup = [
		    		    '<div id="'+id+'" class="popup-container" style="position: absolute; left:'+(pos.left-200)+'px; top:'+(pos.top+25)+'px;">',
		    		    '<div class="arrow-up"></div>',
		    		    '<div class="snetbx-popup snetbx-popup-window">'+message+'</div>',
		    		    '</div>'
		    		].join('\n');
    		$j('#snetb-explorer-container').append(popup);
	    }
 }

function snetbClosePopup(e) {
		if(e.target.className == 'snetbx-popup') {
			return;
		};
		var $j = jQuery.noConflict();
		$j('#'+SnetbExplorerClient.activeAnchor.id).remove();
		$j('body').unbind('click', snetbClosePopup);
}