/**
 * @name Sections view
 * 
 *  @description
 * - toggle the sections
 * - loads the content of a section
 */
Refinery.View.Sections = Backbone.View.extend({
	
	filters_section: null,
	
	current_section: null,
	
	el: $('#work'),
	
	initialize: function() {
		_.extend(this, Backbone.Events);
	},
	
	events: {
		"click a.work-menu": 	"toggleSection",
	},
	
	render: function(section, params) {
		this.openSection(section, params);
		
		if(!this.filters_section) {
			this.filters_section = new Refinery.View.SectionsFilter();
		}
		this.filters_section.render(section, params);
	},
	
	openSection: function(section, params) {
		var $elem = this.el.find('#' + section + '-block').find('a.work-menu');
		if((params && !$elem.hasClass('active')) || !$elem.hasClass('active')) {
			$elem.click();
		}
	},
	
	toggleSection: function(evt) {
		var self = this;
		var el_section = $(evt.target).attr('href').replace('#!/', '');
		
		if(Sectionize.isAnimating()) {
			return false;
		}
		
		Sectionize.toggle(evt.target, {
			before: function() { },
			after: function(elem, state) { }
		});
		
		if(this.current_section == el_section) {
			this.current_section = null;
			return false;
		} else {
			console.log('should load bar for section ' + el_section);
			$.ajax({
				url: "backend/get_disciplines_bar.php",
				type: "POST",
				data: {category: el_section},
				success: function(data){
					$('#' + el_section + '-discbar').html(data);
				}
			});
		}
		
		this.current_section = el_section;
	},
	
	resetSection: function() {
		this.current_section = null;
	}
	
});


/**
 * @name Section's filter
 *
 * @description
 * - toggles the filters
 * - adds/removes location hash
 * - add filtered content
 */
Refinery.View.SectionsFilter = Backbone.View.extend({
	
	filters: [],
	
	section: null,
	
	current_params: null,
	
	el: $('#work-inner').find('.sub-menu'),
	
	initialize: function() {
		//cl('initialize :: section filter')
		_.extend(this, Backbone.Events);
	},
	
	events: {
		'click li a': 'selectFilter'
	},
	
	render: function(section, params) {
		this.section = section;
		this.current_params = params;
		this.setFilterFromParams(params);
		//cl(':: fetching content for ', 'section: ', this.section, ', filters: ', this.filters);
	},
	
	/**
	 * Actual item click event
	 */
	selectFilter: function(evt) {
		evt.preventDefault();
		this.elementFilterize($(evt.target).attr('rel'));
		this.changeLocation();
	},
	
	/**
	 * Selects the params and changes the location accoddingly
	 */
	setFilterFromParams: function(params) {
		if(!params) {
			this.filters = ['show-all'];
			this.selectFilterTags();
			return;
		}
		
		// Reads the filters from the #URL
		this.filters = Refinery.Filters.getParamsFor('discipline', params);
		if(this.filters == null) {
			this.filters = ['show-all'];
		}
		
		// reset the filters if there are more than one filter and it contains 'show-all'
		if(this.filters.length > 1 && this.filters.indexOf('show-all') >= 0) {
			this.filters = ['show-all'];
			this.changeLocation();
		}
		
		this.selectFilterTags();
	},
	
	/**
	 * Changes the location hash
	 */
	changeLocation: function() {
		var filters = Refinery.Filters.replaceSegmentFilter('discipline', this.filters, this.current_params);
		var prefix = '#!/' + this.section + '/';
		window.location = prefix + filters;
	},
	
	/**
	 * Selects/add classes to selected filter elements
	 */
	selectFilterTags: function() {
		var section_elem = $('#' + this.section + '-block');
		$(section_elem).find('.sub-menu a').removeClass('sub-active');
		for(var item in this.filters) {
			$(section_elem).find('.sub-menu a[rel="' + this.filters[item] + '"]').addClass('sub-active');
		}
	},
	
	
	/**
	 * Adds/Removes filters based on the clicked element
	 */
	elementFilterize: function(elem_filter) {
		if(elem_filter == 'show-all') {
			this.filters = [];
			this.filters.push('show-all');
		} else {
			if(this.isShowAll() >= 0) {
				this.filters.splice(this.isShowAll(), 1);
			}
			if(this.filters.indexOf(elem_filter) >= 0) {
				this.filters.splice(this.filters.indexOf(elem_filter), 1);
			} else {
				this.filters.push(elem_filter);
			}
			if(this.filters.length == 0) {
				this.filters.push('show-all');
			}
		}
	},
	
	/**
	 * Returns the index of 'show-all' string in the filters
	 */
	isShowAll: function() {
		var elem_index = this.filters.indexOf('show-all');
		return elem_index;
	}
	
});



/**
 * @name Filter tab manu
 * 
 * @description
 * - toggles the filter menu visibility
 * - loads filter content(filter types)
 */
Refinery.View.FilterMenu = Backbone.View.extend({
	
	current_section: null,
	current_params: null,
	cache: {},
	
	filters: {
		'discipline': [],
		'deliverable': [],
		'keywords': [],
		'year': []
	},
	
	el: $('#filter-panel'),
	
	/**
	 * Filter anchor events
	 */
	events: {
		'click #filter-trigger': 				'togglePanel',
		'click .discipline a': 					'filterDiscipline',
		'click .deliverable a': 				'filterDeliverable',
		'click .keywords a': 					'filterKeywords',
		'click .year a': 						'filterYear',
		'click #filter-header .header-right':	'clearFilters'
	},
	
	initialize: function() {
		cl('initialize:: filter tab');
		_.extend(this, Backbone.Events);
	},
	
	render: function(section, params) {
		this.current_params 	= params;
		this.current_section	= section;
		
		// preloads the filters
		this.setFilterFromParams();
		
		// Ajax load filter content
		this.loadFilter();
		
		//this.showFilterTab();
		this.loadSectionContent();
	},
	
	/**
	 * Loads the filter content
	 * either from AJAX or cache
	 */
	loadFilter: function() {
		var filter_cache_name = this.current_section + '-filter-contents'
		if(this.cache[filter_cache_name]) {
			this.el.find('#filter-content > div').hide();
			var $filter_elem = this.el.find('#filter-content').find('.' + filter_cache_name).show();
			this.displayFilter();
		} else {
			this.hideFilterTab();
			this.options.parent.loadFilterContent(this.current_section, function(content) {
				this.displayFilter(content);
			});
		}
	},
	
	/**
	 * Displays the filter content
	 */
	displayFilter: function(filter_content) {
		if(filter_content) {
			this.addFilterCacheElement(filter_content);
		}
		this.showFilterTab();
		this.setFilterFromParams();
	},
	
	/**
	 * Adds the filter cache element to the filters container
	 */
	addFilterCacheElement: function(content) {
		var cdummy = document.createElement('div');
		var cache_name = this.current_section + '-filter-contents'
		
		// add it to cache if not already defined
		if(this.cache[cache_name]) {
			return;
		}
		
		this.el.find('#filter-content').append(cdummy);
		$(cdummy).html(content);
		$(cdummy).addClass(cache_name);
		this.cache[cache_name] = true;
	},
	
	/**
	 * Loads the section content -  this is the filtered one
	 * should be named displayContent - it's content is already loaded so...
	 */
	loadSectionContent: function() {
		var self = this;
		var $section = $('#' + this.current_section + '-block');
		
		this.options.parent.loadSectionContent(this.current_section, this.filters, function(content) {
			$section.find('.section-dynamic-content').html(content)
			$section.find('.subcontent').attr('class', 'subcontent-active');
			$('body, html').animate({scrollTop: 0}, function() {
				self.animateContent($section)
			});
		});
	},
	
	/**
	 * Selects the params and changes the location accoddingly
	 */
	setFilterFromParams: function() {
		var filter_types = ['discipline', 'deliverable', 'keywords', 'year'];
		
		for(var i = 0; i < filter_types.length; i++) {
			//cl('for:: ', filter_types[i], ' ', Refinery.Filters.getParamsFor(filter_types[i], this.current_params))
			var filters = Refinery.Filters.getParamsFor(filter_types[i], this.current_params);
			this.selectFilterTags(filter_types[i], filters);
			
			// if there are filters, set the this.filters[filter_type] array
			if(filters) {
				this.filters[filter_types[i]] = filters;
			} else {// else insert just this.filters[filter_type] = ['show-all']
				this.filters[filter_types[i]] = ['show-all'];
			}
		}
	},
	
	/**
	 * Selects/add classes to selected filter elements
	 */
	selectFilterTags: function(by_filter_type, elements) {
		$('#filter-content').find('.' + by_filter_type + ' a').removeClass('featured');
		for(var i in elements) {
			var $elem = $('#filter-content').find('.' + by_filter_type + ' a[rel="' + elements[i] + '"]');
			$elem.addClass('featured');
		}
	},
	
	/**
	 * Filter by discipline
	 */
	filterDiscipline: function(evt) {
		this.elementFilterize('discipline', $(evt.target).attr('rel'))
		this.changeLocation('discipline');
		return false;
	},
	
	/**
	 * Filter by deliverable
	 */
	filterDeliverable: function(evt) {
		this.elementFilterize('deliverable', $(evt.target).attr('rel'))
		this.changeLocation('deliverable');
		return false;
	},
	
	/**
	 * Filter by keywords
	 */
	filterKeywords: function(evt) {
		this.elementFilterize('keywords', $(evt.target).attr('rel'))
		this.changeLocation('keywords');
		return false;
	},
	
	/**
	 * Filter by year
	 */
	filterYear: function(evt) {
		this.elementFilterize('year', $(evt.target).attr('rel'))
		this.changeLocation('year');
		return false;
	},
	
	/**
	 * Clears all the filters
	 */
	clearFilters: function() {
		var filter_types = ['discipline', 'deliverable', 'keywords', 'year'];
		
		for(var i = 0; i < filter_types.length; i++) {
			this.filters[filter_types[i]] = null;
		}
		
		this.changeLocation();
	},
	
	/**
	 * Adds/Removes filters based on the clicked element
	 */
	elementFilterize: function(filter_type, elem_filter) {
		var current_filter = this.filters[filter_type];
		var filter_value = elem_filter//Refinery.Filters.encodeString(elem_filter);
		
		if(filter_value == 'show-all') {
			current_filter.splice(0);
			current_filter.push('show-all');
		} else {
			if(this.isShowAll(filter_type) >= 0)
				current_filter.splice(this.isShowAll(filter_type), 1);
				
			if(current_filter.indexOf(filter_value) >= 0)
				current_filter.splice(current_filter.indexOf(filter_value), 1);
			else
				current_filter.push(filter_value);
				
			if(current_filter.length == 0)
				current_filter.push('show-all');
		}
		this.filters[filter_type] = current_filter;
		//cl(this.filters[filter_type])
	},
	
	/**
	 * Changes the location hash
	 */
	changeLocation: function(filter_type) {
		var url = '#!/';
		
		if(!filter_type || typeof(filter_type) === 'undefined') {
			url += this.current_section;
		} else {
			var filters = Refinery.Filters.replaceSegmentFilter(filter_type, this.filters[filter_type], this.current_params);
			url += this.current_section + '/' + filters;
		}
		
		window.location = url;
	},
	
	/**
	 * Returns the index of 'show-all' string in the filters
	 */
	isShowAll: function(filter_type) {
		var elem_index = this.filters[filter_type].indexOf('show-all');
		return elem_index;
	},
	
	/**
	 * Toggles panel visibility
	 */
	togglePanel: function() {
		var $elem = $(this.el); var width = $elem.width() - 30;
		if($elem.hasClass('hidden'))
			$elem.animate({ 'margin-left': 0 }, 200, 'linear').removeClass('hidden');
		else
			$elem.animate({ 'margin-left': '-' + width + 'px' }, 200, 'linear').addClass('hidden');
		return false;
	},
	
	/**
	 * Initial show of the filter tab
	 */
	showFilterTab: function() {
		this.el.fadeIn(600);//.find('.filter-title span').html(this.current_section);
	},
	
	/**
	 * Closes the filter tab
	 */
	hideFilterTab: function() {
		var filter_panel_width = $('#filter-panel').width() - 30;
		$('#filter-panel').addClass('hidden').fadeOut(500).css({
			'margin-left': '-' + filter_panel_width + 'px'
		});
		this.el.find('#filter-content > div').hide();
	},
	
	/**
	 * Animates the show/hide content process
	 */
	animateContent: function(section) {
		$(section).find('.subcontent-active').animate(
			{'opacity': 1},
			{
				easing: 'easeInOutQuint',
				duration: 1000,
				complete: function() {
					RandomLoader.load($(this).find('.section-dynamic-content img'));
				}
			}
		);
	}
	
});


/**
 * @name Thumb view and overlay
 *
 * @description
 * - opens the overlay
 * - alot of stuff to be done
 */
Refinery.View.ThumbView = Backbone.View.extend({
	
	el: $('#dummy-work-inner .section-dynamic-content, #search-wrap'),
	
	initialize: function() {
		_.extend(this, Backbone.Events);
	},
	
	events: {
		'click img': 'openThumb'
	},
	
	openThumb: function(evt) {
		evt.preventDefault();
		var image_id = $(evt.target).attr('class');
		this._getOverlayContent(image_id);
	},
	
	_getOverlayContent: function(image_id) {
		var self = this;
		$.ajax({
			url:  'backend/get_overlay.php',
			type: 'POST',
			data: {image_id: image_id },
			success: function(data) {
				//self._openOverlay();
				self._populateOverlay(data);
				self._openOverlay();
				self._VideoJS();
				self._overlayInitSlider();
			}
		});
	},
	
	_populateOverlay: function(overlay_content){
		var self = this;
		
		$('#overlay').html(overlay_content);
		self._adjustOverlayPosition();
		self._handleOverlayHovers();
		self._handleOverlayScrollbar();
		
		$('.overlay-block ul a img').click(function(evt){
			evt.preventDefault();
			var image_id = $(this).attr('class');
			$.ajax({
				url: "backend/get_overlay.php",
				cache: false,
				type: "POST",
				data: {image_id: image_id},
				success: function(data){
					self._populateOverlay(data);
					self._VideoJS();
					self._overlayInitSlider();
				}
			});
		});
		
		
	},
	
	_openOverlay: function() {
		$("#overlay").dialog({
			width: "100%",
			height: 'auto',
			draggable: false,
			resizable: false,
			position: ['left','top'] ,
			modal: true,
			buttons: {
				Ok: function() {
					$( this ).dialog( "close" );
					$('#overlay').html('');
				}
			},
			open: function() {
				// jquery ui dialog fix
				$('.ui-dialog').css({ 'top': 0 });
			}
		});
		
		this._adjustOverlayPosition();
		
		$('#close-button').click( function() {
			$( "#overlay" ).dialog('close');
		});
	},
	
	_handleOverlayScrollbar:function() {
		// initial overlay height
		$('#overlay-left').height($(window).height());
		
		var scroll;
		var timeout = setTimeout(function() {
			$('#overlay-left').jScrollPane({
				showArrows: true,
				verticalDragMaxHeight: 66,
				verticalDragMinHeight: 66,
				verticalGutter: 100,
				horizontalGutter: 10
			});
			$('#overlay-left').css('overflow', 'visible');
		}, 0)
		
		// reinitialize the scroll if the window is resizing
		$(window).resize(function() {
			$('#overlay-left').height($(window).height());
			$('#overlay-left').jScrollPane({
				showArrows: true,
				verticalDragMaxHeight: 66,
				verticalDragMinHeight: 66,
				verticalGutter: 10
			});
			$('#overlay-left').css('overflow', 'visible');
		});
	},
	
	_handleOverlayHovers: function(){
		$('.img-container img').mouseenter(function(){
			$(this).parent().find('.tooltip').css('display', 'block');
			$(this).parent().find('.tooltip').css('top', '-13px');
			$(this).css('opacity', '1');
		});
		
		$('.img-container img').mouseleave(function(){
			$(this).parent().find('.tooltip').css('display', 'none');
			$(this).parent().find('.tooltip').css('top', '-20px');
			if($(this).parents('#other-print').length < 1)
				$(this).css('opacity', '0.5');
		});
		
		$('.img-container .tooltip').mouseenter(function(event){
			$(this).css('display', 'block');
			$(this).css('top', '-13px');
			$(this).parent().find('img').css('opacity', '1');
			
		});
		
		$('.img-container .tooltip').mouseleave(function(event){
			$(this).css('display', 'none');
			$(this).css('top', '-20px');
			if($(this).parents('#other-print').length < 1)
				$(this).parent().find('img').css('opacity', '0.5');
		});
	},
	
	_adjustOverlayPosition: function(){
		var cHeight = $(window).height();
		var cWidth = parseInt($(window).width());
		var remainingWidth = cWidth - 1124;
		var marginLeft = parseInt(remainingWidth / 2);
		$('#slider-wrapper').css('margin-left', marginLeft + 'px');
	},
	
	_VideoJS: function(){
		$('#overlay video').VideoJS();
		//$('#overlay video')[0].player.play();
		/*$('.bx-next').click(function(){
			$('#overlay video').not(':first').each(function(){
				$(this)[0].player.pause();
			});
			return false;
		});

		$('.bx-prev').click(function(){
			$('#overlay video').not(':first').each(function(){
				$(this)[0].player.pause();
			});
			return false;
		});*/

	},

	_overlayInitSlider: function(){
		$('#main-slider').bxSlider({
			infiniteLoop: false,
			speed: 300,
			hideControlOnEnd: true,
			onAfterSlide: function(a,b,c){
				c.find('video').filter(':first').each(function(){
					$(this)[0].player.play();
				});
			}

		});
		$('.bx-next').click(function(){
			$('#overlay video').not(':first').each(function(){
				$(this)[0].player.pause();
			});
			return false;
		});

		$('.bx-prev').click(function(){
			$('#overlay video').not(':first').each(function(){
				$(this)[0].player.pause();
			});
			return false;
		});


		$('#main-slider li').filter(':first').css('visibility', 'hidden');
		//$('#overlay video')[1].player.play();
	}
	
})
