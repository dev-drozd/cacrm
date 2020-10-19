<div class="sUser[deleted] deleted[/deleted]" id="client_{id}">
	<a href="/settings/franchise/edit/{id}" onclick="Page.get(this.href); return false;" class="uInfo">
		<div class="uInfo">
			{name}
		</div>
	</a>
	<div class="pInfo">
		<span>{phone}</span>
	</div>
	<div class="pInfo">
		<span>{email}</span>
	</div>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			<li><a href="/settings/franchise/edit/{id}" onclick="Page.get(this.href); return false;" ><span class="fa fa-pencil"></span> Edit</a></li>
			[deleted][not-deleted]<li><a href="javascript:Settings.delFranchise({id});"><span class="fa fa-times"></span> Delete</a></li>[/deleted]
		</ul>
	</div>
</div>