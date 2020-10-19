{include="camera/menu.tpl"}
<section class="mngContent bfw">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>{lang=Statuses} <a href="#" class="btn addBtn" onclick="camera.openStatus(); return false;">Add Status</a></div>
	<div class="mngSearch">
		<input type="text" placeholder="Status search" onkeypress="if(event.keyCode == 13) Search(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="userList">
		{status}
	</div>
	{include="doload.tpl"}
</section>