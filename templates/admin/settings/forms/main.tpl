{include="settings/menu.tpl"}
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>{lang=allForms} <a href="/settings/forms/add" class="btn addBtn" onclick="Page.get(this.href); return false;">{lang=addForm}</a></div>
	<div class="mngSearch">
		<input type="text" placeholder="{lang=formSearch}" onkeypress="if(event.keyCode == 13) Search(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="userList">
		{forms}
	</div>
	{include="doload.tpl"}
</section>