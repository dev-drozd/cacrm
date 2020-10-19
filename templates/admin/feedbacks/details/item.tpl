<tr id="feedback_{id}" data-stars="{ratting}" class="[nfb]nFb[not-nfb]wFb[/nfb]">
	<td data-label="ID:">
		#{id}
	</td>
	<td data-label="Staff:" class="usrt">
		<a href="/users/view/{staff-id}" target="_blank">
			[sava]<img src="/uploads/images/users/{staff-id}/thumb_{sava}" class="miniRound">[not-sava]<span class="fa fa-user-secret miniRound"></span>[/sava]
			{staff_name}
		</a>
	</td>
	<td data-label="Customer:" class="usrt">
		<a href="/users/view/{customer-id}" target="_blank">
			[cava]<img src="/uploads/images/users/{customer-id}/thumb_{cava}" class="miniRound">[not-cava]<span class="fa fa-user-secret miniRound"></span>[/cava]
			{customer_name}
		</a>
	</td>
	<td data-label="Phones:">
		{phone}
	</td>
	<td class="fb_ratting" data-label="Ratting:">
		{star}
	</td>
	<td id="comment_{id}" data-label="Comment:">
		{comment}
	</td>
	<td data-label="Date:">{date}</td>
</tr>