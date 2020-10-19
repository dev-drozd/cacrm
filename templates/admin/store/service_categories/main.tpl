{include="store/menu.tpl"}
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>Service Categories [add-services]<a href="#" class="btn addBtn" onclick="store.addServiceCategory(); return false;">Add category</a>[/add-services]</div>
	<div class="mngSearch">
		<input type="text" placeholder="Category search" onkeypress="if(event.keyCode == 13) Search(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="userList">
		{invCategories}
	</div>
	{include="doload.tpl"}
</section>