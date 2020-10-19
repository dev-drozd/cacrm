<aside class="sideNvg">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>Manage</div>
	<ul class="mng">
		<li><a href="/quote" onclick="Page.get(this.href); return false;"><span class="fa fa-cart-plus" style="color: #A2CE4E;"></span>All quote requests</a></li>
	</ul>
</aside>
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>Quotes</div>
	<div class="mngSearch">
		<input type="text" name="search" placeholder="Quote search" onkeypress="if(event.keyCode == 13) Search(this.value);" oninput="checkBarcode(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">Search <span class="total">Total: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="userList">
		{quotes}
	</div>
	{include="doload.tpl"}
</section>