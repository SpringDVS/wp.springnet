PopupAnchor = function(id, anchor) {
	this.id = id;
	this.anchor = anchor;
};

var SnetPopup = {
	activePopupId: undefined,
		
	popupProfile: function(message, id, left, boxOffset, top, arrowLeft, anchor) {
		var $j = jQuery.noConflict();
    	var popup = [
    	            '<div id="'+id+'-arrow" class="arrow-up" style="top: '+top+'px; left: '+(left+arrowLeft)+'px;"></div>',
 	    		    '<div id="'+id+'" class="popup-profile-container" style="position: absolute; left:'+(left + boxOffset)+'px; top:'+(top+5)+'px;">',
 	    		      	'<div class="snet-popup popup-profile-window">'+message+'</div>',
 	    		    '</div>'
 	    		].join('\n');
    	this.activePopupId = id;
    	$j('#'+anchor).append(popup);
    	$j('body').click(SnetPopup.closePopup);
	},

	clearPopups: function() {
		var $j = jQuery.noConflict();
		if(this.activePopupId != undefined) {
			$j('#'+this.activePopupId).remove();
		}
	},
	
	closePopup: function(e) {
		var $j = jQuery.noConflict();
		if(e.target.className == 'snet-popup') {
			return;
		};
		$j('#'+SnetPopup.activePopupId).remove();
		$j('#'+SnetPopup.activePopupId+'-arrow').remove();
		$j('body').unbind('click', SnetPopup.closePopup);		
	}	
	
}

