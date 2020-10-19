{include="settings/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>{lang=Settings} <span class="fa fa-chevron-right ltl"></span> <font id="stitle">Franchise</font>
		<button class="btn btnAddGr" onclick="Page.get('/settings/franchise/add');" style="display: inline-block; width: 150px;">Add Franchise</button>
	</div>
	<div class="mngSearch">
		<input type="text" name="search" placeholder="Search" onkeypress="if(event.keyCode == 13) Search(this.value);" oninput="checkBarcode(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="userList">
		{items}
	</div>
</section>