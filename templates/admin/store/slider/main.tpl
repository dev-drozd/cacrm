{include="store/menu.tpl"}
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>{lang=allSlides} [add]<a href="/store/slider/add" class="btn addBtn" onclick="Page.get(this.href); return false;">{lang=addSlide}</a>[/add]</div>
	<div class="mngSearch">
		<input type="text" placeholder="{lang=slideSearch}" onkeypress="if(event.keyCode == 13) Search(this.value);" oninput="checkBarcode(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="userList">
		{items}
	</div>
	{include="doload.tpl"}
</section>