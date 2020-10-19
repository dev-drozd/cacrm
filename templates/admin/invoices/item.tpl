<div class="sUser [conducted]paid[not-conducted]unpaid[/conducted] {refund} {refund-confirm}" id="invoice_{id}">
	<a href="/invoices/view/{id}" onclick="Page.get(this.href); return false;" class="invInfo wp10">
		<span class="thShort">ID: </span>#{id}
	</a>
	<a href="/invoices/view/{id}" onclick="Page.get(this.href); return false;" class="invInfo wpDate">
		<span class="thShort">Date: </span>{date}
	</a>
	<div class="invInfo wp15">
		[estimate]
			No customer
		[not-estimate]
			[quick_sell]
				Quick sale
			[not-quick_sell]
			<span class="thShort">Customer: </span><a href="/users/view/{customer-id}" onclick="Page.get(this.href); return false;" >{customer-name} {customer-lastname}</a>
			[/quick_sell]
			[refund_request]<br>Refund: <a href="/users/view/{refund-id}" onclick="Page.get(this.href); return false;" >{refund-name} {refund-lastname}</a>[/refund_request]
		[/estimate]
	</div>
	<a href="/invoices/view/{id}" onclick="Page.get(this.href); return false;" class="invInfo wp15">
		<span class="thShort">Phone: </span>{customer-phone}
	</a>
	<div class="invInfo wp10">
		<span class="thShort">Pay method: </span>{pay}
	</div>
	<div class="invInfo wp10">
		<span class="thShort">Amount: </span>{currency}{amount}
	</div>
	<div class="invInfo wp10">
		<span class="thShort">Paid: </span>{currency}{paid}
	</div>
	<div class="invInfo wp10">
		<span class="thShort">Due: </span>{currency}{due}
	</div>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
		[deleted]
			<li><a href="javascript:invoices.restore({id})"><span class="fa fa-arrow-left"></span> Restore invoice</a></li>
		[not-deleted]
			[conducted][not-conducted]
				<li><a href="/invoices/view/{id}" onclick="Page.get(this.href); return false;"><span class="fa fa-credit-card"></span> {lang=Checkout}</a></li>
				[order][not-order][edit][refund_confirm][not-refund_confirm][add]<li><a href="/invoices/edit/{id}" onclick="Page.get(this.href); return false;"><span class="fa fa-pencil"></span> {lang=editInvoice}</a></li>[/add][/refund_confirm][/edit][/order]
			[/conducted]
			<li><a href="/invoices/view/{id}" onclick="Page.get(this.href); return false;"><span class="fa fa-eye"></span> {lang=viewInvoice}</a></li>
			[conducted]
				[owner]<li><a href="javascript:invoices.del({id})"><span class="fa fa-times"></span> {lang=delInvoice}</a></li>[/owner]
			[not-conducted]
				[paid]
					[owner]<li><a href="javascript:invoices.del({id})"><span class="fa fa-times"></span> {lang=delInvoice}</a></li>[/owner]
				[not-paid]
					<li><a href="javascript:invoices.del({id})"><span class="fa fa-times"></span> {lang=delInvoice}</a></li>
				[/paid]
			[/conducted]
			<li><a href="/invoices/print/{id}" target="_blank"><span class="fa fa-print"></span> {lang=printInvoice}</a></li>
			<li><a href="/users/view/{customer-id}" target="_blank"><span class="fa fa-user"></span> {lang=viewCustomer}</a></li>
			[refund_confirm]
				[user_refund_confirm]<li><a href="javascript:invoices.refundConfirm({id})"><span class="fa fa-check"></span> Confirm Refund</a></li>[/user_refund_confirm]
				[user_refund_confirm]<li><a href="javascript:invoices.refundDecline({id})"><span class="fa fa-times"></span> Decline Refund</a></li>[/user_refund_confirm]
			[not-refund_confirm]
				[refund-invoice]
				[not-refund-invoice]
					<li><a href="/invoices/make_refund/{id}" onclick="Page.get(this.href); return false;"><span class="fa fa-arrow-left"></span> Refund</a></li>
				[/refund-invoice]
			[/refund_confirm]
			[/deleted]
			<!--<li><a href="javascript:invoices.email({id})" onclick="Page.get(this.href); return false;"><span class="fa fa-envelope-o"></span> {lang=emailInvoice}</a></li>-->
		</ul>
	</div>
</div>