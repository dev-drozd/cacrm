{include="settings/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>{lang=allRefferals}
		<button class="btn btnAddRef" onclick="Settings.openRefferal();">{lang=AddRefferal}</button>
	</div>
	<div class="mngSearch">
		<input type="text" placeholder="{lang=RefferalSearch}" onkeypress="if(event.keyCode == 13) Search(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="userList">
		{referrals}
	</div>
	{include="doload.tpl"}
</section>