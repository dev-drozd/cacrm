{include="settings/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span> SMS {lang=Settings}
	</div>
	<form class="uForm" method="post" onsubmit="Settings.saveSMS(this, event);">
		<div class="iGroup">
			<label>{lang=SmsInterval}</label>
			<input type="number" name="sms_send_interval" value="{sms-send-interval}" step="0.01" min="0">
		</div>
		<div class="iGroup">
			<label>{lang=SmsLimit}</label>
			<input type="number" name="sms_send_limit" value="{sms-send-limit}" min="0">
		</div>
		<div class="iGroup">
			<label>{lang=SmsCount}</label>
			<div class="alrSMS">{sms-sended-today}</div>
		</div>
		<!-- <div class="iGroup">
			<label>Balance</label>
			<div class="alr alrSuccess alrSMS">
				<span class="fa fa-usd"></span>
				<span id="balance">123</span>
			</div>
		</div> -->
		<div class="sGroup">
			<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> {lang=save}</button>
		</div>
	</form>
</section>