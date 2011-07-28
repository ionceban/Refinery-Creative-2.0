/**
 * Section menu
 * - toggles the main items from #work section
 */
var Sectionize = (function() {
	var DEFAULT_TUNE 	= 95;
	var container 		= $('#dummy-work-inner');
	
	var current_margin 	= 0;
	var current_elem 	= null;
	var current_slide 	= null;
	var is_animating 	= false;
	
	
	var getOffset = function() {
		var elem_offset = $(current_elem).parent().offset().top;
		return elem_offset - DEFAULT_TUNE;
	}
	
	var setCurrentSlide = function(elem) {
		current_slide = elem;
	}
	
	var closeCurrent = function(callback) {
		is_animating = true;
		
		$(container).animate({
			'margin-top': 0
		}, 250, function() {
			is_animating = false;
			window.location = '#!/'
			callback.call(callback);
		});
		
		current_margin = 0;
		setCurrentSlide(null);
	}
	
	var closeAndOpen = function(callback) {
		current_margin += getOffset();
		is_animating = true;
		
		$(container).animate({
			'margin-top': '-' + current_margin + 'px'
		}, 250, function() {
			is_animating = false;
			callback.call(callback);
		});
		
		setCurrentSlide(current_elem);
	}
	
	var moveTo = function(elem) {
		current_elem = elem;
		if(current_elem == current_slide) {
			closeCurrent(function() {
				Callbacks.after({ elem: current_elem, state: 'closed' })
			});
		} else {
			closeAndOpen(function() {
				Callbacks.after({ elem: current_elem, state: 'open' })
			});
		}
	}
	
	var resetSections = function(cb) {
		setCurrentSlide(null);
		current_margin = 0;
		
		$(container).animate({
			'margin-top': 0
		}, 250, function() {
			is_animating = false
			closeAllSections();
			if(cb) {
				cb.call(this);
			}
			closeAllSubcategories();
		});
	}
	
	/**
	 * Closes all the sections left
	 */
	var closeAllSections = function() {
		$('#dummy-work-inner').find('.work-section a.active').each(function() {
			$(this).removeClass('active');
		});
	}
	
	/**
	 * Closes all the sections categories left displayed
	 */ 
	var closeAllSubcategories = function() {
		$('#dummy-work-inner').find('.subcontent-active').attr('class', 'subcontent')
		$('#dummy-work-inner').find('.subcontent').css({'opacity': 0})
	}
	
	/**
	 * Toggles the sections visibility
	 */
	var toggleSections = function(section, state) {
		closeAllSections();
		
		if(state == 'open') {
			$(section).addClass('active');
			toggleCategory($(section).next(), 'open');
		} else {
			$(section).removeClass('active');
			toggleCategory($(section).next(), 'close');
		}
	}
	
	/**
	 * Toggles sections categories visibility
	 */
	var toggleCategory = function(category, state) {
		// TEMP
		return;
		var elem_class = (state == 'open') ? 'subcontent-active' : 'subcontent';
		$(category).attr('class', elem_class);
	}
	
	
	/**
	 * Callbacks
	 */
	var Callbacks = {
		before: function() {
			closeAllSubcategories();
			if(Options.before)
				Options.before.call();
		},
		
		after: function(options) {
			toggleSections(options.elem, options.state);
			if(Options.after) {
				Options.after.call(Options.after, options.elem, options.state);
			}
		},
		
		clear: function() {
			Options.before = null;
			Options.after = null;
		}
	}
	
	/**
	 * The main options object
	 * contains only(for the moment) the callbacks
	 */
	var Options = {
		before: null,
		after: null
	};
	
	/**
	 * Public interface
	 */
	return {
		toggle: function(elem, _options) {
			jQuery.extend(Options, _options);
			Callbacks.before();
			moveTo(elem);
		},
		
		reset: function(callback) {
			resetSections(callback);
		},
		
		isAnimating: function() {
			return is_animating;
		}
	}
})();

