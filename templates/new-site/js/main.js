$.getJSON('/language/'+lang_code+'/site/javascript.json', function(r){
	window.lang = r;
});

function nearStore(e) {
	var addBounds = new google.maps.LatLngBounds();
	for (n in locations) {
		if(locations[n].address.substr(locations[n].address.length -5) == e.value){
			addBounds.extend(locations[n]);
			console.log(n,locations[n].address);
			locations[n].marker.infowindow.open(map, locations[n].marker);
			map.fitBounds(addBounds);
			if(map.zoom > 19)
				map.setZoom(19);
			if(window.scrollY < 200)
				smoothScroll('map', 300);
		}
	}
	e.value = '';
}

function rad(x) {
	return x*Math.PI/180;
}
function find_closest_marker(lat, lng) {
	if (lat && lng) {
		var R = 6371; // radius of earth in km
		var distances = [];
		var closest = -1;
		for( i=0;i<locations.length; i++ ) {
			var mlat = locations[i]['lat'];
			var mlng = locations[i]['lng'];
			var dLat  = rad(mlat - lat);
			var dLong = rad(mlng - lng);
			var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
				Math.cos(rad(lat)) * Math.cos(rad(lat)) * Math.sin(dLong/2) * Math.sin(dLong/2);
			var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
			var d = R * c;
			distances[i] = d;
			if ( closest == -1 || d < distances[closest] ) {
				closest = i;
			}
		}

		$('.header-tel > a').attr('href','tel:'+locations[closest].phone).text('+1 '+locations[closest].phone)
		//$('.neare_store').html(locations[closest].name);
		locations.unshift(...locations.splice(closest,1));
		window.map_id = locations[closest].map_id;
		console.log('ok');
	}
}

if (navigator.geolocation) {
	navigator.geolocation.getCurrentPosition(function(p) {
			find_closest_marker(p.coords.latitude, p.coords.longitude);
		},
		function(e){}
	);
}

function initMap() {

	window.bounds = new google.maps.LatLngBounds();
	window.geocoder = new google.maps.Geocoder();

    var styles = [
        {
            "featureType": "administrative",
            "elementType": "labels.text.fill",
            "stylers": [
                {
                    "color": "#444444"
                }
            ]
        },
        {
            "featureType": "landscape",
            "elementType": "all",
            "stylers": [
                {
                    "color": "#f2f2f2"
                }
            ]
        },
        {
            "featureType": "poi",
            "elementType": "all",
            "stylers": [
                {
                    "visibility": "off"
                }
            ]
        },
        {
            "featureType": "road",
            "elementType": "all",
            "stylers": [
                {
                    "saturation": -100
                },
                {
                    "lightness": 45
                }
            ]
        },
        {
            "featureType": "road.highway",
            "elementType": "all",
            "stylers": [
                {
                    "visibility": "simplified"
                }
            ]
        },
        {
            "featureType": "road.arterial",
            "elementType": "labels.icon",
            "stylers": [
                {
                    "visibility": "off"
                }
            ]
        },
        {
            "featureType": "transit",
            "elementType": "all",
            "stylers": [
                {
                    "visibility": "off"
                }
            ]
        },
        {
            "featureType": "water",
            "elementType": "all",
            "stylers": [
                {
                    "color": "#90ceff"
                },
                {
                    "visibility": "on"
                }
            ]
        }
    ];


    var element = document.getElementById('map');
	
	if(element){
		var options = {
			zoom: 16,
			center: locations[0],
			styles: styles
		};

		window.map = new google.maps.Map(element, options);

		for(var i=0; i< locations.length; i++){
		
			locations[i].marker = new google.maps.Marker({
				position: locations[i],
				icon: '/templates/new-site/img/map.svg',
				map: map,
				infowindow: new google.maps.InfoWindow({
					content: '\
					<div class="maps-mc">\
						<h3>'+locations[i].name+'</h3>\
						<p>'+locations[i].address+'</p>\
						<a href="tel:'+locations[i].phone+'">'+locations[i].phone+'</a>\
					</div>\
				'
				})
			});
			
			bounds.extend(locations[i].marker.position);
			google.maps.event.addListener(locations[i].marker, 'click', function (){
				for(let k in locations){
					locations[k].marker.infowindow.close();
				}
				this['infowindow'].open(map, this);
			});
		}
		
		if (locations.length > 1)
			map.fitBounds(bounds);
		else
			map.setZoom(16);
	}
}

// Page
var Page = {
    init: function(href) {
		//ga('send', 'pageview');
		if ($('#map').length)
			initMap();
    },
    get: function(href, back) {
        if (!back) {
            history.pushState({
                link: href
            }, null, href);
        }
        $.getJSON(href, function(r) {
            if (r == 'loggout') location.reload();
            document.title = r.title;
            $('#page').html(r.content);
            Page.init(href);
        });
    }
};