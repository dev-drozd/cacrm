<div class="sUser [conducted]paid[not-conducted]unpaid[/conducted]" id="invoice_history_{id}" onclick="Page.get('/invoices/view/{invoice-id}');">
	<div class="invInfo wCs">
		<span class="thShort">Customer: </span><a href="/users/view/{customer-id}" onclick="Page.get(this.href); return false;">{customer-name} {customer-lastname}</a>
	</div>
	<div class="invInfo wDt">
		<span class="thShort">Date: </span>{date}
	</div>
	<div class="invInfo wAm">
		<span class="thShort">Pay method: </span>{type}
	</div>
	<div class="invInfo wAm">
		<span class="thShort">Amount: </span>{currency}{amount}
	</div>
	<div class="invInfo wPh">
		<span class="thShort">Staff: </span><a href="/users/view/{staff-id}" onclick="Page.get(this.href); return false;">{staff-name} {staff-lastname}</a>
	</div>
</div>