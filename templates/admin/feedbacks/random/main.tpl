{include="feedbacks/menu.tpl"}
<div class="pnl fw lPnl">
	<div class="pnlTitle">
		Feedbacks
	<br>
	Here shown customers which are haven't any business in existing (new) system, they were transferred from old system.
	</div>
	<div class="mngSearch">
		<input type="text" name="searchText" placeholder="Search..." onkeypress="if(event.keyCode == 13) Search(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">Search <span class="total">Total: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="tbl">
		<div class="tHead">
			<div class="tr">
				<div class="th w5">New</div>
				<div class="th">Customer</div>
				<div class="th">Phone</div>
				<div class="th">Rating</div>
				<div class="th">Comment</div>
			</div>
		</div>
		<div class="tBody userList">
			{feedbacks}
		</div>
	</div>
	{include="doload.tpl"}
</div>