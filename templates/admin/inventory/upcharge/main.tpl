{include="inventory/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>{lang=allUpchargeServices}
		[add]<a href="#" class="btn addBtn" onclick="inventory.addUpcharge(); return false;">Add service</a>[/add]
	</div>
	<div class="mngSearch">
		<input type="text" value="{query}" placeholder="Service search" onkeypress="if(event.keyCode == 13) Search2(this.value);" oninput="checkBarcode(this.value);">
		<button class="btn btnSearch" onclick="Search2($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="userList">
		{upcharges}
	</div>
	{include="doload.tpl"}
</section>