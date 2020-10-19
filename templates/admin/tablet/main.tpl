<section class="pnl fw lPnl">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>{title}</div>
	<div class="mngSearch">
		<input type="text" name="search" placeholder="Customer search" onkeypress="if(event.keyCode == 13) Search(this.value);" oninput="checkBarcode(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">Search <span class="total">Total: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="userList">
		{customers}
	</div>
	{include="doload.tpl"}
</section>