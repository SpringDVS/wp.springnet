
var SNetBulletinsLatestCli = {
    network: "",
    request: function(network, query) {
    	SNetBulletinsLatestCli.network = network;
    	query = query == '' ? '' : '?'+query;
    	
    	uri = network+"/bulletin/"+query;

        var $j = jQuery.noConflict();
        $j('#spring-bulletin-loader').show();

    	$j.post(sn_gateway_bulletin.ajax_url, {
            _ajax_nonce: sn_gateway_bulletin.nonce,
             action: "gateway_bulletin_request",
             uri: uri,
    	}, function(response) {
    		data = $j.parseJSON(response);
    	    if(data.service == "error"){ console.log("Service Error"); return; }
    	    if(data.status != "ok"){ console.log("Service Error"); console.log(data.uri); return; }
    	    SNetBulletinsLatestCli.apply(data.content);
    	});

    },
    
    rerequest: function(query) {
        tag = query == "" ? "none" : query;
        var $j = jQuery.noConflict();
        
        $j("#sdvs-bulletin-list-filter").text(tag);
        query = query == "" ? "" : "tags="+query;
        SNetBulletinsLatestCli.request(
            SNetBulletinsLatestCli.network,
            query
        );
        
    },
    
    requestProfile: function(node) {
        uri = node+"/orgprofile/";

        var $j = jQuery.noConflict();
        $j('#spring-bulletin-loader').show();

    	$j.post(sn_gateway_bulletin.ajax_url, {
            _ajax_nonce: sn_gateway_bulletin.nonce,
             action: "gateway_bulletin_request",
             uri: uri,
    	}, function(response) {
    		data = $j.parseJSON(response);
    	    if(data.service == "error"){ console.log("Service Error"); return; }
    	    if(data.status != "ok"){ console.log("Service Error"); console.log(data.uri); return; }
    	    SNetBulletinsLatestCli.applyProfile(data.content);
    	});
    },
    
    requestContent: function(node, uid) {
        uri = node+"/bulletin/"+uid+"/";

        var $j = jQuery.noConflict();
        $j('#spring-bulletin-loader').show();
        
       	$j.post(sn_gateway_bulletin.ajax_url, {
            _ajax_nonce: sn_gateway_bulletin.nonce,
             action: "gateway_bulletin_request",
             uri: uri,
    	}, function(response) {
    		data = $j.parseJSON(response);
    		if(data.service == "error"){ console.log("Service Error"); return; }
    		if(data.status != "ok"){ console.log("Service Error"); console.log(data.uri); return; }
    		SNetBulletinsLatestCli.applyContent(data.content);
    	});

    },
    
    apply: function(bulletins) {
        
        var $j = jQuery.noConflict();
        html = "";
        for(index in bulletins) {
            for(node in  bulletins[index]) {
                list = bulletins[index][node];
                
                list_html = "";
                l = list.length-1;
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
                
                eid = node.replace(/\./g, "-");
                
                html += [
                    "<tr><td class='node-uri'>",
                    "<a href='javascript:void(0);' onclick='SNetBulletinsLatestCli.requestProfile(`"+node+"`)'>"+node+"</a> &rsaquo;&rsaquo;",
                    "</td></tr>",
                    
                    "<tr id='"+eid+"-profile'class='profile-view'><td>",
                    "</td></tr>",

                    "<tr><td><table class='inner'><tbody>"+list_html+"</tbody></table></td></tr>"
                ].join('\n');
                
                
            }
        }
        $j("#sdvs-bulletin-list-body").empty();
        $j("#sdvs-bulletin-list-body").html(html);
        $j('#spring-bulletin-loader').hide();
    },

    applyProfile: function(profile) {
        var $j = jQuery.noConflict();
        for(index in profile) {
            for(node in profile[index]) {
                eid = node.replace(/\./g, "-");
                
                item = profile[index][node];
                html = [
                    "<div class='profile-block'>",
                    "<h3>"+item.name+"</h3>",
                    "<a target='_blank' href='"+item.website+"'>"+item.website+"</a><br>",
                    "<a class='control' href='javascript:void(0);' onclick='SNetBulletinsLatestCli.hideProfile(`"+node+"`)'>hide</a>",
                    "</div>"
                ].join('\n');
                element = $j("#"+eid+"-profile");
                element.html(html);
                element.show();
                
            }
        }
        $j('#spring-bulletin-loader').hide();
    },
    
    hideProfile: function(node) {
        var $j = jQuery.noConflict();
         eid = node.replace(/\./g, "-");
        element = $j("#"+eid+"-profile");
        element.hide();
    },
    
    applyContent: function(bulletins) {
    	 var $j = jQuery.noConflict();
    	 for(index in bulletins) {
    		 for(node in  bulletins[index]) {
    			 item = bulletins[index][node];
    			 
    			 info = [
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
        element = $j("#content-"+uid);
        element.hide();
    },
}