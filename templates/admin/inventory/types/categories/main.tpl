
[store]
{include="store/menu.tpl"}
[not-store]
{include="inventory/types/menu.tpl"}
[/store]
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>{lang=allBrands} <a href="#" class="btn addBtn" onclick="inventory.addCategory(); return false;">{lang=addBrand}</a></div>
	<div class="mngSearch">
		<input type="text" placeholder="Brand search" onkeypress="if(event.keyCode == 13) Search(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="userList">
		{invCategories}
	</div>
	{include="doload.tpl"}
</section>