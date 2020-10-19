<div class="sUser" id="invCategory_{id}">
	<a href='javascript:store.addCategory({id}, "{name}", {parent-json})' class="uInfo">
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
			<li><a href='javascript:store.addCategory({id}, "{name}", {parent-json})'><span class="fa fa-pencil"></span> {lang=editCategory}</a></li>
			<li><a href="javascript:store.delCategory({id})"><span class="fa fa-times"></span> {lang=delCategory}</a></li>
		</ul>
	</div>
	<div class="childs">
	</div>
</div>