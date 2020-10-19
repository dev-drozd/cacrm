<div class="sUser" id="invGroup_{id}">
	<a href="/inventory/types/edit/{id}" onclick="Page.get(this.href); return false;" class="uInfo">
		<div class="uInfo">
			{name}
		</div>
	</a>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			<li><a href='javascript:inventory.openType({id}, "{name}", "{type}", {category})'><span class="fa fa-pencil"></span> {lang=editGroup}</a></li>
			<li><a href="/inventory/types/edit/{id}" onclick="Page.get(this.href); return false;" ><span class="fa fa-list"></span> {lang=editOptions}</a></li>
			<li><a href="javascript:inventory.delGroup({id})"><span class="fa fa-times"></span> {lang=delGroup}</a></li>
		</ul>
	</div>
</div>