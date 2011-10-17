/**
 * Application config
 *
 * @name Refinery website
 * @namespace Refinery
 */
var Refinery = {
	version: '0.1.0',
	currentSection: null
};


Refinery.Config = {
	routes: {
		"/":								"index",
		"/website/:section":					"viewWebsiteMains",
		"/:section":      						"showSection",
		"/:section/*params":      				"showFilteredSection"
	},
	
	feed_url: {
		section_content: 'backend/get_filtered_images.php',
		filter_content: 'backend/get_filter.php'
	}
};


Refinery.Router = {};


Refinery.Model = {};


Refinery.View = {};


Refinery.Filters = (function() {
	var filter_types = ['discipline', 'deliverables', 'keywords', 'year'];
	
	var current_filters = { type: null, values: [] };
	
	var params_regex = {
		discipline: 	/discipline\=[\w\d\-\s\%\/]+/i,
		keywords: 		/keywords\=[\w\d\-\s\%\/]+/i,
		deliverable:	/deliverable\=[\w\d\-\s\%\/]+/i,
		year:			/year\=[\w\d\-\s\%\/]+/i
	}
	
	
	return {
		encodeString: function(str) {
			if(str && typeof(str) == 'string' && str.length > 0)
				return encodeURIComponent(str);
			return str;
		},
		
		getParamsFor: function(filter_type, params) {
			if(!params) {
				return null;
			}
			
			if(params_regex[filter_type]) {
				var result = decodeURIComponent(params).match(params_regex[filter_type]);
				if(result && result[0]) {
					return result[0].replace(filter_type + '=', '').split('_');
				}
			}
			return null;
		},
		
		replaceSegmentFilter: function(filter_type, filters, params) {
			if(params_regex[filter_type]) {
				var tmp_filters = filter_type + '=' + filters.join('_').replace(/\//g, '%2F').replace(/\s/, '%20');
				var result;
				
				if(params) {
					result = params.replace(params_regex[filter_type], tmp_filters).replace(/\//g, '%2F');
				}
				
				// if this filter type is not set add it to the param string
				if(this.getParamsFor(filter_type, params) == null) {
					if(typeof(result) === 'undefined') {
						var suffix = 'filter?' + tmp_filters;
						result = '';
					} else {
						var suffix = '&' + tmp_filters;
					}
					result += suffix;
				}
				return result;
			}
			return null;
		}
	}
})();