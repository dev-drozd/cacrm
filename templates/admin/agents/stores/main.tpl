{include="agents/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>Partner stores
		<a href="/agents/stores/add" class="btn addBtn" onclick="Page.get(this.href); return false;">Add Store</a>
	</div>
	<div class="mngSearch">
		<input type="text" placeholder="Store search" onkeypress="if(event.keyCode == 13) Search(this.value);" oninput="checkBarcode(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">Search <span class="total">Total: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="userList">
		{stores}
	</div>
	{include="doload.tpl"}
</section>
<script>
	$(function() {
		if (location.search.indexOf('?q=') >= 0) {
			var q = location.search.match(/\?q=(.*)/i);
			$('input[name="search"]').val(q[1]);
			Search(q[1])
		}
	})
</script>