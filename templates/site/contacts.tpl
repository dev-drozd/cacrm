<div class="page">
    <div id="map" style="margin-top: -60px"></div>
    <div class="ctnr contacts dClear">
        <div class="map-area">
            <div class="map-list">
                {objects}
            </div>
        </div>
        <div>
            <h2>Questions or Comments</h2>
            <form onsubmit="sendFeedback(this, event)">
                <div class="input-group fl-left w100 mr10">
                    <label>Name</label>
                    <input type="text" name="full_name" placeholder="Your name">
                </div>
                <div class="input-group fl-left w100">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="Yout email">
                </div>
				<div class="clear"></div>  
                <div class="input-group w100">
                    <label>Message</label>
                    <textarea name="message" placeholder="Message"></textarea>
                </div>
                <div class="submin-group">
                    <button class="btn btnSubmit" type="submit">Send message</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    /*var map = '',
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
    }*/

    function setMarker(el) {
        $('.map-item.active').removeClass('active');
        $(el).addClass('active');
        marker.setPosition(new google.maps.LatLng($(el).attr('data-lat'),$(el).attr('data-lng')));
        map.setCenter(marker.getPosition());
        map.setZoom(16);
    }

    $(document).ready(function() {
        $('.map-item').first().addClass('active');
        initMap($('.map-item.active').attr('data-lat'), $('.map-item.active').attr('data-lng'));
    })
</script>