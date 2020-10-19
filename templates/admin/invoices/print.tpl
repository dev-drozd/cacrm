<div class="uForm print">
		<div class="uTitle dClear">
			<div class="uName wid50">
				<div>
					Customer: {customer-name} {customer-lastname}
					<p>{customer-address}</p>
				</div>
				<div class="aRight">
					{date}
				</div>
			</div>
		</div>
		<div class="tbl payInfo">
			<div class="tr">
				<div class="th">
					Item
					<button type="button" class="btn btnMini hdn" onclick="invoices.addInventory({id});"><span class="fa fa-plus"></span> Add inventory</button>
				</div>
				<div class="th w10">
					Qty
				</div>
				<div class="th w100">
					Amount
				</div>
				<div class="th w10">
					Tax
				</div>
			</div>
			{inventory}
			[discount]
			<div class="tr">
				<div class="td">
					{discount-name}
				</div>
				<div class="td w10">
					
				</div>
				<div class="td w100">
					-{discount-percent}%
				</div>
				<div class="td w10">
					
				</div>
			</div>
			[/discount]
		</div>
		
		{invoices} 

		<div class="dClear">
			<div class="tbl payTotalInfo">
				<div class="tr">
					<div class="td aRight invInfo">
						Subtotal
					</div>
					<div class="td tAmount">
						<span id="subtotal">{subtotal}</span>$
					</div>
				</div>
				<div class="tr">
					<div class="td aRight invInfo">
						Tax
					</div>
					<div class="td tAmount">
						<span id="tax">{tax}</span>$
					</div>
				</div>
				<div class="tr">
					<div class="td aRight invInfo">
						Total
					</div>
					<div class="td tAmount">
						<span id="total">{total}</span>$
					</div>
				</div>
			</div>
		</div>
		<div class="dClear">
			<div class="tbl payTotalInfo">
				<div class="tr">
					<div class="td aRight invInfo">
						Paid
					</div>
					<div class="td tAmount">
						<span id="paid">{paid}</span>$
					</div>
				</div>
				<div class="tr">
					<div class="td aRight invInfo">
						Due
					</div>
					<div class="td tAmount">
						<span id="due">{due}</span>$
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<script>
		window.onload = function() {
			window.print();
		}
	</script>