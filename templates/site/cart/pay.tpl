<div class="ctnr">
	<div class="page checkout">
		<h1>proceed to Checkout</h1>
		<div class="checkout-nav">
			<a href="#" onclick="return false;">Registration</a>
			<a href="#" onclick="return false;">Delivery info</a>
			<a href="#" class="active" onclick="return false;">Payment</a>
		</div>

		<div class="checkout-payment">
			<form onsubmit="cart.pay(this, event, {id});">
				<input type="hidden" name="id" value="{id}">
				<input type="hidden" name="amount" value="{amount}">
				<div class="enter-card">
					<div class="ec-title">
						Enter card details
					</div>
					<div>
						<div class="input-group">
							<label>Card number</label>
							<input type="number" name="card-number">
						</div>
						<div class="flex">
							<div class="input-group">
								<label>Expary date</label>
								<select name="month">
									<option value="01">Jan</option>
									<option value="02">Feb</option>
									<option value="03">Mar</option>
									<option value="04">Apr</option>
									<option value="05">May</option>
									<option value="06">Jun</option>
									<option value="07">Jul</option>
									<option value="08">Aug</option>
									<option value="09">Sep</option>
									<option value="10">Oct</option>
									<option value="11">Nov</option>
									<option value="12">Dec</option>
								</select>
							</div>
							<div class="input-group">
								<label></label>
								<select name="exp-year">
									{years}
								</select>
							</div>
							<div class="input-group">
								<label>CVV</label>
								<input type="password" name="code">
							</div>
						</div>
					</div>
				</div>
				<button class="btn">Continue</button>
			</form>
		</div>
	</div>
</div>