<div class="ctnr">
	<h1>Please, enter your credit card details</h1>
	
	<form class="enter-credit" onsubmit="cart.pay(this, event, {id});">
		<input type="hidden" name="id" value="{id}">
		<input type="hidden" name="amount" value="{amount}">
		<div class="card-number">
			<h2>Card number</h2>
			<input type="number" name="card-number">
		</div>
		<div class="dClear">
			<div class="card-exp">
				<div>
					<h2>Month:</h2>
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
				<div>
					<h2>Year:</h2>
					<select name="exp-year">
						{years}
					</select>
				</div>
			</div>
			<div class="card-code">
				<h2>CVV</h2>
				<input type="password" name="code">
			</div>
		</div>
		<div class="submit">
			<button class="btn btnSubmit">Pay</button>
		</div>
	</form> 
	
</div>