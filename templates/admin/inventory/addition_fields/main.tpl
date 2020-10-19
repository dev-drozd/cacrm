{include="analytics/menu.tpl"}
<div class="mngContent">
	<div class="pnlTitle">
		Addition fields
	</div>
	<div class="mngSearch">
		<input type="text" name="searchText" value="{query}" placeholder="{lang=staffSearch}" onkeypress="if(event.keyCode == 13) Search2(this.value);">
		<button class="btn btnSearch" onclick="Search2($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="tbl">
		<div class="tHead">
			<div class="tr">
				<div class="th wp20">Staff</div>
				<div class="th w125">Name</div>
				<div class="th wp10">Price</div>
				<div class="th wp10">Tax</div>
				<div class="th wp15">Store</div>
				<div class="th wp10">Invoice</div>
				<div class="th wp10">Type</div>
				<div class="th wp15"></div>
				<div class="th wp15"></div>
			</div>
		</div>
	</div>
	<div class="userList tbl">
		<div class="tBody">
			{fields}
		</div>
	</div>
	{include="doload.tpl"}
</div>