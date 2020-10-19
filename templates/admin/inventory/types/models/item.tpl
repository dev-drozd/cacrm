<div class="sUser" id="invModel_{id}">
	<a href='javascript:inventory.addModel({id}, "{name}", {category-json})' class="uInfo">
		<div class="uInfo">
			{name}
		</div>
	</a>
	<div class="pInfo">
		<a href="#" onclick="$('.mngSearch > input').val(this.innerText); Search(this.innerText); return false;">{category-name}</a>
	</div>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			<li><a href='javascript:inventory.addModel({id}, "{name}", {category-json})'><span class="fa fa-pencil"></span> {lang=editModel}</a></li>
			<li><a href="javascript:inventory.delModel({id})"><span class="fa fa-times"></span> {lang=delModel}</a></li>
		</ul>
	</div>
	<div class="childs">
	</div>
</div>