<div class="tr">
	<div class="td wp20 lh45">
		<a href="/users/view/{user-id}" target="_blank">
			[ava]
				<img src="/uploads/images/users/{user-id}/thumb_{image}" class="miniRound">
			[not-ava]
				<span class="fa fa-user-secret miniRound"></span>
			[/ava]
			{name} {lastname}
		</a>
	</div>
	<div class="td w10"><span class="thShort">Points: </span><span[owner] ondblclick="salary.pointsEdit({id}, this);"[/owner]>{points}</span>[rate]<span class="fa fa-check pGr"></span>[/rate]</div>
	<div class="td w200"><span class="thShort">Date: </span>{date}</div>
	<div class="td w125"><span class="thShort">Store: </span>{object}</div>
	<div class="td wAuto"><span class="thShort">Action: </span>{action}</div>
</div>