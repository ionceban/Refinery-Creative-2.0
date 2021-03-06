var LiveSeek = (function() {
	var dummy_val = 		'Search...';
	var field_modified = 	false;
	var current_ajax =		false;
	var ajax_url = 			'backend/get_search_images.php';
	var input_field = 		$('#search-input');
	var search_wrap = 		$('#search-wrap');
	var overlay_state = 	0;
	
	/**
	 * openOverlay
	 * @description opens the overlay
	 */
	var openOverlay = function() {
		if(overlay_state == 0) {
			$(search_wrap).show();
			overlay_state = 1;
		}
		// toggles the filter tab's visibility
		toggleFiltertab('hide');
		
		// animates the page scroll to the search container
		scrollToContainer();
	}
	
	/**
	 * closeOverlay
	 * @description closes the overlay
	 */
	var closeOverlay = function() {
		$('#search-results').empty();
		$(search_wrap).hide();
		resetInput();
		overlay_state = 0;
		$(input_field).blur();
		// toggles the filter tab's visibility
		toggleFiltertab('show');
	}
	
	var fuckMe = function() {
		if(Refinery.currentSection != null) {
			var isWebSection = ['work', 'about', 'careers', 'contact'].indexOf(Refinery.currentSection);
			if(isWebSection != -1) {
				$('body, html').animate({ scrollTop: 0 }, {duraction: 350});
				window.location = '#!/';
			} else {
				Sectionize.goToSection($(Refinery.currentSection));
			}
		} else {
			Sectionize.goToSection(0);
		}
	}
	
	/**
	 * animateToContainer
	 * @description scroll the window to the container's x
	 */
	var scrollToContainer = function(where) {
		var winGutter = (navigator.appVersion.indexOf("Win") != -1 || navigator.appVersion.indexOf("win") != -1) ? 76 : 88;
		var alreadyScrolled = true,
			scrollValue = $('#search-wrap').offset().top - winGutter;
			
		if(alreadyScrolled) {
			$('body, html').animate({ scrollTop: scrollValue }, {
				duration: 350,
				easing: 'easeInOutQuint'
			});
		}
		alreadyScrolled = ($('#search-wrap').css('display') == 'block') ? false : true;
	}
	
	/**
	 * toggles the filter container visibility
	 */
	var toggleFiltertab = function(state) {
		var $filterTab = $('#filter-panel');
		var width = $filterTab.width();
		if(state == 'hide') {
			$filterTab.animate({ 'margin-left': '-' + width + 'px' }, 200, 'linear');
		} else {
			$filterTab.animate({ 'margin-left': '-' + (width - 30) + 'px' }, 200, 'linear');
		}
	}
	
	/**
	 * abortAjax
	 * @description aborts all the search ajax requests
	 */
	var abortAjax = function() {
		if(current_ajax) {
			current_ajax.abort();
		}
	}
	
	/**
	 * resetInput
	 * @description resets the inputs value to dummy_val
	 */
	var resetInput = function() {
		field_modified = false;
		abortAjax();
		$(input_field).attr('value', dummy_val);
	}
	
	/**
	 * addTriggers
	 * @description add defaul mouse bindings to the input field - focus, blur, keydown
	 */
	var addDefaultTriggers = function() {
		$(input_field).keyup(function(e) {
			var code = (e.keyCode ? e.keyCode : e.which);
			if(code == 27) {
				closeOverlay();
				fuckMe();
				return;
			}
			field_modified = true;
			openOverlay();
			performSearch($(this).val());
		});
		
		$(input_field).focus(function() {
			if($(input_field).val() == dummy_val && !field_modified) {
				$(input_field).attr('value', '');
			}
		});
		
		$(input_field).blur(function() {
			if($(input_field).val() == '') {
				field_modified = false;
				$(input_field).attr('value', dummy_val);
			}
		});
		
		$('#close-search-wrap').click(function(e) {
			e.preventDefault();
			abortAjax();
			fuckMe();
			closeOverlay();
		});
		
		$('#close-button').click(function(e) {
			e.preventDefault();
			abortAjax();
			fuckMe();
			closeOverlay();
			resetInput();
		});
	}
	
	/**
	 * doAjax
	 * @description does the actual ajax request
	 */
	var doAjax = function(cb) {
		abortAjax();
		
		current_ajax = $.ajax({
			url:  ajax_url,
			type: 'POST',
			data: { query_string: $('#search-input').val() },
			success: function(data) {
				if(cb && typeof(cb) === 'function') {
					cb.call(this, data);
				}
			}
		});
	}
	
	/**
	 * performSearch
	 * @description receives a query and initiates the ajax search
	 */
	var performSearch = function(query) {
		doAjax(function(data) {
			$(search_wrap).find('#search-results').html(data);
			animateContent();
		});
	}
	
	/**
	 * Animates the thumbnails
	 */
	var animateContent = function() {
		RandomLoader.load($(search_wrap).find('#search-results').find('img'));
	}
	
	
	// public
	return {
		init: function() {
			addDefaultTriggers();
		},
		
		destruct: function() {
			closeOverlay();
		}
	}
}());