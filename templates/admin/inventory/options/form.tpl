{include="inventory/groups/menu.tpl"}
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>{lang=addGroup}</div>
	<form class="uForm" method="post" onsubmit="inventory.addGroup(this, event, {id});">
		<div class="iGroup">
			<label>{lang=Name}</label>
			<input type="text" name="name" value="{name}">
		</div>
		<div class="iGroup">
			<label>{lang=Type}</label>
			<select name="type">
				<option value="service" checked>Service</option>
				<option value="inventory">Inventory</option>
			</select>
		</div>
		{options}
		<div class="iGroup addOpt">
			<button class="btn btnSubmit ao" onclick="options.add(this); return false;"><span class="fa fa-plus"></span> {lang=addOption}</button>
		</div>
		<div class="sGroup">
			<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> {send}</button>
		</div>
	</form>
</section>