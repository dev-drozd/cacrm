{include="store/menu-settings.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-left" onclick="history.back()"></span>All items 
		<a href="/store/nav/add[parent]/{parent}[/parent]" class="btn addBtn" onclick="Page.get(this.href); return false;">Add item</a>
	</div>
	<div class="mngSearch">
		<input type="text" placeholder="{lang=PagesSearch}" onkeypress="if(event.keyCode == 13) Search(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	[nav]<div class="nav">{nav}</div>[/nav]
	<div class="userList">
		{items}
	</div>
	{include="doload.tpl"}
</section>