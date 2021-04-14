function junotoys_googlemap_init(dom_obj, coords) {
	"use strict";
	if (typeof JUNOTOYS_STORAGE['googlemap_init_obj'] == 'undefined') junotoys_googlemap_init_styles();
	JUNOTOYS_STORAGE['googlemap_init_obj'].geocoder = '';
	try {
		var id = dom_obj.id;
		JUNOTOYS_STORAGE['googlemap_init_obj'][id] = {
			dom: dom_obj,
			markers: coords.markers,
			geocoder_request: false,
			opt: {
				zoom: coords.zoom,
				center: null,
				scrollwheel: false,
				scaleControl: false,
				disableDefaultUI: false,
				panControl: true,
				zoomControl: true, //zoom
				mapTypeControl: false,
				streetViewControl: false,
				overviewMapControl: false,
				styles: JUNOTOYS_STORAGE['googlemap_styles'][coords.style ? coords.style : 'default'],
				mapTypeId: google.maps.MapTypeId.ROADMAP
			}
		};
		
		junotoys_googlemap_create(id);

	} catch (e) {
		
		dcl(JUNOTOYS_STORAGE['strings']['googlemap_not_avail']);

	};
}

function junotoys_googlemap_create(id) {
	"use strict";

	// Create map
	JUNOTOYS_STORAGE['googlemap_init_obj'][id].map = new google.maps.Map(JUNOTOYS_STORAGE['googlemap_init_obj'][id].dom, JUNOTOYS_STORAGE['googlemap_init_obj'][id].opt);

	// Add markers
	for (var i in JUNOTOYS_STORAGE['googlemap_init_obj'][id].markers)
		JUNOTOYS_STORAGE['googlemap_init_obj'][id].markers[i].inited = false;
	junotoys_googlemap_add_markers(id);
	
	// Add resize listener
	jQuery(window).resize(function() {
		if (JUNOTOYS_STORAGE['googlemap_init_obj'][id].map)
			JUNOTOYS_STORAGE['googlemap_init_obj'][id].map.setCenter(JUNOTOYS_STORAGE['googlemap_init_obj'][id].opt.center);
	});
}

function junotoys_googlemap_add_markers(id) {
	"use strict";
	for (var i in JUNOTOYS_STORAGE['googlemap_init_obj'][id].markers) {
		
		if (JUNOTOYS_STORAGE['googlemap_init_obj'][id].markers[i].inited) continue;
		
		if (JUNOTOYS_STORAGE['googlemap_init_obj'][id].markers[i].latlng == '') {
			
			if (JUNOTOYS_STORAGE['googlemap_init_obj'][id].geocoder_request!==false) continue;
			
			if (JUNOTOYS_STORAGE['googlemap_init_obj'].geocoder == '') JUNOTOYS_STORAGE['googlemap_init_obj'].geocoder = new google.maps.Geocoder();
			JUNOTOYS_STORAGE['googlemap_init_obj'][id].geocoder_request = i;
			JUNOTOYS_STORAGE['googlemap_init_obj'].geocoder.geocode({address: JUNOTOYS_STORAGE['googlemap_init_obj'][id].markers[i].address}, function(results, status) {
				"use strict";
				if (status == google.maps.GeocoderStatus.OK) {
					var idx = JUNOTOYS_STORAGE['googlemap_init_obj'][id].geocoder_request;
					if (results[0].geometry.location.lat && results[0].geometry.location.lng) {
						JUNOTOYS_STORAGE['googlemap_init_obj'][id].markers[idx].latlng = '' + results[0].geometry.location.lat() + ',' + results[0].geometry.location.lng();
					} else {
						JUNOTOYS_STORAGE['googlemap_init_obj'][id].markers[idx].latlng = results[0].geometry.location.toString().replace(/\(\)/g, '');
					}
					JUNOTOYS_STORAGE['googlemap_init_obj'][id].geocoder_request = false;
					setTimeout(function() { 
						junotoys_googlemap_add_markers(id); 
						}, 200);
				} else
					dcl(JUNOTOYS_STORAGE['strings']['geocode_error'] + ' ' + status);
			});
		
		} else {
			
			// Prepare marker object
			var latlngStr = JUNOTOYS_STORAGE['googlemap_init_obj'][id].markers[i].latlng.split(',');
			var markerInit = {
				map: JUNOTOYS_STORAGE['googlemap_init_obj'][id].map,
				position: new google.maps.LatLng(latlngStr[0], latlngStr[1]),
				clickable: JUNOTOYS_STORAGE['googlemap_init_obj'][id].markers[i].description!=''
			};
			if (JUNOTOYS_STORAGE['googlemap_init_obj'][id].markers[i].point) markerInit.icon = JUNOTOYS_STORAGE['googlemap_init_obj'][id].markers[i].point;
			if (JUNOTOYS_STORAGE['googlemap_init_obj'][id].markers[i].title) markerInit.title = JUNOTOYS_STORAGE['googlemap_init_obj'][id].markers[i].title;
			JUNOTOYS_STORAGE['googlemap_init_obj'][id].markers[i].marker = new google.maps.Marker(markerInit);
			
			// Set Map center
			if (JUNOTOYS_STORAGE['googlemap_init_obj'][id].opt.center == null) {
				JUNOTOYS_STORAGE['googlemap_init_obj'][id].opt.center = markerInit.position;
				JUNOTOYS_STORAGE['googlemap_init_obj'][id].map.setCenter(JUNOTOYS_STORAGE['googlemap_init_obj'][id].opt.center);				
			}
			
			// Add description window
			if (JUNOTOYS_STORAGE['googlemap_init_obj'][id].markers[i].description!='') {
				JUNOTOYS_STORAGE['googlemap_init_obj'][id].markers[i].infowindow = new google.maps.InfoWindow({
					content: JUNOTOYS_STORAGE['googlemap_init_obj'][id].markers[i].description
				});
				google.maps.event.addListener(JUNOTOYS_STORAGE['googlemap_init_obj'][id].markers[i].marker, "click", function(e) {
					var latlng = e.latLng.toString().replace("(", '').replace(")", "").replace(" ", "");
					for (var i in JUNOTOYS_STORAGE['googlemap_init_obj'][id].markers) {
						if (latlng == JUNOTOYS_STORAGE['googlemap_init_obj'][id].markers[i].latlng) {
							JUNOTOYS_STORAGE['googlemap_init_obj'][id].markers[i].infowindow.open(
								JUNOTOYS_STORAGE['googlemap_init_obj'][id].map,
								JUNOTOYS_STORAGE['googlemap_init_obj'][id].markers[i].marker
							);
							break;
						}
					}
				});
			}
			
			JUNOTOYS_STORAGE['googlemap_init_obj'][id].markers[i].inited = true;
		}
	}
}

function junotoys_googlemap_refresh() {
	"use strict";
	for (id in JUNOTOYS_STORAGE['googlemap_init_obj']) {
		junotoys_googlemap_create(id);
	}
}

function junotoys_googlemap_init_styles() {
	// Init Google map
	JUNOTOYS_STORAGE['googlemap_init_obj'] = {};
	JUNOTOYS_STORAGE['googlemap_styles'] = {
		'default': []
	};
	if (window.junotoys_theme_googlemap_styles!==undefined)
		JUNOTOYS_STORAGE['googlemap_styles'] = junotoys_theme_googlemap_styles(JUNOTOYS_STORAGE['googlemap_styles']);
}