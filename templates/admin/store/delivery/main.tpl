{include="store/menu-settings.tpl"}
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>{lang=DeliveryMethods} <a href="#" class="btn addBtn" onclick="store.openDelivery(); return false;">{lang=addDelivery}</a></div>
	<div class="mngSearch">
		<input type="text" placeholder="{lang=DeliverySearch}" onkeypress="if(event.keyCode == 13) Search(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="userList">
		{delivery}
	</div>
	{include="doload.tpl"}
</section>