{include="store/menu.tpl"}
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>All Discounts <a href="#" class="btn addBtn" onclick="store.addDiscount(this); return false;">Add Discount</a></div>
	<div class="mngSearch">
		<input type="text" placeholder="Search" onkeypress="if(event.keyCode == 13) Search(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">Search <span class="total">Total: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="userList">
		{discounts}
	</div>
	{include="doload.tpl"}
</section>