<div class="sUser" id="form_{id}">
	<a href="/settings/forms/edit/{id}" onclick="Page.get(this.href); return false;" class="uInfo">
		<div class="uInfo">
			{name}
		</div>
	</a>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>		
			<li><a href="/settings/forms/edit/{id}" onclick="Page.get(this.href); return false;" ><span class="fa fa-pencil"></span> {lang=editForm}</a></li>
			<li><a href="javascript:Settings.delForm({id})"><span class="fa fa-times"></span> {lang=delForm}</a></li>
		</ul>
	</div>
</div>