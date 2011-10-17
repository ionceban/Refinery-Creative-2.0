/**
 * Section menu
 * - toggles the main items from #work section
 */
var Sectionize = (function() {
	var winGutter = (navigator.appVersion.indexOf("Win") != -1 || navigator.appVersion.indexOf("win") != -1) ? 76 : 90;
	var defaults = {
		topGutter: winGutter
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
		$(elem).next('.subcontent-active').fadeOut(500, function() {
			$(this).attr('class', 'subcontent').find('.section-dynamic-content').empty();
		})
		currentElement = null;
		scrollTo(0);
		window.location = '#!/';
	}
	
	/**
	 * closes a section and opens a new one
	 */
	var openSection = function(elem) {
		if(currentElement) {
			$(currentElement).next('.subcontent-active').fadeOut(250, function() {
				$(this).attr('class', 'subcontent').animate(
					{'opacity': 0},
					{
						easing: 'easeInOutExpo',
						duration: 750,
						complete: function() {
							$(this).find('.section-dynamic-content').empty();
							scrollTo(getOffset(elem));
						}
					}
				);
			})
		} else {
			scrollTo(getOffset(elem));
		}
		
		currentElement = elem;
	}
	
	/**
	 * justGoToSection
	 */
	var justGoToSection = function(elem) {
		if(elem == 0) {
			$('body, html').animate({ scrollTop: 0});
			return false;
		}
		scrollTo(getOffset(elem));
	}
	
	var scrollToElement = function(elem) {
		var elemOffset = getOffset(elem);
		openSection(elem);
	}
	
	// Public
	return {
		toggle: function(elem, _options) {
			scrollToElement(elem);
		},
		
		goToSection: function(elem) {
			justGoToSection(elem);
		},
		
		reset: function(elem) {
			cl('Sectionize.reset()')
			resetScroll(elem);
		},
		
		closeAll: function(cb) {
			if(currentElement == null) {
				if(cb && typeof(cb) !== 'undefined') {
					cb.call(this);
				}
				return false;
			}
			
			$('#dummy-work-inner')
			.find('.subcontent-active')
			.hide()
			.attr('class', 'subcontent')
			.find('.section-dynamic-content')
			.empty().after(function() {
				if(cb) {
					scrollTo(0, function() {
						cb.call(this);
					});
				}
			});
			currentElement = null;
		}
	}
})();