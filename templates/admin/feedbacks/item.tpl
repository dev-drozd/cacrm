<div class="tr">
	<div class="td" style="width: 60px;">
		[chat][not-chat]
		<span class="thShort">Issue ID: </span><a href="/issues/view/{issue}" target="_blank">#{issue}</a> 
		<br><a href="#" onclick="issues.addFeedback({issue}); return false;" class="eBtn"><span class="fa fa-plus"></span></a>
		[/chat]
	</div>
	<div class="td" style="width: 100px;"><span class="thShort">Date: </span>{date}<br>{time}</div>
	<div class="td lh45" style="width: 200px;">
		<span class="thShort flLeft" style="margin-right: 10px;">Staff: </span><a href="/users/view/{staff_id}" target="_blank">
			[ava]<img src="/uploads/images/users/{staff_id}/thumb_{ava}" class="miniRound">[not-ava]<span class="fa fa-user-secret miniRound"></span>[/ava]
			{staff_name}
		</a>
	</div>
	<div class="td lh45" style="width: 260px;">
		<span class="thShort flLeft" style="margin-right: 10px;">Customer: </span><a href="[chat]/im/support/[not-chat]/users/view/[/chat]{customer_id}" target="_blank">
			[cava]<img src="/uploads/images/[chat]guests[not-chat]users[/chat]/{customer_id}/thumb_{cava}" class="miniRound">[not-cava]<span class="fa fa-user-secret miniRound"></span>[/cava]
			{customer_name}
		</a>
	</div>
	<div class="td" style="width: 150px;"><span class="thShort">Phone: </span>{phone}</div>
	<div class="td fb_ratting" style="width: 200px;" [staff][not-staff]onmouseover="issues.starFeedback(this, event);" onmouseout="issues.starFeedback(this, event);" onclick="issues.starFeedback(this, event, {id});"[/staff] data-rate="{ratting}">
		<span class="thShort">Ratings: </span>{star}
	</div>
	<div class="td" style="width: auto;">
		[custom]
			<span class="hnt hntTop" data-title="Custom feedback"><span class="fa fa-user"></span></span> 
			<a href="/users/view/{send_staff_id}" target="_blank">{send_staff_name}</a><br>
		[/custom]
		[sms]<span class="hnt hntTop" data-title="SMS feedback"><span class="fa fa-comment"></span></span>[/sms]
		[email]<span class="hnt hntTop" data-title="Email feedback"><span class="fa fa-envelope"></span></span>[/email]
		[tablet]<span class="hnt hntTop" data-title="In store feedback"><span class="fa fa-home"></span></span>[/tablet]
		[chat]<span class="hnt hntTop" data-title="Chat feedback"><span class="fa fa-comment"></span></span>[/chat]
		 {comment}
	</div>
</div>