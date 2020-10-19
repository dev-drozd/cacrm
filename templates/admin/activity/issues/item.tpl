<div class="tr" onclick="if (!$(event.target).hasClass('nc')) Page.get('/issues/view/{id}');">
	<div class="td"{style}><span class="thShort">ID: </span>#{id}</div>
	<div class="td lh45"{style}><span class="thShort flLeft" style="margin-right: 10px">Owner: </span>
		[user]
			<a href="/users/view/{user_id}" target="_blank" class="nc">
				[user_image]
					<img src="/uploads/images/users/{user_id}/thumb_{user_image}" class="miniRound">
				[not-user_image]
					<span class="fa fa-user-secret miniRound"></span>
				[/user_image]
				{user_name} {user_lastname}
			</a>
		[not-user]
			[object_image]
				<img src="/uploads/images/stores/{object_id}/thumb_{object_image}" class="miniRound">
			[not-object_image]
				<span class="fa fa-user-secret miniRound"></span>
			[/object_image]
			{object_name}
		[/user]
	</div>
	<div class="td"{style}><span class="thShort">Date: </span>{date}</div>
	<div class="td"{style}><span class="thShort">Total: </span>{currency}{total}</div>
	<div class="td"{style}><span class="thShort">Type: </span>{type}</div>
	<div class="td"{style}><span class="thShort">Paid: </span>{paid}</div>
	<div class="td"{style}><span class="thShort">Staff: </span><a href="/users/view/{staff_id}" target="_blank" class="nc">{staff_name} {staff_lastname}</a></div>
	<div class="td"{style}><span class="thShort">Location: </span>{location}</div>
	<div class="td stL"{style}><span class="thShort">Current status: </span>{current_status}</div>
	<div class="td stL"{style}><span class="thShort">Status: </span>{status}</div>
</div>