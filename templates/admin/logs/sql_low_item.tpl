<tr onclick="$(this).find('code').toggleClass('open')">
	<td style="background: #{style};">
		<span class="thShort flLeft" style="margin-right: 10px">Staff: </span>
		<a href="/users/view/{uid}" target="_blank">
			[ava]<img src="/uploads/images/users/{uid}/thumb_{ava}" class="miniRound">[not-ava]<span class="fa fa-user-secret miniRound"></span>[/ava]
			{name} {lastname}
		</a>
	</td>
	<td data-label="Time:" style="background: #{style};"><b style="color: #f00;">{time}s</b></td>
	<td data-label="Query:" style="background: #{style};">{query}</td>
	<td data-label="URL:" style="background: #{style};"><a href="{url}?debug" target="_blank">{url}</a></td>
	<td data-label="Date:" style="background: #{style};">{date}</td>
</tr>