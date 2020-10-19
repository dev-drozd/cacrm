{include="inventory/categories/menu.tpl"}
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>{title}</div>
	<form class="uForm" method="post" onsubmit="inventory.sendCategory(this, event, {id});">
		<div class="iGroup">
			<label>{lang=Name}</label>
			<input type="text" name="name" value="{name}">
		</div>
		<!--<div class="iGroup">
			<label>{lang=Type}</label>
			<select name="type">
				<option value="service" checked>Service</option>
				<option value="inventory">Inventory</option>
			</select>
		</div>-->
		<div class="iGroup">
			<label>{lang=Parent}</label>
			<input type="hidden" name="parent">
			<button class="btn btnSubmit" onclick="inventory.selParent('Select parent', 'parent', {id});return false;">Select</button>
		</div>
		<div class="sGroup">
			<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> {send}</button>
		</div>
	</form>
</section>
<script>
$(function() {
	$('input[name="parent"]').data({parent-id});
});
</script>