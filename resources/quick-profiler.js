// Dynamically load jQuery if not already loaded
if (typeof jQuery === 'undefined') {
	var script = document.createElement('script');
	script.type = 'text/javascript';
	script.src = 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js';
	
	// Onload
	var QPLoaded = function() { $(document).ready(QP.init); };
	if (script.readyState) {  //IE
		script.onreadystatechange = function() {
	        if (script.readyState === 'loaded' || script.readyState === 'complete') {
	            script.onreadystatechange = null;
	            QPLoaded();
	        }
	    };
	} else {
		script.onload = QPLoaded;
	}
	
	document.body.appendChild(script);
}

/**
 * Quick Profiler
 */
var QP = (function() {
	var DETAILS_SHOWN = false,  // start with details shown?
	    FULL_HEIGHT   = true, // whether to start with full height
	    START_TINY    = false,  // start as the tiny panel?
	    container; // container reference

	var self = {

		init : function() {
			container = $('#pqp-container');

			self.toggleDetails(DETAILS_SHOWN);
			if (DETAILS_SHOWN) {
				self.toggleHeight(FULL_HEIGHT);
			}
			if (START_TINY) {
				self.hide();
			}
			container.css('display', 'block');
		},

		/**
		 * Hides/shows the metrics details
		 */
		toggleDetails : function(show) {
			var show = (typeof show === 'undefined') ? !DETAILS_SHOWN : show;

			if (show) {
				container.removeClass('hideDetails');
				DETAILS_SHOWN = true;
			} else {
				container.addClass('hideDetails');
				DETAILS_SHOWN = false;
			}
		},

		/**
		 * Toggles height of details between normal and large
		 */
		toggleHeight : function(expand) {
			var expand = (typeof expand === 'undefined') ? !FULL_HEIGHT : expand;
			
			if (!DETAILS_SHOWN) {
				self.toggleDetails(true);
			}
			if (expand) {
				container.addClass('tallDetails');
				FULL_HEIGHT = true;
			} else {
				container.removeClass('tallDetails');
				FULL_HEIGHT = false;
			}
		},

		/**
		 * Change from one metric to another.
		 */
		changeTab : function(tab, selectedItem) {
			$.each(container.find('.pqp-box, #pqp-metrics td'), function(i, el) {
				if (el.id === 'pqp-' + tab) {
					$(el).addClass('selected');
				} else {
					$(el).removeClass('selected');
				}
			});
			$(selectedItem).addClass('selected');

			self.toggleDetails(true);
		},

		show : function() {
			container.removeClass('tiny');
		},

		hide : function() {
			container.addClass('tiny');
		},
	};

	return self;
})();
if (typeof jQuery !== 'undefined') {
	$(document).ready(QP.init);
}