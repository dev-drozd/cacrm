<div class="tr">
	<div class="td">
		<a href="/users/view/{id}" onclick="Page.get(this.href); return false;">
			[image]
				<img src="/uploads/images/users/{id}/{image}" class="miniRound">
			[not-image]
				<span class="fa fa-user-secret miniRound"></span>
			[/image]
			{name}
		</a>
	</div>
	<div class="td">{seconds}</div>
	<div class="td">{currency} {hour_profit}</div>
	<div class="td">{points}</div>
	<div class="td">{currency} {amount}</div>
	<div class="td"><a href="/analytics/users_profit/{id}" onclick="Page.get(this.href); return false;">Details</a></div>
</div>