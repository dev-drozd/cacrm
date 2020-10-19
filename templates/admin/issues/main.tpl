<aside class="sideNvg">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>{lang=Manage}</div>
	<ul class="mng">
		<li><a href="/issues" onclick="Page.get(this.href); return false;"><span class="fa fa-cart-plus" style="color: #A2CE4E;"></span>{lang=allIssues}</a></li>
	</ul>
</aside>
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>{lang=allIssues}</div>
	<div class="mngSearch">
		<input type="text" placeholder="{lang=issuesSearch}" onkeypress="if(event.keyCode == 13) Search(this.value);" oninput="checkBarcode(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">{lang=search} <span class="total">Search total: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="userList">
		{issues}
	</div>
	{include="doload.tpl"}
</section>
<script src="{theme}/new-js/issues.js"></script>