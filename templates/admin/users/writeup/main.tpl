{include="users/sett_menu.tpl"}
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>Write up <a href="#" class="btn addBtn" onclick="user.openWriteup(); return false;">Add write up</a></div>
	<div class="mngSearch">
		<input type="text" placeholder="Search" onkeypress="if(event.keyCode == 13) Search(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="userList">
		{writeup}
	</div>
	{include="doload.tpl"}
</section>