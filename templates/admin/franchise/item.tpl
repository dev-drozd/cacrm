<div class="sUser[deleted] deleted[/deleted]" id="client_{id}">
	<div class="uThumb">
		[image]<div><img src="/uploads/images/franchises/{id}/thumb_{image}" onclick="showPhoto(this.src);"><span class="fa fa-search-plus" onclick="showPhoto(this.previousSibling.src);"></span></div>[not-image]<span class="fa fa-user-secret"></span>[/image]
	</div>
	<a href="/franchise/edit/{id}" onclick="Page.get(this.href); return false;" class="uInfo">
		<div class="uInfo">
			{name}
		</div>
	</a>
	<div class="pInfo">
		<span>{phone}</span>
	</div>
	<div class="pInfo">
		<span>
			<a href="mailto:{email}">{email}</a>
		</span>
	</div>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			<li><a href="/franchise/view/{id}" onclick="Page.get(this.href); return false;"><span class="fa fa-eye"></span> View</a></li>
			<li><a href="/franchise/edit/{id}" onclick="Page.get(this.href); return false;"><span class="fa fa-pencil"></span> Edit</a></li>
			[deleted][not-deleted]<li><a href="javascript:Settings.delFranchise({id});"><span class="fa fa-times"></span> Delete</a></li>[/deleted]
		</ul>
	</div>
</div>