<div id="map"></div>
<div class="ctnr contacts dClear">
	<div class="map-area">
		<div class="map-list">
			{objects}
		</div>
	</div>
    <div>
        <h2>Feedback</h2>
        <form onsubmit="sendFeedback(this, event)">
            <div class="iGroup">
                <label>Name</label>
                <input type="text" name="full_name" placeholder="Your name">
            </div>
            <div class="iGroup">
                <label>Email</label>
                <input type="email" name="email" placeholder="Yout email">
            </div>
            <div class="iGroup">
                <label>Message</label>
                <textarea name="message" placeholder="Message"></textarea>
            </div>
            <div class="sGroup">
                <button class="btn btnSubmit" type="submit">Send message</button>
            </div>
        </form>
    </div>
</div>

<script>
    var map = '',
        marker = '';
    function initMap(lat, lng){
        var myLatLng = {
            lat: parseFloat(lat),
            lng: parseFloat(lng)
        };

        map = new google.maps.Map(document.getElementById('map'), {
            center: myLatLng,
            scrollwheel: false,
            zoom: 16,
            mapTypeId: google.maps.MapTypeId['roadmap'] 
        });

        marker = new google.maps.Marker({
            position: myLatLng,
            map: map
        });
    }

    function setMarker(el) {
        $('.map-item.active').removeClass('active');
        $(el).addClass('active');
        marker.setPosition(new google.maps.LatLng($(el).attr('data-lat'),$(el).attr('data-lng')));
        map.setCenter(marker.getPosition());
    }

    $(document).ready(function() {
        $('.map-item').first().addClass('active');
        initMap($('.map-item.active').attr('data-lat'), $('.map-item.active').attr('data-lng'));
    })
</script>