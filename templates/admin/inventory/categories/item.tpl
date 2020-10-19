<div class="sUser" id="invCategory_{id}">
	<a href="/inventory/categories/edit/{id}" onclick="Page.get(this.href); return false;" class="uInfo">
		<div class="uInfo">
			{name}
		</div>
	</a>
	<div class="pInfo">
		<a href="#" onclick="$('.mngSearch > input').val(this.innerText); Search(this.innerText); return false;">{parent-name}</a>
	</div>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			<li><a href='javascript:inventory.addCategory({id}, "{name}", {parent-json})'><span class="fa fa-pencil"></span> {lang=editCategory}</a></li>
			<li><a href="/inventory/groups/edit/{id}" onclick="Page.get(this.href); return false;" ><span class="fa fa-list"></span> {lang=editOptions}</a></li>
			<li><a href="javascript:inventory.delCategory({id})"><span class="fa fa-times"></span> {lang=delCategory}</a></li>
		</ul>
	</div>
</div>