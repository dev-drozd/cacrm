<div class="tr" id="feedback_{id}" data-stars="{ratting}">
	<div class="td w5">
		<a href="#" onclick="issues.addRandomFeedback({id}); return false;" class="eBtn"><span class="fa fa-plus"></span></a>
	</div>
	<div class="td lh45">
		<span class="thShort flLeft" style="margin-right: 10px;">Customer:</span><a href="/users/view/{id}" target="_blank">
			[cava]<img src="/uploads/images/users/{id}/thumb_{cava}" class="miniRound">[not-cava]<span class="fa fa-user-secret miniRound"></span>[/cava]
			{customer_name}
		</a>
	</div>
	<div class="td"><span class="thShort">Phone: </span>{phone}</div>
	<div class="td fb_ratting" onmouseover="issues.starFeedback(this, event);" onmouseout="issues.starFeedback(this, event);" onclick="issues.starFeedback(this, event, {id}, 1);" data-rate="{ratting}">
		<span class="thShort">Rating: </span>{star}
	</div>
	<div class="td" id="comment_{id}"><span class="thShort">Comment: </span>{comment}</div>
</div>