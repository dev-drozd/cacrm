<div class="ctnr">
	<div class="page">
		<h1>Cart</h1>

		<div class="cart">
			<h2>Your items</h2>
			<div class="cart-products">
				{items}
			</div>

			<div class="flex">
				<div class="cart-delivery">
					<h2>Delivery</h2>
					<select name="delivery" onchange="cart.changeDelivery();">
						{delivery}
					</select>
				</div>
				<div class="cart-total">
					<h2>Total</h2>
					<span class="cart-total-price">{scurrency}<span id="total" data-total="{dtotal}" data-tax="{tax}" data-currency="{currency}">{total}</span></span>
					<button class="btn" onclick="[user]cart.send();[not-user]Page.get('/cart/login')[/user]">Proceed to checkout</button>
				</div>
			</div>
		</div>
	</div>

	<div class="stores-near">
		<div class="sn-title">Store near you</div>
		<div class="sn-search flex">
			<input name="search" placeholder="Enter your zip or address" onkeyup="if (event.keyCode == 13) nearStore(this.value);">
			<span class="fa fa-search" onclick="nearStore($(this).prev().val());"></span>
		</div>
	</div>
</div>

<div id="map"></div>