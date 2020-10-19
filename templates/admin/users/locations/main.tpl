<div class="mt dClear">
	Attention. The section is under construction.
</div>

{include="users/sett_menu.tpl"}
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>{lang=allUsers}</div>
	<div class="mngSearch">
		<input type="text" placeholder="{lang=LocationSearch}" onkeypress="if(event.keyCode == 13) Search(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="userList">
		{locations}
	</div>
	{include="doload.tpl"}
</section>