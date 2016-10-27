var SnetNetViewClient = {
	network: "",
	// https://css-tricks.com/snippets/javascript/get-url-variables/
    getQueryVariable: function(variable) {
            var query = window.location.search.substring(1);
            var vars = query.split("&");
            for (var i=0;i<vars.length;i++) {
                   var pair = vars[i].split("=");
                   if(pair[0] === variable){return pair[1];}
            }
            return(false);
    },
    
    requestTopography: function(network) {

    	var $j = jQuery.noConflict();
    	
    	var oldcat = SnetbExplorerClient.category;

    	this.network = network;
    	
    	

    	$j.post(sn_gateway_bulletin.ajax_url, {
            _ajax_nonce: sn_gateway_netview.nonce,
             action: "gateway_snet_netview",
             network: network,
    	}, function(response) {

    		var data = $j.parseJSON(response);
    		SnetNetViewClient.renderTopography(data);
    	});
    },
    
	renderTopography: function(net) {
		var $j = jQuery.noConflict();

		var c = $j('#snet-network-widget-viewport');

        
        var l = net.length;

        
        var ctx = c[0].getContext('2d'); 
        console.log(ctx);
        
        var cc = { x: c.width()/2, y: c.height()/2, r: (c.width()/2)-70 };
        
        nodePos = [];
        
        
        var angle = 0;

        
        var step = (l%2 === 0 && l < 5) ? 360.0 / (l+1) : 360.0 / (l) ;
        
        for( var i = 0; i < l; i++ ){
            
            var rad = angle * Math.PI / 180;
            
            var pos = {
                x: cc.x + (cc.r * Math.cos(rad) ),
                y: cc.y + (cc.r * Math.sin(rad) ) 
            };

            nodePos.push(pos);
            angle = angle + step;
        }
        

        for( var i = 0; i < l; i++) {
            var node = nodePos[i];
            for( var j = 0; j < l; j++) {
                if( i === j) {
                    continue;
                }
                var t = nodePos[j];
                ctx.moveTo(node.x,node.y);
                ctx.lineWidth = 1;
                ctx.setLineDash([5,10]);
                ctx.strokeStyle = "#CFCFCF";
                ctx.lineTo(t.x, t.y);
                ctx.stroke();
            }
        }
        ctx.lineWidth = 1;
        angle = 0;

        for( var i = 0; i < l; i++ ) {
            var p = nodePos[i];
            ctx.beginPath();
            

            ctx.arc(p.x, p.y, 10, 0, 2*Math.PI);
            ctx.fillStyle = "#7C4978";
            ctx.lineWidth = 2;
            ctx.setLineDash([0,0]);
            ctx.strokeStyle = "#5F235B";
            ctx.fill();
            ctx.stroke();
            
            ctx.fillStyle = "#3B373F";
            var offset =ctx.measureText(net[i].name).width / 2;
            ctx.font = 'bold 12pt monospace';
            ctx.fillText(net[i].name, p.x-offset, p.y+25);
            angle = angle + step;
        }

        ctx.font = 'bold 16pt monospace';
        
        var offset =ctx.measureText(this.network).width / 2;
        ctx.fillText(this.network, cc.x - offset, cc.y);
	}
}