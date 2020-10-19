<div class="sUser[deleted] deleted[/deleted]" id="user_{id}">
	<a href="/agents/customers/{id}" onclick="Page.get(this.href); return false;" class="uInfo">
		<div class="uInfo">
			{name} {lastname}
		</div>
	</a>
	<div class="pInfo">
		<b>{phone}</b><br>
		<i>{email}</i>
	</div>
	<div class="pInfo balance">
		<b>Balance:</b><br>
		<span>${balance}</span>
	</div>
	<div class="pInfo">
		<b>Payment:</b><br>
		<span>{payment}[payment]: {payment_account}[/payment]</span>
	</div>
</div>