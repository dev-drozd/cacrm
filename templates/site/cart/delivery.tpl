<div class="ctnr">
	<div class="page checkout">
		<h1>proceed to Checkout</h1>
		<div class="checkout-nav">
			<a href="#" onclick="return false;">Registration</a>
			<a href="#" class="active" onclick="return false;">Delivery info</a>
			<a href="#" onclick="return false;">Payment</a>
		</div>

		<div class="checkout-delivery">
			<form onsubmit="cart.sendDelivery(this, event, {id});">
				<div class="input-group">
					<label>Zipcode</label>
					<input type="text" name="zipcode" oninput="checkState(this.value);" onkeyup="checkState(this.value);">
				</div>
				<div class="input-group" id="country">
					<label>Country</label>
					<input type="hidden" name="country">
					<ul class="hdn"></ul>
				</div>
				<div class="input-group" id="state" style="display: none;">
					<label>State</label>
					<input type="hidden" name="state">
					<ul class="hdn"></ul>
				</div>
				<div class="input-group" id="city" style="display: none;">
					<label>City</label>
					<input type="hidden" name="city">
					<ul class="hdn"></ul>
				</div>
				<div class="input-group">
					<label>Address</label>
					<textarea name="address"></textarea>
				</div>
				<button class="btn">Continue</button>
			</form>
		</div>
	</div>
</div>

<script>
window.pendingZip = false;
window.checkState = function(a){
	clearTimeout(window.pendingZip);
	window.pendingZip = setTimeout(function() {
		$('input[name="country"]').removeData().next().remove();
		$('input[name="country"]').after($('<ul/>'));
		$('input[name="state"]').removeData().next().remove();
		$('input[name="state"]').after($('<ul/>'));
		$('input[name="city"]').removeData().next().remove();
		$('input[name="city"]').after($('<ul/>'));
		if (a.length) {
			$.getJSON('/geo/zipcode/'+a, function(j){
				if(!$.isEmptyObject(j)){
					getCoutries(j.country);
					getStates(j.country, j.state, 1);
					getCities(j.state, a, 1, 1);
				} else {
					getCoutries();
				}
			});
		} else {
			getCoutries();
		}
	}, 200);
};
	
function getCoutries(code) {
	$.get('/geo/countries', function (r) {
		if (r){
			var items = '', lId = 0;
			$.each(r, function(i, v) {
				if(code && i == code){
					var f = {};
					f[i] = {name: v};
					$('input[name="country"]').data(f);
				}
				items += '<li data-value="' + i + '">' + v + '</li>';
			});
			$('#country > ul').html(items).sForm({
				action: '/geo/countries',
				data: {
					nIds: Object.keys($('input[name="country"]').data() || {}).join(','),
					query: $('#country > .sfWrap input').val()
				},
				all: true,
				select: $('input[name="country"]').data(),
				s: true
			}, $('input[name="country"]'), getStates);
		}
	}, 'json');
}

function getCities(c, zcode, f, v) {
	if ($('input[name="state"]').data()) {
		$('#city').show();
		$.get('/geo/cities/' + (c || Object.keys($('input[name="state"]').data()).join(',')) + (v ? '/' + zcode : ''), function (r) {
			if (r){
				var items = '', lId = 0;
				$.each(r, function(i, v) {
					if(zcode && i == zcode){
						var f = {};
						f[i] = {name: v};
						$('input[name="city"]').data(f);
					}
					items += '<li data-value="' + i + '">' + v + '</li>';
				});
				$('#city > ul').html(items).sForm({
					action: '/geo/cities/' + Object.keys($('input[name="state"]').data()).join(',') + (v ? '/' + zcode : ''),
					data: {
						nIds: Object.keys($('input[name="city"]').data()).join(','),
						query: $('#city > .sfWrap input').val()
					},
					all: true,
					select: $('input[name="city"]').data(),
					s: true
				}, $('input[name="city"]'), function() {
					$('input[name="zipcode"]').val(Object.keys($('input[name="city"]').data()).join(','));
				});
			}
		}, 'json');
	}
}

function getStates(c, code, f) {
	if ($('input[name="country"]').data()) {
		$('#state').show();
		$.get('/geo/states/' + (c || Object.keys($('input[name="country"]').data()).join(',')), function (r) {
			if (r){
				var items = '', lId = 0;
				$.each(r, function(i, v) {
					if(code && i == code){
						var f = {};
						f[i] = {name: v};
						$('input[name="state"]').data(f);
					}
					items += '<li data-value="' + i + '">' + v + '</li>';
				});
				$('#state > ul').html(items).sForm({
					action: '/geo/states/' + Object.keys($('input[name="country"]').data()).join(','),
					data: {
						nIds: Object.keys($('input[name="state"]').data()).join(','),
						query: $('#state > .sfWrap input').val()
					},
					all: true,
					select: $('input[name="state"]').data(),
					s: true
				}, $('input[name="state"]'), getCities);
			}
		}, 'json');
	}
}

getCoutries();
</script>