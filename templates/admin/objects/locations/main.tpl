{include="objects/menu.tpl"}
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>{object} {lang=Locations} <a href="#" class="btn addBtn" onclick="objects.openLocation(null, null, {object-id}); return false;">{lang=addLocation}</a></div>
	<div class="mngSearch">
		<input type="text" placeholder="{lang=locationSearch}" onkeypress="if(event.keyCode == 13) Search(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="userList">
		{locations}
	</div>
	{include="doload.tpl"}
</section>