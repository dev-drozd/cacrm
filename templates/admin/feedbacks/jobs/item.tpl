<tr id="feedback_{id}" data-stars="{ratting}" class="[nfb]nFb[not-nfb]wFb[/nfb]">
	<td data-label="Issue:">
		<a href="/issues/view/{id}" target="_blank">#{id}</a>
	</td>
	<td data-label="Customer:">
		<a href="/users/view/{customer-id}" target="_blank">
			[cava]<img src="/uploads/images/users/{customer-id}/thumb_{cava}" class="miniRound">[not-cava]<span class="fa fa-user-secret miniRound"></span>[/cava]
			{customer_name}
		</a>
	</td>
	<td data-label="Phones:">
		{phone}
	</td>
	<td class="fb_ratting" [fb]onmouseover="issues.starFeedback(this, event);" onmouseout="issues.starFeedback(this, event);" onclick="issues.starFeedback(this, event, {fb-id}, 1);" [/fb]data-rate="{ratting}" data-label="Ratting:">
		{star}
	</td>
	<td id="comment_{id}" data-label="Comment:">
		{comment}
	</td>
	<td data-label="Job date:">{date}</td>
	<td data-label="Feedback date:">{fb-date}</td>
	<td align="center" data-label="Actions:">
	[nfb]
		<a href="#" onclick="UsrPhone.box({customer-id}, this.parentNode.parentNode); return false;" class="eBtn act-btn" style="float: none">
			<span class="fa fa-phone"></span>
		</a>
		<a href="#" onclick="issues.addRandomFeedback({id}, 'job'); return false;" class="eBtn act-btn" style="float: none">
			<span class="fa fa-plus"></span>
		</a>
	[/nfb]
	</td>
</tr>