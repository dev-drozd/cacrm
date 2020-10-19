{include="store/menu-settings.tpl"}
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>{lang=PaymentMethods} <a href="#" class="btn addBtn" onclick="store.openPayment(); return false;">{lang=addPayment}</a></div>
	<div class="mngSearch">
		<input type="text" placeholder="{lang=PaymentSearch}" onkeypress="if(event.keyCode == 13) Search(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="userList">
		{payment}
	</div>
	{include="doload.tpl"}
</section>