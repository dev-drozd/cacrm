{include="settings/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>{title}
	</div>
	[deleted]
	<div class="mt dClear">
		Client deleted
	</div>
	[/deleted]
	<form class="uForm" method="post" [deleted][not-deleted]onsubmit="Settings.sendFranchise(this, event, {id});"[/deleted]>
		<div class="iGroup">
			<label>Name</label>
			<input type="text" name="name" value="{name}">
		</div>
		
		<div class="iGroup plusNew flex">
			<label>Phone</label>
			<div class="phones">
				<div class="phone" data-code="+1">
					<input type="search" name="phone[]" placeholder="(XXX) XXX-XXXX" class="sfWrap" value="" oninput="Phones.format(event);" onkeyup="Phones.format(event);" onblur="Phones.blur(this);" required>
					<input type="radio" name="sms" value="0" checked>
				</div>
				<span class="fa fa-plus" onclick="Phones.add(this.parentNode);"></span>
			</div>
		</div>
		
		<div class="iGroup">
			<label>Address</label>
			<textarea name="address">{address}</textarea>
		</div>
		<div class="iGroup">
			<label>Email</label>
			<input type="text" name="email" value="{email}">
		</div>
		<div class="iGroup">
			<label>Website</label>
			<input type="text" name="website" value="{website}">
		</div>
		<div class="iGroup">
			<label>IP</label>
			<input type="text" name="ip" value="{ip}">
		</div>
		<div class="iGroup">
			<label>Timezones</label>
			<select name="timezone">
				{timezones}
			</select>
		</div>
		<div class="iGroup">
			<label>Contract</label>
			<input type="text" name="contract" value="{Contract}">
		</div>
		<div class="iGroup">
			<label>Amount</label>
			<input type="number" name="amount" value="{amount}" step="0.001">
		</div>
		
		<div class="iGroup imgGroup">
			<label>Photo</label>
			<div class="dragndrop">
				<span class="fa fa-download"></span>
				Click or drag and drop file here
			</div>
		</div>
		
		[deleted]
		[not-deleted]
		<div class="sGroup">
			<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> {send}</button>
		</div>
		[/deleted]
	</form>
</section>