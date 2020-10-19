{include="settings/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>{title}
	</div>
	<form class="uForm" method="post" onsubmit="Settings.sendPoints(this, event);">
		[camera]
		[not-camera]
		[user_suspention]
		[not-user_suspention]
		<div class="iGroup" [ecommerce]style="display: none"[/ecommerce]>
			<label>[trade_in]Trade in, %[not-trade_in]{lang=Points}[/trade_in]</label>
			<input name="{name}" id="points_value" type="number" value="{points}" step="0.001">
		</div>
		[/user_suspention]
		[/camera]
		{fields}
		<div class="sGroup">
			<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> Save</button>
		</div>
	</form>
</section>