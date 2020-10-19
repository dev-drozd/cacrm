<div class="tr">
	<div class="td wp20">{date}</div>
	<div class="td w125">{store}</div>
	<div class="td wp20 lh45">
		<a href="/users/view/{booker-id}" target="_blank">
			[bava]
				<img src="/uploads/images/users/{booker-id}/thumb_{bimage}" class="miniRound">
			[not-bava]
				<span class="fa fa-user-secret miniRound"></span>
			[/bava]
			{bname} {blastname}
		</a>
	</div>
	<div class="td wp20 lh45">
		<a href="/users/view/{staff-id}" target="_blank">
			[ava]
				<img src="/uploads/images/users/{staff-id}/thumb_{image}" class="miniRound">
			[not-ava]
				<span class="fa fa-user-secret miniRound"></span>
			[/ava]
			{name} {lastname}
		</a>
	</div>
	<div class="td w125">{amount}</div>
</div>