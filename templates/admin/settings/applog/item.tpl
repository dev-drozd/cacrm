<div class="sUser[deleted] deleted[/deleted]" id="user_{id}">
	<div class="uThumb">
		[ava]<div><img src="/uploads/images/users/{id}/thumb_{ava}" onclick="showPhoto(this.src);"><span class="fa fa-search-plus" onclick="showPhoto(this.previousSibling.src);"></span></div>[not-ava]<span class="fa fa-user-secret"></span>[/ava]
	</div>
	<a href="/users/view/{id}" onclick="Page.get(this.href); return false;" class="uInfo">
		<div class="uInfo">
			{name} {lastname}
		</div>
	</a>
	<div class="uInfo">
		<b style="color: green">Installed</b>
	</div>
	
	<div class="uInfo">
		<b>Last used:</b> {last-visit}
	</div>

	<div class="uInfo">
		[android]<span class="fa fa-android" style="color: #77c159;"></span>[/android]
		[ios]<span class="fa fa-apple" style="color: #777777;margin-left: 5px;"></span>[/ios]
	</div>
	<div class="uInfo">
		<a href="javascript:msgToApp({id})">
			<span class="fa fa-comment" style="color: #888;"></span>
		</a>
	</div>
</div>