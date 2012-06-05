var QP = (function() {
	var DETAILS_SHOWN = false,
	    FULL_HEIGHT = false,
	    container, // container reference
	    pQp; // main DIV

	var self = {

		init : function() {
			container = $('#pqp-container');
			pQp = $('#pQp');

			window.setTimeout(function() { container.css('display', 'block'); }, 10);
			DETAILS_SHOWN = !container.hasClass('hideDetails');
		},

		toggleDetails : function(show) {
			var show = (typeof show === 'undefined') ? DETAILS_SHOWN : show;

			if (show) {
				container.addClass('hideDetails');
				DETAILS_SHOWN = false;
			} else {
				container.removeClass('hideDetails');
				DETAILS_SHOWN = true;
			}
		},

		toggleHeight : function(expand) {
			var expand = (typeof expand === 'undefined') ? !FULL_HEIGHT : expand;
			if (expand) {
				container.addClass('tallDetails');
				FULL_HEIGHT = true;
			} else {
				container.removeClass('tallDetails');
				FULL_HEIGHT = false;
			}
		},

		changeTab : function(tab, selectedItem) {
			$.each(pQp.find('.pqp-box, #pqp-metrics td'), function(i, el) {
				if (el.id === '#pqp-' + tab) {
					$(el).addClass('selected');
				} else {
					$(el).removeClass('selected');
				}
			});
			$(selectedItem).addClass('selected');
			$('#pqp-' + tab).addClass('selected');

			self.toggleDetails(false);
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
$(document).ready(QP.init);