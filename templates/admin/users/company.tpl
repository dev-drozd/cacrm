{include="users/menu.tpl"}
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>{lang=allCompanies}</div>
	<div class="mngSearch">
		<input type="text" name="search" placeholder="User search" onkeypress="if(event.keyCode == 13) Search(this.value);" oninput="checkBarcode(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="userList">
		{users}
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