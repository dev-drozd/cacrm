<div class="sUser[deleted] deleted[/deleted]" id="user_{id}">
	<div class="uThumb">
		[ava]<div><img src="/uploads/images/users/{id}/thumb_{ava}" onclick="showPhoto(this.src);"><span class="fa fa-search-plus" onclick="showPhoto(this.previousSibling.src);"></span></div>[not-ava]<span class="fa fa-user-secret"></span>[/ava]
	</div>
	<a href="/users/view/{id}" onclick="Page.get(this.href); return false;" class="uInfo">
		<div class="uInfo">
			{name} {lastname}
		</div>
	</a>
	<div class="pInfo">
		<span><a href="tel:{phone}">{phone}</a></span>
		<br />
		<span>{reg-date}</span>
	</div>
	<div class="uMore">
		[deny]
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			<li><a href="/users/view/{id}" onclick="Page.get(this.href); return false;" ><span class="fa fa-eye"></span> {lang=viewUser}</a></li>
			[edit]<li><a href="/users/edit/{id}" onclick="Page.get(this.href); return false;" ><span class="fa fa-pencil"></span> {lang=editUser}</a></li>[/edit]
			[delete]<li><a href="javascript:user.[deleted]restore[not-deleted]del[/deleted]({id});"><span class="fa fa-times"></span> [deleted]{lang=restoreUser}[not-deleted]{lang=delUser}[/deleted]</a></li>[/delete]
		</ul>
		[/deny]
	</div>
</div>