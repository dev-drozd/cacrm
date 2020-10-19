<div class="ctnr">

	<div class="category cart">
		<div class="paTitle">Shopping cart</div>
		<div class="dClear">
			<div class="cItems">
				<div class="cItem cHdr">
					<div class="ciPhoto">
						Photo
					</div>
					<div class="ciName">
						Name
					</div>
					<div class="ciQnt">
						Qty
					</div>
					<div class="ciPrice">
						Subtotal
					</div>
					<div class="ciDel"></div>
				</div>
				{items}
				<div class="cItem cHdr cPd">
					<div class="ciPhoto">
						Delivery
					</div>
					<div class="ciName">
						<select name="delivery" onchange="cart.changeDelivery();">
							{delivery}
						</select>
					</div>
					<div class="ciQnt"></div>
					<div class="ciPrice">
						<span id="del-cur">{d_currency}</span>
						<span id="del-price">{d_price}</span>
					</div>
					<div class="ciDel"></div>
				</div>
				<div class="cItem cHdr cPd">
					<div class="ciPhoto">
						Payment
					</div>
					<div class="ciName">
						<select name="payment" onchange="cart.changePayment();">
							{payment}
						</select>
					</div>
					<div class="ciQnt"></div>
					<div class="ciPrice"></div>
					<div class="ciDel"></div>
				</div>
				<div class="cItem aRight">
					<button class="btn btnClear" onclick="cart.clear();">Clear cart</button>
				</div>
			</div>
			
			
			<div class="ciItemTotal">
				<div>
					<div class="ciTotal">
						Subtotal:
					</div>
					<div class="ciTPrice">
						{scurrency}<span id="subtotal" data-total="{dsubtotal}">{subtotal}</span>
					</div>
				</div>
				<div>
					<div class="ciTotal">
						Tax:
					</div>
					<div class="ciTPrice">
						{scurrency}<span id="tax">{tax}</span>
					</div>
				</div>
				<div>
					<div class="ciTotal">
						Total:
					</div>
					<div class="ciTPrice">
						{scurrency}<span id="total" data-total="{dtotal}" data-currency="{currency}">{total}</span>
					</div>
				</div>	
			</div>
			<div class="dClear"></div>
			
			<h2>Make purchase</h2>
			[user]
				<form onsubmit="cart.send(this, event);">
					<div class="iGroup">
						<label>Zipcode</label>
						<input type="text" name="zipcode" oninput="checkState(this.value);" onkeyup="checkState(this.value);">
					</div>
					<div class="iGroup" id="country">
						<label>Country</label>
						<input type="hidden" name="country">
						<ul class="hdn"></ul>
					</div>
					<div class="iGroup" id="state" style="display: none;">
						<label>State</label>
						<input type="hidden" name="state">
						<ul class="hdn"></ul>
					</div>
					<div class="iGroup" id="city" style="display: none;">
						<label>City</label>
						<input type="hidden" name="city">
						<ul class="hdn"></ul>
					</div>
					<div class="iGroup">
						<label>Address</label>
						<textarea name="address"></textarea>
					</div>
					<div class="iGroup">
						<label>Note</label>
						<textarea name="note"></textarea>
					</div>
				
					<div class="orderBtn">
						<button type="submit" class="btn btnClear">Make purchase</button>
					</div>
				</form>
			[not-user]
				<div class="orderLogin">
					<a href="javascript:account.login();">Login</a> or <a href="javascript:account.registration();">register</a> to make order
				</div>
			[/user]
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