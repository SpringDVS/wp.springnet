
var SNetBulletinsLatestCli = {
    network: "",
    tags: "",
    limit: "",
    activeAnchor: undefined,

    request: function(network, tags, limit) {
    	SNetBulletinsLatestCli.network = network;

        var $j = jQuery.noConflict();
        $j('#spring-bulletin-loader').show();

    	$j.post(sn_gateway_bulletin.ajax_url, {
            _ajax_nonce: sn_gateway_bulletin.nonce,
             action: "gateway_bulletin_request",
             network: network,
             tags: tags,
             limit: limit
    	}, function(response) {
    		data = $j.parseJSON(response);
    	    if(data.service == "error"){ console.log("Service Error"); return; }
    	    if(data.status != "ok"){ console.log("Service Error"); console.log(data.uri); return; }
    	    SNetBulletinsLatestCli.apply(data.content);
    	});

    },
    
    rerequest: function(tags) {
        self.tags = tags;
        tags = tags == "" ? "none" : tags;
        var $j = jQuery.noConflict();
        
        $j("#sdvs-bulletin-list-filter").text(tags);

        SNetBulletinsLatestCli.request(
            SNetBulletinsLatestCli.network,
            self.tags,
            self.limit
        );
        
    },
    
    requestProfile: function(node) {

        var $j = jQuery.noConflict();
        $j('#spring-bulletin-loader').show();

    	$j.post(sn_gateway_bulletin.ajax_url, {
            _ajax_nonce: sn_gateway_bulletin.nonce,
             action: "gateway_bulletin_request",
             node: node,
    	}, function(response) {
    		data = $j.parseJSON(response);
    	    if(data.service == "error"){ console.log("Service Error"); return; }
    	    if(data.status != "ok"){ console.log("Service Error"); console.log(data.uri); return; }
    	    SNetBulletinsLatestCli.applyProfile(data.content);
    	});
    },
    
    requestContent: function(node, uid) {

    	var $j = jQuery.noConflict();
        $j('#spring-bulletin-loader').show();
        
       	$j.post(sn_gateway_bulletin.ajax_url, {
            _ajax_nonce: sn_gateway_bulletin.nonce,
             action: "gateway_bulletin_request",
             node: node,
             uid: uid
    	}, function(response) {
    		var data = $j.parseJSON(response);
    		if(data.service == "error"){ console.log("Service Error"); return; }
    		if(data.status != "ok"){ console.log("Service Error"); console.log(data.uri); return; }
    		SNetBulletinsLatestCli.applyContent(data.content);
    	});

    },
    
    safeEid: function(node) {
   	 return "snetbl303-" + node.replace(/\./g, "-");
    },
    
    apply: function(bulletins) {
        
        var $j = jQuery.noConflict();
        var html = "";
        for(index in bulletins) {
            for(node in  bulletins[index]) {
                var list = bulletins[index][node];
                
                var list_html = "";
                var l = list.length-1;
                for(i in list) {
                    item = list[i];
                    
                    for(ti in item.tags) {
                        tag = item.tags[ti];
                        item.tags[ti] = "<a href='javascript:void(0)' onclick='SNetBulletinsLatestCli.rerequest(`"+tag+"`)'>" + tag +"</a>"
                    }
                    
                    list_html += ([
                        "<tr><td><a class='title' href='javascript:void(0)' onclick='SNetBulletinsLatestCli.requestContent(`"+node+"`,`"+item.uid+"`)'>" + item.title +"</a> &rsaquo;&rsaquo;</td></tr>",
                        "<tr><td style='display: none;' id='content-"+item.uid+"'>Content</td></tr>"
                    ].join('\n'));
                    
                    if(i == l) {
                    	list_html += "<tr><td class='details'>tags: "+item.tags.join(', ')+"</td></tr>";
                    } else {
                    	list_html += "<tr><td class='details separator'>tags: "+item.tags.join(', ')+"</td></tr>";
                    }
                }
                
                var eid = node.replace(/\./g, "-");
                
               html += [
                    "<tr><td class='node-uri'>",
                    "<a id='"+this.safeEid(node)+"-profile' href='javascript:void(0);'>"+node+"</a> &rsaquo;&rsaquo;",
                    "</td></tr>",

                    "<tr><td><table class='inner'><tbody>"+list_html+"</tbody></table></td></tr>"
                ].join('\n');
                
                
            }
        }
        

	
		
		
        $j("#sdvs-bulletin-list-body").empty();
        $j("#sdvs-bulletin-list-body").html(html);
        $j('#spring-bulletin-loader').hide();
        
        for(index in bulletins) {
            for(node in  bulletins[index]) {
            	
            	var v = function(node) {
            		prefix = SNetBulletinsLatestCli.safeEid(node)
            		$j('#'+prefix+'-profile').click(function(){            		
	            		SnetPopup.clearPopups();
	            		var idPopup = prefix+"-popup";
	            		SNetBulletinsLatestCli.activeAnchor = new PopupAnchor(idPopup, this.id)
	            		SNetBulletinsLatestCli.requestProfile(node);
            		});
            	};
            	
            	v(node);
            }
        }
    },

    applyProfile: function(profile) {
    	
       	var $j = jQuery.noConflict();
    	var pid = SNetBulletinsLatestCli.activeAnchor.id;
    	var anchor = SNetBulletinsLatestCli.activeAnchor.anchor;
		var pos = $j('#'+anchor).position();
		var arrowLeft = 20;
		var left = pos.left;
		var top = pos.top + 20;
		
		
		
    	
    	
    	var m = "";
        for(index in profile) {
            for(node in profile[index]) {
                                
                var item = profile[index][node];
                m = [
        		         '<h3 class="snetbx-popup">' + item.name + '</h3>',
        		         '<a target="_BLANK" class="snetbx-popup" href="' + item.website + '">' + item.website + '</a>'
        		         ].join('\n');
            }
        }
        
        SnetPopup.popupProfile(m, pid, left, 0, top, arrowLeft, 'snetbl-list-container');
        $j('#spring-bulletin-loader').hide();
    },
    
    hideProfile: function(node) {
        var $j = jQuery.noConflict();
        var eid = node.replace(/\./g, "-");
        var element = $j("#"+eid+"-profile");
        element.hide();
    },
    
    applyContent: function(bulletins) {
    	 var $j = jQuery.noConflict();
    	 for(index in bulletins) {
    		 for(node in  bulletins[index]) {
    			 var item = bulletins[index][node];
    			 
    			 var info = [
    			         item.content,
    			         "<br><a href='javascript:void(0);' onclick='SNetBulletinsLatestCli.hideContent(`"+item.uid+"`)'>hide</div>"
    			       ].join('\n');
    			 $j('#content-'+item.uid).html(info);
    			 $j('#content-'+item.uid).show();
    		 }
    	 }
    	 
         $j('#spring-bulletin-loader').hide();
    },
    
    hideContent: function(uid) {
        var $j = jQuery.noConflict();
        var element = $j("#content-"+uid);
        element.hide();
    }
}