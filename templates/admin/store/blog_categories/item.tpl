<div class="sUser" id="invCategory_{id}">
	<a href='javascript:store.addBlogCategory({id}, "{name}", "{pathname}", {parent-json})' class="uInfo">
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
			[edit-blog]<li><a href='javascript:store.addBlogCategory({id}, "{name}", "{pathname}", {parent-json})'><span class="fa fa-pencil"></span> {lang=editCategory}</a></li>[/edit-blog]
			[del-blog]<li><a href="javascript:store.delBlogCategory({id})"><span class="fa fa-times"></span> {lang=delCategory}</a></li>[/del-blog]
		</ul>
	</div>
	<div class="childs">
	</div>
</div>