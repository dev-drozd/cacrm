<div class="tr{confirm}" data-id="{id}">
	<div class="td lh45" style="width: 250px"><span class="thShort flLeft">Staff: </span>
		<a href="/users/view/{user-id}" target="_blank">
			[ava]
				<img src="/uploads/images/users/{user-id}/thumb_{image}" class="miniRound">
			[not-ava]
				<span class="fa fa-user-secret miniRound"></span>
			[/ava]
			{name} {lastname}
		</a>
	</div>
	<div class="td" style="width: 100px"><span class="thShort">Date: </span>{date}</div>
	<div class="td w125"><span class="thShort">Clock in: </span>{start_time}</div>
	<div class="td w125"><span class="thShort">Lunch: </span>{break_start}</div>
	<div class="td w125"><span class="thShort">End lunch: </span>{break_end}</div>
	<div class="td w125"><span class="thShort">Clock out: </span>{end_time} {confirmbt}</div>
	[hours]<div class="td w125"><span class="thShort">Worcking time: </span>{seconds}</div>[/hours]
	[salary][time-money]<div class="td w125" style="width: 125px"><span class="thShort">Salary: </span>${pay}</div>[/time-money][/salary]
</div>
[line]
<div class="tr line-sub">
	<div class="td lh45" style="width: 250px"></div>
	<div class="td" style="width: 100px"></div>
	<div class="td w125"></div>
	<div class="td w125"></div>
	<div class="td w125"></div>
	<div class="td w125"></div>
	[hours]<div class="td w125">{week-total}</div>[/hours]
	[salary][time-money]<div class="td w125" style="width: 125px">${week-salary}[tax]${week-salary-fee}[/tax]</div>[/time-money][/salary]
</div>
[/line]