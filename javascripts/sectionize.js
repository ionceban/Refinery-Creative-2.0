/**
 * Section menu
 * - toggles the main items from #work section
 */
var Sectionize = (function() {
	var defaults = {
		topGutter: 69
	}
	
	var currentElement = null;
	
	
	/**
	 * Get the offset of an elemenet
	 */
	var getOffset = function(elem) {
		if($(elem)) {
			return $(elem).offset().top - defaults.topGutter;
		}
		return null;
	}
	
	
	/**
	 * Moves the scroll to a desired element position
	 */
	var scrollTo = function(position, cb) {
		$('body, html').animate({ scrollTop: position }, {
			duration: 350,
			easing: 'easeInOutQuint',
			complete: function() {
				if(cb)
					cb.call(this);
			}
		});
	}
	
	/**
	 * reset the fucking scroll
	 */
	var resetScroll = function(elem) {
		closeSection(elem);
	}
	
	/**
	 * closes a section
	 */
	var closeSection = function(elem) {
		$(elem).next('.subcontent-active').hide().find('.section-dynamic-content').empty();
		currentElement = null;
		window.location = '#!/';
		scrollTo(0);
	}
	
	/**
	 * closes a section and opens a new one
	 */
	var openSection = function(elem) {
		if(currentElement) {
			$(currentElement).next('.subcontent-active').fadeOut(250).animate(
				{'opacity': 0},
				{
					easing: 'easeInOutExpo',
					duration: 500,
					complete: function() {
						$(this).find('.section-dynamic-content').empty();
						scrollTo(getOffset(elem));
					}
				}
			);
		} else {
			scrollTo(getOffset(elem));
		}
		
		currentElement = elem;
	}
	
	var scrollToElement = function(elem) {
		var elemOffset = getOffset(elem);
		//if(currentElement == elem && ($(currentElement).attr('href') == window.location.hash)) {
		//	closeSection(elem);
		//} else {
			openSection(elem);
		//}
	}
	
	// Public
	return {
		toggle: function(elem, _options) {
			scrollToElement(elem);
		},
		
		reset: function(elem) {
			resetScroll(elem);
		}
	}
})();

