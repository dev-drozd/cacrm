{include="store/menu.tpl"}
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>{lang=Orders}</div>
	<div class="mngSearch">
		<input type="text" placeholder="{lang=OrderSearch}" onkeypress="if(event.keyCode == 13) Search(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="usLiHead">
		<div class="sUser head">
			<div class="invInfo wp10">
				ID
			</div>
			<div class="invInfo wpDate">
				Date
			</div>
			<div class="invInfo wp15">
				Customer
			</div>
			<div class="invInfo wp10">
				Total
			</div>
			<div class="invInfo wp20">
				Info
			</div>
			<div class="invInfo wp15">
				Status
			</div>
			<div class="uMore">
				Options
			</div>
		</div>
	</div>
	<div class="userList orders">
		{orders}
	</div>
	{include="doload.tpl"}
</section>