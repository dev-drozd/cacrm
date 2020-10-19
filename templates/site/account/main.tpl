<div class="ctnr">
	<div class="page account">
		<h1>Hello, {name} {lastname}!</h1>
		<div class="a-center"><a href="#">Logout</a></div>
		<div class="flex">
			<div class="profile">
				<span>Your name:</span> {name} {lastname}<br>
				<span>Your phone:</span> {phone}<br>
				<span>Your email:</span> {email}<br>
				[address]
				<span>Delivery address:</span> {address}<br>
				[/address]
				<button class="btn" onclick="Page.get('/account/edit')">Change info</button>
			</div>
			<div class="orders">
				<div class="order header">
					<div class="o-id">
						Order
					</div>
					<div class="o-date">
						Date
					</div>
					<div class="o-price">
						Price
					</div>
					<div class="o-checkout">
						Delivery
					</div>
					<div class="o-delivery">
						Status
					</div>
				</div>
				{orders}
			</div>
		</div>
	</div>
</div>