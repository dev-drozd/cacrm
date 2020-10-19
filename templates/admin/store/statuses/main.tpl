{include="store/menu-settings.tpl"}
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>{lang=allStatuses} <a href="#" class="btn addBtn" onclick="store.openStatus(); return false;">{lang=addStatus}</a></div>
	<div class="mngSearch">
		<input type="text" placeholder="{lang=StatusSearch}" onkeypress="if(event.keyCode == 13) Search(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="userList">
		{status}
	</div>
	{include="doload.tpl"}
</section>