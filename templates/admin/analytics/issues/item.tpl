<div class="tr">
	<div class="td"><a href="/issues/view/{id}" onclick="Page.get(this.href); return false;">{id}</a></div>
	<div class="td">
		<a href="/users/view/{intake-id}" onclick="Page.get(this.href); return false;">
			[intake-ava]
				<img src="/uploads/images/users/{intake-id}/{intake-ava}" class="miniRound">
			[not-intake-ava]
				<span class="fa fa-user-secret miniRound"></span>
			[/intake-ava]
			{intake-name}
		</a>
	</div>
	<div class="td">{statuses}</div>
</div>