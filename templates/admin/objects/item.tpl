<div class="sUser[close] close[/close]" id="object_{id}">
	<div class="uThumb">
		[ava]<div><img src="/uploads/images/stores/{id}/thumb_{ava}" onclick="showPhoto(this.src);"><span class="fa fa-search-plus" onclick="showPhoto(this.previousSibling.src);"></span></div>[not-ava]<span class="fa fa-user-secret"></span>[/ava]
	</div>
	<a href="/objects/edit/{id}" onclick="Page.get(this.href); return false;" class="uInfo">
		<div class="uInfo">
			{name}
			<p>{descr}</p>
		</div>
	</a>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			[edit]<li><a href="/objects/edit/{id}" onclick="Page.get(this.href); return false;" ><span class="fa fa-pencil"></span> {lang=editObject}</a></li>[/edit]
			[edit]<li><a href="/objects/locations/{id}" onclick="Page.get(this.href); return false;" ><span class="fa fa-pencil"></span> {lang=editLocations}</a></li>[/edit]
			[edit]<li><a href="/objects/devices/{id}" onclick="Page.get(this.href); return false;" ><span class="fa fa-laptop"></span> {lang=Devices}</a></li>[/edit]
			[edit]<li><a href="/users?staffs={id}" onclick="Page.get(this.href); return false;" ><span class="fa fa-laptop"></span> View staffs</a></li>[/edit]
			[edit]<li><a href="/activity/issues?title=1&object={id}" onclick="Page.get(this.href); return false;" ><span class="fa fa-laptop"></span> View old jobs</a></li>[/edit]
			[add]
			[close]
				<li>
					<a href="javascript:objects.close({id})" class="clstore">
						<span class="fa fa-play"></span> Open object
					</a>
				</li>
			[not-close]
				<li>
					<a href="javascript:objects.close({id}, 1)" class="clstore">
						<span class="fa fa-stop"></span> Close object
					</a>
				</li>
			[/close]
			[/add]
			[add]<li><a href="javascript:objects.del({id})"><span class="fa fa-times"></span> {lang=delObject}</a></li>[/add]
		</ul>
	</div>
</div>