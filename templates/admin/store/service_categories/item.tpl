<div class="sUser" id="invCategory_{id}">
	<a href='javascript:store.addServiceCategory({id}, "{name}", {parent-json})' class="uInfo">
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
			[edit-services]<li><a href='javascript:store.addServiceCategory({id}, "{name}", {parent-json})'><span class="fa fa-pencil"></span> Edit category</a></li>[/edit-services]
			[del-services]<li><a href="javascript:store.delServiceCategory({id})"><span class="fa fa-times"></span> Delete category</a></li>[/del-services]
		</ul>
	</div>
	<div class="childs">
	</div>
</div>