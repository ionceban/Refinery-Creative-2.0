Refinery.Router.DefaultUrls = Backbone.Router.extend({
	
	live_requests: {
		section: null,
		filter: null
	},
	
	current_section: null,
	
	views: {
		sections: 			null,
		section_filter: 	null,
		section_project: 	null,
		filter_menu: 		null,
		header: 			null
	},
	
	/**
	 * the main init constructor
	 */
	initialize: function() {
		_.extend(this, Backbone.Events);
		
		// Un-comment this in production
		this._hideSplashpage({
			after: function() {
				setTimeout(function() {
					Backbone.history.start();
				}, 200);
			}
		});
	},
	
	/**
	 * index action
	 */
	index: function() {
		$('body, html').animate({scrollTop: 0});
		
		// clears the "web" sections
		this._clearWebSections();
		
		// in here there is a DAMN problem
		//Sectionize.reset();
		
		// abort section content render
		this._abortSectionRender();
		
		// aborts the filter load
		this._abortFilterRender();
		
		// closes the filter menu tab
		if(this.views.filter_menu)
			this.views.filter_menu.hideFilterTab();
			
		// Resets the sections view	
		if(this.views.sections)	
			this.views.sections.resetSection();
	},
	
	/**
	 * Show main website sections
	 * MAJOR PROBLEMS HERE
	 */
	viewWebsiteMains: function(subsection) {
		// Goes to the specific websection
		Sectionize.reset(function() {
			webSections.reactTo(subsection);
		});
		
		// clears the "web" sections
		this._clearWebSections();
		
		// abort section content render
		this._abortSectionRender();
		
		// aborts the filter load
		this._abortFilterRender();
		
		// closes the filter menu tab
		if(this.views.filter_menu)
			this.views.filter_menu.hideFilterTab();
			
		// Resets the sections view	
		if(this.views.sections)	
			this.views.sections.resetSection();
	},
	
	/**
	 * show section action
	 * Shows only the section without the filters
	 */
	showSection: function(section) {
		// clears the "web" sections
		this._clearWebSections();
		
		// Render the actual sections - slides and section filters
		if(!this.views.sections) {
			this.views.sections = new Refinery.View.Sections();
		}
		this.views.sections.render(section);
		
		// Render the filter menu
		if(!this.views.filter_menu) {
			this.views.filter_menu = new Refinery.View.FilterMenu({ parent: this });
		}
		// render the menu
		this.views.filter_menu.render(section);
	},
	
	/**
	 * Filter section action
	 * Show the section along with the filters
	 */
	showFilteredSection: function(section, params) {
		// clears the "web" sections
		this._clearWebSections();
		
		// Render the actual sections - slides and section filters
		if(!this.views.sections) {
			this.views.sections = new Refinery.View.Sections();
		}
		this.views.sections.render(section, params);
		
		// Render the filter menu
		if(!this.views.filter_menu) {
			this.views.filter_menu = new Refinery.View.FilterMenu({ parent: this });
		}
		// render the menu
		this.views.filter_menu.render(section, params);
	},
	
	/**
	 * Loads the filter tab content 
	 */
	loadFilterContent: function(section, callback) {
		if(this.live_requests.filter) {
			this._abortFilterRender();
		}
		
		var self = this;
		this.live_requests.filter = $.ajax({
			url: Refinery.Config.feed_url.filter_content,
			type: 'POST',
			data: { category: section },
			success: function(data) {
				callback.call(self.views.filter_menu, data);
			}
		});
	},
	
	/**
	 * This should load the section content
	 * based on a section and category
	 */
	loadSectionContent: function(section, filters, callback) {
		if(this.live_requests.section) {
			this._abortSectionRender();
		}
		
		var self = this;
		this.live_requests.section = $.ajax({
			url: Refinery.Config.feed_url.section_content,
			type: 'POST',
			beforeSend: function() {
				$('#loader-bar').show();
			},
			data: {
				category: section,
				discipline: filters.discipline.join('_'),
				deliverable: filters.deliverable.join('_'),
				keywords: filters.keywords.join('_'),
				year: filters.year.join('_')
			},
			success: function(data) {
				$('#loader-bar').hide();
				callback.call(self.views.filter_menu, data);
			}
		});
	},
	
	/**
	 * Aborts the render of a section
	 */
	_abortSectionRender: function() {
		if(!this.live_requests.section) return;
		this.live_requests.section.abort();
		this.live_requests.section = null;
	},
	
	/**
	 * Aborts the render of the filter menu
	 */
	_abortFilterRender: function() {
		if(!this.live_requests.filter) return;
		this.live_requests.filter.abort();
		this.live_requests.filter= null;
	},
	
	/**
	 * Hides the splash page(intro)
	 */
	_hideSplashpage: function(options) {
		this._clearWebSections();
		if(options && options.when == 'now') {
			$('#main-logo').hide();
			$('#header').css({ 'top': 0 })
			return;
		}
		
		if($('#main-logo').css('display') == 'none') {
			return;
		}
		
		var timeout = setTimeout(function() {
			$('#content-wrapper').css({ 'display': 'none', 'opacity': 0 });
			
			$('#main-logo').fadeOut(500, function() {
				$('#push-content').css({'min-height': 0});
				$('#push-content').animate({
					'height': 0
				},
				{
					//easing: 'easeInOutExpo',
					//easing: 'linear',
					//easing: 'easeInSine',
					easing: 'easeInOutQuint',
					//easing: 'easeOutBack',
					duration: 450,

					complete: function() {
						$('#container').css({ 'padding-top': 64, 'height': 'auto' });
						$('#header').css({ 'position': 'fixed', 'top': 0 });
						
						$('#content-wrapper').css({ 'display': 'block' }).animate({ 'opacity': 1 }, 300, function() {
							if(options && options.after)
								options.after.call(options.after);
						});
						$('#push-content').remove();
					}
				});
			});
		}, 700)
	},
	
	/**
	 * Scroll to top
	 */
	_clearWebSections: function() {
		$('#loader-bar').hide();
		$('#top-bar').find('a').removeClass('active');
	}
	
});
