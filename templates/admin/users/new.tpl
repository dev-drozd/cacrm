{include="users/sett_menu.tpl"}
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>{lang=NewUsers}</div>
	<form class="uForm" method="post" onsubmit="user.sendDetails(this, event);">
		<div class="iGroup fw">
			<label>
				{lang=EnterEmails}
				<span>{lang=perLine}</span>
			</label>
			<textarea name="emails"></textarea>
		</div>
		<div class="sGroup">
			<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> {lang=sendDetails}</button>
		</div>
	</form>
</section>