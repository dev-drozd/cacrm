<div class="sUser" id="invCategory_{id}">
	<span class="fa fa-bars" draggable="true" ondragover="categories.dragover(event)" ondragstart="categories.dragstart(event)" ondragend="categories.dragend(event);" onmousedown="$(this).parent().addClass('drag');" onmouseup="$(this).parent().removeClass('drag');"></span>
	<a href='javascript:inventory.addCategory({id}, "{name}", {parent-json})' class="uInfo">
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
			<li><a href='javascript:inventory.addCategory({id}, "{name}", {parent-json})'><span class="fa fa-pencil"></span> {lang=editBrand}</a></li>
			<li><a href="javascript:inventory.delCategory({id})"><span class="fa fa-times"></span> {lang=delBrand}</a></li>
		</ul>
	</div>
	<div class="childs">
	</div>
</div>