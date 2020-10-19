{include="inventory/types/menu.tpl"}
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>{lang=allVendors} <a href="#" class="btn addBtn" onclick="inventory.openVendor(); return false;">{lang=addVendor}</a></div>
	<div class="mngSearch">
		<input type="text" placeholder="Vendors search" onkeypress="if(event.keyCode == 13) Search(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="userList">
		{vendors}
	</div>
	{include="doload.tpl"}
</section>