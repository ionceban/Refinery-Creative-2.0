function cl(o) {
	try {
		console.log.apply(console, arguments);
	} catch(e) {}
}


/**
 * Scrollize object
 */
var Scrollize = (function() {
	var DEFAULT_OFFSET = 53;
	var CORRECTION = { DOWN: 0, UP: -30 }
	
	var current_tune = 0;
	var current_scroll = 0;
	
	var default_container = $('#dummy-stack');
	var stacks = [];
	
	var areas = {
		'work': 	'#work',
		'about': 	'#about',
		'careers':	'#careers',
		'contact':	'#contact'
	}
	
	var adjustTune = function() {
		current_tune = (DEFAULT_OFFSET + $(default_container).height())
	}
	
	var whereIsElem = function(_elem) {
		var $elem = $(_elem);
		var offset = (($elem.offset().top - current_tune) - current_scroll);
		var in_stack = stacks.indexOf(_elem);
		
		if(offset <= CORRECTION.DOWN && in_stack < 0) {
			stacks.push(_elem);
			return 'down';
		}
		if(offset > CORRECTION.UP && in_stack >= 0) {
			stacks.splice(in_stack, 1);
			return 'up';
		}
		//cl(offset)
	}
	
	var adjustElements = function(elem_state, current_element) {
		if(elem_state == 'down') {
			$(default_container).find('h1.'+'h_f_' + current_element).show();
		}
		if(elem_state == 'up') {
			$(default_container).find('h1.'+'h_f_' + current_element).hide();
		}
	}
	
	var monitor = function() {
		for(var item in areas) {
			adjustElements(whereIsElem(areas[item]), item);
		}
	}
	
	return {
		react: function(_current_scroll) {
			current_scroll = _current_scroll;
			adjustTune();
			monitor();
		}
	}
})();


var webSections = (function() {
	var current_section = null;
	
	var tunes = {
		'work': 50,
		'about': 86,
		'careers': 114,
		'contact': 142
	}
	
	return {
		reactTo: function(subsection, options) {
			if(options && options.before) {
				options.before.call(this)
			}
			$('#top-bar').find('a[rel="' + subsection + '"]').addClass('active');
			var scroll = $('#' + subsection).offset().top - tunes[subsection];
			
			$('html, body').animate({
				scrollTop: scroll
			},
			{
				duration: 1250,
				easing: 'easeInOutExpo',
				//easing: 'linear',
				//easing: 'easeInSine',
				//easing: 'easeInOutQuint',
				//easing: 'easeOutBack',
			});
			
			current_section = subsection;
		}
	}
})();


/**
 * If "some" conditions are satisfied then start the application
 * - hash
 * - history
 * - content
 * - splash page toggle
 */
var startApplication = function() {
	var application_urls = new Refinery.Router.DefaultUrls({ routes: Refinery.Config.routes });
	$('#dummy-stack').show();
	$('#content-wrapper').show();
	$('#header-inner').addClass('active');
}


/**
 * Images random loader
 */
var RandomLoader = (function() {
    var timer;
    var stack;
    
    var animateElement = function(element, cb) {
        $(element).animate({
            'opacity': 1
        }, 300);
    }
    
    var killInterval = function() {
        clearInterval(timer);
    }
    
    var removeFromStack = function(stack_elem) {
        stack.splice(stack_elem, 1);
    }
    
    var intervalLoader = function() {
        timer = setInterval(function() {
            var elem_index = Math.floor(Math.random() * stack.length) - 1;
            if(elem_index < 0) {
               elem_index = 0; 
            }
            var elem = stack[elem_index ];
            
            if(stack.length < 1) {
                killInterval();
                removeFromStack(elem_index);
                return false;
            }
            
            animateElement(elem);
            removeFromStack(elem_index);
        }, 200)
    }
    
    return {
        load: function(items_arr) {
            stack = items_arr;
            intervalLoader();
        }
    }
}());


/**
 * Dom ready
 */
$(function() {
	/*
		Scrollize
	*/
	$(document).scroll(function() {
		Scrollize.react($(window).scrollTop());
	});
	//Scrollize.react($(window).scrollTop());
	
	
	/*
		hide filter panel
	*/
	var filter_panel_width = $('#filter-panel').width() - 30;
	$('#filter-panel')//.show()
	.addClass('hidden')
	.css({
		'margin-left': '-' + filter_panel_width + 'px'
	});
	
	
	/*
		Thumbnails view
	*/
	var thumb_views = new Refinery.View.ThumbView();
	
	
	/*
		Slider
	*/
	$('#main-slider').bxSlider({
		displaySlideQty: 1,
		moveSlideQty: 1             
	});
	
	
	/*
		Set the container to the window's height
	*/
	$(window).resize(function() {
		if($('#push-content').length < 1) return;
		var wh = $(window).height();
		
		// always modify the container's height = window.height
		$('#container').css({'padding-top': 1}).height($(window).height());
		
		// if the container is smaller than 550 return
		if($('#container').height() > 550) {
			$('#push-content').css({ 'height': (wh - 61) + 'px' })
		}
	});
	$('#container').height($(window).height());
	$('#push-content').css({ 'height': ($(window).height() - 61) + 'px' });
	
	
	/*
		Start backbone.history if a link on the header has been click
	*/
	$('#top-bar').find('ul li a').click(function(){
		startApplication();
	});
	
	
	/*
		start Routers if there is a hash
	*/
	if(window.location.hash && window.location.hash.length > 0) {
		$('video').hide();
		$('#refinery-poster').show();
		startApplication();
	}
	
	
	/*
		Show site button handler
	*/
	$('#btn-showsite').click(function() {
		startApplication();
		window.location.hash = '#!/';
		return false;
	});
});