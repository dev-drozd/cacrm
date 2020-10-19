{include="invoices/discount/menu.tpl"}
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>{lang=allDiscounts} <a href="#" class="btn addBtn" onclick="invoices.openDiscount(); return false;">{lang=addDiscount}</a></div>
	<div class="mngSearch">
		<input type="text" placeholder="{lang=discountSearch}" onkeypress="if(event.keyCode == 13) Search(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="userList">
		{discounts}
	</div>
	{include="doload.tpl"}
</section>