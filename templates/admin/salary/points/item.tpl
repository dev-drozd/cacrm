<div class="tr">
	<div class="td w50 lh45">
		<a href="/users/view/{user-id}" target="_blank">
			[ava]
				<img src="/uploads/images/users/{user-id}/thumb_{image}" class="miniRound">
			[not-ava]
				<span class="fa fa-user-secret miniRound"></span>
			[/ava]
			{name} {lastname}
		</a>
	</div>
	<div class="td w125 pPoints"><span class="thShort">All points: </span>{points}</div>
	<div class="td w125 pCurrent"><span class="thShort">New points: </span>{current}</div>
	<div class="td w275">
		<input name="money" type="number" step="0.001" value="{money}" class="point_money">
		<button class="btn btnPoints" type="button" onclick="salary.pointsPayout({user-id}, this)"><span class="fa fa-check"></span> Payout</button>
	</div>
</div>