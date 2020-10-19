<div class="tr">
	<div class="td w125 pPoints">{date}</div>
	<div class="td lh45">
		<a href="/users/view/{user-id}" target="_blank">
			[ava]
				<img src="/uploads/images/users/{user-id}/thumb_{image}" class="miniRound">
			[not-ava]
				<span class="fa fa-user-secret miniRound"></span>
			[/ava]
			{name} {lastname}
		</a>
	</div>
	<div class="td lh45">
		<a href="/users/view/{puser-id}" target="_blank">
			[pava]
				<img src="/uploads/images/users/{puser-id}/thumb_{pimage}" class="miniRound">
			[not-pava]
				<span class="fa fa-user-secret miniRound"></span>
			[/pava]
			{pname} {plastname}
		</a>
	</div>
	<div class="td w125 pPoints"><span class="thShort">Points: </span>{points}</div>
	<div class="td w125 pCurrent"><span class="thShort">Payout: </span>{amount}</div>
</div>