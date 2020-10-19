<div class="tr" id="rec_{id}">
	<div class="td">
		<a href="/objetcs/edit/{store-id}" onclick="Page.get(this.href); return false;">{store}</a>
	</div>
	<div class="td">{date}</div>
	<div class="td">{type}</div>
	<div class="td">
		[issue-id]
		<a href="/issues/view/{issue-id}" onclick="Page.get(this.href); return false;">Issue #{issue-id}</a>:
		<div>{statuses}</div>
		[/issue-id]
	</div>
	<div class="td">{currency} {amount}</div>
</div>