<div class="imMes[my] my[/my][first] first[/first]" data-msg="{id}" data-usr="{uid}">
		[first]
		<a href="/users/view/{uid}" target="_blank">
			[ava]
				<img src="/uploads/images/users/{uid}/thumb_{image}" class="imImg">
			[not-ava]
				<span class="fa fa-user imImg"></span>
			[/ava]
		</a>
		[/first]
	<div>
		<div class="imMesTop">
			[first]<a href="/users/view/{uid}" target="_blank">{name} {lastname}</a>[/first]
		</div>
		<div class="imMesText[new] new[/new]">
			[my]<span class="fa fa-[del]undo[not-del]times[/del] imDel" onclick="Im.[del]undo[not-del]del[/del]({id});"></span>[/my]
			<span class="imDate">{date}</span>
			{message}
		</div>
	</div>
</div>