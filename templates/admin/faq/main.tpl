<div class="pnl fw lPnl">
	<div class="pnlTitle">
		{lang=allFaq}
		<a href="/faq/add" class="btn addBtn" onclick="Page.get(this.href); return false;">{lang=AddNew}</a>
	</div>
	<div class="mngSearch">
		<input type="text" name="searchText" placeholder="Search" onkeypress="if(event.keyCode == 13) Search(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="userList faq">
		{faq}
	</div>
	{include="doload.tpl"}
</div>