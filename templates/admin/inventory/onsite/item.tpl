<div class="sUser {confirmed}" id="onsite_{id}">
	<a href="[edit-service]/inventory/onsite/edit/{id}[not-edit-service]#[/edit-service]" onclick="Page.get(this.href); return false;" class="uInfo">
		{name}
	</a>
	<div class="uMore">
		[menu]
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			[edit-service]<li><a href="/inventory/onsite/edit/{id}" onclick="Page.get(this.href); return false;" ><span class="fa fa-pencil"></span> {lang=editService}</a></li>[/edit-service]
			[delete-service]<li><a href="javascript:inventory.delOnsite({id})"><span class="fa fa-times"></span> {lang=delService}</a></li>[/delete-service]
		</ul>
		[/menu]
	</div>
</div>